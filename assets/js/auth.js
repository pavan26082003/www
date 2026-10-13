(() => {
    'use strict';

    const qs = (selector, scope = document) => scope.querySelector(selector);
    const qsa = (selector, scope = document) => [...scope.querySelectorAll(selector)];

    qsa('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            const icon = qs('i', button);
            icon?.classList.toggle('bi-eye', showing);
            icon?.classList.toggle('bi-eye-slash', !showing);
        });
    });

    const phone = qs('#phone');
    phone?.addEventListener('input', () => {
        phone.value = phone.value.replace(/\D/g, '').slice(0, 10);
    });

    const password = qs('#password');
    const meter = qs('#passwordMeterBar');
    const strengthText = qs('#passwordStrength');

    const passwordScore = (value) => {
        let score = 0;
        if (value.length >= 8) score++;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;
        return score;
    };

    password?.addEventListener('input', () => {
        const score = passwordScore(password.value);
        const levels = [
            { label: 'Password strength', width: '0%', color: '#94a3b8' },
            { label: 'Weak', width: '25%', color: '#dc2626' },
            { label: 'Fair', width: '50%', color: '#ea580c' },
            { label: 'Good', width: '75%', color: '#0f766e' },
            { label: 'Strong', width: '100%', color: '#15803d' },
        ];
        const level = levels[score];
        if (meter) {
            meter.style.width = level.width;
            meter.style.backgroundColor = level.color;
        }
        if (strengthText) {
            strengthText.textContent = level.label;
            strengthText.style.color = level.color;
        }
    });

    const form = qs('#signupForm');
    if (!form) return;

    const submitButton = qs('#submitButton', form);
    const buttonLabel = qs('.button-label', submitButton);
    const spinner = qs('.spinner-border', submitButton);

    const fieldMap = {
        email: 'emailError',
        phone: 'phoneError',
        password: 'passwordError',
        confirm_password: 'confirmPasswordError',
        terms: 'termsError',
    };

    const clearErrors = () => {
        Object.values(fieldMap).forEach((id) => {
            const node = document.getElementById(id);
            if (node) node.textContent = '';
        });
        qsa('.input-group-custom', form).forEach((node) => node.classList.remove('has-error'));
        qs('#terms', form)?.classList.remove('is-invalid');
        const formError = qs('#formError');
        if (formError) { formError.textContent = ''; formError.classList.add('d-none'); }
    };

    const showErrors = (errors = {}) => {
        Object.entries(errors).forEach(([field, message]) => {
            if (field === 'form') {
                const formError = qs('#formError');
                if (formError) { formError.textContent = String(message); formError.classList.remove('d-none'); }
                return;
            }
            const errorNode = document.getElementById(fieldMap[field] || '');
            if (errorNode) errorNode.textContent = String(message);
            const input = qs(`[name="${CSS.escape(field)}"]`, form);
            input?.closest('.input-group-custom')?.classList.add('has-error');
            if (field === 'terms') input?.classList.add('is-invalid');
        });
        const firstInvalid = qs('.has-error input, .is-invalid', form);
        firstInvalid?.focus({ preventScroll: true });
        firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    const setLoading = (loading) => {
        submitButton.disabled = loading;
        buttonLabel.textContent = loading ? 'Creating Account...' : 'Create Account';
        spinner.classList.toggle('d-none', !loading);
    };

    const updateCsrf = (hash) => {
        const csrfInput = qs('input[name="csrf_token"]', form);
        if (csrfInput && hash) csrfInput.value = hash;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        const confirmPassword = qs('#confirm_password');
        const clientErrors = {};
        if (!/^\S+@\S+\.\S+$/.test(qs('#email').value.trim())) clientErrors.email = 'Enter a valid email address.';
        if (!/^\d{10}$/.test(phone.value)) clientErrors.phone = 'Mobile number must contain exactly 10 digits.';
        if (passwordScore(password.value) < 4) clientErrors.password = 'Use 8+ characters with uppercase, lowercase, number, and special character.';
        if (confirmPassword.value !== password.value) clientErrors.confirm_password = 'Password confirmation does not match.';
        if (!qs('#terms').checked) clientErrors.terms = 'You must agree to the Terms & Conditions.';

        if (Object.keys(clientErrors).length) {
            showErrors(clientErrors);
            return;
        }

        setLoading(true);
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });
            const payload = await response.json();
            updateCsrf(payload.csrfHash);

            if (!response.ok || !payload.success) {
                showErrors(payload.errors || { form: payload.message || 'Please correct the form.' });
                return;
            }

            const toastEl = qs('#appToast');
            const toastMessage = qs('#appToastMessage');
            toastEl.classList.add('text-bg-success');
            toastMessage.textContent = payload.message || 'Account created successfully.';
            bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 1800 }).show();
            form.reset();
            setTimeout(() => window.location.assign(payload.redirect), 2000);
        } catch (error) {
            showErrors({ form: 'A network error occurred. Please try again.' });
        } finally {
            setLoading(false);
        }
    });
})();
