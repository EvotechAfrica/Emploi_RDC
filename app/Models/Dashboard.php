<?php
namespace App\Models;

use App\Core\Model;

class Dashboard extends Model
{
    public function stats(array $account)
    {
        if ($account['type'] === 'candidat') {
            $queries = [
                'Candidatures envoyées' => 'SELECT COUNT(*) FROM candidature c JOIN candidat_profil p ON p.id=c.candidat_id WHERE p.candidat_id=? AND c.deleted_at IS NULL',
                'Offres favorites' => 'SELECT COUNT(*) FROM favori f JOIN candidat_profil p ON p.id=f.candidat_id WHERE p.candidat_id=?',
                'Entretiens à suivre' => 'SELECT COUNT(*) FROM candidature c JOIN candidat_profil p ON p.id=c.candidat_id WHERE p.candidat_id=? AND c.deleted_at IS NULL AND c.statut="entretien"',
            ];
        } elseif ($account['type'] === 'company') {
            $queries = [
                'Offres publiées' => 'SELECT COUNT(*) FROM offre o JOIN company e ON e.id=o.company_id WHERE e.utilisateur_id=? AND o.deleted_at IS NULL AND o.statut="publiee"',
                'Candidatures reçues' => 'SELECT COUNT(*) FROM candidature c JOIN offre o ON o.id=c.offre_id JOIN company e ON e.id=o.company_id WHERE e.utilisateur_id=? AND c.deleted_at IS NULL AND o.deleted_at IS NULL',
                'Offres en brouillon' => 'SELECT COUNT(*) FROM offre o JOIN company e ON e.id=o.company_id WHERE e.utilisateur_id=? AND o.deleted_at IS NULL AND o.statut="brouillon"',
            ];
        } else {
            $queries = [
                'Candidats inscrits' => 'SELECT COUNT(*) FROM candidat WHERE deleted_at IS NULL',
                'Entreprises inscrites' => 'SELECT COUNT(*) FROM company WHERE deleted_at IS NULL',
                'Offres publiées' => 'SELECT COUNT(*) FROM offre WHERE deleted_at IS NULL AND statut="publiee"',
            ];
        }
        $stats = [];
        foreach ($queries as $label => $sql) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(strpos($sql, '?') !== false ? [$account['id']] : []);
            $stats[$label] = (int) $stmt->fetchColumn();
        }
        return $stats;
    }
}
