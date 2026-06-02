<?php
$stats = $stats ?? [];
$dashboardSlug = $dashboardSlug ?? 'general';
$activeRoute = 'dashboard';

if ($dashboardSlug === 'asm-head-manager') {
    $headManagerSectionRoutes = [
        'summary-report' => 'summary_report',
        'monthly-sales-report' => 'monthly_sales_report',
        'daily-tech-productivity' => 'daily_tech_productivity',
        'technician-incentive' => 'technician_incentive',
        'tech-team-activation' => 'tech_team_activation',
        'technician-per-soc' => 'technician_per_soc',
        'productivity-per-area' => 'productivity_per_area',
        'pending-job-order' => 'pending_job_order',
        'sales-turn-ins' => 'sales_turn_ins',
        'faq' => 'faq',
    ];
    $headManagerSection = strtolower(trim((string)($_GET['section'] ?? '')));
    $activeRoute = $headManagerSectionRoutes[$headManagerSection] ?? 'dashboard';
}

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

if ($dashboardSlug === 'asm-head-manager') {
    $products = [
        ['key' => 's2s', 'name' => 'Surf2Sawa', 'short' => 'S2S', 'value' => '123,700', 'image' => 'images/s2s.jpg', 'tone' => 'text-rose-600', 'badge' => 'bg-rose-100 text-rose-600', 'color' => '#ef4444'],
        ['key' => 'fiberx', 'name' => 'FiberX', 'short' => 'FIBERX', 'value' => '93,700', 'image' => 'images/fiberx.png', 'tone' => 'text-indigo-600', 'badge' => 'bg-indigo-100 text-indigo-600', 'color' => '#4f46e5'],
        ['key' => 'bida', 'name' => 'Bida', 'short' => 'BIDA', 'value' => '803,700', 'image' => 'images/bida.jpg', 'tone' => 'text-orange-600', 'badge' => 'bg-orange-100 text-orange-600', 'color' => '#f97316'],
        ['key' => 'sme', 'name' => 'SME', 'short' => 'SME', 'value' => '83,700', 'image' => 'images/sme.png', 'tone' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-600', 'color' => '#10b981'],
    ];

    $summaryProducts = [
        's2s' => ['name' => 'Surf2Sawa', 'short' => 'S2S', 'image' => 'images/s2s.jpg', 'logo_class' => 'dashboard-product-logo', 'period' => 'Jan. 2026 - Jan. 2026', 'forecast' => 86, 'rows' => [
            ['MARGARET CUNANAN', 55, 24, 8, 87, 29, 11, 2, 42, 0, '52.73%', '40.63%', '48.28%', 45],
            ['VINCENT JOHN VILLENA', 18, 8, 3, 29, 10, 8, 3, 21, 0, '55.56%', '100.00%', '72.41%', 22],
            ['PAUL DANIEL CABUNDOC', 8, 12, 15, 35, 2, 10, 0, 12, 7, '25.00%', '37.04%', '34.29%', 13],
            ['ACE VILLARDO', 0, 6, 2, 8, 0, 3, 2, 5, 0, '0%', '62.50%', '62.50%', 5],
            ['INHOUSE PAGE', 0, 1, 0, 1, 0, 1, 0, 1, 0, '0%', '100.00%', '100.00%', 1],
        ]],
        'fiberx' => ['name' => 'FiberX', 'short' => 'FIBERX', 'image' => 'images/fiberx.png', 'logo_class' => '', 'period' => 'Jan. 2026 - Jan. 2026', 'forecast' => 73, 'rows' => [
            ['MARGARET CUNANAN', 41, 20, 9, 70, 24, 10, 4, 38, 1, '58.54%', '50.00%', '54.29%', 40],
            ['VINCENT JOHN VILLENA', 16, 9, 4, 29, 9, 8, 2, 19, 0, '56.25%', '88.89%', '65.52%', 20],
            ['PAUL DANIEL CABUNDOC', 6, 10, 13, 29, 2, 7, 1, 10, 6, '33.33%', '25.93%', '34.48%', 11],
            ['ACE VILLARDO', 1, 4, 2, 7, 0, 2, 1, 3, 0, '0%', '50.00%', '42.86%', 3],
        ]],
        'bida' => ['name' => 'Bida', 'short' => 'BIDA', 'image' => 'images/bida.jpg', 'logo_class' => 'dashboard-product-logo', 'period' => 'Jan. 2026 - Jan. 2026', 'forecast' => 68, 'rows' => [
            ['MARGARET CUNANAN', 38, 18, 7, 63, 22, 9, 3, 34, 0, '57.89%', '50.00%', '53.97%', 37],
            ['VINCENT JOHN VILLENA', 15, 7, 3, 25, 8, 6, 2, 16, 1, '53.33%', '85.71%', '64.00%', 17],
            ['PAUL DANIEL CABUNDOC', 7, 8, 10, 25, 3, 7, 0, 10, 4, '42.86%', '37.50%', '40.00%', 11],
            ['INHOUSE PAGE', 0, 2, 0, 2, 0, 2, 0, 2, 0, '0%', '100.00%', '100.00%', 2],
        ]],
        'sme' => ['name' => 'SME', 'short' => 'SME', 'image' => 'images/sme.png', 'logo_class' => '', 'period' => 'Jan. 2026 - Jan. 2026', 'forecast' => 61, 'rows' => [
            ['MARGARET CUNANAN', 30, 16, 6, 52, 18, 8, 2, 28, 0, '60.00%', '50.00%', '53.85%', 30],
            ['VINCENT JOHN VILLENA', 12, 7, 3, 22, 7, 6, 2, 15, 0, '58.33%', '85.71%', '68.18%', 16],
            ['PAUL DANIEL CABUNDOC', 5, 8, 8, 21, 2, 6, 0, 8, 3, '40.00%', '28.57%', '38.10%', 9],
            ['ACE VILLARDO', 0, 4, 1, 5, 0, 2, 1, 3, 0, '0%', '50.00%', '60.00%', 3],
        ]],
    ];

    $selectedSummaryProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
    if (!isset($summaryProducts[$selectedSummaryProductKey])) {
        $selectedSummaryProductKey = 's2s';
    }
    $selectedSummaryProduct = $summaryProducts[$selectedSummaryProductKey];

    $monthlyRegionDetails = [
        'NCLZ' => [
            'title' => 'North Central Luzon',
            'summary' => 'Total municipalities in NCLZ: 6',
            'items' => [
                ['name' => 'Tarlac', 'value' => 128],
                ['name' => 'Angeles', 'value' => 104],
                ['name' => 'Mabalacat', 'value' => 96],
                ['name' => 'San Fernando', 'value' => 88],
                ['name' => 'Cabanatuan', 'value' => 73],
                ['name' => 'Baliwag', 'value' => 51],
            ],
        ],
        'NCRR' => [
            'title' => 'NCRR Municipalities',
            'summary' => 'Total municipalities in NCRR: 10',
            'items' => [
                ['name' => 'Taguig', 'value' => 532],
                ['name' => 'Manila', 'value' => 418],
                ['name' => 'Quezon City', 'value' => 309],
                ['name' => 'South Caloocan', 'value' => 218],
                ['name' => 'Paranaque', 'value' => 141],
                ['name' => 'North Caloocan', 'value' => 137],
                ['name' => 'Malabon', 'value' => 132],
                ['name' => 'Muntinlupa', 'value' => 118],
                ['name' => 'Valenzuela', 'value' => 108],
                ['name' => 'Navotas', 'value' => 105],
            ],
        ],
        'REGION IV-A' => [
            'title' => 'Region IV-A Municipalities',
            'summary' => 'Total municipalities in Region IV-A: 7',
            'items' => [
                ['name' => 'Antipolo', 'value' => 166],
                ['name' => 'Calamba', 'value' => 139],
                ['name' => 'Dasmarinas', 'value' => 126],
                ['name' => 'Bacoor', 'value' => 117],
                ['name' => 'Santa Rosa', 'value' => 93],
                ['name' => 'General Trias', 'value' => 75],
                ['name' => 'Lipa', 'value' => 68],
            ],
        ],
        'SLB' => [
            'title' => 'South Luzon Bicol',
            'summary' => 'Total municipalities in SLB: 5',
            'items' => [
                ['name' => 'Legazpi', 'value' => 94],
                ['name' => 'Naga', 'value' => 82],
                ['name' => 'Sorsogon', 'value' => 63],
                ['name' => 'Daraga', 'value' => 51],
                ['name' => 'Tabaco', 'value' => 44],
            ],
        ],
        'VISMIN' => [
            'title' => 'Visayas and Mindanao',
            'summary' => 'Total municipalities in VISMIN: 6',
            'items' => [
                ['name' => 'Cebu City', 'value' => 151],
                ['name' => 'Davao City', 'value' => 132],
                ['name' => 'Iloilo City', 'value' => 104],
                ['name' => 'Cagayan de Oro', 'value' => 91],
                ['name' => 'Bacolod', 'value' => 83],
                ['name' => 'General Santos', 'value' => 59],
            ],
        ],
        'OTHERS' => [
            'title' => 'Other Municipalities',
            'summary' => 'Total municipalities in Others: 8',
            'items' => [
                ['name' => 'Pasay', 'value' => 86],
                ['name' => 'Pasig', 'value' => 72],
                ['name' => 'Las Pinas', 'value' => 66],
                ['name' => 'Marikina', 'value' => 38],
                ['name' => 'Makati', 'value' => 23],
                ['name' => 'Pateros', 'value' => 15],
                ['name' => 'Mandaluyong', 'value' => 5],
                ['name' => 'San Mateo', 'value' => 1],
            ],
        ],
    ];

    $monthlyReports = [
        's2s' => [
            'title' => 'NCRR - January 2026 (18 municipalities)',
            'month' => 'January',
            'year' => '2026',
            'regions' => ['NCLZ', 'NCRR', 'REGION IV-A', 'SLB', 'VISMIN'],
            'active_region' => 'NCRR',
            'labels' => ['Taguig', 'Manila', 'Quezon City', 'South Caloocan', 'Paranaque', 'North Caloocan', 'Malabon', 'Muntinlupa', 'Valenzuela', 'Navotas', 'Others'],
            'values' => [532, 418, 309, 218, 141, 137, 132, 118, 108, 105, 304],
            'region_details' => $monthlyRegionDetails,
            'color' => '#2563eb',
            'soft' => 'bg-blue-50 text-blue-700 border-blue-100',
        ],
        'fiberx' => [
            'title' => 'NCRR - January 2026 (18 municipalities)',
            'month' => 'January',
            'year' => '2026',
            'regions' => ['NCLZ', 'NCRR', 'REGION IV-A', 'SLB', 'VISMIN'],
            'active_region' => 'NCRR',
            'labels' => ['Taguig', 'Manila', 'Quezon City', 'South Caloocan', 'Paranaque', 'North Caloocan', 'Malabon', 'Muntinlupa', 'Valenzuela', 'Navotas', 'Others'],
            'values' => [456, 372, 287, 204, 165, 149, 142, 127, 121, 109, 270],
            'region_details' => $monthlyRegionDetails,
            'color' => '#7c3aed',
            'soft' => 'bg-violet-50 text-violet-700 border-violet-100',
        ],
        'bida' => [
            'title' => 'NCRR - January 2026 (18 municipalities)',
            'month' => 'January',
            'year' => '2026',
            'regions' => ['NCLZ', 'NCRR', 'REGION IV-A', 'SLB', 'VISMIN'],
            'active_region' => 'NCRR',
            'labels' => ['Taguig', 'Manila', 'Quezon City', 'South Caloocan', 'Paranaque', 'North Caloocan', 'Malabon', 'Muntinlupa', 'Valenzuela', 'Navotas', 'Others'],
            'values' => [398, 331, 276, 191, 150, 138, 128, 115, 103, 97, 255],
            'region_details' => $monthlyRegionDetails,
            'color' => '#ef4444',
            'soft' => 'bg-rose-50 text-rose-700 border-rose-100',
        ],
        'sme' => [
            'title' => 'NCRR - January 2026 (18 municipalities)',
            'month' => 'January',
            'year' => '2026',
            'regions' => ['NCLZ', 'NCRR', 'REGION IV-A', 'SLB', 'VISMIN'],
            'active_region' => 'NCRR',
            'labels' => ['Taguig', 'Manila', 'Quezon City', 'South Caloocan', 'Paranaque', 'North Caloocan', 'Malabon', 'Muntinlupa', 'Valenzuela', 'Navotas', 'Others'],
            'values' => [331, 288, 238, 176, 139, 125, 117, 105, 96, 89, 232],
            'region_details' => $monthlyRegionDetails,
            'color' => '#10b981',
            'soft' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        ],
    ];
    $selectedMonthlyProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
    if (!isset($monthlyReports[$selectedMonthlyProductKey])) {
        $selectedMonthlyProductKey = 's2s';
    }
    $selectedMonthlyReport = $monthlyReports[$selectedMonthlyProductKey];
    $selectedMonthlyProduct = null;
    foreach ($products as $product) {
        if ($product['key'] === $selectedMonthlyProductKey) {
            $selectedMonthlyProduct = $product;
            break;
        }
    }
    $selectedMonthlyProduct = $selectedMonthlyProduct ?? $products[0];

    if ($activeRoute === 'monthly_sales_report') {
        $monthlyTotal = array_sum($selectedMonthlyReport['values']);
        $monthlyTopIndex = array_search(max($selectedMonthlyReport['values']), $selectedMonthlyReport['values'], true);
        $monthlyTopArea = $selectedMonthlyReport['labels'][$monthlyTopIndex] ?? '-';
        $monthlyAverage = count($selectedMonthlyReport['values']) > 0 ? round($monthlyTotal / count($selectedMonthlyReport['values'])) : 0;
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Monthly Sales Report</h1>
                <p class="text-xs text-slate-600">Activated job orders by municipality, product, month, and region.</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 space-y-4">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <?php foreach ($products as $product): ?>
                        <?php
                            $isSelectedProduct = $selectedMonthlyProductKey === $product['key'];
                            $productUrl = '?section=monthly-sales-report&product=' . rawurlencode((string)$product['key'])
                                . '&month=' . rawurlencode((string)($_GET['month'] ?? $selectedMonthlyReport['month']))
                                . '&year=' . rawurlencode((string)($_GET['year'] ?? $selectedMonthlyReport['year']));
                        ?>
                        <a href="<?= htmlspecialchars($productUrl) ?>"
                           class="group rounded-xl border p-3 transition-all duration-200 <?= $isSelectedProduct ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="h-20 rounded-lg border <?= $isSelectedProduct ? 'border-primary-100 bg-white' : 'border-slate-100 bg-slate-50' ?> p-2 flex items-center justify-center">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold <?= $isSelectedProduct ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide"><?= htmlspecialchars($product['short']) ?> Monthly</p>
                                </div>
                                <?php if ($isSelectedProduct): ?>
                                    <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <form method="GET" class="flex flex-col lg:flex-row lg:items-end gap-3 border-t border-slate-100 pt-4">
                    <input type="hidden" name="section" value="monthly-sales-report">
                    <input type="hidden" name="product" value="<?= htmlspecialchars($selectedMonthlyProductKey) ?>">
                    <label class="flex flex-col gap-1 text-xs font-medium text-slate-600">
                        Month
                        <select name="month" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                            <?php foreach (['January', 'February', 'March', 'April', 'May', 'June'] as $month): ?>
                                <option value="<?= htmlspecialchars($month) ?>" <?= (($_GET['month'] ?? $selectedMonthlyReport['month']) === $month) ? 'selected' : '' ?>><?= htmlspecialchars($month) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="flex flex-col gap-1 text-xs font-medium text-slate-600">
                        Year
                        <select name="year" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                            <?php foreach (['2026', '2025', '2024'] as $year): ?>
                                <option value="<?= htmlspecialchars($year) ?>" <?= (($_GET['year'] ?? $selectedMonthlyReport['year']) === $year) ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">Filter</button>
                </form>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 via-white to-cyan-50 px-4 py-4 md:px-5">
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="h-16 w-32 shrink-0 rounded-lg border border-slate-200 bg-white p-2 flex items-center justify-center shadow-sm">
                                <img src="<?= App\Config\App::url($selectedMonthlyProduct['image']) ?>" alt="<?= htmlspecialchars($selectedMonthlyProduct['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($selectedMonthlyProduct['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Monthly Sales Activation</p>
                                <h2 class="text-lg font-extrabold text-slate-900"><?= htmlspecialchars($selectedMonthlyReport['title']) ?></h2>
                                <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars((string)($_GET['month'] ?? $selectedMonthlyReport['month'])) ?> <?= htmlspecialchars((string)($_GET['year'] ?? $selectedMonthlyReport['year'])) ?> - <?= htmlspecialchars($selectedMonthlyProduct['name']) ?></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 xl:w-[430px]">
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">Total Orders</p>
                                <p class="text-lg font-extrabold text-slate-900"><?= number_format($monthlyTotal) ?></p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">Top Area</p>
                                <p class="text-lg font-extrabold text-blue-600"><?= htmlspecialchars($monthlyTopArea) ?></p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">Avg / Area</p>
                                <p class="text-lg font-extrabold text-emerald-600"><?= number_format($monthlyAverage) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <?php foreach ($selectedMonthlyReport['regions'] as $region): ?>
                            <button type="button"
                                    class="monthly-region-trigger inline-flex items-center justify-center rounded-lg border px-3 py-2 text-[11px] font-bold transition-colors <?= $region === $selectedMonthlyReport['active_region'] ? 'border-primary-600 bg-primary-600 text-white shadow-sm' : 'border-blue-200 bg-blue-50 text-blue-700 hover:border-primary-300 hover:bg-cyan-50' ?>"
                                    data-region="<?= htmlspecialchars($region) ?>">
                                <?= htmlspecialchars($region) ?>
                            </button>
                        <?php endforeach; ?>
                        <button type="button"
                                class="monthly-region-trigger inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-600 transition-colors hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700"
                                data-region="OTHERS">
                            OTHERS
                        </button>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 md:p-4">
                        <div class="h-[360px]">
                            <canvas id="head-manager-monthly-report-chart"></canvas>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Total Activated Job Orders Summary</p>
                            <p class="mt-2 text-2xl font-extrabold text-blue-600"><?= number_format($monthlyTotal) ?></p>
                            <p class="text-xs text-slate-500">Total orders</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Municipalities</p>
                            <p class="mt-2 text-2xl font-extrabold text-slate-900"><?= count($selectedMonthlyReport['labels']) ?></p>
                            <p class="text-xs text-slate-500">Tracked areas</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Average Per Municipality</p>
                            <p class="mt-2 text-2xl font-extrabold text-emerald-600"><?= number_format($monthlyAverage) ?></p>
                            <p class="text-xs text-slate-500">Activated orders</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="monthly-region-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4 py-6">
                <div class="w-full max-w-xl rounded-xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-r from-cyan-50 via-white to-slate-50 px-5 py-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Region Details</p>
                            <h3 id="monthly-region-modal-title" class="text-lg font-extrabold text-primary-700">Other Municipalities</h3>
                        </div>
                        <button type="button" id="monthly-region-modal-close" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-700" aria-label="Close region details">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-5">
                        <div class="rounded-xl border border-cyan-100 bg-cyan-50/70 px-4 py-3 text-center">
                            <p class="text-[11px] uppercase tracking-wide text-primary-700 font-extrabold">Summary</p>
                            <p id="monthly-region-modal-summary" class="mt-1 text-sm font-semibold text-slate-600">Total municipalities in Others: 8</p>
                        </div>

                        <div id="monthly-region-modal-list" class="mt-4 max-h-[360px] overflow-y-auto rounded-xl border border-slate-200 divide-y divide-slate-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                if (typeof Chart === 'undefined') return;
                const canvas = document.getElementById('head-manager-monthly-report-chart');
                if (!canvas) return;

                const accent = <?= json_encode($selectedMonthlyReport['color']) ?>;
                const gradient = canvas.getContext('2d').createLinearGradient(0, 0, 0, 360);
                gradient.addColorStop(0, accent);
                gradient.addColorStop(1, 'rgba(14, 116, 144, 0.45)');

                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($selectedMonthlyReport['labels']) ?>,
                        datasets: [{
                            label: 'Activated Job Orders',
                            data: <?= json_encode($selectedMonthlyReport['values']) ?>,
                            backgroundColor: gradient,
                            borderColor: accent,
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: 46,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#475569', boxWidth: 14, font: { size: 11, weight: '600' } },
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#dbeafe',
                                padding: 12,
                                cornerRadius: 10,
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${Number(context.raw).toLocaleString()}`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#64748b', font: { size: 10, weight: '600' }, maxRotation: 0, autoSkip: false },
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(14, 116, 144, .10)' },
                                ticks: { color: '#64748b', font: { size: 11 } },
                                title: { display: true, text: 'Number of Activated Job Orders', color: '#64748b', font: { size: 11, weight: '600' } },
                            },
                        },
                    },
                });

                const regionDetails = <?= json_encode($selectedMonthlyReport['region_details']) ?>;
                const modal = document.getElementById('monthly-region-modal');
                const modalTitle = document.getElementById('monthly-region-modal-title');
                const modalSummary = document.getElementById('monthly-region-modal-summary');
                const modalList = document.getElementById('monthly-region-modal-list');
                const closeModal = document.getElementById('monthly-region-modal-close');

                const hideModal = () => {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                };

                const showModal = (regionKey) => {
                    const detail = regionDetails[regionKey] || regionDetails.OTHERS;
                    if (!modal || !modalTitle || !modalSummary || !modalList || !detail) return;

                    modalTitle.textContent = detail.title;
                    modalSummary.textContent = detail.summary;
                    modalList.innerHTML = detail.items.map((item) => `
                        <div class="flex items-center justify-between gap-3 bg-white px-4 py-3 hover:bg-cyan-50/60 transition-colors">
                            <span class="text-sm font-bold uppercase text-slate-600">${item.name}</span>
                            <span class="inline-flex min-w-12 justify-center rounded-full bg-blue-500 px-3 py-1.5 text-sm font-extrabold text-white shadow-sm">${Number(item.value).toLocaleString()}</span>
                        </div>
                    `).join('');

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                };

                document.querySelectorAll('.monthly-region-trigger').forEach((button) => {
                    button.addEventListener('click', () => showModal(button.dataset.region || 'OTHERS'));
                });

                closeModal?.addEventListener('click', hideModal);
                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) hideModal();
                });
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') hideModal();
                });
            });
        </script>
        <?php
        return;
    }

    if ($activeRoute === 'summary_report') {
        $summaryTotals = array_fill(0, 10, 0);
        foreach ($selectedSummaryProduct['rows'] as $row) {
            foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 13] as $totalIndex => $rowIndex) {
                $summaryTotals[$totalIndex] += (int)$row[$rowIndex];
            }
        }
        $partnerRate = $summaryTotals[0] > 0 ? number_format(($summaryTotals[4] / $summaryTotals[0]) * 100, 2) . '%' : '0%';
        $inhouseBase = $summaryTotals[1] + $summaryTotals[2] + $summaryTotals[8];
        $inhouseRate = $inhouseBase > 0 ? number_format((($summaryTotals[5] + $summaryTotals[6] + $summaryTotals[8]) / $inhouseBase) * 100, 2) . '%' : '0%';
        $totalRate = $summaryTotals[3] > 0 ? number_format(($summaryTotals[7] / $summaryTotals[3]) * 100, 2) . '%' : '0%';
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Summary Report</h1>
                <p class="text-xs text-slate-600">Product turn-ins, activations, and conversion rate monitoring.</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 space-y-4">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <?php foreach ($summaryProducts as $productKey => $product): ?>
                        <?php
                            $isSelectedProduct = $selectedSummaryProductKey === $productKey;
                            $productUrl = '?section=summary-report&product=' . rawurlencode((string)$productKey)
                                . '&from=' . rawurlencode((string)($_GET['from'] ?? '2026-01-29'))
                                . '&to=' . rawurlencode((string)($_GET['to'] ?? '2026-01-29'));
                        ?>
                        <a href="<?= htmlspecialchars($productUrl) ?>"
                           class="group rounded-xl border p-3 transition-all duration-200 <?= $isSelectedProduct ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="h-20 rounded-lg border <?= $isSelectedProduct ? 'border-primary-100 bg-white' : 'border-slate-100 bg-slate-50' ?> p-2 flex items-center justify-center">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain <?= htmlspecialchars($product['logo_class']) ?>">
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold <?= $isSelectedProduct ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide"><?= htmlspecialchars($product['short']) ?> Report</p>
                                </div>
                                <?php if ($isSelectedProduct): ?>
                                    <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <form method="GET" class="flex flex-col lg:flex-row lg:items-end gap-3 border-t border-slate-100 pt-4">
                    <input type="hidden" name="section" value="summary-report">
                    <input type="hidden" name="product" value="<?= htmlspecialchars($selectedSummaryProductKey) ?>">
                    <label class="flex flex-col gap-1 text-xs font-medium text-slate-600">
                        From
                        <input type="date" name="from" value="<?= htmlspecialchars((string)($_GET['from'] ?? '2026-01-29')) ?>" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                    </label>
                    <label class="flex flex-col gap-1 text-xs font-medium text-slate-600">
                        To
                        <input type="date" name="to" value="<?= htmlspecialchars((string)($_GET['to'] ?? '2026-01-29')) ?>" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                    </label>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">Filter</button>
                </form>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 via-white to-cyan-50 px-4 py-4 md:px-5">
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="h-16 w-32 shrink-0 rounded-lg border border-slate-200 bg-white p-2 flex items-center justify-center shadow-sm">
                                <img src="<?= App\Config\App::url($selectedSummaryProduct['image']) ?>" alt="<?= htmlspecialchars($selectedSummaryProduct['name']) ?>" class="max-h-full max-w-full object-contain <?= htmlspecialchars($selectedSummaryProduct['logo_class']) ?>">
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Selected Product</p>
                                <h2 class="text-lg font-extrabold text-slate-900"><?= htmlspecialchars($selectedSummaryProduct['name']) ?> Summary Report</h2>
                                <p class="text-xs text-slate-500 mt-1">
                                    <?= htmlspecialchars((string)($_GET['from'] ?? '2026-01-29')) ?> to <?= htmlspecialchars((string)($_GET['to'] ?? '2026-01-29')) ?>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 xl:w-[560px]">
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">Turn-ins</p>
                                <p class="text-lg font-extrabold text-slate-900"><?= $summaryTotals[3] ?></p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">Activated</p>
                                <p class="text-lg font-extrabold text-emerald-600"><?= $summaryTotals[7] ?></p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">Conversion</p>
                                <p class="text-lg font-extrabold text-blue-600"><?= htmlspecialchars($totalRate) ?></p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">Forecast</p>
                                <p class="text-lg font-extrabold text-indigo-600"><?= $summaryTotals[9] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 md:p-5">
                    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                        <table class="min-w-[1180px] w-full border-collapse text-[11px]">
                            <thead class="text-white">
                                <tr class="bg-primary-700">
                                    <th rowspan="2" class="border border-primary-600 px-3 py-3 text-left">AREA SALES MANAGER</th>
                                    <th colspan="4" class="border border-primary-600 px-3 py-3 text-center">TURN-INS</th>
                                    <th colspan="5" class="border border-primary-600 px-3 py-3 text-center">ACTIVATED</th>
                                    <th colspan="3" class="border border-primary-600 px-3 py-3 text-center">CONVERSION RATE (%)</th>
                                    <th rowspan="2" class="border border-primary-600 px-3 py-3 text-center">EOM FORECAST</th>
                                </tr>
                                <tr class="bg-primary-600">
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">PARTNERS</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">INHOUSE INSIDE AOI</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">INHOUSE OUTSIDE AOI</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center bg-emerald-600">TOTAL</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">PARTNERS</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">INHOUSE WITHIN AOI</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">INDIRECT ACTIVATION</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center bg-emerald-600">TOTAL</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">INHOUSE OUTSIDE AOI</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">PARTNER</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center">INHOUSE</th>
                                    <th class="border border-primary-500 px-2 py-2.5 text-center bg-emerald-600">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selectedSummaryProduct['rows'] as $row): ?>
                                    <tr class="bg-white text-slate-900 hover:bg-cyan-50/50 transition-colors">
                                        <td class="border border-slate-200 px-3 py-3 font-bold text-slate-800"><?= htmlspecialchars((string)$row[0]) ?></td>
                                        <?php foreach (array_slice($row, 1, 13) as $cellIndex => $cell): ?>
                                            <?php $isTotalCell = in_array($cellIndex, [3, 7, 12], true); ?>
                                            <td class="border border-slate-200 px-2 py-3 text-center <?= $isTotalCell ? 'bg-emerald-50 text-emerald-700 font-extrabold' : 'font-semibold text-slate-700' ?>"><?= htmlspecialchars((string)$cell) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="bg-primary-700 text-white font-bold">
                                    <td class="border border-primary-600 px-3 py-3">GRAND TOTAL (<?= htmlspecialchars($selectedSummaryProduct['period']) ?>)</td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[0] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[1] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[2] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[3] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[4] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[5] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[6] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[7] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[8] ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= htmlspecialchars($partnerRate) ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= htmlspecialchars($inhouseRate) ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= htmlspecialchars($totalRate) ?></td>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= $summaryTotals[9] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return;
    }
    ?>

    <div class="space-y-5">
        <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
            <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Product Sales Activation Performance Dashboard</h1>
            <p class="text-xs text-slate-600">Head Manager monitoring for product activations and daily performance.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center hover:shadow-md transition-shadow">
                    <div class="w-full h-24 bg-white rounded-lg border border-slate-100 p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                        <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                    </div>
                    <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['name']) ?> Activation</p>
                    <p class="text-xl font-extrabold <?= htmlspecialchars($product['tone']) ?> mt-1"><?= htmlspecialchars($product['value']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 xl:col-span-2">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Monthly Sales Activation</h2>
                        <p class="text-xs text-slate-500">Product activation trend by month.</p>
                    </div>
                    <label class="flex items-center gap-2 text-xs text-slate-500">
                        Filter
                        <select class="px-2 py-1 rounded-md border border-slate-200 bg-white text-slate-600 focus:outline-none" aria-label="Filter monthly activation">
                            <option>Please select year</option>
                            <option>2026</option>
                            <option>2025</option>
                        </select>
                    </label>
                </div>
                <div class="h-64 md:h-72">
                    <canvas id="head-manager-monthly-chart"></canvas>
                </div>
                <div class="flex flex-wrap justify-center gap-2 mt-3">
                    <?php foreach ($products as $product): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold <?= htmlspecialchars($product['badge']) ?>"><?= htmlspecialchars($product['short']) ?> Activation</span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Daily Sales Activation</h2>
                        <p class="text-xs text-slate-500">Current activation mix.</p>
                    </div>
                    <input type="date" class="max-w-[138px] px-2 py-1 rounded-md border border-slate-200 bg-white text-xs text-slate-600 focus:outline-none" aria-label="Select daily activation date">
                </div>
                <div class="rounded-lg bg-blue-50 border border-blue-100 px-3 py-2 mb-3 flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-600">Overall Activation</span>
                    <strong class="text-2xl font-extrabold text-blue-600">330</strong>
                </div>
                <div class="h-56">
                    <canvas id="head-manager-daily-chart"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-3">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-2 text-center"><p class="text-base font-bold text-slate-800">120</p><p class="text-[10px] uppercase text-slate-400">S2S</p></div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-2 text-center"><p class="text-base font-bold text-slate-800">90</p><p class="text-[10px] uppercase text-slate-400">FiberX</p></div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-2 text-center"><p class="text-base font-bold text-slate-800">70</p><p class="text-[10px] uppercase text-slate-400">SME</p></div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-2 text-center"><p class="text-base font-bold text-slate-800">50</p><p class="text-[10px] uppercase text-slate-400">Bida</p></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') return;

            const monthlyCanvas = document.getElementById('head-manager-monthly-chart');
            if (monthlyCanvas) {
                new Chart(monthlyCanvas, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [
                            { label: 'S2S', data: [200, 218, 190, 230, 205, 180, 188, 205, 232, 205, 190, 185], borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, .10)' },
                            { label: 'FiberX', data: [165, 172, 154, 176, 160, 145, 152, 164, 181, 162, 151, 155], borderColor: '#4f46e5', backgroundColor: 'rgba(79, 70, 229, .10)' },
                            { label: 'Bida', data: [123, 132, 121, 136, 124, 113, 118, 126, 137, 121, 116, 123], borderColor: '#f97316', backgroundColor: 'rgba(249, 115, 22, .10)' },
                            { label: 'SME', data: [74, 82, 71, 83, 72, 67, 78, 70, 88, 73, 72, 76], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, .10)' },
                        ].map((dataset) => ({
                            ...dataset,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderWidth: 2,
                            tension: 0.38,
                            fill: false,
                        })),
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#dbeafe',
                                padding: 12,
                                cornerRadius: 10,
                            },
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11 } } },
                            y: { beginAtZero: true, grid: { color: 'rgba(14, 116, 144, .12)' }, ticks: { color: '#64748b', font: { size: 11 } } },
                        },
                    },
                });
            }

            const dailyCanvas = document.getElementById('head-manager-daily-chart');
            if (dailyCanvas) {
                new Chart(dailyCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['S2S', 'FiberX', 'SME', 'Bida'],
                        datasets: [{
                            data: [120, 90, 70, 50],
                            backgroundColor: ['#ef4444', '#4f46e5', '#10b981', '#f97316'],
                            borderColor: '#ffffff',
                            borderWidth: 4,
                            hoverOffset: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#dbeafe',
                                padding: 12,
                                cornerRadius: 10,
                            },
                        },
                    },
                });
            }
        });
    </script>
    <?php
    return;
}
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
