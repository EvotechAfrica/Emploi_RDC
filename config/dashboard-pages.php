<?php
// Explicit destinations: never build an include path from request parameters.
$views = [
    'candidat' => [
        'profil'=>'candidat/profil', 'cv'=>'candidat/cv', 'competences'=>'candidat/competences',
        'candidatures'=>'candidatures/index', 'favoris'=>'favoris/index', 'alertes'=>'alertes/index',
        'messages'=>'messages/index', 'notifications'=>'notifications/index',
    ],
    'company' => [
        'profil-entreprise'=>'company/profil', 'offres'=>'company/offres', 'publier'=>'company/create-offre',
        'candidatures'=>'company/candidatures', 'messages'=>'messages/index',
        'abonnements'=>'abonnements/index', 'paiements'=>'paiements/index', 'notifications'=>'notifications/index',
    ],
    'admin' => [
        'utilisateurs'=>'admin/utilisateurs', 'entreprises'=>'admin/companies', 'candidats'=>'admin/candidats',
        'offres'=>'admin/offres', 'categories'=>'admin/categories', 'competences'=>'admin/competences',
        'plans'=>'admin/plans', 'paiements'=>'admin/paiements', 'signalements'=>'admin/signalements',
        'avis'=>'admin/avis', 'audit'=>'admin/audit',
    ],
    'IT' => ['audit'=>'admin/audit', 'signalements'=>'admin/signalements', 'notifications'=>'notifications/index'],
];
$routes = [];
foreach ($views as $role=>$sections) {
    foreach ($sections as $section=>$view) {
        $routes[strtolower($role) . '/' . $section] = ['role'=>$role, 'section'=>$section, 'view'=>$view];
    }
}
return $routes;
