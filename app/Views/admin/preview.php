<?php
$escape = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$assetBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$title = $sections[$section][0];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($title) ?> — Administration EMPLOI_RDC</title>
    <meta name="robots" content="noindex,nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $escape($assetBase) ?>/assets/css/admin.css">
    <script>try { const t = localStorage.getItem('theme'); document.documentElement.classList.toggle('dark', t === 'dark' || (!t && matchMedia('(prefers-color-scheme: dark)').matches)); } catch (_) { document.documentElement.classList.toggle('dark', matchMedia('(prefers-color-scheme: dark)').matches); }</script>
    <script src="<?= $escape($assetBase) ?>/assets/js/admin.js" defer></script>
</head>
<body data-section="<?= $escape($section) ?>">
<a class="skip-link" href="#adminMain">Aller au contenu</a>
<button id="sidebarOverlay" aria-label="Fermer le menu" hidden></button>
<aside class="sidebar" id="sidebar">
    <a href="?page=admin-apercu" class="sidebar-logo"><div class="logo-icon">ER</div><div><h1>EMPLOI_RDC</h1><span>Administration</span></div></a>
    <nav class="sidebar-nav" aria-label="Administration">
        <span class="nav-label">Gestion de la plateforme</span>
        <?php foreach ($sections as $key => [$label, $icon]): ?><a class="nav-link <?= $section === $key ? 'active' : '' ?>" href="?page=admin-apercu&amp;section=<?= $key ?>" <?= $section === $key ? 'aria-current="page"' : '' ?>><i class="fas fa-<?= $icon ?>" aria-hidden="true"></i><?= $escape($label) ?></a><?php endforeach; ?>
    </nav>
    <div class="sidebar-user"><div class="user-avatar">AD</div><div class="user-info"><p>Administrateur</p><span>Aperçu de démonstration</span></div><a class="logout-btn" href="?page=accueil" aria-label="Quitter l’aperçu"><i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i></a></div>
</aside>
<div class="main-content">
    <header class="top-bar"><div class="top-bar-inner"><button id="sidebarToggle" class="mobile-menu-btn action-btn" aria-controls="sidebar" aria-expanded="false" aria-label="Ouvrir le menu"><i class="fas fa-bars" aria-hidden="true"></i></button><span>Espace administrateur</span><div class="top-actions"><a href="?page=accueil" class="btn btn-secondary">Voir le site</a><button id="adminTheme" class="action-btn" aria-label="Changer de thème"><i class="fas fa-circle-half-stroke" aria-hidden="true"></i></button></div></div></header>
    <main id="adminMain" class="page-content">
        <div class="preview-notice"><i class="fas fa-circle-info" aria-hidden="true"></i> Aperçu interactif — données fictives. Les modifications disparaissent au rechargement.</div>
        <div><nav class="breadcrumbs" aria-label="Fil d’Ariane"><a class="breadcrumb-item" href="?page=accueil">Accueil</a><span aria-hidden="true">›</span><a class="breadcrumb-item" href="?page=admin-apercu">Administration</a><span aria-hidden="true">›</span><span class="breadcrumb-item active" aria-current="page"><?= $escape($title) ?></span></nav><h2 class="page-title"><?= $escape($title) ?></h2><p class="page-subtitle"><?= $section === 'utilisateurs' ? 'Gérez les comptes et consultez les rôles de la plateforme.' : 'Retrouvez les informations de votre plateforme en un seul endroit.' ?></p></div>
        <?php if ($section === 'dashboard'): ?>
            <div class="stats-grid"><?php foreach ([['1 250','Offres actives','briefcase'],['300','Entreprises','building'],['45 000','Candidats','users'],['24','Signalements à examiner','flag']] as [$number,$label,$icon]): ?><div class="stat-card"><span class="stat-icon"><i class="fas fa-<?= $icon ?>" aria-hidden="true"></i></span><p><?= $label ?></p><strong><?= $number ?></strong><small>Données de démonstration</small></div><?php endforeach; ?></div>
            <div class="dashboard-grid"><section class="table-card"><div class="panel-heading"><h3>Dernières offres</h3><a href="?page=admin-apercu&amp;section=offres">Voir les offres →</a></div><div class="table-wrapper"><table><thead><tr><th scope="col">Poste</th><th scope="col">Entreprise</th><th scope="col">Statut</th></tr></thead><tbody><tr><td class="user-cell">Développeur Laravel</td><td>Entreprise Alpha</td><td><span class="badge badge-active">Publiée</span></td></tr><tr><td>Comptable senior</td><td>Entreprise Beta</td><td><span class="badge badge-pending">À valider</span></td></tr><tr><td>Chargé de programme</td><td>Association Gamma</td><td><span class="badge badge-active">Publiée</span></td></tr></tbody></table></div></section><section class="table-card"><div class="panel-heading"><h3>Actions rapides</h3></div><div class="quick-links"><a class="btn btn-secondary" href="?page=admin-apercu&amp;section=utilisateurs">Gérer les utilisateurs →</a><a class="btn btn-secondary" href="?page=admin-apercu&amp;section=companies">Consulter les entreprises →</a><a class="btn btn-secondary" href="?page=admin-apercu&amp;section=signalements">Examiner les signalements →</a></div></section></div>
        <?php elseif (in_array($section, ['utilisateurs','candidats','companies'], true)): ?>
            <?php if ($section === 'utilisateurs'): ?><div class="view-tabs" role="group" aria-label="Vue du tableau"><button class="btn btn-primary" data-view="users" aria-pressed="true">Utilisateurs</button><button class="btn btn-secondary" data-view="roles" aria-pressed="false">Rôles & accès</button></div><?php endif; ?>
            <div class="toolbar"><div class="search-box"><label class="sr-only" for="tableSearch">Rechercher dans le tableau</label><input id="tableSearch" class="search-input" type="search" placeholder="Rechercher un nom, un email…"></div><div class="top-actions"><label class="sr-only" for="roleFilter">Filtrer par rôle</label><select id="roleFilter" class="form-select"><option value="">Tous les rôles</option value="admin">Administrateur</option><option value="candidat">Candidat</option><option value="company">Entreprise</option></select><button id="addUser" class="btn btn-primary"><i class="fas fa-plus" aria-hidden="true"></i>Ajouter un utilisateur</button></div></div>
            <section id="usersPanel" class="table-card"><div class="table-wrapper"><table><caption class="sr-only">Comptes de démonstration</caption><thead><tr><th scope="col"><button id="sortUsers" class="sort-button">Nom <span aria-hidden="true">↕</span></button></th><th scope="col">Adresse e-mail</th><th scope="col">Rôle</th><th scope="col">Statut</th><th scope="col">Actions</th></tr></thead><tbody id="usersBody"></tbody></table></div><div class="pagination"><span id="resultCount" role="status"></span><div class="top-actions"><button id="previousPage" class="btn btn-secondary">Précédent</button><button id="nextPage" class="btn btn-secondary">Suivant</button></div></div></section>
            <section id="rolesPanel" class="table-card" hidden><div class="table-wrapper"><table><thead><tr><th scope="col">Rôle</th><th scope="col">Description</th><th scope="col">Comptes</th></tr></thead><tbody id="rolesBody"></tbody></table></div></section>
            <noscript><p>Activez JavaScript pour explorer les comptes fictifs et les fenêtres de gestion.</p></noscript>
        <?php else: ?>
            <section class="table-card"><div class="empty-state"><i class="fas fa-<?= $escape($sections[$section][1]) ?> empty-icon" aria-hidden="true"></i><h3><?= $escape($title) ?></h3><p>Cette rubrique est prête à accueillir les données de la plateforme.</p><p>Le module de gestion sera raccordé lors du développement du service.</p><a href="?page=admin-apercu&amp;section=utilisateurs" class="btn btn-primary">Explorer la gestion des utilisateurs</a></div></section>
        <?php endif; ?>
    </main>
    <footer class="admin-footer"><span>© <?= date('Y') ?> EMPLOI_RDC</span><span>Administration · Aperçu</span></footer>
</div>
<dialog id="userDialog" class="admin-dialog" aria-labelledby="userDialogTitle"><div class="modal-header"><h3 id="userDialogTitle" class="modal-title">Ajouter un utilisateur</h3><button class="modal-close" data-close="userDialog" aria-label="Fermer">×</button></div><form id="userForm" method="dialog"><input id="editId" type="hidden"><div class="form-group"><label for="userName" class="form-label">Nom complet</label><input id="userName" class="form-input" required minlength="3" maxlength="100" autocomplete="off"></div><div class="form-group"><label for="userEmail" class="form-label">Adresse e-mail</label><input id="userEmail" type="email" class="form-input" required maxlength="254" autocomplete="off"></div><div class="form-group"><label for="userRole" class="form-label">Rôle</label><select id="userRole" class="form-select"><option value="candidat">Candidat</option><option value="company">Entreprise</option><option value="admin">Administrateur</option></select></div><div class="form-group"><label for="userStatus" class="form-label">Statut</label><select id="userStatus" class="form-select"><option value="active">Actif</option><option value="inactive">Désactivé</option></select></div><p class="form-hint">Démonstration uniquement : aucun compte réel ne sera créé ou modifié.</p><p id="formError" role="alert"></p><div class="modal-actions"><button type="button" class="btn btn-secondary" data-close="userDialog">Annuler</button><button class="btn btn-primary" type="submit">Enregistrer dans l’aperçu</button></div></form></dialog>
<dialog id="deleteDialog" class="admin-dialog" aria-labelledby="deleteTitle"><h3 id="deleteTitle" class="modal-title">Désactiver l’utilisateur ?</h3><p class="dialog-description">Le compte fictif <strong id="deleteName"></strong> sera marqué comme désactivé dans cet aperçu.</p><div class="modal-actions"><button class="btn btn-secondary" data-close="deleteDialog">Annuler</button><button class="btn btn-primary" id="confirmDelete">Désactiver</button></div></dialog>
<div id="adminToast" class="toast" role="status" hidden></div>
</body></html>
