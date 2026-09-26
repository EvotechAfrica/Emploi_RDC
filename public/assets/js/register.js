'use strict';
(() => {
    const form = document.getElementById('registerForm');
    const status = document.getElementById('registerStatus');
    const password = document.getElementById('registerPassword');
    const confirm = document.getElementById('registerConfirm');
    form.noValidate = true;
    const clearError = field => {
        field.removeAttribute('aria-invalid');
        document.getElementById(`${field.id}Error`)?.remove();
        const descriptions = (field.getAttribute('aria-describedby') || '').split(' ').filter(id => id && id !== `${field.id}Error`);
        if (descriptions.length) field.setAttribute('aria-describedby', descriptions.join(' '));
        else field.removeAttribute('aria-describedby');
    };
    const selectAccount = type => {
        const company = type === 'company';
        document.getElementById('accountType').value = type;
        document.getElementById('googleAccountType').value = type;
        document.getElementById('accountBreadcrumb').textContent = company ? 'Entreprise' : 'Candidat';
        document.getElementById('registerSubmitLabel').textContent = `Créer mon compte ${company ? 'entreprise' : 'candidat'}`;
        document.querySelectorAll('[data-account]').forEach(link => link.setAttribute('aria-current', String(link.dataset.account === type)));
        document.querySelectorAll('[data-account-fields]').forEach(fields => { fields.disabled = fields.hidden = fields.dataset.accountFields !== type; });
        form.action = `?page=inscription&type=${type}`;
        form.querySelectorAll('input, select').forEach(clearError);
        status.textContent = '';
    };
    document.querySelectorAll('[data-account]').forEach(link => link.addEventListener('click', event => {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault(); selectAccount(link.dataset.account); history.pushState(null, '', link.href);
    }));
    window.addEventListener('popstate', () => selectAccount(new URLSearchParams(location.search).get('type') === 'company' ? 'company' : 'candidat'));
    document.getElementById('registerTheme').addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (_) { /* Theme works without storage. */ }
    });
    document.querySelectorAll('[data-password-toggle]').forEach(button => button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        const visible = input.type === 'password'; input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
        button.setAttribute('aria-label', `${visible ? 'Masquer' : 'Afficher'} ${input === confirm ? 'la confirmation du mot de passe' : 'le mot de passe'}`);
        button.firstElementChild.className = visible ? 'fas fa-eye-slash' : 'fas fa-eye';
    }));
    form.querySelectorAll('input, select').forEach(field => {
        const clear = () => { clearError(field); status.textContent = ''; if (field === password || field === confirm) { confirm.setCustomValidity(''); clearError(confirm); } };
        field.addEventListener('input', clear); field.addEventListener('change', clear);
    });
    form.addEventListener('submit', event => {
        event.preventDefault();
        confirm.setCustomValidity(password.value === confirm.value ? '' : 'Les mots de passe ne correspondent pas.');
        let firstInvalid = null;
        form.querySelectorAll('input, select').forEach(field => {
            clearError(field);
            if (!field.willValidate) return;
            let message = field.validationMessage;
            if (field.required && !field.value.trim()) message = 'Veuillez renseigner ce champ.';
            else if (field.validity.typeMismatch) message = 'Veuillez saisir une adresse e-mail valide.';
            if (field === password && field.value && field.value.length < 8) message = 'Utilisez au moins 8 caractères.';
            if (!message) return;
            firstInvalid ??= field;
            const error = document.createElement('p'); error.id = `${field.id}Error`; error.className = 'field-error'; error.textContent = message;
            (field.closest('.relative') || field).insertAdjacentElement('afterend', error);
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', `${field.getAttribute('aria-describedby') || ''} ${error.id}`.trim());
        });
        if (firstInvalid) { status.textContent = 'Veuillez corriger les champs indiqués.'; firstInvalid.focus(); return; }
        form.querySelector('button[type="submit"]').disabled = true;
        form.submit();
    });
})();
