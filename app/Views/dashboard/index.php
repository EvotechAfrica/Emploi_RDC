<?php
$e = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$roleLabel = ['candidat'=>'Candidat', 'company'=>'Entreprise', 'admin'=>'Administrateur', 'IT'=>'Équipe technique'][$account['type']];
$pageTitle = $section === 'dashboard' ? 'Tableau de bord' : $menu[$section][0];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $e($pageTitle) ?> — EMPLOI_RDC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $e(BASE_URL) ?>/assets/css/admin.css">
    <style>.stats-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.dashboard-welcome{padding:1.5rem;line-height:1.7}.dashboard-welcome p{color:var(--color-text-secondary);margin:.75rem 0}.dashboard-nav{display:flex;gap:.75rem;flex-wrap:wrap}.sidebar-user{overflow-wrap:anywhere}.dashboard-logout{margin-top:1rem}@media(max-width:600px){.stats-grid{grid-template-columns:1fr}}</style>
    <script>try { const t = localStorage.getItem('theme'); document.documentElement.classList.toggle('dark', t === 'dark' || (!t && matchMedia('(prefers-color-scheme: dark)').matches)); } catch (_) {}</script>
    <script src="<?= $e(BASE_URL) ?>/assets/js/admin.js" defer></script>
</head>
<body data-account-role="<?= $e($account['type']) ?>">
<a class="skip-link" href="#dashboardMain">Aller au contenu</a>
<button id="sidebarOverlay" aria-label="Fermer le menu" hidden></button>
<aside id="sidebar" class="sidebar">
    <a href="?page=dashboard" class="sidebar-logo"><div class="logo-icon">ER</div><div><h1>EMPLOI_RDC</h1><span>Espace <?= $e($roleLabel) ?></span></div></a>
    <nav class="sidebar-nav" aria-label="Espace personnel">
        <a class="nav-link <?= $section === 'dashboard' ? 'active' : '' ?>" href="?page=dashboard" <?= $section === 'dashboard' ? 'aria-current="page"' : '' ?>><i class="fas fa-table-cells-large" aria-hidden="true"></i>Tableau de bord</a>
        <span class="nav-label"><?= $e($roleLabel) ?></span>
        <?php foreach ($menu as $key => [$label, $icon]): ?>
            <a class="nav-link <?= $section === $key ? 'active' : '' ?>" href="?page=<?= $e($menuLinks[$key]) ?>" <?= $section === $key ? 'aria-current="page"' : '' ?>><i class="fas fa-<?= $e($icon) ?>" aria-hidden="true"></i><?= $e($label) ?></a>
        <?php endforeach; ?>
        <span class="nav-label">Compte</span>
        <a class="nav-link" href="?page=compte"><i class="fas fa-gear" aria-hidden="true"></i>Mon compte</a>
        <a class="nav-link" href="?page=accueil"><i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>Voir le site</a>
    </nav>
    <div class="sidebar-user"><div class="user-info"><p><?= $e($account['name']) ?></p><span><?= $e($account['email']) ?></span></div></div>
    <form class="dashboard-logout" method="post" action="?page=deconnexion"><input type="hidden" name="csrf" value="<?= $e(\App\Core\CSRF::token()) ?>"><button class="btn btn-secondary">Se déconnecter</button></form>
</aside>
<div class="main-content">
    <header class="top-bar"><div class="top-bar-inner"><button id="sidebarToggle" class="mobile-menu-btn action-btn" aria-controls="sidebar" aria-expanded="false" aria-label="Ouvrir le menu">☰</button><span>Espace <?= $e($roleLabel) ?></span><button id="adminTheme" class="action-btn" aria-label="Changer de thème">◐</button></div></header>
    <main class="page-content" id="dashboardMain">
        <nav class="breadcrumbs" aria-label="Fil d’Ariane"><a class="breadcrumb-item" href="?page=accueil">Accueil</a><span aria-hidden="true">›</span><?php if ($section !== 'dashboard'): ?><a class="breadcrumb-item" href="?page=dashboard">Tableau de bord</a><span aria-hidden="true">›</span><?php endif; ?><span class="breadcrumb-item active" aria-current="page"><?= $e($pageTitle) ?></span></nav>
        <?php if ($section === 'dashboard'): ?>
        <div><h2 class="page-title">Bonjour, <?= $e($account['name']) ?></h2><p class="page-subtitle">Bienvenue dans votre tableau de bord <?= $e(mb_strtolower($roleLabel)) ?>.</p></div>
        <?php if ($stats !== null): ?><div class="stats-grid"><?php foreach ($stats as $label=>$value): ?><section class="stat-card"><p><?= $e($label) ?></p><strong><?= number_format($value,0,',',' ') ?></strong></section><?php endforeach; ?></div><?php else: ?><p role="status">Vos statistiques sont momentanément indisponibles.</p><?php endif; ?>
        <section class="table-card dashboard-welcome"><h3>Votre espace personnel</h3><p>Retrouvez les informations de votre compte et gérez votre connexion Google.</p><div class="dashboard-nav"><a class="btn btn-primary" href="?page=compte">Gérer mon compte</a><?php if ($account['type'] === 'candidat'): ?><a class="btn btn-secondary" href="?page=accueil#offres">Explorer les offres</a><?php elseif (in_array($account['type'],['admin','IT'],true)): ?><a class="btn btn-secondary" href="?page=admin-apercu">Aperçu des modules d’administration</a><?php endif; ?></div></section>
        <?php else: ?>
            <h2 class="page-title"><?= $e($pageTitle) ?></h2>
            <section class="table-card dashboard-welcome" data-page="<?= $e($contentView) ?>">
                <?php require VIEW_PATH . '/' . $contentView . '.php'; ?>
            </section>
        <?php endif; ?>
    </main>
    <footer class="admin-footer"><span>© <?= date('Y') ?> EMPLOI_RDC</span><span>Tableau de bord · <?= $e($roleLabel) ?></span></footer>
</div>
</body></html>
