<?php
$stats = $stats ?? [];
$dashboardSlug = $dashboardSlug ?? 'general';
$activeRoute = 'dashboard';

$roleHighlights = [
    'super-admin' => 'Full system controls and user governance.',
    'admin' => 'Operational controls and account maintenance.',
    'admin-sales-admin' => 'Sales Admin operations and monitoring dashboard.',
    'admin-dispatcher' => 'Dispatcher operations and coordination dashboard.',
    'admin-tech-leaders' => 'Tech Leaders oversight and workstream dashboard.',
    'admin-sales-team-leader' => 'Sales Team Leader performance dashboard.',
    'admin-validator' => 'Validation workflow and queue dashboard.',
    'admin-backend' => 'Backend operations and platform workflow dashboard.',
    'admin-marketing' => 'Marketing performance and campaign dashboard.',
    'admin-supervisor' => 'Supervisor-level operations and compliance dashboard.',
    'admin-product-business-manager' => 'Product and business performance dashboard.',
    'head-manager' => 'Team-wide sales and operations oversight.',
    'accounting' => 'Billing, reconciliation, and payout monitoring.',
    'asm-manager' => 'Manager-level subscriber and activation tracking.',
    'asm-super-manager' => 'Super manager pipeline and performance snapshot.',
    'asm-area-sales-manager' => 'Area sales manager activation pipeline.',
    'asm-head-manager' => 'Head manager approvals and performance checks.',
    'inhouse-sales' => 'In-house sales output and targets.',
    'msa-partners' => 'Partner account overview and performance.',
    'sme-sales' => 'SME sales monitoring and progress.',
    'general' => 'General dashboard overview.',
];
?>

<div class="space-y-6">
    <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
        <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
        <p class="text-xs text-slate-600"><?= htmlspecialchars($roleHighlights[$dashboardSlug] ?? $roleHighlights['general']) ?></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Total users</p>
            <p class="text-2xl font-semibold text-slate-900 mt-2"><?= (int) ($stats['total_users'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Active users</p>
            <p class="text-2xl font-semibold text-emerald-700 mt-2"><?= (int) ($stats['active_users'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Inactive users</p>
            <p class="text-2xl font-semibold text-rose-700 mt-2"><?= (int) ($stats['inactive_users'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Admins</p>
            <p class="text-2xl font-semibold text-indigo-700 mt-2"><?= (int) ($stats['admins'] ?? 0) ?></p>
        </div>
    </div>
</div>
