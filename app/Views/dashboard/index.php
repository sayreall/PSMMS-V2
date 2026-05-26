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
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden">
                <img src="<?= App\Config\App::url('images/s2s.jpg') ?>" alt="S2S" class="max-w-full max-h-full object-contain">
            </div>
            <p class="text-[10px] uppercase tracking-wide text-slate-400">FiberX Activation</p>
            <p id="stat-active-users" class="text-xl font-extrabold text-primary-700 mt-1"><?= (int)($stats['active_users'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden">
                <img src="<?= App\Config\App::url('images/fiberx.png') ?>" alt="FiberX" class="max-w-full max-h-full object-contain">
            </div>
            <p class="text-[10px] uppercase tracking-wide text-slate-400">FiberX New</p>
            <p id="stat-total-users" class="text-xl font-extrabold text-indigo-600 mt-1"><?= (int)($stats['total_users'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden">
                <img src="<?= App\Config\App::url('images/bida.jpg') ?>" alt="Bida" class="max-w-full max-h-full object-contain">
            </div>
            <p class="text-[10px] uppercase tracking-wide text-slate-400">Bida</p>
            <p id="stat-admins" class="text-xl font-extrabold text-blue-600 mt-1"><?= (int)($stats['admins'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
            <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden">
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
                ['ASM', '5', 'bg-cyan-100 text-cyan-600'],
                ['Inhouse', '56', 'bg-violet-100 text-violet-600'],
                ['Partners', '29', 'bg-orange-100 text-orange-600'],
                ['Sales Agent', '429', 'bg-emerald-100 text-emerald-600'],
                ['Admin Users', (string)($stats['admins'] ?? 0), 'bg-blue-100 text-blue-600'],
                ['Managers', '4', 'bg-slate-100 text-slate-600'],
                ['Municipality', '210', 'bg-teal-100 text-teal-600'],
                ['Installer', '137', 'bg-indigo-100 text-indigo-600'],
                ['Plan', '5', 'bg-rose-100 text-rose-600'],
                ['Products', '4', 'bg-amber-100 text-amber-600'],
            ];
            ?>
            <?php foreach ($badges as [$label, $count, $tone]): ?>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 flex items-center gap-3 hover:shadow-md transition-shadow">
                <div class="w-8 h-8 rounded-lg <?= $tone ?> flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2M12 22a10 10 0 100-20 10 10 0 000 20z"/></svg>
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
