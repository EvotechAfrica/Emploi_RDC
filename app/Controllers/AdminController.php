<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
    public function preview()
    {
        // Presentation only: no database access or administrative actions.
        header('Cache-Control: no-store');
        header('X-Robots-Tag: noindex, nofollow');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            http_response_code(405);
            header('Allow: GET');
            return;
        }
        $sections = [
            'dashboard' => ['Tableau de bord', 'table-cells-large'],
            'utilisateurs' => ['Utilisateurs', 'users'],
            'companies' => ['Entreprises', 'building'],
            'candidats' => ['Candidats', 'user-tie'],
            'offres' => ['Offres d’emploi', 'briefcase'],
            'categories' => ['Catégories', 'layer-group'],
            'paiements' => ['Paiements', 'credit-card'],
            'plans' => ['Plans & abonnements', 'gem'],
            'signalements' => ['Signalements', 'flag'],
            'audit' => ['Journal d’activité', 'clock-rotate-left'],
        ];
        $section = $_GET['section'] ?? 'dashboard';
        if (!is_string($section) || !isset($sections[$section])) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        $this->view('admin/preview', compact('sections', 'section'));
    }
}
