function validateAuthField(field) {
    const rules = (field.dataset.validate || '').split('|').filter(Boolean);
    if (!rules.length) return true;

    const value = (field.value || '').trim();
    let error = '';

    for (const rule of rules) {
        const [name, param] = rule.split(':');

        if (name === 'required' && value === '') {
            error = 'This field is required.';
        }
        if (name === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            error = 'Enter a valid email address.';
        }
        if (name === 'min' && value && value.length < Number(param || 0)) {
            error = `Must be at least ${param} characters.`;
        }
        if (error) break;
    }

    const matchSelector = field.dataset.match;
    if (!error && matchSelector) {
        const matchField = document.querySelector(matchSelector);
        if (matchField && matchField.value !== field.value) {
            error = 'Values do not match.';
        }
    }

    const form = field.closest('form');
    if (form) {
        const errorEl = form.querySelector(`[data-error-for="${field.name}"]`);
        if (errorEl) {
            errorEl.textContent = error;
        }
    }

    field.classList.toggle('border-red-300', Boolean(error));
    return !error;
}

function validateAuthForm(form) {
    let ok = true;
    form.querySelectorAll('[data-validate]').forEach((field) => {
        if (!validateAuthField(field)) {
            ok = false;
        }
    });
    return ok;
}

function bindAuthForms() {
    document.querySelectorAll('form[data-ajax="true"]').forEach((form) => {
        form.querySelectorAll('[data-validate]').forEach((field) => {
            field.addEventListener('input', () => validateAuthField(field));
        });

        form.addEventListener('submit', async (event) => {
            if (!validateAuthForm(form)) {
                event.preventDefault();
                return;
            }

            event.preventDefault();

            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method.toUpperCase(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: formData,
            });

            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        Object.keys(data.errors).forEach((field) => {
                            const errorEl = form.querySelector(`[data-error-for="${field}"]`);
                            if (errorEl) {
                                errorEl.textContent = data.errors[field][0] || 'Invalid value.';
                            }
                        });
                    }
                    return;
                }

                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
            }

            window.location.reload();
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindAuthForms();
});
