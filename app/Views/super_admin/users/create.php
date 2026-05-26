<?php
$activeRoute = 'users';
$errors = $errors ?? [];
$old = $old ?? [];
?>

<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Create user</h1>
        <p class="text-sm text-slate-500">Add a new team member with role-based access.</p>
    </div>

    <form class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-6 space-y-4" method="POST" action="<?= App\Config\App::url('users') ?>" data-ajax="true">
        <?= \App\Helpers\Csrf::field() ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">First name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                       data-validate="required|min:2">
                <p class="mt-1 text-xs text-red-500" data-error-for="first_name"><?= $errors['first_name'][0] ?? '' ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Middle name</label>
                <input type="text" name="middle_name" value="<?= htmlspecialchars($old['middle_name'] ?? '') ?>"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30">
                <p class="mt-1 text-xs text-red-500" data-error-for="middle_name"><?= $errors['middle_name'][0] ?? '' ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Last name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                       data-validate="required|min:2">
                <p class="mt-1 text-xs text-red-500" data-error-for="last_name"><?= $errors['last_name'][0] ?? '' ?></p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                   data-validate="required|email">
            <p class="mt-1 text-xs text-red-500" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Company email</label>
            <input type="email" name="company_email" value="<?= htmlspecialchars($old['company_email'] ?? '') ?>"
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                   data-validate="required|email">
            <p class="mt-1 text-xs text-red-500" data-error-for="company_email"><?= $errors['company_email'][0] ?? '' ?></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password"
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                   data-validate="required|min:8">
            <p class="mt-1 text-xs text-red-500" data-error-for="password"><?= $errors['password'][0] ?? '' ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                <select name="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none" data-validate="required">
                    <?php $role = $old['role'] ?? 'accounting'; ?>
                    <option value="accounting" <?= $role === 'accounting' ? 'selected' : '' ?>>Accounting</option>
                    <option value="asm_manager" <?= $role === 'asm_manager' ? 'selected' : '' ?>>ASM Manager</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="head_manager" <?= $role === 'head_manager' ? 'selected' : '' ?>>Head Manager</option>
                    <option value="super_admin" <?= $role === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                </select>
                <p class="mt-1 text-xs text-red-500" data-error-for="role"><?= $errors['role'][0] ?? '' ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none" data-validate="required">
                    <?php $status = $old['status'] ?? 'active'; ?>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <p class="mt-1 text-xs text-red-500" data-error-for="status"><?= $errors['status'][0] ?? '' ?></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-4 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Create user</button>
            <a href="<?= App\Config\App::url('users') ?>" class="px-4 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</div>
