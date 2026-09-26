<?php
$assetBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
if (substr($assetBase, -7) !== '/public') { $assetBase .= '/public'; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — EMPLOI_RDC</title>
    <meta name="description" content="Connectez-vous à votre compte EMPLOI_RDC pour postuler et gérer vos candidatures.">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#eff8ff',
                            100: '#dcefff',
                            200: '#b8e0ff',
                            300: '#85ccff',
                            400: '#4db7ff',
                            500: '#0098FF',
                            600: '#007ACC',
                            700: '#0065A9',
                            800: '#00558f',
                            900: '#004672',
                        },
                        gold: {50: '#fffbeb',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                        slideUp: { '0%': { opacity: 0, transform: 'translateY(15px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        try { const theme = localStorage.getItem('theme'); document.documentElement.classList.toggle('dark', theme === 'dark' || (!theme && matchMedia('(prefers-color-scheme: dark)').matches)); }
        catch (_) { document.documentElement.classList.toggle('dark', matchMedia('(prefers-color-scheme: dark)').matches); }
    </script>
    <style>
        button { color: #007ACC; }
        #submitBtn { background: #007ACC; color: white; }
        #submitBtn:hover { background: #0065A9; }
        [data-provider] { border-color: #007ACC; color: #007ACC; }
        [data-provider] span { color: inherit; }
        .google-login { display: flex; align-items: center; justify-content: center; gap: .75rem; width: 100%; min-height: 3rem; padding: .875rem 1rem; border: 1px solid #007ACC; border-radius: .75rem; background: white; font-size: .875rem; font-weight: 600; line-height: 1.4; transition: background-color .2s, border-color .2s, box-shadow .2s; }
        .google-login svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        .google-login:hover { background: #eff8ff; border-color: #0065A9; box-shadow: 0 2px 8px #007acc15; }
        .google-login:active { background: #dcefff; }
        .dark .google-login { background: #1e293b; border-color: #4db7ff; }
        .dark .google-login:hover { background: #27384d; }
        .dark .google-login:active { background: #30465f; }
        #themeToggle i, #eyeIcon { color: #007ACC; }
        .dark [data-provider], .dark #themeToggle i, .dark #eyeIcon { color: #4db7ff; }
        input[type="checkbox"] { accent-color: #007ACC; }
        :focus-visible { outline: 3px solid #4db7ff; outline-offset: 3px; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-gold-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 flex flex-col">

    <!-- =========================================================
         HEADER MINIMAL
    ========================================================== -->
    <header class="w-full px-4 sm:px-6 lg:px-8 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Logo -->
            <a href="?page=accueil" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-600 to-primary-800 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-500/30 group-hover:scale-105 transition">
                    ER
                </div>
                <div class="hidden sm:block">
                    <span class="font-extrabold text-lg text-slate-900 dark:text-white">EMPLOI<span class="text-primary-600 dark:text-primary-400">_RDC</span></span>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 -mt-1">🇨🇩 Le job, près de chez vous</p>
                </div>
            </a>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button id="themeToggle" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-white/60 dark:hover:bg-slate-800 transition" aria-label="Basculer le thème">
                    <i class="fas fa-moon dark:hidden text-slate-600"></i>
                    <i class="fas fa-sun hidden dark:inline text-gold-400"></i>
                </button>
                <a href="?page=accueil" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-800 rounded-lg transition">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </header>

    <!-- =========================================================
         FORMULAIRE DE CONNEXION
    ========================================================== -->
    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">

        <div class="w-full max-w-md animate-slide-up">

            <!-- Carte -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl shadow-slate-200/60 dark:shadow-black/40 border border-slate-200 dark:border-slate-800 p-8 sm:p-10">

                <!-- Logo mobile -->
                <div class="sm:hidden flex justify-center mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-primary-500/30">
                        ER
                    </div>
                </div>

                <!-- Titre -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        Connexion à votre compte
                    </h1>                   
                </div>

                <!-- Formulaire -->
                <p id="authStatus" role="status" tabindex="-1" class="mb-5 text-sm text-slate-600 dark:text-slate-300"><?= htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <form id="loginForm" method="post" action="?page=connexion" class="space-y-5" novalidate><input type="hidden" name="csrf" value="<?= htmlspecialchars(\App\Core\CSRF::token(), ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Adresse e-mail
                        </label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                            <input
                                type="email"
                                id="email" aria-describedby="emailError"
                                name="email"
                                autocomplete="email"
                                required
                                placeholder="votreadresse@gmail.com"
                                class="w-full pl-11 pr-4 py-3.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition"
                            >
                        </div>
                        <p id="emailError" aria-live="polite" class="hidden mt-1.5 text-xs text-red-500">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span></span>
                        </p>
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Mot de passe
                            </label>
                            <a href="#authStatus" data-auth-unavailable="La réinitialisation du mot de passe sera disponible prochainement." class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                                Mot de passe oublié ?
                            </a>
                        </div>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                            <input
                                type="password"
                                id="password" aria-describedby="passwordError"
                                name="password"
                                autocomplete="current-password"
                                required
                                placeholder="••••••••"
                                class="w-full pl-11 pr-12 py-3.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition"
                            >
                            <button
                                type="button"
                                id="passwordToggle" aria-controls="password" aria-pressed="false"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                                aria-label="Afficher ou masquer le mot de passe"
                            >
                                <i id="eyeIcon" class="fas fa-eye text-slate-400 text-sm"></i>
                            </button>
                        </div>
                        <p id="passwordError" aria-live="polite" class="hidden mt-1.5 text-xs text-red-500">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span></span>
                        </p>
                    </div>

                    <!-- Bouton principal -->
                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full py-3.5 px-4 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 active:scale-[0.98] transition flex items-center justify-center gap-2"
                    >
                        <span id="submitText">Se connecter</span>
                        <i id="submitIcon" class="fas fa-arrow-right"></i>
                        <svg id="submitLoader" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </button>
                </form>

                <!-- Séparateur -->
                <div class="relative my-7">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-3 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-medium">
                            ou continuer avec
                        </span>
                    </div>
                </div>

                <!-- Connexion sociale -->
                <div>
                    <button type="submit" form="googleStart" data-provider="Google" class="google-login">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Se connecter avec Google</span>
                    </button>
                  
                </div>

                <!-- Pied de carte -->
                <p class="text-center text-sm text-slate-600 dark:text-slate-400 mt-8">
                    Vous n'avez pas de compte ?
                    <a href="?page=inscription" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                        S'inscrire
                    </a>
                </p>
            </div>

            <!-- Note de sécurité -->
            <div class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <i class="fas fa-shield-alt text-green-500"></i>
                <span>Vos identifiants restent confidentiels.</span>
            </div>

            <!-- Copyright -->
            <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
                © 2026 EMPLOI_RDC — Fait avec ❤️ à Kinshasa
            </p>
        </div>
    </main>

    <!-- =========================================================
         JAVASCRIPT
    ========================================================== -->
    <script src="<?= htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8') ?>/assets/js/login.js" defer></script>
<form id="googleStart" method="post" action="?page=google-start"><input type="hidden" name="csrf" value="<?= htmlspecialchars(\App\Core\CSRF::token(), ENT_QUOTES, 'UTF-8') ?>"><input id="googleAccountType" type="hidden" name="account_type" value="<?= htmlspecialchars($accountType ?? 'candidat', ENT_QUOTES, 'UTF-8') ?>"></form></body>
</html>
