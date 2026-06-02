<?php
$activeRoute = 'profile';
$user = $user ?? [];
$errors = \App\Helpers\Session::getFlash('errors') ?? [];

$status = strtolower((string)($user['status'] ?? 'active'));
$statusLabel = $status !== '' ? ucfirst($status) : 'Unknown';
$statusClass = 'manager-badge manager-badge-pending';
if ($status === 'active') {
    $statusClass = 'manager-badge manager-badge-approved';
} elseif ($status === 'inactive') {
    $statusClass = 'manager-badge manager-badge-declined';
}

$createdAt = $user['created_at'] ?? '';
$createdLabel = $createdAt ? date('M d, Y', strtotime($createdAt)) : '-';
$updatedAt = $user['updated_at'] ?? '';
$updatedLabel = $updatedAt ? date('M d, Y', strtotime($updatedAt)) : '-';

$displayName = trim((string)($user['name'] ?? '')) !== '' ? (string)$user['name'] : 'Unknown User';
$displayRole = trim((string)($displayRole ?? ''));
$roleLabel = strtoupper($displayRole !== '' ? $displayRole : str_replace('_', ' ', (string)($user['role'] ?? '-')));
$emailLabel = (string)($user['email'] ?? '-');
$companyEmailLabel = (string)($user['company_email'] ?? '-');
$contactLabel = (string)($user['contact_no'] ?? '-');

$heroDetails = [
    ['label' => 'Role', 'value' => $roleLabel, 'icon' => 'badge'],
    ['label' => 'Email', 'value' => $emailLabel, 'icon' => 'mail'],
    ['label' => 'Company Email', 'value' => $companyEmailLabel !== '' ? $companyEmailLabel : '-', 'icon' => 'mail-open'],
    ['label' => 'Contact Number', 'value' => $contactLabel !== '' ? $contactLabel : '-', 'icon' => 'phone'],
    ['label' => 'Status', 'value' => $statusLabel, 'icon' => 'shield'],
];
?>

<style>
    .profile-hero {
        border-radius: 16px;
        overflow: hidden;
        background:
            linear-gradient(110deg, rgba(59, 130, 246, 0.85), rgba(15, 23, 42, 0.8)),
            url('<?= App\Config\App::url('images/bgprofile.png') ?>') center center / cover no-repeat;
        min-height: 430px;
        width: 100%;
    }
    .profile-chip-icon {
        width: 24px;
        height: 24px;
        border-radius: 7px;
        background: #fbbf24;
        color: #1f2937;
    }
    .profile-info-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
    }
    html.dark .profile-info-card {
        background: linear-gradient(180deg, #0d1830 0%, #0a1429 100%);
        border-color: #223a63;
    }
    html.dark .profile-info-card h2,
    html.dark .profile-info-card p {
        color: #dbe7ff !important;
    }
    html.dark .profile-info-card .text-slate-500 {
        color: #90a7d0 !important;
    }
    html.dark .profile-info-card .border-slate-200 {
        border-color: #27456f !important;
    }
    html.dark .profile-info-card .bg-slate-50 {
        background: #09152c !important;
        border-color: #2a4a79 !important;
        color: #dbe7ff !important;
    }
</style>

<div class="w-full max-w-none space-y-6">
    <div class="profile-hero p-6 md:p-8 text-white shadow-lg">
        <div class="flex flex-col lg:flex-row gap-6 lg:items-start min-h-[360px]">
            <div class="w-56 h-56 md:w-72 md:h-72 rounded-xl border-4 border-white/70 bg-white/20 overflow-hidden shadow-xl flex items-center justify-center">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= App\Config\App::url('uploads/' . $user['avatar']) ?>" alt="Avatar" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-6xl font-bold"><?= strtoupper(substr($displayName, 0, 1)) ?></span>
                <?php endif; ?>
            </div>

            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight"><?= htmlspecialchars($displayName) ?></h1>
                        <p class="text-white/90 text-base"><?= htmlspecialchars($roleLabel) ?></p>
                    </div>
                    <button type="button" onclick="openProfileEditModal()" class="px-4 py-2 rounded-lg bg-white/15 border border-white/40 text-sm font-semibold hover:bg-white/25">
                        Edit profile
                    </button>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($heroDetails as $item): ?>
                        <div class="flex items-start gap-3">
                            <div class="profile-chip-icon flex items-center justify-center mt-0.5">
                                <?php if (($item['icon'] ?? '') === 'badge'): ?>
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 3l7 4v5c0 4-2.8 7.6-7 9-4.2-1.4-7-5-7-9V7l7-4z"></path>
                                        <path d="M9 12l2 2 4-4"></path>
                                    </svg>
                                <?php elseif (($item['icon'] ?? '') === 'mail'): ?>
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                        <path d="M3 7l9 6 9-6"></path>
                                    </svg>
                                <?php elseif (($item['icon'] ?? '') === 'mail-open'): ?>
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M2 8l10 6 10-6"></path>
                                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                    </svg>
                                <?php elseif (($item['icon'] ?? '') === 'phone'): ?>
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.6a2 2 0 0 1-.5 2.1L8 9.6a16 16 0 0 0 6.4 6.4l1.2-1.2a2 2 0 0 1 2.1-.5c.8.3 1.7.6 2.6.7A2 2 0 0 1 22 16.9z"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l9 4.5v6c0 5-3.5 9.5-9 11-5.5-1.5-9-6-9-11v-6L12 2z"></path>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-white/75"><?= htmlspecialchars($item['label']) ?></p>
                                <p class="text-sm md:text-base font-semibold break-words"><?= htmlspecialchars($item['value']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-info-card p-5 md:p-6">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
            <h2 class="text-lg md:text-xl font-bold text-slate-900">Basic Details</h2>
            <span class="<?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-slate-500 mb-1">Full name</p>
                <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($displayName) ?></div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Email</p>
                <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($emailLabel) ?></div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Company email</p>
                <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($companyEmailLabel !== '' ? $companyEmailLabel : '-') ?></div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Contact number</p>
                <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($contactLabel !== '' ? $contactLabel : '-') ?></div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Role</p>
                <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($roleLabel) ?></div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Member since</p>
                <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($createdLabel) ?></div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Last update</p>
                <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($updatedLabel) ?></div>
            </div>
        </div>
    </div>
</div>

<template id="profile-edit-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Edit Profile</h3>
            <button type="button" class="manager-modal-close" onclick="closeModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('dashboard/profile') ?>" enctype="multipart/form-data">
            <?= \App\Helpers\Csrf::field() ?>

            <div class="profile-modal-grid">
                <label class="manager-modal-field">
                    <span>Profile image</span>
                    <input type="file" name="avatar" accept="image/*">
                    <p class="mt-1 text-xs text-red-500" data-error-for="avatar"><?= $errors['avatar'][0] ?? '' ?></p>
                </label>

                <label class="manager-modal-field">
                    <span>Full name</span>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" data-validate="required|min:2">
                    <p class="mt-1 text-xs text-red-500" data-error-for="name"><?= $errors['name'][0] ?? '' ?></p>
                </label>

                <label class="manager-modal-field">
                    <span>Email</span>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" data-validate="required|email">
                    <p class="mt-1 text-xs text-red-500" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
                </label>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save changes</button>
                <button type="button" class="manager-modal-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    function openProfileEditModal() {
        const template = document.getElementById('profile-edit-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }
</script>
