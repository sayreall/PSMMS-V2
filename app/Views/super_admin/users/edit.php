<?php
$activeRoute = 'users';
$errors = $errors ?? [];
$old = $old ?? [];
$user = $user ?? [];
?>

<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Edit user</h1>
        <p class="text-sm text-slate-500">Update user details, access, and status.</p>
    </div>

    <form class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-6 space-y-4" method="POST" action="<?= App\Config\App::url('users/' . $user['id']) ?>" data-ajax="true">
        <?= \App\Helpers\Csrf::field() ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">First name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? $user['first_name'] ?? '') ?>"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                       data-validate="required|min:2">
                <p class="mt-1 text-xs text-red-500" data-error-for="first_name"><?= $errors['first_name'][0] ?? '' ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Middle name</label>
                <input type="text" name="middle_name" value="<?= htmlspecialchars($old['middle_name'] ?? $user['middle_name'] ?? '') ?>"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30">
                <p class="mt-1 text-xs text-red-500" data-error-for="middle_name"><?= $errors['middle_name'][0] ?? '' ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Last name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? $user['last_name'] ?? '') ?>"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                       data-validate="required|min:2">
                <p class="mt-1 text-xs text-red-500" data-error-for="last_name"><?= $errors['last_name'][0] ?? '' ?></p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? $user['email'] ?? '') ?>"
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                   data-validate="required|email">
            <p class="mt-1 text-xs text-red-500" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Company email</label>
            <input type="email" name="company_email" value="<?= htmlspecialchars($old['company_email'] ?? $user['company_email'] ?? '') ?>"
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                   data-validate="required|email">
            <p class="mt-1 text-xs text-red-500" data-error-for="company_email"><?= $errors['company_email'][0] ?? '' ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                <?php $role = $old['role'] ?? $user['role'] ?? 'accounting'; ?>
                <select name="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none" data-validate="required">
                    <option value="accounting" <?= $role === 'accounting' ? 'selected' : '' ?>>Accounting</option>
                    <option value="asm_manager" <?= $role === 'asm_manager' ? 'selected' : '' ?>>ASM Manager</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="head_manager" <?= $role === 'head_manager' ? 'selected' : '' ?>>Head Manager</option>
                    <option value="super_admin" <?= $role === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                    <option value="inhouse_sales" <?= $role === 'inhouse_sales' ? 'selected' : '' ?>>In-House Sales</option>
                    <option value="msa_partners" <?= $role === 'msa_partners' ? 'selected' : '' ?>>MSA Partners</option>
                </select>
                <p class="mt-1 text-xs text-red-500" data-error-for="role"><?= $errors['role'][0] ?? '' ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <?php $status = $old['status'] ?? $user['status'] ?? 'active'; ?>
                <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none" data-validate="required">
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <p class="mt-1 text-xs text-red-500" data-error-for="status"><?= $errors['status'][0] ?? '' ?></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-4 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Save changes</button>
            <a href="<?= App\Config\App::url('users') ?>" class="px-4 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</div>
