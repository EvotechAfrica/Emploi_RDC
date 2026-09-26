<?php
namespace App\Models;

use App\Core\Model;

class AuthAccount extends Model
{
    private function one($sql, array $values)
    {
        $stmt = $this->db->prepare($sql); $stmt->execute($values); return $stmt->fetch();
    }

    public function emailExists($email)
    {
        return $this->one('SELECT id FROM candidat WHERE email = ? LIMIT 1', [$email])
            || $this->one('SELECT id FROM utilisateur WHERE email = ? LIMIT 1', [$email])
            || $this->one('SELECT id FROM company WHERE email = ? LIMIT 1', [$email]);
    }

    public function load($type, $id)
    {
        if ($type === 'candidat') {
            $row = $this->one('SELECT id, email, CONCAT(prenom, " ", nom) AS name, password_hash FROM candidat WHERE id = ? AND deleted_at IS NULL', [$id]);
        } elseif (in_array($type, ['admin','IT','company'], true)) {
            $row = $this->one('SELECT id,email,email AS name,mot_de_passe AS password_hash FROM utilisateur WHERE id = ? AND role = ? AND statut = "actif" AND deleted_at IS NULL AND deux_fa_active = 0', [$id,$type]);
            if ($row && $type === 'company') {
                $company = $this->one('SELECT nom FROM company WHERE utilisateur_id = ? AND deleted_at IS NULL', [$id]);
                if (!$company) return false;
                $row['name'] = $company['nom'];
            }
        } else return false;
        if (!$row) return false;
        $row['type'] = $type;
        return $row;
    }

    public function passwordLogin($email, $password)
    {
        $candidate = $this->one('SELECT id,password_hash FROM candidat WHERE email = ? AND deleted_at IS NULL', [$email]);
        $user = $this->one('SELECT id,role,mot_de_passe AS password_hash FROM utilisateur WHERE email = ? AND deleted_at IS NULL', [$email]);
        foreach ([[$candidate,'candidat'],[$user,$user['role'] ?? '']] as [$row,$type]) {
            if ($row && password_verify($password, $row['password_hash'])) return $this->load($type, $row['id']);
        }
        // Perform a password check even when the email does not exist.
        if (!$candidate && !$user) password_verify($password, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
        return false;
    }

    public function rateLimited($email, $ip)
    {
        $row = $this->one('SELECT COUNT(*) AS count FROM login_attempt WHERE created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE) AND succes = 0 AND (email = ? OR ip = ?)', [$email,$ip]);
        return $row['count'] >= 10;
    }

    public function attempt($email, $ip, $success)
    {
        $stmt = $this->db->prepare('INSERT INTO login_attempt (email,ip,succes) VALUES (?,?,?)');
        $stmt->execute([$email,$ip,$success ? 1 : 0]);
    }

    public function googleAccount($subject)
    {
        $identity = $this->one('SELECT account_type,account_id FROM google_identity WHERE subject = ?', [$subject]);
        if (!$identity) return null;
        $account = $this->load($identity['account_type'], $identity['account_id']);
        if (!$account) throw new \DomainException('Ce compte est indisponible. Contactez le support.');
        return $account;
    }

    public function linkGoogle(array $account, array $claims)
    {
        if (strcasecmp($account['email'], $claims['email']) !== 0) throw new \DomainException('Sélectionnez le compte Google utilisant la même adresse e-mail que votre compte.');
        $stmt = $this->db->prepare('INSERT INTO google_identity (subject,account_type,account_id) VALUES (?,?,?)');
        $stmt->execute([$claims['sub'],$account['type'],$account['id']]);
    }

    public function create(array $data, array $google = null)
    {
        if ($this->emailExists($data['email'])) throw new \DomainException('Cette adresse est déjà utilisée. Connectez-vous avec votre mot de passe, puis associez Google depuis votre compte.');
        $this->db->beginTransaction();
        try {
            $hash = password_hash($google ? bin2hex(random_bytes(32)) : $data['password'], PASSWORD_DEFAULT);
            if ($data['type'] === 'candidat') {
                $stmt = $this->db->prepare('INSERT INTO candidat (nom,prenom,ville,email,telephone,password_hash) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$data['nom'],$data['prenom'],$data['ville'],$data['email'],$data['telephone'] ?: null,$hash]);
                $id = $this->db->lastInsertId();
            } else {
                $stmt = $this->db->prepare('INSERT INTO utilisateur (email,mot_de_passe,role,email_verifie) VALUES (?, ?, "company", ?)');
                $stmt->execute([$data['email'],$hash,$google ? 1 : 0]); $id = $this->db->lastInsertId();
                $stmt = $this->db->prepare('INSERT INTO company (utilisateur_id,nom,slug,ville,email,telephone_contact,secteur,rccm,password_hash) VALUES (?,?,?,?,?,?,?,?,?)');
                $stmt->execute([$id,$data['nom'],'entreprise-' . bin2hex(random_bytes(12)),$data['ville'],$data['email'],$data['telephone'] ?: null,$data['secteur'],$data['rccm'] ?: null,$hash]);
            }
            if ($google) {
                $stmt = $this->db->prepare('INSERT INTO google_identity (subject,account_type,account_id) VALUES (?,?,?)');
                $stmt->execute([$google['sub'],$data['type'],$id]);
            }
            $account = $this->load($data['type'], $id);
            $this->db->commit(); return $account;
        } catch (\Throwable $e) { $this->db->rollBack(); throw $e; }
    }
}
