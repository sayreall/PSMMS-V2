function getCompanyEmailDomain(field) {
    return (field.dataset.domain || '').trim();
}

function syncCompanyEmail(localField) {
    if (localField.disabled) return null;
    const domain = getCompanyEmailDomain(localField);
    if (!domain) return null;

    const targetName = localField.dataset.emailTarget;
    if (!targetName) return null;

    const form = localField.closest('form');
    if (!form) return null;

    const scope = localField.closest('.form-col') || form;
    const targetField = scope.querySelector(`[name="${targetName}"]`) || form.querySelector(`[name="${targetName}"]`);
    if (!targetField) return null;

    const local = (localField.value || '').trim().split('@')[0].replace(/\s+/g, '');
    if (localField.value !== local) {
        localField.value = local;
    }
    targetField.value = local ? `${local}@${domain}` : '';
    return targetField;
}

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
        if (name === 'numeric' && value && !/^\d+$/.test(value)) {
            error = 'Only digits are allowed.';
        }
        if (name === 'min' && value && value.length < Number(param || 0)) {
            error = `Must be at least ${param} characters.`;
        }
        if (name === 'max' && value && value.length > Number(param || 0)) {
            error = `Must be at most ${param} characters.`;
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

function syncCopiedFields(form) {
    form.querySelectorAll('[data-copy-from]').forEach((field) => {
        const source = form.querySelector(`[name="${field.dataset.copyFrom}"]`);
        if (source) {
            field.value = source.value;
        }
    });
}

function bindAuthForms() {
    document.querySelectorAll('form[data-ajax="true"]').forEach((form) => {
        const formId = form.id || '';
        const isLoginForm = formId === 'login-form';
        const failureTitle = isLoginForm ? 'Login failed' : 'Registration failed';
        const fallbackFailureText = isLoginForm
            ? 'Please check your login details and try again.'
            : 'Please check your form details and try again.';

        form.querySelectorAll('[data-validate], [data-validate-base]').forEach((field) => {
            field.addEventListener('input', () => {
                syncCopiedFields(form);
                if (field.dataset.emailTarget) {
                    const linkedField = syncCompanyEmail(field);
                    if (linkedField) {
                        validateAuthField(linkedField);
                    }
                }
                validateAuthField(field);
            });
        });

        form.addEventListener('submit', async (event) => {
            syncCopiedFields(form);
            form.querySelectorAll('[data-email-target]').forEach((field) => {
                syncCompanyEmail(field);
            });
            if (!validateAuthForm(form)) {
                event.preventDefault();
                return;
            }

            event.preventDefault();

            const formData = new FormData(form);
            let response;
            try {
                response = await fetch(form.action, {
                    method: form.method.toUpperCase(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: formData,
                });
            } catch (error) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network error',
                        text: 'Unable to submit the form. Please check your connection and try again.',
                        confirmButtonText: 'OK',
                    });
                }
                return;
            }

            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const data = await response.json();

                if (!response.ok) {
                    let firstError = '';
                    if (data.errors) {
                        Object.keys(data.errors).forEach((field) => {
                            const errorEl = form.querySelector(`[data-error-for="${field}"]`);
                            if (errorEl) {
                                errorEl.textContent = data.errors[field][0] || 'Invalid value.';
                            }
                            if (!firstError && data.errors[field] && data.errors[field][0]) {
                                firstError = data.errors[field][0];
                            }
                        });
                    }
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: failureTitle,
                            text: data.error || firstError || fallbackFailureText,
                            confirmButtonText: 'OK',
                        });
                    }
                    return;
                }

                if (data.redirect) {
                    if (window.Swal) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Account Created',
                            text: data.message || 'Registration submitted successfully.',
                            confirmButtonText: 'Continue',
                        });
                    }
                    window.location.href = data.redirect;
                    return;
                }
            } else {
                if (!response.ok) {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: failureTitle,
                            text: 'Something went wrong. Please try again.',
                            confirmButtonText: 'OK',
                        });
                    }
                    return;
                }

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
            }

            window.location.reload();
        });
    });
}

function bindPasswordToggles() {
    document.querySelectorAll('[data-password-target]').forEach((button) => {
        button.addEventListener('click', () => {
            const selector = (button.dataset.passwordTarget || '').trim();
            if (!selector) return;
            const field = document.querySelector(selector);
            if (!field) return;

            const showing = field.type === 'text';
            field.type = showing ? 'password' : 'text';
            button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            button.setAttribute('title', showing ? 'Show password' : 'Hide password');
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindAuthForms();
    bindPasswordToggles();
});
