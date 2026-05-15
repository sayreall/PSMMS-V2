<?php
$activeRoute = 'profile';
$user = $user ?? [];
$errors = \App\Helpers\Session::getFlash('errors') ?? [];
?>

<div class="max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">My profile</h1>
        <p class="text-sm text-slate-500">View your account information and update it from the edit action.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900">Profile details</h2>
                <button type="button" onclick="openProfileEditModal()" class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100" aria-label="Edit profile">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                    </svg>
                </button>
            </div>

            <div class="mt-4 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-primary-50 border border-primary-200 flex items-center justify-center text-primary-700 font-bold text-lg overflow-hidden">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= App\Config\App::url('uploads/' . $user['avatar']) ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-base font-semibold text-slate-900"><?= htmlspecialchars($user['name'] ?? '-') ?></p>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Full name</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($user['name'] ?? '-') ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Email</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Role</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= strtoupper(str_replace('_', ' ', htmlspecialchars($user['role'] ?? '-'))) ?></div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Status</p>
                    <div class="px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700"><?= ucfirst(htmlspecialchars($user['status'] ?? '-')) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <h2 class="text-lg font-semibold text-slate-900">Change password</h2>
            <form class="mt-4 space-y-4" method="POST" action="<?= App\Config\App::url('dashboard/password') ?>" data-ajax="true">
                <?= \App\Helpers\Csrf::field() ?>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Current password</label>
                    <input type="password" name="current_password"
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                           data-validate="required|min:6">
                    <p class="mt-1 text-xs text-red-500" data-error-for="current_password"><?= $errors['current_password'][0] ?? '' ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">New password</label>
                    <input type="password" name="new_password" id="new-password"
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                           data-validate="required|min:8">
                    <p class="mt-1 text-xs text-red-500" data-error-for="new_password"><?= $errors['new_password'][0] ?? '' ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
                    <input type="password" name="confirm_password" data-match="#new-password"
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                           data-validate="required">
                    <p class="mt-1 text-xs text-red-500" data-error-for="confirm_password"><?= $errors['confirm_password'][0] ?? '' ?></p>
                </div>

                <button type="submit" class="px-4 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Update password</button>
            </form>
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
