<?php
$activeRoute = 'users';
$users = $users ?? [];
$pagination = $pagination ?? [];
$search = $search ?? '';
$status = $status ?? '';
$role = $role ?? '';
$sort = $sort ?? 'id';
$order = $order ?? 'DESC';
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Users</h1>
            <p class="text-sm text-slate-500">Manage access, roles, and account status.</p>
        </div>
        <a href="<?= App\Config\App::url('users/create') ?>" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">
            + Add user
        </a>
    </div>

    <form class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 grid grid-cols-1 md:grid-cols-4 gap-3" method="GET" action="<?= App\Config\App::url('users') ?>">
        <div class="md:col-span-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search name or email"
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/30">
        </div>
        <div>
            <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none">
                <option value="">All status</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div>
            <select name="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:outline-none">
                <option value="">All roles</option>
                <option value="accounting" <?= $role === 'accounting' ? 'selected' : '' ?>>Accounting</option>
                <option value="asm_manager" <?= $role === 'asm_manager' ? 'selected' : '' ?>>ASM Manager</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="head_manager" <?= $role === 'head_manager' ? 'selected' : '' ?>>Head Manager</option>
                <option value="super_admin" <?= $role === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                <option value="inhouse_sales" <?= $role === 'inhouse_sales' ? 'selected' : '' ?>>In-House Sales</option>
                <option value="msa_partners" <?= $role === 'msa_partners' ? 'selected' : '' ?>>MSA Partners</option>
            </select>
        </div>
        <div class="md:col-span-4 flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm hover:bg-primary-700">Filter</button>
            <a href="<?= App\Config\App::url('users') ?>" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/70 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-sm font-medium text-slate-800">Activation Breakdown</h2>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 rounded-md border border-slate-200 bg-white text-xs text-slate-600 hover:bg-slate-50">Jan 10, 2026 - Feb 10, 2026</button>
                <button class="px-3 py-1.5 rounded-md border border-slate-200 bg-white text-xs text-slate-600 hover:bg-slate-50">Activation</button>
                <button class="w-8 h-8 rounded-md border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center">...</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="users-table" class="display w-full text-xs">
                <thead>
                    <tr class="bg-slate-50">
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Contact No.</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                        <td>
                            <div class="w-7 h-7 rounded-full bg-sky-100 text-sky-700 border border-sky-200 flex items-center justify-center text-xs font-bold">
                                <?= strtoupper(substr($user['first_name'] ?? $user['name'] ?? 'U', 0, 1)) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= strtoupper(str_replace('_', ' ', htmlspecialchars($user['role'] ?? '-'))) ?></td>
                        <td><?= htmlspecialchars($user['contact_no'] ?? '0956 8618 473') ?></td>
                        <td class="space-x-2 whitespace-nowrap">
                            <a href="<?= App\Config\App::url('users/' . $user['id'] . '/edit') ?>" class="text-cyan-600 hover:text-cyan-700">Edit</a>
                            <?php if (($user['status'] ?? '') === 'pending'): ?>
                            <form method="POST" action="<?= App\Config\App::url('users/' . $user['id'] . '/approve') ?>" class="inline" data-confirm="true" data-confirm-text="Approve this account?">
                                <?= \App\Helpers\Csrf::field() ?>
                                <button type="submit" class="text-emerald-600 hover:text-emerald-700">Approve</button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="<?= App\Config\App::url('users/' . $user['id']) ?>" class="inline" data-confirm="true" data-confirm-text="Delete this user?">
                                <?= \App\Helpers\Csrf::field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $user['status'] === 'active' ? 'bg-emerald-500 text-white' : ($user['status'] === 'pending' ? 'bg-amber-500 text-white' : 'bg-rose-500 text-white') ?>">
                                <?= htmlspecialchars($user['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3">
            <?php include App\Config\App::basePath('app/Views/partials/pagination.php'); ?>
        </div>
    </div>
</div>
