<?php
namespace App\Models;

use App\Core\Model;

class Offre extends Model
{
    public function getPublished()
    {
        $sql = "SELECT o.*, c.nom AS company_nom, cat.nom AS categorie_nom
                FROM offre o
                INNER JOIN company c ON c.id = o.company_id
                INNER JOIN categorie cat ON cat.id = o.categorie_id
                WHERE o.statut = 'publiee' AND o.deleted_at IS NULL
                ORDER BY o.date_publication DESC";
        return $this->db->query($sql)->fetchAll();
    }
}