document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('global-search');
    if (!input) return;

    const baseUrl = document.body.dataset.baseUrl || '';

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const query = input.value.trim();
            if (!query) return;
            window.location.href = `${baseUrl}/users?search=${encodeURIComponent(query)}`;
        }
    });
});
