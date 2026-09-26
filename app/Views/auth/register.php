<?php
$assetBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
if (substr($assetBase, -7) !== '/public') { $assetBase .= '/public'; }
$escape = static function ($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte — EMPLOI_RDC</title>
    <script>try { const t = localStorage.getItem('theme'); document.documentElement.classList.toggle('dark', t === 'dark' || (!t && matchMedia('(prefers-color-scheme: dark)').matches)); } catch (_) { document.documentElement.classList.toggle('dark', matchMedia('(prefers-color-scheme: dark)').matches); }</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = {darkMode:'class',theme:{extend:{colors:{primary:{50:'#eff8ff',100:'#dcefff',200:'#b8e0ff',300:'#85ccff',400:'#4db7ff',500:'#0098FF',600:'#007ACC',700:'#0065A9',800:'#00558f',900:'#004672'}}}}};</script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $escape($assetBase) ?>/assets/css/style.css?v=20260922-register">
    <style>
        .account-choice { display:flex; align-items:center; justify-content:center; gap:.75rem; padding:1rem; border:1px solid #007ACC; border-radius:.75rem; color:#007ACC; font-weight:600; }
        .account-choice[aria-current="true"] { background:#007ACC; color:white; }
        .account-choice:hover { box-shadow:0 0 0 2px #007acc30; }
        .dark .account-choice:not([aria-current="true"]) { color:#4db7ff; border-color:#4db7ff; }
        .register-label { display:block; font-size:.875rem; font-weight:600; margin-bottom:.5rem; }
        #registerForm [aria-invalid="true"] { border-color:#ef4444; }
        .field-error { color:#ef4444; font-size:.75rem; margin-top:.4rem; }
    </style>
    <script src="<?= $escape($assetBase) ?>/assets/js/register.js" defer></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-primary-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-slate-800 dark:text-slate-100">
    <header class="wrap py-5 flex items-center justify-between gap-3">
        <a href="?page=accueil" class="flex items-center gap-2" aria-label="EMPLOI RDC — Accueil"><span class="logo">ER</span><span class="hidden sm:block font-extrabold">EMPLOI<span class="text-primary-600 dark:text-primary-400">_RDC</span></span></a>
        <div class="flex items-center gap-2"><a href="?page=connexion" class="text-link">Se connecter</a><button type="button" id="registerTheme" class="icon-button" aria-label="Changer de thème"><i class="fas fa-moon dark:hidden" aria-hidden="true"></i><i class="fas fa-sun hidden dark:inline" aria-hidden="true"></i></button></div>
    </header>
    <main class="max-w-2xl mx-auto px-4 py-6 sm:py-10">
        <nav aria-label="Fil d’Ariane" class="mb-6 text-sm muted"><ol class="flex flex-wrap items-center gap-2">
            <li><a href="?page=accueil" class="hover:underline">Accueil</a></li><li aria-hidden="true">›</li>
            <li><a href="?page=inscription" class="hover:underline">Créer un compte</a></li><li aria-hidden="true">›</li>
            <li id="accountBreadcrumb" aria-current="page" class="text-primary-600 dark:text-primary-400 font-semibold"><?= $accountType === 'company' ? 'Entreprise' : 'Candidat' ?></li>
        </ol></nav>
        <div class="card !rounded-3xl !p-6 sm:!p-10 shadow-xl">
            <div class="text-center mb-7"><h1 class="text-2xl sm:text-3xl font-extrabold">Créez votre compte</h1><p class="muted text-sm mt-2">Votre prochaine opportunité commence ici.</p></div>
            <nav aria-label="Type de compte" class="grid grid-cols-2 gap-3 mb-7" <?= $google ? 'hidden' : '' ?>>
                <a href="?page=inscription&amp;type=candidat" data-account="candidat" class="account-choice" aria-current="<?= $accountType === 'candidat' ? 'true' : 'false' ?>"><i class="fas fa-user" aria-hidden="true"></i>Candidat</a>
                <a href="?page=inscription&amp;type=company" data-account="company" class="account-choice" aria-current="<?= $accountType === 'company' ? 'true' : 'false' ?>"><i class="fas fa-building" aria-hidden="true"></i>Entreprise</a>
            </nav>
            <p id="registerStatus" role="status" tabindex="-1" class="text-sm muted mb-5"><?= $escape($error ?? '') ?></p>
            <form id="registerForm" method="post" action="?page=inscription&amp;type=<?= $accountType ?>" class="space-y-5"><input type="hidden" name="csrf" value="<?= htmlspecialchars(\App\Core\CSRF::token(), ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($google): ?><input type="hidden" name="google_completion" value="1"><p class="text-sm muted">Adresse Google vérifiée. Complétez votre profil pour créer votre compte.</p><?php endif; ?><input id="accountType" type="hidden" name="account_type" value="<?= $accountType ?>">
                <fieldset data-account-fields="candidat" <?= $accountType !== 'candidat' ? 'hidden disabled' : '' ?>><legend class="font-bold mb-4">Votre profil candidat</legend><?php require __DIR__ . '/register-candidat.php'; ?></fieldset>
                <fieldset data-account-fields="company" <?= $accountType !== 'company' ? 'hidden disabled' : '' ?>><legend class="font-bold mb-4">Votre entreprise</legend><?php require __DIR__ . '/register-company.php'; ?></fieldset>
                <div><label class="register-label" for="registerEmail">Adresse e-mail *</label><input id="registerEmail" value="<?= $escape($google['claims']['email'] ?? '') ?>" <?= $google ? 'readonly' : '' ?> name="email" type="email" autocomplete="email" class="field" placeholder="vous@exemple.com" required maxlength="150"></div>
                <div><label class="register-label" for="registerPhone">Téléphone <span class="muted font-normal">(facultatif)</span></label><input id="registerPhone" name="telephone" type="tel" autocomplete="tel" class="field" placeholder="+243 …" maxlength="20"></div>
                <div <?= $google ? 'hidden' : '' ?>><label class="register-label" for="registerPassword">Mot de passe *</label><div class="relative"><input id="registerPassword" <?= $google ? 'disabled' : '' ?> name="password" type="password" autocomplete="new-password" minlength="8" maxlength="72" required class="field !pr-12" aria-describedby="passwordHint"><button type="button" data-password-toggle="registerPassword" class="icon-button absolute right-1 top-1" aria-controls="registerPassword" aria-pressed="false" aria-label="Afficher le mot de passe"><i class="fas fa-eye" aria-hidden="true"></i></button></div><p id="passwordHint" class="muted text-xs mt-2">Utilisez au moins 8 caractères.</p></div>
                <div <?= $google ? 'hidden' : '' ?>><label class="register-label" for="registerConfirm">Confirmer le mot de passe *</label><div class="relative"><input id="registerConfirm" <?= $google ? 'disabled' : '' ?> name="password_confirmation" type="password" autocomplete="new-password" required class="field !pr-12"><button type="button" data-password-toggle="registerConfirm" class="icon-button absolute right-1 top-1" aria-controls="registerConfirm" aria-pressed="false" aria-label="Afficher la confirmation du mot de passe"><i class="fas fa-eye" aria-hidden="true"></i></button></div></div>
                <button type="submit" class="primary-button w-full !py-3.5"><span id="registerSubmitLabel">Créer mon compte <?= $accountType === 'company' ? 'entreprise' : 'candidat' ?></span><i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                <p class="text-xs muted">Les champs marqués d’un astérisque sont obligatoires. </p>
            </form>
            <div class="flex items-center gap-3 my-6" aria-hidden="true"><span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span><span class="text-xs muted">ou</span><span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span></div>
            <button type="submit" form="googleStart" id="registerGoogle" class="secondary-button w-full !py-3.5" <?= $google ? 'hidden' : '' ?>>
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Continuer avec Google</span>
            </button>
            <p class="text-center text-sm muted mt-7">Vous avez déjà un compte ? <a href="?page=connexion" class="text-link">Se connecter</a></p>
        </div>
        <p class="text-center text-xs muted mt-6">© 2026 EMPLOI_RDC — Fait avec ❤️ à Kinshasa</p>
    </main>
<form id="googleStart" method="post" action="?page=google-start"><input type="hidden" name="csrf" value="<?= htmlspecialchars(\App\Core\CSRF::token(), ENT_QUOTES, 'UTF-8') ?>"><input id="googleAccountType" type="hidden" name="account_type" value="<?= htmlspecialchars($accountType ?? 'candidat', ENT_QUOTES, 'UTF-8') ?>"></form></body>
</html>
