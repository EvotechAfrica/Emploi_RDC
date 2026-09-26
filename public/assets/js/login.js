'use strict';
(() => {
    const form = document.getElementById('loginForm');
    const status = document.getElementById('authStatus');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const visibility = document.getElementById('passwordToggle');
    document.getElementById('themeToggle').addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (_) { /* Theme still works without storage. */ }
    });
    visibility.addEventListener('click', () => {
        const visible = password.type === 'password';
        password.type = visible ? 'text' : 'password';
        visibility.setAttribute('aria-pressed', String(visible));
        visibility.setAttribute('aria-label', visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        document.getElementById('eyeIcon').className = `fas ${visible ? 'fa-eye-slash' : 'fa-eye'} text-sm`;
    });
    const showError = (field, message) => {
        const error = document.getElementById(`${field.id}Error`);
        field.setAttribute('aria-invalid', String(Boolean(message)));
        field.classList.toggle('border-red-500', Boolean(message));
        field.classList.toggle('border-slate-200', !message);
        field.classList.toggle('dark:border-slate-700', !message);
        error.querySelector('span').textContent = message;
        error.classList.toggle('hidden', !message);
    };
    [email, password].forEach(field => field.addEventListener('input', () => { showError(field, ''); status.textContent = ''; }));
    form.addEventListener('submit', event => {
        event.preventDefault();
        email.value = email.value.trim();
        const emailError = !email.value ? 'Veuillez saisir votre adresse e-mail.' : email.validity.typeMismatch ? 'Adresse e-mail invalide.' : '';
        const passwordError = !password.value ? 'Veuillez saisir votre mot de passe.' : '';
        showError(email, emailError);
        showError(password, passwordError);
        if (emailError || passwordError) { (emailError ? email : password).focus(); return; }
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitText').textContent = 'Connexion…';
        form.submit();
    });
    document.querySelectorAll('[data-auth-unavailable]').forEach(link => link.addEventListener('click', event => {
        event.preventDefault(); status.textContent = link.dataset.authUnavailable; status.focus();
    }));
})();
