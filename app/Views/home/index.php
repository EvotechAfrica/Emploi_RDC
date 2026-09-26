<?php
$categories = [
    ['Tech & IT', 'laptop-code', 'primary', 142],
    ['Finance & Banque', 'chart-line', 'emerald', 98],
    ['Mines & Énergie', 'hard-hat', 'amber', 87],
    ['Logistique', 'truck', 'sky', 65],
    ['Vente & Marketing', 'bullhorn', 'pink', 112],
    ['Santé', 'heartbeat', 'red', 54],
    ['ONG & Humanitaire', 'hands-helping', 'violet', 73],
    ['Administration', 'building', 'indigo', 89],
];
$featured = [
    ['Développeur Fullstack Senior', 'Airtel RDC', 'Kinshasa', 'CDI', '$2500 - $3500', 'Il y a 2h', 'Tech & IT'],
    ['Ingénieur Minier Junior', 'Rawbank SA', 'Lubumbashi', 'CDI', '$3000 - $4500', 'Il y a 5h', 'Mines & Énergie'],
    ['Chef de Projet Digital', 'Vodacom Congo', 'Kinshasa', 'CDI', '$2000 - $3000', 'Hier', 'Tech & IT'],
];
$jobs = [
    ['Développeur Laravel', 'Gécamines', 'Kinshasa', 'CDI', '$1200 - $1800', 'Il y a 1h', 'Tech & IT'],
    ['Comptable Senior', 'Rawbank', 'Kinshasa', 'CDI', '$1500 - $2200', 'Il y a 3h', 'Finance & Banque'],
    ['Ingénieur Civil', 'Mutanda Mining', 'Lubumbashi', 'CDD', '$2000 - $2800', 'Il y a 6h', 'Mines & Énergie'],
    ['Chargé de Programme', 'Oxfam RDC', 'Goma', 'CDI', '$1800 - $2500', 'Il y a 8h', 'ONG & Humanitaire'],
    ['Data Analyst', 'Vodacom', 'Kinshasa', 'CDI', '$1600 - $2400', 'Hier', 'Tech & IT'],
    ['Infirmier Diplômé', 'UNICEF RDC', 'Bukavu', 'CDD', '$900 - $1400', 'Hier', 'Santé'],
];
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$assetBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
if (substr($assetBase, -7) !== '/public') {
    $assetBase .= '/public';
}
$renderJob = static function ($job, $id, $premium = false) use ($escape) {
    [$title, $company, $city, $contract, $salary, $date, $category] = $job;
?>
    <article class="job-card card <?= $premium ? 'premium' : '' ?>" data-job data-title="<?= $escape($title . ' ' . $company) ?>" data-city="<?= $escape($city) ?>" data-category="<?= $escape($category) ?>" data-contract="<?= strtolower($contract) ?>">
        <?php if ($premium): ?><span class="sponsored"><i class="fas fa-crown"></i> SPONSORISÉ</span><?php endif; ?>
        <div class="flex items-start gap-3 mb-4">
            <div class="company-avatar"><?= $escape(substr($company, 0, 1)) ?></div>
            <div class="min-w-0 flex-1">
                <h3 class="font-bold text-lg"><?= $escape($title) ?></h3>
                <p class="muted text-sm"><?= $escape($company) ?></p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 mb-3"><span class="tag"><i class="fas fa-map-marker-alt mr-1"></i><?= $escape($city) ?></span><span class="tag contract"><?= $escape($contract) ?></span></div>
        <p class="font-semibold text-primary-600 dark:text-primary-400 mb-4"><?= $escape($salary) ?> <span class="muted text-xs font-normal">/ mois</span></p>
        <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-3"><span class="muted text-xs"><i class="far fa-clock mr-1"></i><?= $escape($date) ?></span>
            <div class="flex items-center gap-3"><?php if ($premium): ?><button class="text-primary-600 dark:text-primary-400 text-sm font-semibold" data-unavailable="Les candidatures seront disponibles après l’activation du module de recrutement.">Postuler →</button><?php endif; ?><button class="favorite icon-button" data-favorite="<?= $escape($id) ?>" aria-label="Sauvegarder <?= $escape($title) ?>" aria-pressed="false"><i class="far fa-heart"></i></button></div>
        </div>
    </article>
<?php
};
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMPLOI_RDC — Trouvez l'emploi de vos rêves en RDC</title>
    <meta name="description" content="Plateforme de recherche d'emploi en République Démocratique du Congo. Offres à Kinshasa, Lubumbashi, Goma et partout en RDC.">
    <script>
        try {
            const t = localStorage.getItem('theme');
            document.documentElement.classList.toggle('dark', t === 'dark' || (!t && matchMedia('(prefers-color-scheme: dark)').matches));
        } catch (_) {
            document.documentElement.classList.toggle('dark', matchMedia('(prefers-color-scheme: dark)').matches);
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff8ff',
                            100: '#dcefff',
                            200: '#b8e0ff',
                            300: '#85ccff',
                            400: '#4db7ff',
                            500: '#0098FF',
                            600: '#007ACC',
                            700: '#0065A9',
                            800: '#00558f',
                            900: '#004672'
                        },
                        gold: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $escape($assetBase) ?>/assets/css/style.css?v=20260922-buttons">
    <script src="<?= $escape($assetBase) ?>/assets/js/app.js" defer></script>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300">
    <a class="skip-link" href="#main">Aller au contenu</a>
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800">
        <div class="wrap flex items-center justify-between h-16 lg:h-20 gap-4">
            <a href="#" class="flex items-center gap-2 shrink-0" aria-label="EMPLOI RDC — Accueil"><span class="logo">ER</span><span class="hidden sm:block"><span class="font-extrabold text-lg">EMPLOI<span class="text-primary-600 dark:text-primary-400">_RDC</span></span><small class="block text-[10px] muted">🇨🇩 Le job, près de chez vous</small></span></a>
            <nav class="hidden lg:flex items-center gap-1" aria-label="Navigation principale"><?php foreach (['offres' => 'Offres', 'entreprises' => 'Entreprises', 'categories' => 'Catégories', 'blog' => 'Blog', 'about' => 'À propos'] as $id => $label): ?><a class="nav-link" href="#<?= $id ?>"><?= $label ?></a><?php endforeach; ?></nav>
            <div class="hidden lg:flex items-center gap-2"><span class="tag">FR</span><button class="icon-button" data-theme aria-label="Changer de thème"><i class="fas fa-moon dark:hidden"></i><i class="fas fa-sun hidden dark:inline"></i></button><button class="icon-button" aria-label="Notifications" data-unavailable="Les notifications seront disponibles après connexion."><i class="far fa-bell"></i></button><button class="icon-button" aria-label="Messages" data-unavailable="La messagerie sera disponible après connexion."><i class="far fa-comment-dots"></i></button><button class="primary-button text-sm" data-unavailable="La publication d’offres sera disponible après l’activation de l’espace recruteur."><i class="fas fa-plus-circle"></i> Publier une offre</button><a class="nav-link" href="?page=<?= isset($_SESSION['account']) ? 'compte' : 'connexion' ?>"><?= isset($_SESSION['account']) ? 'Mon compte' : 'Connexion' ?></a></div>
            <button id="mobileMenuToggle" class="icon-button lg:hidden" aria-label="Ouvrir le menu" aria-controls="mobileMenu" aria-expanded="false"><i class="fas fa-bars"></i></button>
        </div>
        <nav id="mobileMenu" class="hidden lg:hidden border-t border-slate-200 dark:border-slate-800 p-4" aria-label="Navigation mobile"><?php foreach (['offres' => 'Offres', 'entreprises' => 'Entreprises', 'categories' => 'Catégories', 'blog' => 'Blog', 'about' => 'À propos'] as $id => $label): ?><a class="nav-link block" href="#<?= $id ?>"><?= $label ?></a><?php endforeach; ?><div class="flex gap-2 my-3"><a class="secondary-button flex-1" href="?page=<?= isset($_SESSION['account']) ? 'compte' : 'connexion' ?>"><?= isset($_SESSION['account']) ? 'Mon compte' : 'Connexion' ?></a><a class="primary-button flex-1" href="?page=inscription">Inscription</a></div><button class="primary-button w-full" data-unavailable="L’espace recruteur sera bientôt disponible.">Publier une offre</button><button class="secondary-button w-full mt-2" data-theme>Changer de thème</button></nav>
    </header>
    <main id="main">
        <section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-gold-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900">
            <div class="absolute top-0 -left-20 w-96 h-96 bg-primary-300/30 dark:bg-primary-700/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 right-0 w-96 h-96 bg-gold-300/30 rounded-full blur-3xl"></div>
            <div class="wrap relative py-12 lg:py-20 grid lg:grid-cols-2 gap-10 items-center">
                <div><span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 mb-4"><span class="w-2 h-2 bg-green-500 rounded-full"></span>1 250+ offres actives en RDC</span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">Trouvez l'emploi de <span class="text-primary-600 dark:text-primary-400">vos rêves</span> en <span class="text-gold-600">RDC</span></h1>
                    <p class="mt-5 text-lg muted max-w-xl">Connectez votre talent aux meilleures entreprises à Kinshasa, Lubumbashi, Goma et partout en République Démocratique du Congo.</p>
                    <form id="searchForm" class="mt-8 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-3 border border-slate-200 dark:border-slate-800">
                        <div class="grid sm:grid-cols-2 gap-2"><label class="sr-only" for="keyword">Poste, mot-clé</label><input id="keyword" class="field sm:col-span-2" type="search" placeholder="Poste, mot-clé…"><label class="sr-only" for="category">Catégorie</label><select id="category" class="field">
                                <option value="">Toutes catégories</option><?php foreach ($categories as $category): ?><option><?= $escape($category[0]) ?></option><?php endforeach; ?>
                            </select><label class="sr-only" for="city">Ville</label><select id="city" class="field">
                                <option value="">Toutes les villes</option><?php foreach (['Kinshasa', 'Lubumbashi', 'Goma', 'Matadi', 'Bukavu'] as $city): ?><option><?= $city ?></option><?php endforeach; ?>
                            </select><button class="primary-button sm:col-span-2" type="submit"><i class="fas fa-search"></i> Chercher</button></div>
                    </form>
                    <div class="mt-6 flex flex-wrap items-center gap-4"><button class="secondary-button" data-how-link><i class="fas fa-briefcase"></i> Je suis recruteur</button><span class="text-sm muted"><i class="fas fa-fire text-orange-500 mr-1"></i><strong>48</strong> nouvelles offres aujourd'hui</span></div>
                    <p class="muted text-xs mt-4">Aperçu de la plateforme : offres, chiffres et témoignages de démonstration.</p>
                </div>
                <div class="relative hidden lg:block">
                    <div class="grid grid-cols-2 gap-4"><?php foreach (['1573497019940-1c28c88b4f3e', '1560250097-0b93528c311a', '1580894732444-8ecded7900cd', '1594744803329-e58b31de8bf5'] as $i => $photo): ?><img src="https://images.unsplash.com/photo-<?= $photo ?>?w=400&amp;h=500&amp;fit=crop" alt="Professionnel au travail" width="400" height="500" class="hero-photo <?= $i % 2 ? 'mt-8 -rotate-2' : 'rotate-2' ?>"><?php endforeach; ?></div>
                    <div class="absolute -bottom-4 -left-4 card flex items-center gap-3 shadow-2xl"><span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600"><i class="fas fa-check"></i></span>
                        <div>
                            <p class="text-xs muted">Candidature envoyée</p>
                            <p class="text-sm font-semibold">Développeur Fullstack</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="categories" class="section bg-white dark:bg-slate-950">
            <div class="wrap">
                <div class="section-heading">
                    <div>
                        <h2>Catégories populaires</h2>
                        <p class="muted mt-2">Explorez les secteurs qui recrutent le plus en RDC</p>
                    </div><a href="#offres" class="text-link">Voir toutes les catégories →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4"><?php foreach ($categories as [$name, $icon, $color, $count]): ?><a href="#offres" data-category-link="<?= $escape($name) ?>" class="card category-card"><span class="category-icon" style="--accent:<?= ['primary' => '#007ACC', 'emerald' => '#059669', 'amber' => '#d97706', 'sky' => '#0284c7', 'pink' => '#db2777', 'red' => '#dc2626', 'violet' => '#7c3aed', 'indigo' => '#007ACC'][$color] ?>"><i class="fas fa-<?= $icon ?>"></i></span>
                            <h3 class="font-semibold"><?= $escape($name) ?></h3>
                            <p class="text-sm muted mt-1"><?= $count ?> offres</p>
                        </a><?php endforeach; ?></div>
            </div>
        </section>
        <section class="section bg-gradient-to-br from-amber-50 to-white dark:from-slate-900 dark:to-slate-950">
            <div class="wrap">
                <div class="section-heading">
                    <div class="flex gap-3 items-center"><span class="logo gold"><i class="fas fa-star"></i></span>
                        <div>
                            <h2>Offres à la une</h2>
                            <p class="muted mt-2">Les opportunités premium mises en avant par nos partenaires</p>
                        </div>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6"><?php foreach ($featured as $i => $job) {
                                                                            $renderJob($job, 'featured-' . $i, true);
                                                                        } ?></div>
            </div>
        </section>
        <section id="offres" class="section bg-slate-50 dark:bg-slate-900">
            <div class="wrap">
                <h2>Dernières offres publiées</h2>
                <p class="muted mt-2 mb-8">Les opportunités les plus récentes sur la plateforme</p>
                <div class="flex gap-2 mb-8 overflow-x-auto pb-2" role="group" aria-label="Filtrer par contrat"><?php foreach (['all' => 'Tous', 'cdi' => 'CDI', 'cdd' => 'CDD', 'stage' => 'Stage', 'freelance' => 'Freelance'] as $value => $label): ?><button class="filter-button <?= $value === 'all' ? 'selected' : '' ?>" data-contract-filter="<?= $value ?>" aria-pressed="<?= $value === 'all' ? 'true' : 'false' ?>"><?= $label ?></button><?php endforeach; ?></div>
                <p id="resultCount" class="muted text-sm mb-4" aria-live="polite"></p>
                <div id="jobGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-5"><?php foreach ($jobs as $i => $job) {
                                                                                        $renderJob($job, 'job-' . $i);
                                                                                    } ?></div>
                <div id="emptyResults" class="hidden card text-center">
                    <p>Aucune offre ne correspond à votre recherche.</p><button class="text-link mt-3" data-reset-search>Réinitialiser les filtres</button>
                </div>
                <div class="text-center mt-10"><button class="primary-button" data-unavailable="Toutes les offres de démonstration sont déjà affichées. Le catalogue complet sera disponible après connexion à la base de données.">Voir plus d'offres <i class="fas fa-arrow-right"></i></button></div>
            </div>
        </section>
        <section id="entreprises" class="section bg-white dark:bg-slate-950">
            <div class="wrap">
                <div class="text-center mb-12">
                    <h2>Elles font confiance à EMPLOI_RDC</h2>
                    <p class="muted mt-3">Rejoignez les entreprises leaders qui recrutent les meilleurs talents congolais</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4"><?php foreach (['Vodacom' => 12, 'Airtel' => 8, 'Rawbank' => 15, 'Gécamines' => 22, 'UNICEF' => 9, 'Oxfam' => 6] as $company => $count): ?><div class="card text-center">
                            <div class="company-avatar mx-auto mb-3"><?= substr($company, 0, 1) ?></div>
                            <p class="font-semibold text-sm"><?= $company ?></p>
                            <p class="text-xs muted mt-1"><i class="fas fa-check-circle text-blue-500"></i> <?= $count ?> offres</p>
                        </div><?php endforeach; ?></div>
            </div>
        </section>
        <section id="how" class="section bg-gradient-to-br from-primary-50 to-white dark:from-slate-900 dark:to-slate-950">
            <div class="wrap">
                <div class="text-center mb-12">
                    <h2>Comment ça marche ?</h2>
                    <p class="muted mt-3">Que vous soyez candidat ou recruteur, tout est simple</p>
                </div>
                <div class="flex justify-center gap-2 mb-12" role="group" aria-label="Choisir votre profil"><button class="filter-button selected" data-how="candidats" aria-pressed="true" aria-controls="howCandidats">Pour les Candidats</button><button class="filter-button" data-how="entreprises" aria-pressed="false" aria-controls="howEntreprises">Pour les Entreprises</button></div>
                <?php foreach (['Candidats' => [['Créez votre profil', 'Inscrivez-vous gratuitement en 2 minutes et complétez votre CV en ligne.'], ['Postulez en 1 clic', 'Parcourez les offres et postulez directement depuis la plateforme.'], ['Soyez recruté', 'Discutez avec les recruteurs et décrochez le job de vos rêves.']], 'Entreprises' => [['Créez votre compte', 'Inscrivez votre entreprise avec vérification RCCM pour plus de crédibilité.'], ['Choisissez un plan', 'Basic, Standard ou Premium — payez via Mobile Money.'], ['Gérez vos recrutements', 'Suivez les candidatures et échangez avec les candidats.']]] as $audience => $steps): ?><div id="how<?= $audience ?>" class="<?= $audience === 'Entreprises' ? 'hidden ' : '' ?>grid md:grid-cols-3 gap-6"><?php foreach ($steps as $i => [$title, $description]): ?><div class="card"><span class="logo mb-4 <?= $audience === 'Entreprises' ? 'gold' : '' ?>"><?= $i + 1 ?></span>
                                <h3 class="font-bold text-lg mb-2"><?= $title ?></h3>
                                <p class="muted text-sm"><?= $description ?></p>
                            </div><?php endforeach; ?></div><?php endforeach; ?>
            </div>
        </section>
        <section class="section bg-white dark:bg-slate-950">
            <div class="wrap">
                <div class="text-center mb-12">
                    <h2>Pourquoi choisir EMPLOI_RDC ?</h2>
                    <p class="muted mt-3">Une plateforme pensée pour le marché congolais</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"><?php foreach ([['brain', 'Matching intelligent', 'Recommandations personnalisées basées sur vos compétences et votre profil.'], ['mobile-alt', 'Paiement local', 'M-Pesa, Orange Money, Airtel Money — payez comme vous voulez.'], ['comments', 'Messagerie directe', 'Discutez directement avec les recruteurs sans intermédiaire.'], ['shield-alt', 'Entreprises vérifiées', 'Toutes les entreprises sont contrôlées via RCCM et identifiant national.'], ['flag', '100 % Congolaise', 'Une plateforme locale adaptée aux réalités du marché RDC.'], ['bolt', 'Candidature rapide', 'Postulez en 2 clics avec votre profil pré-rempli.']] as [$icon, $title, $description]): ?><div class="card feature-card"><span class="logo mb-4"><i class="fas fa-<?= $icon ?>"></i></span>
                            <h3 class="font-bold mb-2"><?= $title ?></h3>
                            <p class="muted text-sm"><?= $description ?></p>
                        </div><?php endforeach; ?></div>
            </div>
        </section>
        <section class="section bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900 text-white">
            <div class="wrap">
                <div class="text-center mb-12">
                    <h2>Emploi_RDC en chiffres</h2>
                    <p class="mt-3 text-primary-100">La plateforme de référence pour l'emploi en RDC</p>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6"><?php foreach (['1 250+' => 'Offres publiées', '300+' => 'Entreprises inscrites', '45k+' => 'Candidats inscrits', '78%' => 'Taux de recrutement'] as $number => $label): ?><div class="text-center p-6 rounded-2xl bg-white/10 border border-white/20">
                            <p class="text-4xl lg:text-5xl font-extrabold text-gold-400"><?= $number ?></p>
                            <p class="mt-2 text-sm text-primary-100"><?= $label ?></p>
                        </div><?php endforeach; ?></div>
            </div>
        </section>
        <section class="section bg-slate-50 dark:bg-slate-900">
            <div class="wrap">
                <div class="text-center mb-12">
                    <h2>Ils nous font confiance</h2>
                    <p class="muted mt-3">Découvrez les témoignages de notre communauté</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6"><?php foreach ([['12', 'Jean Kabila', 'Développeur Fullstack · Kinshasa', "Grâce à EMPLOI_RDC, j’ai trouvé un poste de développeur en 2 semaines seulement. La messagerie directe avec les recruteurs change tout !"], ['45', 'Marie Ilunga', 'DRH · Vodacom Congo', "En tant que RH, publier une offre n’a jamais été aussi simple. Le suivi des candidatures est impeccable et le paiement Mobile Money un vrai plus."], ['32', 'Patrick Mwamba', 'Ingénieur Minier · Lubumbashi', "La fonctionnalité de matching par compétences m’a permis de trouver des offres vraiment adaptées à mon profil. Je recommande à 100% !"]] as [$photo, $name, $role, $quote]): ?><figure class="card">
                            <div class="text-gold-500 mb-3" aria-label="5 étoiles sur 5">★★★★★</div>
                            <blockquote class="italic mb-4">« <?= $escape($quote) ?> »</blockquote>
                            <figcaption class="flex items-center gap-3"><img loading="lazy" src="https://i.pravatar.cc/100?img=<?= $photo ?>" width="48" height="48" alt="" class="rounded-full ring-2 ring-primary-500">
                                <div>
                                    <p class="font-semibold"><?= $name ?></p>
                                    <p class="text-xs muted"><?= $role ?></p>
                                </div>
                            </figcaption>
                        </figure><?php endforeach; ?></div>
            </div>
        </section>
        <section id="blog" class="section bg-white dark:bg-slate-950">
            <div class="wrap">
                <div class="section-heading">
                    <div>
                        <h2>Conseils carrière</h2>
                        <p class="muted mt-2">Nos articles pour booster votre carrière en RDC</p>
                    </div><button class="text-link" data-unavailable="Les articles seront disponibles prochainement.">Voir tous les articles →</button>
                </div>
                <div class="grid md:grid-cols-3 gap-6"><?php foreach ([['1586281380349-632531db7ed4', 'Carrière', 'Comment réussir son CV en RDC en 2026 ?', '5', '12'], ['1579532537598-459ecdaf39cc', 'Salaires', 'Les salaires dans le secteur minier au Katanga', '8', '08'], ['1600880292203-757bb62b4baf', 'Entretien', "10 questions à préparer pour votre entretien d’embauche", '6', '03']] as [$photo, $category, $title, $minutes, $day]): ?><article class="card overflow-hidden !p-0"><img loading="lazy" src="https://images.unsplash.com/photo-<?= $photo ?>?w=600&amp;h=400&amp;fit=crop" alt="<?= $escape($category) ?>" width="600" height="400" class="w-full h-48 object-cover">
                            <div class="p-5"><span class="tag"><?= $category ?></span>
                                <h3 class="font-bold text-lg mt-3"><?= $escape($title) ?></h3>
                                <p class="text-xs muted mt-3"><i class="far fa-clock"></i> <?= $minutes ?> min <span class="ml-4"><i class="far fa-calendar"></i> <?= $day ?> jan 2026</span></p>
                            </div>
                        </article><?php endforeach; ?></div>
            </div>
        </section>
        <section id="alertes" class="section bg-gradient-to-br from-gold-50 via-white to-primary-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900">
            <div class="max-w-4xl mx-auto px-4">
                <div class="card text-center !p-8 lg:!p-12 shadow-2xl"><span class="logo mx-auto mb-5"><i class="fas fa-bell"></i></span>
                    <h2>Recevez les meilleures offres</h2>
                    <p class="muted mt-3">Inscrivez-vous et soyez alerté dès qu'une offre correspond à votre profil.</p>
                    <form id="newsletterForm" class="mt-8 max-w-2xl mx-auto">
                        <div class="flex flex-col sm:flex-row gap-3"><label for="newsletterEmail" class="sr-only">Votre adresse email</label><input id="newsletterEmail" type="email" required placeholder="Votre adresse email" class="field flex-1 min-w-0"><button class="primary-button" type="submit"><i class="fas fa-paper-plane"></i> S'abonner</button></div>
                        <fieldset class="mt-5 flex flex-wrap justify-center gap-4 text-sm">
                            <legend class="sr-only">Fréquence des alertes</legend><?php foreach (['immediate' => 'Immédiat', 'quotidien' => 'Quotidien', 'hebdomadaire' => 'Hebdomadaire'] as $value => $label): ?><label><input type="radio" name="freq" value="<?= $value ?>" <?= $value === 'immediate' ? 'checked' : '' ?>> <?= $label ?></label><?php endforeach; ?>
                        </fieldset>
                        <p id="newsletterStatus" class="mt-4 text-sm muted" role="status"></p>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <footer id="about" class="bg-slate-900 dark:bg-slate-950 text-slate-300 pt-16">
        <div class="wrap pb-12 border-b border-slate-800">
            <p class="text-center text-sm font-semibold text-slate-400 mb-6">Moyens de paiement acceptés</p>
            <div class="flex flex-wrap justify-center gap-4"><?php foreach (['M-Pesa', 'Orange Money', 'Airtel Money', 'VISA', 'Mastercard'] as $payment): ?><span class="px-5 py-2.5 bg-white/5 rounded-xl border border-slate-800 font-bold text-sm"><?= $payment ?></span><?php endforeach; ?></div>
        </div>
        <div class="wrap py-12 grid grid-cols-2 md:grid-cols-5 gap-8">
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center gap-2 mb-4"><span class="logo">ER</span><span class="font-extrabold text-white">EMPLOI<span class="text-primary-400">_RDC</span></span></div>
                <p class="text-sm text-slate-400">La plateforme n°1 de l'emploi en RDC. Trouvez ou publiez votre prochaine opportunité en quelques clics.</p>
                <div class="flex gap-3 mt-4"><?php foreach (['facebook-f' => 'Facebook', 'linkedin-in' => 'LinkedIn', 'x-twitter' => 'X', 'whatsapp' => 'WhatsApp'] as $icon => $label): ?><button aria-label="<?= $label ?>" class="icon-button" data-unavailable="Nos réseaux sociaux seront renseignés prochainement."><i class="fab fa-<?= $icon ?>"></i></button><?php endforeach; ?></div>
            </div>
            <?php foreach (['Candidats' => ['Rechercher une offre' => '#offres', 'Créer une alerte' => '#alertes', 'Blog carrière' => '#blog', 'FAQ' => '#how'], 'Entreprises' => ['Publier une offre' => '', 'Tarifs & plans' => '', 'Annuaire entreprises' => '#entreprises', 'Espace recruteur' => ''], 'Légal' => ['CGU' => '', 'Confidentialité' => '', 'Mentions légales' => '', 'Cookies' => 'cookies']] as $heading => $links): ?><div>
                    <h3 class="font-bold text-white mb-4"><?= $heading ?></h3>
                    <ul class="space-y-2 text-sm"><?php foreach ($links as $label => $target): ?><li><?php if ($target === 'cookies'): ?><button id="cookieSettings">Cookies</button><?php elseif ($target): ?><a href="<?= $target ?>"><?= $label ?></a><?php else: ?><button data-unavailable="Cette page sera disponible prochainement."><?= $label ?></button><?php endif; ?></li><?php endforeach; ?></ul>
                </div><?php endforeach; ?>
            <div>
                <h3 class="font-bold text-white mb-4">Contact</h3>
                <ul class="space-y-3 text-sm">
                    <li>Avenue de la Paix, Gombe<br>Kinshasa, RDC</li>
                    <li><a href="mailto:contact@emploi-rdc.cd" class="break-words">contact@emploi-rdc.cd</a></li>
                    <li><a href="tel:+243800000000">+243 800 000 000</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800">
            <div class="wrap py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-400">© 2026 EMPLOI_RDC. Tous droits réservés. Fait avec ❤️ à Kinshasa.</p><button class="secondary-button" data-theme><i class="fas fa-moon dark:hidden"></i><i class="fas fa-sun hidden dark:inline"></i><span class="dark:hidden">Mode sombre</span><span class="hidden dark:inline">Mode clair</span></button>
            </div>
        </div>
    </footer>
    <button id="scrollTopBtn" class="hidden fixed bottom-6 right-6 z-40 logo !rounded-full shadow-xl" aria-label="Retour en haut"><i class="fas fa-arrow-up"></i></button>
    <button class="fixed bottom-6 left-6 z-40 logo !rounded-full shadow-xl" aria-label="Contacter le support" data-unavailable="Le chat sera bientôt disponible. Vous pouvez nous écrire à contact@emploi-rdc.cd."><i class="fas fa-headset"></i></button>
    <div id="cookieBanner" class="hidden fixed bottom-0 inset-x-0 z-50 bg-slate-900 text-white border-t border-slate-700 p-5" role="region" aria-label="Préférences cookies">
        <div class="wrap flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-300"><i class="fas fa-cookie-bite text-gold-400 mr-2"></i>Vous pouvez accepter ou refuser les cookies facultatifs. Votre choix sera mémorisé.</p>
            <div class="flex gap-2 shrink-0"><button class="secondary-button" data-cookie="rejected">Refuser</button><button class="primary-button" data-cookie="accepted">Accepter</button></div>
        </div>
    </div>
    <dialog id="infoDialog" class="card max-w-md w-full">
        <h2 class="!text-xl mb-3">EMPLOI_RDC</h2>
        <p id="infoMessage" class="muted"></p>
        <form method="dialog" class="mt-5 text-right"><button class="primary-button">Fermer</button></form>
    </dialog>
</body>

</html>