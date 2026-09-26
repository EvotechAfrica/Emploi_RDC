'use strict';
(() => {
    const read = (key, fallback = null) => { try { return localStorage.getItem(key) ?? fallback; } catch (_) { return fallback; } };
    const write = (key, value) => { try { localStorage.setItem(key, value); } catch (_) { /* Storage may be unavailable in private browsing. */ } };
    const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.querySelectorAll('[data-theme]').forEach(button => button.addEventListener('click', () => {
        write('theme', document.documentElement.classList.toggle('dark') ? 'dark' : 'light');
    }));
    const menu = document.getElementById('mobileMenu');
    const menuToggle = document.getElementById('mobileMenuToggle');
    const closeMenu = () => { menu.classList.add('hidden'); menuToggle.setAttribute('aria-expanded', 'false'); menuToggle.setAttribute('aria-label', 'Ouvrir le menu'); menuToggle.firstElementChild.className = 'fas fa-bars'; };
    menuToggle.addEventListener('click', () => {
        const open = menu.classList.toggle('hidden') === false;
        menuToggle.setAttribute('aria-expanded', String(open));
        menuToggle.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        menuToggle.firstElementChild.className = open ? 'fas fa-times' : 'fas fa-bars';
    });
    menu.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && !menu.classList.contains('hidden')) { closeMenu(); menuToggle.focus(); } });
    let contract = 'all';
    const keyword = document.getElementById('keyword');
    const category = document.getElementById('category');
    const city = document.getElementById('city');
    const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    const filter = () => {
        let count = 0;
        document.querySelectorAll('[data-job]').forEach(card => {
            const matches = normalize(card.dataset.title).includes(normalize(keyword.value)) && (!category.value || card.dataset.category === category.value) && (!city.value || card.dataset.city === city.value) && (contract === 'all' || card.dataset.contract === contract);
            card.hidden = !matches;
            if (matches && card.closest('#jobGrid')) count++;
        });
        document.getElementById('resultCount').textContent = `${count} offre${count > 1 ? 's' : ''} de démonstration affichée${count > 1 ? 's' : ''}`;
        document.getElementById('emptyResults').classList.toggle('hidden', count !== 0);
        document.querySelectorAll('[data-contract-filter]').forEach(button => {
            const active = button.dataset.contractFilter === contract;
            button.classList.toggle('selected', active);
            button.setAttribute('aria-pressed', String(active));
        });
    };
    document.getElementById('searchForm').addEventListener('submit', event => { event.preventDefault(); filter(); document.getElementById('offres').scrollIntoView({ behavior: reducedMotion ? 'instant' : 'smooth' }); });
    document.querySelectorAll('[data-contract-filter]').forEach(button => button.addEventListener('click', () => { contract = button.dataset.contractFilter; filter(); }));
    document.querySelectorAll('[data-category-link]').forEach(link => link.addEventListener('click', () => { category.value = link.dataset.categoryLink; keyword.value = ''; city.value = ''; contract = 'all'; filter(); }));
    document.querySelector('[data-reset-search]').addEventListener('click', () => { keyword.value = ''; category.value = ''; city.value = ''; contract = 'all'; filter(); });
    filter();
    let saved;
    try { saved = JSON.parse(read('emploi-rdc-favorites', '[]')); } catch (_) { saved = []; }
    const favorites = new Set(Array.isArray(saved) ? saved.filter(item => typeof item === 'string') : []);
    document.querySelectorAll('[data-favorite]').forEach(button => {
        const render = () => { const active = favorites.has(button.dataset.favorite); button.setAttribute('aria-pressed', String(active)); button.firstElementChild.className = active ? 'fas fa-heart' : 'far fa-heart'; };
        render();
        button.addEventListener('click', () => { const id = button.dataset.favorite; favorites.has(id) ? favorites.delete(id) : favorites.add(id); write('emploi-rdc-favorites', JSON.stringify([...favorites])); render(); });
    });
    const switchHow = audience => {
        document.querySelectorAll('[data-how]').forEach(button => { const active = button.dataset.how === audience; button.classList.toggle('selected', active); button.setAttribute('aria-pressed', String(active)); });
        document.getElementById('howCandidats').classList.toggle('hidden', audience !== 'candidats');
        document.getElementById('howEntreprises').classList.toggle('hidden', audience !== 'entreprises');
    };
    document.querySelectorAll('[data-how]').forEach(button => button.addEventListener('click', () => switchHow(button.dataset.how)));
    document.querySelector('[data-how-link]').addEventListener('click', () => { switchHow('entreprises'); document.getElementById('how').scrollIntoView({ behavior: reducedMotion ? 'instant' : 'smooth' }); });
    const scrollButton = document.getElementById('scrollTopBtn');
    const updateScroll = () => scrollButton.classList.toggle('hidden', window.scrollY <= 400);
    window.addEventListener('scroll', updateScroll, { passive: true }); updateScroll();
    scrollButton.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reducedMotion ? 'instant' : 'smooth' }));
    const cookies = document.getElementById('cookieBanner');
    if (!['accepted', 'rejected'].includes(read('emploi-rdc-cookie-choice'))) cookies.classList.remove('hidden');
    document.querySelectorAll('[data-cookie]').forEach(button => button.addEventListener('click', () => { write('emploi-rdc-cookie-choice', button.dataset.cookie); cookies.classList.add('hidden'); }));
    document.getElementById('cookieSettings').addEventListener('click', () => { cookies.classList.remove('hidden'); cookies.querySelector('button').focus(); });
    document.getElementById('newsletterForm').addEventListener('submit', event => { event.preventDefault(); document.getElementById('newsletterStatus').textContent = 'Les alertes email ne sont pas encore activées. Votre adresse n’a pas été enregistrée et aucun abonnement n’a été créé.'; });
    const dialog = document.getElementById('infoDialog');
    document.querySelectorAll('[data-unavailable]').forEach(button => button.addEventListener('click', () => { document.getElementById('infoMessage').textContent = button.dataset.unavailable; dialog.showModal(); }));
})();
