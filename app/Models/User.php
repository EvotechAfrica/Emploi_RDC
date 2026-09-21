<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE email = :email AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
}