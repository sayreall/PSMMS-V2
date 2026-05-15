function openModal(html) {
    const container = document.getElementById('modal-container');
    const content = document.getElementById('modal-content');
    if (!container || !content) return;

    content.innerHTML = html;
    container.classList.remove('hidden');
    container.classList.add('flex');

    requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });
}

function closeModal() {
    const container = document.getElementById('modal-container');
    const content = document.getElementById('modal-content');
    if (!container || !content) return;

    content.classList.remove('manager-modal-shell');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        container.classList.add('hidden');
        container.classList.remove('flex');
        content.innerHTML = '';
    }, 200);
}

function openConfirm(options = {}) {
    const dialog = document.getElementById('confirm-dialog');
    const title = document.getElementById('confirm-title');
    const message = document.getElementById('confirm-message');
    const confirmBtn = document.getElementById('confirm-btn');

    if (!dialog || !title || !message || !confirmBtn) return;

    title.textContent = options.title || 'Confirm';
    message.textContent = options.message || 'Are you sure?';

    const handler = options.onConfirm || (() => {});
    confirmBtn.onclick = () => {
        handler();
        closeConfirm();
    };

    dialog.classList.remove('hidden');
    dialog.classList.add('flex');
}

function closeConfirm() {
    const dialog = document.getElementById('confirm-dialog');
    if (!dialog) return;
    dialog.classList.add('hidden');
    dialog.classList.remove('flex');
}
