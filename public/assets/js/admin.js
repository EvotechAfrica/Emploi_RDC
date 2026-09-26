'use strict';
(() => {
    const $ = id => document.getElementById(id);
    const mobile = matchMedia('(max-width: 1024px)');
    const closeSidebar = () => { $('sidebar').classList.remove('open'); $('sidebarOverlay').hidden = true; $('sidebarToggle').setAttribute('aria-expanded', 'false'); $('sidebar').inert = mobile.matches; };
    closeSidebar();
    $('sidebarToggle').addEventListener('click', () => {
        const open = !$('sidebar').classList.contains('open');
        $('sidebar').classList.toggle('open', open); $('sidebar').inert = !open && mobile.matches;
        $('sidebarOverlay').hidden = !open; $('sidebarToggle').setAttribute('aria-expanded', String(open));
        if (open) $('sidebar').querySelector('a').focus();
    });
    $('sidebarOverlay').addEventListener('click', () => { closeSidebar(); $('sidebarToggle').focus(); });
    mobile.addEventListener('change', closeSidebar);
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && $('sidebar').classList.contains('open')) { closeSidebar(); $('sidebarToggle').focus(); } });
    $('adminTheme').addEventListener('click', () => { const dark = document.documentElement.classList.toggle('dark'); try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (_) {} });
    document.querySelectorAll('[data-close]').forEach(button => button.addEventListener('click', () => $(button.dataset.close).close()));
    if (!$('usersBody')) return;
    const labels = { admin: 'Administrateur', candidat: 'Candidat', company: 'Entreprise' };
    const descriptions = { admin: 'Supervision de la plateforme', candidat: 'Profil, candidatures et favoris', company: 'Offres et suivi des candidatures' };
    const users = [
        {id:1,name:'Admin Démo',email:'admin@example.com',role:'admin',status:'active'},
        {id:2,name:'Jean Démo',email:'jean@example.com',role:'candidat',status:'active'},
        {id:3,name:'Entreprise Alpha',email:'alpha@example.com',role:'company',status:'active'},
        {id:4,name:'Marie Démo',email:'marie@example.com',role:'candidat',status:'active'},
        {id:5,name:'Entreprise Beta',email:'beta@example.com',role:'company',status:'inactive'},
        {id:6,name:'Patrick Démo',email:'patrick@example.com',role:'candidat',status:'inactive'},
        {id:7,name:'Entreprise Gamma',email:'gamma@example.com',role:'company',status:'active'},
    ];
    const section = document.body.dataset.section;
    const fixedRole = section === 'candidats' ? 'candidat' : section === 'companies' ? 'company' : '';
    if (fixedRole) { $('roleFilter').value = fixedRole; $('roleFilter').disabled = true; }
    let page = 1, ascending = true, view = 'users', deletion = null, toastTimer;
    const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    const cell = (row, text, className) => { const td = document.createElement('td'); td.textContent = text; if (className) td.className = className; row.append(td); return td; };
    const badge = (row, text, className) => { const span = document.createElement('span'); span.className = `badge ${className}`; span.textContent = text; cell(row, '').append(span); };
    const toast = message => { clearTimeout(toastTimer); $('adminToast').textContent = message; $('adminToast').hidden = false; toastTimer = setTimeout(() => { $('adminToast').hidden = true; }, 4500); };
    const editUser = user => {
        $('userForm').reset(); $('formError').textContent = '';
        $('editId').value = user?.id || ''; $('userName').value = user?.name || ''; $('userEmail').value = user?.email || '';
        $('userRole').value = user?.role || fixedRole || 'candidat'; $('userRole').disabled = Boolean(fixedRole);
        $('userStatus').value = user?.status || 'active'; $('userDialogTitle').textContent = user ? 'Modifier l’utilisateur' : 'Ajouter un utilisateur';
        $('userDialog').showModal(); $('userName').focus();
    };
    const render = () => {
        const query = normalize($('tableSearch').value.trim());
        const filtered = users.filter(user => (! $('roleFilter').value || user.role === $('roleFilter').value) && normalize(`${user.name} ${user.email} ${labels[user.role]}`).includes(query)).sort((a,b) => (ascending ? 1 : -1) * a.name.localeCompare(b.name, 'fr'));
        const totalPages = Math.max(1, Math.ceil(filtered.length / 5)); page = Math.min(page, totalPages);
        $('usersBody').replaceChildren();
        filtered.slice((page - 1) * 5, page * 5).forEach(user => {
            const row = document.createElement('tr'); cell(row, user.name, 'user-cell'); cell(row, user.email); badge(row, labels[user.role], 'badge-role'); badge(row, user.status === 'active' ? 'Actif' : 'Désactivé', user.status === 'active' ? 'badge-active' : 'badge-inactive');
            const actions = document.createElement('div'); actions.className = 'action-btns';
            const edit = document.createElement('button'); edit.className = 'action-btn'; edit.textContent = 'Modifier'; edit.setAttribute('aria-label', `Modifier ${user.name}`); edit.addEventListener('click', () => editUser(user));
            const remove = document.createElement('button'); remove.className = 'action-btn'; remove.textContent = 'Désactiver'; remove.disabled = user.status === 'inactive'; remove.setAttribute('aria-label', `Désactiver ${user.name}`); remove.addEventListener('click', () => { deletion = user.id; $('deleteName').textContent = user.name; $('deleteDialog').showModal(); });
            actions.append(edit, remove); cell(row, '').append(actions); $('usersBody').append(row);
        });
        if (!filtered.length) { const row = document.createElement('tr'); cell(row, 'Aucun utilisateur ne correspond à votre recherche.', 'empty-state').colSpan = 5; $('usersBody').append(row); }
        $('resultCount').textContent = `${filtered.length} compte(s) · Page ${page} sur ${totalPages}`;
        $('previousPage').disabled = page === 1; $('nextPage').disabled = page === totalPages;
        $('rolesBody').replaceChildren();
        Object.keys(labels).filter(role => normalize(`${labels[role]} ${descriptions[role]}`).includes(query)).forEach(role => { const row = document.createElement('tr'); badge(row, labels[role], 'badge-role'); cell(row, descriptions[role]); cell(row, users.filter(user => user.role === role).length); $('rolesBody').append(row); });
        if (!$('rolesBody').children.length) { const row = document.createElement('tr'); cell(row, 'Aucun rôle trouvé.', 'empty-state').colSpan = 3; $('rolesBody').append(row); }
    };
    $('tableSearch').addEventListener('input', () => { page = 1; render(); });
    $('roleFilter').addEventListener('change', () => { page = 1; render(); });
    $('sortUsers').addEventListener('click', () => { ascending = !ascending; $('sortUsers').parentElement.setAttribute('aria-sort', ascending ? 'ascending' : 'descending'); render(); });
    $('previousPage').addEventListener('click', () => { page--; render(); }); $('nextPage').addEventListener('click', () => { page++; render(); });
    $('addUser').addEventListener('click', () => editUser(null));
    document.querySelectorAll('[data-view]').forEach(button => button.addEventListener('click', () => {
        view = button.dataset.view; $('usersPanel').hidden = view !== 'users'; $('rolesPanel').hidden = view !== 'roles'; $('addUser').hidden = $('roleFilter').hidden = view !== 'users';
        document.querySelectorAll('[data-view]').forEach(tab => { const selected = tab.dataset.view === view; tab.classList.toggle('btn-primary', selected); tab.classList.toggle('btn-secondary', !selected); tab.setAttribute('aria-pressed', String(selected)); });
        $('tableSearch').value = ''; $('tableSearch').placeholder = view === 'roles' ? 'Rechercher un rôle…' : 'Rechercher un nom, un email…'; render();
    }));
    $('userForm').addEventListener('submit', event => {
        event.preventDefault(); const id = Number($('editId').value); const name = $('userName').value.trim(); const email = $('userEmail').value.trim();
        if (name.length < 3) { $('formError').textContent = 'Le nom doit contenir au moins 3 caractères.'; $('userName').focus(); return; }
        if (users.some(user => user.id !== id && user.email.toLowerCase() === email.toLowerCase())) { $('formError').textContent = 'Cette adresse existe déjà dans l’aperçu.'; $('userEmail').focus(); return; }
        const values = {name,email,role:$('userRole').value,status:$('userStatus').value};
        const existing = users.find(user => user.id === id);
        if (existing) Object.assign(existing, values); else users.push({id:Math.max(...users.map(user => user.id)) + 1,...values});
        $('userDialog').close(); render(); toast('Compte fictif enregistré dans l’aperçu.');
    });
    $('confirmDelete').addEventListener('click', () => { const user = users.find(user => user.id === deletion); if (user) user.status = 'inactive'; $('deleteDialog').close(); render(); toast('Compte fictif désactivé dans l’aperçu.'); });
    render();
})();
