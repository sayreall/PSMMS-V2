function appState() {
    return { sidebarOpen: false };
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (!sidebar || !overlay) return;

    const isHidden = sidebar.classList.contains('-translate-x-full');
    sidebar.classList.toggle('-translate-x-full', !isHidden);
    overlay.classList.toggle('hidden', !isHidden);
}

function toggleNotifications() {
    if (typeof showToast === 'function') {
        showToast('Notifications panel coming soon.', 'info');
    }
}

function toggleTheme() {
    const root = document.documentElement;
    const isDark = root.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

function initTheme() {
    const stored = localStorage.getItem('theme');
    if (stored === 'dark') {
        document.documentElement.classList.add('dark');
    }
}

function confirmLogout(event) {
    event.preventDefault();
    const form = event.currentTarget.closest('form');
    const proceed = () => {
        if (form) {
            form.submit();
        }
    };

    if (window.Swal) {
        Swal.fire({
            title: 'Sign out?',
            text: 'You will be logged out of the dashboard.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, log out',
        }).then((result) => {
            if (result.isConfirmed) {
                proceed();
            }
        });
        return false;
    }

    if (window.confirm('Log out from your account?')) {
        proceed();
    }
    return false;
}

function validateField(field) {
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

    field.classList.toggle('border-red-400', Boolean(error));
    return !error;
}

function validateForm(form) {
    const fields = form.querySelectorAll('[data-validate]');
    let isValid = true;
    fields.forEach((field) => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    return isValid;
}

function bindValidation(form) {
    const fields = form.querySelectorAll('[data-validate]');
    fields.forEach((field) => {
        field.addEventListener('input', () => validateField(field));
        field.addEventListener('blur', () => validateField(field));
    });
}

function bindAjaxForms() {
    document.querySelectorAll('form[data-ajax="true"]').forEach((form) => {
        bindValidation(form);

        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            if (!validateForm(form)) {
                event.preventDefault();
                if (typeof showToast === 'function') {
                    showToast('Please fix the highlighted fields.', 'error');
                }
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
                    if (typeof showToast === 'function') {
                        showToast(data.error || 'Request failed.', 'error');
                    }
                    return;
                }

                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (typeof showToast === 'function') {
                    showToast(data.message || 'Saved successfully.', 'success');
                }
                return;
            }

            window.location.reload();
        });
    });
}

function bindConfirmations() {
    document.querySelectorAll('form[data-confirm="true"]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            event.preventDefault();

            const message = form.dataset.confirmText || 'Are you sure?';

            if (window.Swal) {
                Swal.fire({
                    title: 'Confirm action',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, continue',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
                return;
            }

            if (window.confirm(message)) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    });
}

function initDataTables() {
    if (!window.jQuery || !jQuery.fn.DataTable) return;

    const usersTable = jQuery('#users-table');
    if (usersTable.length) {
        usersTable.DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: true,
        });
    }

    const activityTable = jQuery('#activity-table');
    if (activityTable.length) {
        activityTable.DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
        });
    }
}

function initCharts() {
    if (typeof Chart === 'undefined') return;
    Chart.defaults.font.family = 'Manrope, Sora, sans-serif';
    const lineCanvas = document.getElementById('registrations-chart');
    if (lineCanvas) {
        const labels = (window.monthlyRegistrations || []).map((item) => item.month);
        const counts = (window.monthlyRegistrations || []).map((item) => Number(item.count || 0));
        const ctx = lineCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

        new Chart(lineCanvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Registrations',
                    data: counts,
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    tension: 0.42,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        padding: 10,
                        displayColors: false,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                        ticks: { color: '#94a3b8', font: { size: 11 } },
                    },
                },
            },
        });
    }

    const donutCanvas = document.getElementById('sales-donut');
    if (donutCanvas) {
        const centerTextPlugin = {
            id: 'centerText',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                const meta = chart.getDatasetMeta(0);
                if (!meta || !meta.data || !meta.data.length) return;
                const x = meta.data[0].x;
                const y = meta.data[0].y;
                const values = chart.data.datasets[0].data || [];
                const totalValue = values.reduce((sum, val) => sum + Number(val || 0), 0);

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#0f172a';
                ctx.font = '700 22px Manrope';
                ctx.fillText(totalValue.toLocaleString(), x, y - 6);
                ctx.fillStyle = '#64748b';
                ctx.font = '500 11px Manrope';
                ctx.fillText('TOTAL', x, y + 14);
                ctx.restore();
            },
        };

        const stats = window.dashboardStats || {};
        const total = Number(stats.total_users || 0);
        const active = Number(stats.active_users || 0);
        const inactive = Number(stats.inactive_users || 0);
        const admins = Number(stats.admins || 0);
        const others = Math.max(0, total - active - inactive - admins);
        const values = [active, inactive, admins, others];
        const hasData = values.some((v) => v > 0);

        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive', 'Admins', 'Others'],
                datasets: [{
                    data: hasData ? values : [1],
                    backgroundColor: ['#22c55e', '#ef4444', '#4f46e5', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                radius: '84%',
                cutout: '72%',
                layout: {
                    padding: { top: 4, right: 8, bottom: 0, left: 8 },
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', color: '#64748b', font: { size: 11 } },
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        padding: 10,
                    },
                },
            },
            plugins: [centerTextPlugin],
        });
    }
}

async function refreshStats() {
    try {
        const baseUrl = document.body.dataset.baseUrl || '';
        const response = await fetch(`${baseUrl}/dashboard/stats`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) return;
        const data = await response.json();

        const users = data.data?.users || {};
        const mapping = {
            'stat-total-users': users.total,
            'stat-active-users': users.active,
            'stat-inactive-users': users.inactive,
            'stat-admins': users.admins,
        };

        Object.keys(mapping).forEach((key) => {
            const el = document.getElementById(key);
            if (el && typeof mapping[key] !== 'undefined') {
                el.textContent = mapping[key];
            }
        });

        if (typeof showToast === 'function') {
            showToast('Dashboard refreshed.', 'success');
        }
    } catch (error) {
        if (typeof showToast === 'function') {
            showToast('Unable to refresh stats.', 'error');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    bindAjaxForms();
    bindConfirmations();
    initDataTables();
    initCharts();
});
