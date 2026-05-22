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
?>

<div class="max-w-5xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">View profile</h1>
            <p class="text-sm text-slate-500">Quick snapshot of your account information.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openProfileEditModal()" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Edit profile
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-primary-50 border border-primary-200 flex items-center justify-center text-primary-700 font-bold text-xl overflow-hidden">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= App\Config\App::url('uploads/' . $user['avatar']) ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($user['name'] ?? '-') ?></p>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                </div>
            </div>

            <div class="mt-4">
                <span class="<?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
            </div>

            <div class="mt-5 space-y-3">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Role</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700">
                        <?= strtoupper(str_replace('_', ' ', htmlspecialchars($user['role'] ?? '-'))) ?>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Member since</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($createdLabel) ?></div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <h2 class="text-lg font-semibold text-slate-900">Account details</h2>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Full name</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($user['name'] ?? '-') ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Email</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Company email</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($user['company_email'] ?? '-') ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Contact number</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($user['contact_no'] ?? '-') ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Status</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($statusLabel) ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Last update</p>
                    <?php $updatedAt = $user['updated_at'] ?? ''; ?>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700">
                        <?= $updatedAt ? htmlspecialchars(date('M d, Y', strtotime($updatedAt))) : '-' ?>
                    </div>
                </div>
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
