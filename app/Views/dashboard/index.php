<?php
$stats = $stats ?? [];
$recentActivities = $recentActivities ?? [];
$monthlyRegistrations = $monthlyRegistrations ?? [];
$activeRoute = 'dashboard';
?>
<?php if (\App\Helpers\Auth::hasRole('super_admin')): ?>
<div class="space-y-5">
    <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
        <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">PSMMS MANAGEMENT MONITORING SYSTEM V2.0</h1>
        <p class="text-xs text-slate-600">Product Sales Activation Performance Dashboard</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame dashboard-product-logo-frame--light">
                <img src="<?= App\Config\App::url('images/s2s.jpg') ?>" alt="S2S" class="max-w-full max-h-full object-contain dashboard-product-logo">
            </div>
            <p class="text-[10px] uppercase tracking-wide text-slate-400">FiberX Activation</p>
            <p id="stat-active-users" class="text-xl font-extrabold text-primary-700 mt-1"><?= (int)($stats['active_users'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame">
                <img src="<?= App\Config\App::url('images/fiberx.png') ?>" alt="FiberX" class="max-w-full max-h-full object-contain">
            </div>
            <p class="text-[10px] uppercase tracking-wide text-slate-400">FiberX New</p>
            <p id="stat-total-users" class="text-xl font-extrabold text-indigo-600 mt-1"><?= (int)($stats['total_users'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame dashboard-product-logo-frame--light">
                <img src="<?= App\Config\App::url('images/bida.jpg') ?>" alt="Bida" class="max-w-full max-h-full object-contain dashboard-product-logo">
            </div>
            <p class="text-[10px] uppercase tracking-wide text-slate-400">Bida</p>
            <p id="stat-admins" class="text-xl font-extrabold text-blue-600 mt-1"><?= (int)($stats['admins'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame">
                <img src="<?= App\Config\App::url('images/sme.png') ?>" alt="SME" class="max-w-full max-h-full object-contain">
            </div>
            <p class="text-[10px] uppercase tracking-wide text-slate-400">Converge</p>
            <p id="stat-inactive-users" class="text-xl font-extrabold text-emerald-600 mt-1"><?= (int)($stats['inactive_users'] ?? 0) ?></p>
        </div>
    </div>

    <div>
        <p class="text-xs text-slate-500 mb-2">Total count of every status</p>
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3">
            <?php
            $badges = [
                ['ASM', '5', 'bg-cyan-100 text-cyan-600', '<path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm-8 8.25C4.7 16.35 7.9 14 12 14s7.3 2.35 8 6.25c.08.44-.27.75-.72.75H4.72c-.45 0-.8-.31-.72-.75Z"/>'],
                ['Inhouse', '56', 'bg-violet-100 text-violet-600', '<path d="M4 20.5h16v-10L12 4 4 10.5v10Zm5-1v-6h6v6H9Zm2-8h2v-2h-2v2Z"/>'],
                ['Partners', '29', 'bg-orange-100 text-orange-600', '<path d="M8 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8.5 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM2.5 21c.5-4 2.8-7 5.5-7s5 3 5.5 7h-11Zm11.2 0c-.2-2.4-1.05-4.45-2.35-5.9 1.28-.7 2.97-1.1 5.15-1.1 3.2 0 5 2.8 5 7h-7.8Z"/>'],
                ['Sales Agent', '429', 'bg-emerald-100 text-emerald-600', '<path d="M12 11.5a4.25 4.25 0 1 0 0-8.5 4.25 4.25 0 0 0 0 8.5Zm-7 9.5c.65-4.15 3.35-7 7-7s6.35 2.85 7 7H5Zm11.7-17.4 1.05 1.05L20.3 2.1l1.2 1.2-3.75 3.75-2.25-2.25 1.2-1.2Z"/>'],
                ['Admin Users', (string)($stats['admins'] ?? 0), 'bg-blue-100 text-blue-600', '<path d="M12 2.5 5 5.4v5.4c0 4.7 2.95 8.2 7 10.7 4.05-2.5 7-6 7-10.7V5.4l-7-2.9Zm-1 11.85-2.35-2.3 1.25-1.25L11 11.9l3.1-3.15 1.25 1.25L11 14.35Z"/>'],
                ['Managers', '4', 'bg-slate-100 text-slate-600', '<path d="M8 8a4 4 0 0 1 8 0v2h2.5c.6 0 1 .4 1 1v9c0 .6-.4 1-1 1h-13c-.6 0-1-.4-1-1v-9c0-.6.4-1 1-1H8V8Zm2 2h4V8a2 2 0 1 0-4 0v2Zm1 5v3h2v-3h-2Z"/>'],
                ['Municipality', '210', 'bg-teal-100 text-teal-600', '<path d="M12 2.5A7.5 7.5 0 0 0 4.5 10c0 5.35 7.5 11.5 7.5 11.5S19.5 15.35 19.5 10A7.5 7.5 0 0 0 12 2.5Zm0 10.25A2.75 2.75 0 1 1 12 7a2.75 2.75 0 0 1 0 5.75Z"/>'],
                ['Installer', '137', 'bg-indigo-100 text-indigo-600', '<path d="m17.7 3.1 3.2 3.2-2.25 2.25-3.2-3.2 2.25-2.25ZM4 16.8 14.4 6.4l3.2 3.2L7.2 20H4v-3.2Zm2 1.2h.45l8.3-8.3-.45-.45L6 17.55V18Z"/>'],
                ['Plan', '5', 'bg-rose-100 text-rose-600', '<path d="M6 3h8l4 4v14H6V3Zm7 1.5V8h3.5L13 4.5ZM8 12h8v-1.7H8V12Zm0 4h8v-1.7H8V16Zm0 4h5v-1.7H8V20Z"/>'],
                ['Products', '4', 'bg-amber-100 text-amber-600', '<path d="m12 2.5 8 4.4v10.2l-8 4.4-8-4.4V6.9l8-4.4Zm0 2.3L6.35 7.9 12 11l5.65-3.1L12 4.8Zm-6 5v6.15l5 2.75v-6.15L6 9.8Zm7 8.9 5-2.75V9.8l-5 2.75v6.15Z"/>'],
            ];
            ?>
            <?php foreach ($badges as [$label, $count, $tone, $iconPath]): ?>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 flex items-center gap-3 hover:shadow-md transition-shadow">
                <div class="w-8 h-8 rounded-lg <?= $tone ?> flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><?= $iconPath ?></svg>
                </div>
                <div><p class="text-[11px] text-slate-500"><?= htmlspecialchars($label) ?></p><p class="text-xl font-bold text-slate-800"><?= htmlspecialchars($count) ?></p></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 xl:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Activations Trend</h2>
                <button onclick="refreshStats()" class="text-xs px-2 py-1 rounded-md border border-slate-200 hover:bg-slate-50">Refresh</button>
            </div>
            <div class="h-64 md:h-72">
                <canvas id="registrations-chart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Status Breakdown</h2>
            </div>
            <div class="h-56">
                <canvas id="sales-donut"></canvas>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard overview</h1>
            <p class="text-sm text-slate-500">Track platform activity and user growth in real time.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="refreshStats()" class="px-4 py-2 text-sm rounded-lg bg-primary-600 text-white hover:bg-primary-700">Refresh</button>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4"><p class="text-xs uppercase tracking-wide text-slate-400">Total users</p><p id="stat-total-users" class="text-2xl font-semibold text-slate-900 mt-2"><?= (int) ($stats['total_users'] ?? 0) ?></p></div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4"><p class="text-xs uppercase tracking-wide text-slate-400">Active users</p><p id="stat-active-users" class="text-2xl font-semibold text-slate-900 mt-2"><?= (int) ($stats['active_users'] ?? 0) ?></p></div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4"><p class="text-xs uppercase tracking-wide text-slate-400">Inactive users</p><p id="stat-inactive-users" class="text-2xl font-semibold text-slate-900 mt-2"><?= (int) ($stats['inactive_users'] ?? 0) ?></p></div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4"><p class="text-xs uppercase tracking-wide text-slate-400">Admins</p><p id="stat-admins" class="text-2xl font-semibold text-slate-900 mt-2"><?= (int) ($stats['admins'] ?? 0) ?></p></div>
    </div>
</div>
<?php endif; ?>

<script>
window.monthlyRegistrations = <?= json_encode($monthlyRegistrations) ?>;
window.dashboardStats = <?= json_encode($stats) ?>;
</script>
