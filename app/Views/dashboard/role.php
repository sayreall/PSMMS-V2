<?php
$stats = $stats ?? [];
$dashboardSlug = $dashboardSlug ?? 'general';
$activeRoute = 'dashboard';
$dispatchStatuses = $dispatchStatuses ?? [];

$fallbackDispatchStatuses = [
    ['dispatch_status' => 'ACTIVATED', 'color' => '#27d5e1'],
    ['dispatch_status' => 'CANCELLED', 'color' => '#b71c1c'],
    ['dispatch_status' => 'FOR INSTALLATION', 'color' => '#4a148c'],
    ['dispatch_status' => 'RE-SCHEDULE', 'color' => '#beb5b0'],
    ['dispatch_status' => 'ON-HOLD INSTALLATION', 'color' => '#ef6c00'],
    ['dispatch_status' => 'FOR MSA INSTALLATION', 'color' => '#ec407a'],
    ['dispatch_status' => 'FOR ACTIVATION', 'color' => '#00bcd4'],
    ['dispatch_status' => 'RESCHED BY MSA', 'color' => '#ffeb3b'],
    ['dispatch_status' => 'JRU UNINSTALLABLE', 'color' => '#6fe6fc'],
    ['dispatch_status' => 'DOUBLE ENTRY', 'color' => '#1565c0'],
];
$dispatchStatusRows = !empty($dispatchStatuses) ? $dispatchStatuses : $fallbackDispatchStatuses;
$normalizeDispatchStatus = static function (string $status): string {
    return strtoupper(trim(preg_replace('/\s+/', ' ', str_replace('-', ' ', $status)) ?? $status));
};
$dispatchStatusTextColor = static function (string $color): string {
    $hex = ltrim($color, '#');
    if (strlen($hex) !== 6) {
        return '#0f172a';
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return $luminance > 0.65 ? '#0f172a' : '#f8fafc';
};
$dispatchStatusLookup = [];
foreach ($dispatchStatusRows as $statusRow) {
    $label = strtoupper(trim((string)($statusRow['dispatch_status'] ?? '')));
    if ($label === '') {
        continue;
    }
    $color = (string)($statusRow['color'] ?? '#e2e8f0');
    $dispatchStatusLookup[$normalizeDispatchStatus($label)] = [
        'label' => $label,
        'color' => $color,
        'text_color' => $dispatchStatusTextColor($color),
    ];
}
$dispatchStatusKeyMap = [
    'ACTIVATED' => 'activated',
    'CANCELLED' => 'cancelled',
    'DOUBLE ENTRY' => 'double_entry',
    'FOR ACTIVATION' => 'for_activation',
    'FOR INSTALLATION' => 'for_installation',
    'FOR MSA INSTALLATION' => 'msa',
    'FOR REVALIDATION' => 'revalidation',
    'FOR SCHEDULE OF INSTALLATION' => 'schedule',
    'FOR VALIDATION' => 'validation',
    'JRU UNINSTALLABLE' => 'rjo',
    'JRU UNISTALLABLE' => 'rjo',
    'RJO UNINSTALLABLE' => 'rjo',
    'RJO UNISTALLABLE' => 'rjo',
    'ON HOLD INSTALLATION' => 'on_hold',
    'ON-HOLD INSTALLATION' => 'on_hold',
    'RE SCHEDULE' => 'reschedule',
    'RE-SCHEDULE' => 'reschedule',
    'RESCHED BY MSA' => 'resched_by_msa',
];

if (in_array($dashboardSlug, ['admin-dispatcher', 'asm-head-manager', 'asm-manager', 'asm-area-sales-manager'], true)) {
    $headManagerSectionRoutes = [
        'assigning-area' => 'assigning_area',
        'sub-agent-report' => 'sub_agent_report',
        'sales-status' => 'sales_status',
        'summary-report' => 'summary_report',
        'monthly-sales-report' => 'monthly_sales_report',
        'eod-jo-area' => 'eod_jo_area',
        'daily-tech-productivity' => 'daily_tech_productivity',
        'technician-incentive' => 'technician_incentive',
        'tech-team-activation' => 'tech_team_activation',
        'technician-per-soc' => 'technician_per_soc',
        'productivity-per-area' => 'productivity_per_area',
        'pending-job-order' => 'pending_job_order',
        'sales-turn-ins' => 'sales_turn_ins',
        'daily-flow-thru' => 'daily_flow_thru',
        'daily-sales-activation' => 'daily_sales_activation',
        'partners-report' => 'partners_report',
        'tat-activation' => 'tat_activation',
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

if (in_array($dashboardSlug, ['admin-dispatcher', 'asm-head-manager'], true)) {
    $products = [
        ['key' => 's2s', 'name' => 'Surf2Sawa', 'short' => 'S2S', 'value' => '123,452', 'delta' => '+2343', 'delta_positive' => true, 'image' => 'images/s2s.jpg', 'tone' => 'text-rose-600', 'badge' => 'bg-rose-100 text-rose-600', 'color' => '#ef4444'],
        ['key' => 'fiberx', 'name' => 'FiberX', 'short' => 'FIBERX', 'value' => '20,535', 'delta' => '+1,340', 'delta_positive' => true, 'image' => 'images/fiberx.png', 'tone' => 'text-indigo-600', 'badge' => 'bg-indigo-100 text-indigo-600', 'color' => '#4f46e5'],
        ['key' => 'bida', 'name' => 'Bida', 'short' => 'BIDA', 'value' => '7,637', 'delta' => '-116', 'delta_positive' => false, 'image' => 'images/bida.jpg', 'tone' => 'text-orange-600', 'badge' => 'bg-orange-100 text-orange-600', 'color' => '#f97316'],
        ['key' => 'sme', 'name' => 'SME Solutions', 'short' => 'SME', 'value' => '12,423', 'delta' => '+4.2', 'delta_positive' => true, 'image' => 'images/sme.png', 'tone' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-600', 'color' => '#10b981'],
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
        'sme' => ['name' => 'SME Solutions', 'short' => 'SME', 'image' => 'images/sme.png', 'logo_class' => '', 'period' => 'Jan. 2026 - Jan. 2026', 'forecast' => 61, 'rows' => [
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

    if ($activeRoute === 'daily_tech_productivity') {
        $dailyTechDate = (string)($_GET['dispatch_date'] ?? '2026-01-29');
        $dailyTechAreas = [
            'all' => 'ALL AREA',
            'camanava' => 'CAMANAVA',
            'manila' => 'MANILA',
            'paranaque' => 'PARANAQUE',
            'quezon-city' => 'QUEZON CITY',
        ];
        $dailyTechRows = [
            ['area' => 'camanava', 'installer' => 'BUCO/MACLANGBAYAN/TRADIO', 'activated' => 4, 'reschedule' => 1, 'rjo' => 1, 'for_activation' => 0, 'for_installation' => 1, 'cancelled' => 0, 'on_hold' => 1],
            ['area' => 'manila', 'installer' => 'DOMINGO/REYES/ROQUE', 'activated' => 4, 'reschedule' => 0, 'rjo' => 0, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 0],
            ['area' => 'quezon-city', 'installer' => 'HAGANAS/SAGUN/DEASUMILE', 'activated' => 4, 'reschedule' => 1, 'rjo' => 0, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 0],
            ['area' => 'paranaque', 'installer' => 'MIRAS/MORAL/RICO', 'activated' => 4, 'reschedule' => 2, 'rjo' => 1, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 0],
            ['area' => 'camanava', 'installer' => 'ESTILERO/PANINGBATAN/VINAS', 'activated' => 4, 'reschedule' => 1, 'rjo' => 0, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 3],
            ['area' => 'manila', 'installer' => 'LUCIATAN/UNIDA', 'activated' => 3, 'reschedule' => 0, 'rjo' => 1, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 1, 'on_hold' => 0],
            ['area' => 'camanava', 'installer' => 'CERDON/MALONZO/MILLARES', 'activated' => 2, 'reschedule' => 5, 'rjo' => 1, 'for_activation' => 0, 'for_installation' => 1, 'cancelled' => 3, 'on_hold' => 0],
            ['area' => 'paranaque', 'installer' => 'DE LUNA/SALUDAN', 'activated' => 1, 'reschedule' => 1, 'rjo' => 1, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 0],
            ['area' => 'quezon-city', 'installer' => 'PINANGAY/VENERANDA', 'activated' => 1, 'reschedule' => 1, 'rjo' => 2, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 3],
            ['area' => 'manila', 'installer' => 'BENDAL/PONCE', 'activated' => 1, 'reschedule' => 0, 'rjo' => 2, 'for_activation' => 1, 'for_installation' => 1, 'cancelled' => 0, 'on_hold' => 0],
            ['area' => 'paranaque', 'installer' => 'DAVID/MANALAYSAY', 'activated' => 1, 'reschedule' => 1, 'rjo' => 3, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 1, 'on_hold' => 0],
            ['area' => 'quezon-city', 'installer' => 'BASTE/GONZALES', 'activated' => 1, 'reschedule' => 2, 'rjo' => 1, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 2],
            ['area' => 'camanava', 'installer' => 'BIGTASIN/BORMILLA/PEKITPEKIT', 'activated' => 1, 'reschedule' => 2, 'rjo' => 2, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 1],
        ];
        $selectedDailyTechProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $dailyTechProductKeys = array_column($products, 'key');
        if (!in_array($selectedDailyTechProductKey, $dailyTechProductKeys, true)) {
            $selectedDailyTechProductKey = 's2s';
        }
        $dailyTechProductOffsets = [
            's2s' => ['activated' => 0, 'reschedule' => 0, 'rjo' => 0, 'for_activation' => 0, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 0],
            'fiberx' => ['activated' => -1, 'reschedule' => 1, 'rjo' => 0, 'for_activation' => 1, 'for_installation' => 0, 'cancelled' => 0, 'on_hold' => 1],
            'bida' => ['activated' => 1, 'reschedule' => 0, 'rjo' => 1, 'for_activation' => 0, 'for_installation' => 1, 'cancelled' => 0, 'on_hold' => 0],
            'sme' => ['activated' => -1, 'reschedule' => 0, 'rjo' => 0, 'for_activation' => 0, 'for_installation' => 1, 'cancelled' => 1, 'on_hold' => 0],
        ];
        $dailyTechOffsets = $dailyTechProductOffsets[$selectedDailyTechProductKey] ?? $dailyTechProductOffsets['s2s'];
        foreach ($dailyTechRows as &$dailyTechRow) {
            foreach ($dailyTechOffsets as $statusKey => $offset) {
                $dailyTechRow[$statusKey] = max(0, (int)$dailyTechRow[$statusKey] + (int)$offset);
            }
        }
        unset($dailyTechRow);
        $dailyTechStatuses = [];
        foreach ($dispatchStatusRows as $statusRow) {
            $statusLabel = strtoupper(trim((string)($statusRow['dispatch_status'] ?? '')));
            $statusMapKey = $normalizeDispatchStatus($statusLabel);
            $statusDataKey = $dispatchStatusKeyMap[$statusMapKey] ?? null;
            if ($statusDataKey === null) {
                continue;
            }

            $dailyTechStatuses[$statusDataKey] = [
                'key' => $statusDataKey,
                'label' => $statusLabel,
                'color' => (string)($statusRow['color'] ?? '#e2e8f0'),
                'text_color' => $dispatchStatusTextColor((string)($statusRow['color'] ?? '#e2e8f0')),
            ];
        }
        if (empty($dailyTechStatuses)) {
            $dailyTechStatuses = [
                'activated' => ['key' => 'activated', 'label' => 'ACTIVATED', 'color' => '#27d5e1', 'text_color' => '#0f172a'],
                'reschedule' => ['key' => 'reschedule', 'label' => 'RE-SCHEDULE', 'color' => '#beb5b0', 'text_color' => '#0f172a'],
                'rjo' => ['key' => 'rjo', 'label' => 'JRU UNINSTALLABLE', 'color' => '#6fe6fc', 'text_color' => '#0f172a'],
                'for_activation' => ['key' => 'for_activation', 'label' => 'FOR ACTIVATION', 'color' => '#00bcd4', 'text_color' => '#0f172a'],
                'for_installation' => ['key' => 'for_installation', 'label' => 'FOR INSTALLATION', 'color' => '#4a148c', 'text_color' => '#f8fafc'],
                'cancelled' => ['key' => 'cancelled', 'label' => 'CANCELLED', 'color' => '#b71c1c', 'text_color' => '#f8fafc'],
                'on_hold' => ['key' => 'on_hold', 'label' => 'ON-HOLD INSTALLATION', 'color' => '#ef6c00', 'text_color' => '#f8fafc'],
            ];
        }
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Daily Tech Productivity</h1>
                <p class="text-xs text-slate-600">Installer productivity by dispatch date, area, and job order status.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isDailyTechProductActive = $selectedDailyTechProductKey === $product['key'];
                        $dailyTechProductUrl = '?section=daily-tech-productivity&product=' . rawurlencode((string)$product['key'])
                            . '&dispatch_date=' . rawurlencode($dailyTechDate);
                    ?>
                    <a href="<?= htmlspecialchars($dailyTechProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isDailyTechProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isDailyTechProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isDailyTechProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Productivity</p>
                            </div>
                            <?php if ($isDailyTechProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-3">
                    <div class="flex flex-wrap gap-2" role="tablist" aria-label="Daily tech productivity areas">
                        <?php foreach ($dailyTechAreas as $areaKey => $areaLabel): ?>
                            <button type="button"
                                    class="daily-tech-area-tab inline-flex h-9 items-center justify-center rounded-lg border px-4 text-xs font-extrabold transition-colors <?= $areaKey === 'all' ? 'border-primary-500 bg-primary-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700' ?>"
                                    data-area="<?= htmlspecialchars($areaKey) ?>">
                                <?= htmlspecialchars($areaLabel) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <form method="GET" class="flex flex-wrap items-end gap-2 xl:justify-end">
                        <input type="hidden" name="section" value="daily-tech-productivity">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($selectedDailyTechProductKey) ?>">
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Dispatch Date:</span>
                            <input type="date" name="dispatch_date" value="<?= htmlspecialchars($dailyTechDate) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                        </label>
                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Filter</button>
                        <button type="button" id="daily-tech-export" class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700 transition-colors">Export</button>
                    </form>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table id="daily-tech-table" class="min-w-[1120px] w-full border-collapse text-[11px]">
                        <thead class="text-white">
                            <tr class="bg-primary-700">
                                <th class="border border-primary-600 px-3 py-3 text-center">INSTALLER</th>
                                <?php foreach ($dailyTechStatuses as $status): ?>
                                    <th class="border border-primary-600 px-3 py-3 text-center" style="background-color: <?= htmlspecialchars($status['color']) ?>; color: <?= htmlspecialchars($status['text_color']) ?>;">
                                        <?= htmlspecialchars($status['label']) ?>
                                    </th>
                                <?php endforeach; ?>
                                <th class="border border-primary-600 px-3 py-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dailyTechRows as $row): ?>
                                <?php
                                    $rowTotal = 0;
                                    foreach ($dailyTechStatuses as $status) {
                                        $rowTotal += (int)($row[$status['key']] ?? 0);
                                    }
                                ?>
                                <tr class="daily-tech-row bg-white text-slate-700 hover:bg-cyan-50/50 transition-colors" data-area="<?= htmlspecialchars($row['area']) ?>">
                                    <td class="border border-slate-200 px-3 py-2.5 text-center text-[10px] font-bold uppercase leading-tight text-slate-800"><?= htmlspecialchars($row['installer']) ?></td>
                                    <?php foreach ($dailyTechStatuses as $status): ?>
                                        <td class="daily-tech-count border border-slate-200 px-3 py-2.5 text-center font-semibold" data-key="<?= htmlspecialchars($status['key']) ?>">
                                            <?= (int)($row[$status['key']] ?? 0) ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="daily-tech-total border border-slate-200 bg-primary-50 px-3 py-2.5 text-center font-extrabold text-primary-700"><?= (int)$rowTotal ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary-700 text-white font-extrabold">
                                <td class="border border-primary-600 px-3 py-3 text-center">Grand Total</td>
                                <?php foreach ($dailyTechStatuses as $status): ?>
                                    <td class="border border-primary-600 px-3 py-3 text-center" data-total-key="<?= htmlspecialchars($status['key']) ?>">0</td>
                                <?php endforeach; ?>
                                <td class="border border-primary-600 px-3 py-3 text-center" data-total-key="row_total">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const table = document.getElementById('daily-tech-table');
                const rows = Array.from(document.querySelectorAll('.daily-tech-row'));
                const tabs = Array.from(document.querySelectorAll('.daily-tech-area-tab'));
                const exportButton = document.getElementById('daily-tech-export');
                const dispatchDate = <?= json_encode($dailyTechDate) ?>;
                const keys = <?= json_encode(array_values(array_column($dailyTechStatuses, 'key'))) ?>;

                const updateTotals = () => {
                    const visibleRows = rows.filter((row) => !row.classList.contains('hidden'));
                    const totals = Object.fromEntries(keys.map((key) => [key, 0]));
                    let grandTotal = 0;

                    visibleRows.forEach((row) => {
                        keys.forEach((key) => {
                            const value = Number(row.querySelector(`[data-key="${key}"]`)?.textContent || 0);
                            totals[key] += value;
                            grandTotal += value;
                        });
                    });

                    keys.forEach((key) => {
                        const totalCell = table?.querySelector(`[data-total-key="${key}"]`);
                        if (totalCell) totalCell.textContent = totals[key].toLocaleString();
                    });
                    const grandTotalCell = table?.querySelector('[data-total-key="row_total"]');
                    if (grandTotalCell) grandTotalCell.textContent = grandTotal.toLocaleString();
                };

                const setArea = (area) => {
                    rows.forEach((row) => row.classList.toggle('hidden', area !== 'all' && row.dataset.area !== area));
                    tabs.forEach((tab) => {
                        const isActive = tab.dataset.area === area;
                        tab.className = `daily-tech-area-tab inline-flex h-9 items-center justify-center rounded-lg border px-4 text-xs font-extrabold transition-colors ${isActive ? 'border-primary-500 bg-primary-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700'}`;
                    });
                    updateTotals();
                };

                tabs.forEach((tab) => tab.addEventListener('click', () => setArea(tab.dataset.area || 'all')));

                exportButton?.addEventListener('click', () => {
                    const visibleRows = rows.filter((row) => !row.classList.contains('hidden'));
                    const csvRows = [
                        ['Dispatch Date', dispatchDate],
                        [],
                        ['Installer', 'Activated', 'Re-schedule', 'RJO Unistallable', 'For Activation', 'For Installation', 'Cancelled', 'On-hold Installation', 'Total'],
                    ];

                    visibleRows.forEach((row) => {
                        csvRows.push(Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim()));
                    });

                    const totalRow = table?.querySelector('tfoot tr');
                    if (totalRow) csvRows.push(Array.from(totalRow.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim()));

                    const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `daily-tech-productivity-${dispatchDate}.csv`;
                    link.click();
                    URL.revokeObjectURL(link.href);
                });

                setArea('all');
            });
        </script>
        <?php
        return;
    }

    if ($activeRoute === 'technician_incentive') {
        $selectedTechIncentiveProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $techIncentiveProductKeys = array_column($products, 'key');
        if (!in_array($selectedTechIncentiveProductKey, $techIncentiveProductKeys, true)) {
            $selectedTechIncentiveProductKey = 's2s';
        }

        $techIncentiveType = strtolower(trim((string)($_GET['type'] ?? 'individual')));
        if (!in_array($techIncentiveType, ['individual', 'team'], true)) {
            $techIncentiveType = 'individual';
        }

        $techIncentiveMonth = (string)($_GET['month'] ?? '2026-01');
        $techIncentiveSearch = trim((string)($_GET['installer'] ?? ''));
        $techIncentiveMonthLabel = date('F Y', strtotime($techIncentiveMonth . '-01')) ?: 'January 2026';
        $techIncentiveDays = range(1, 29);
        $techIncentiveProductAdjustments = [
            's2s' => ['rate' => 18, 'shift' => 0],
            'fiberx' => ['rate' => 21, 'shift' => 1],
            'bida' => ['rate' => 16, 'shift' => 2],
            'sme' => ['rate' => 24, 'shift' => 3],
        ];
        $techIncentiveAdjustment = $techIncentiveProductAdjustments[$selectedTechIncentiveProductKey] ?? $techIncentiveProductAdjustments['s2s'];
        $techIncentiveNames = $techIncentiveType === 'team'
            ? ['TEAM DOMINGO', 'TEAM APRIL JAY', 'TEAM EMERSON', 'TEAM REN REN', 'TEAM ESTILLERO', 'TEAM SALUPAN', 'TEAM ROGELIO', 'TEAM TANIGAD']
            : ['MICHAEL DOMINGO', 'ROQUE APRIL JAY', 'JOHN EMERSON PABILARI', 'REN REN ULTRA', 'JOSHUA ESTILLERO', 'NORMAN SALUPAN', 'REYES PATRICK MANEZ', 'PRINCE ELDIE RICO', 'JOEY ENRIQUEZ', 'ROLDAN TUGAD', 'BRYAN BANGCO', 'JASON DE LIVIO', 'CORTES ROMAN', 'LEGASPI GIO', 'JEFFERSON LAURELES', 'KENNETH DOLINDO'];
        $techIncentiveRows = [];
        foreach ($techIncentiveNames as $nameIndex => $name) {
            if ($techIncentiveSearch !== '' && stripos($name, $techIncentiveSearch) === false) {
                continue;
            }

            $dayValues = [];
            foreach ($techIncentiveDays as $day) {
                $seed = ($day + $nameIndex + (int)$techIncentiveAdjustment['shift']) % 9;
                $value = $nameIndex > 11 ? ($seed === 0 ? 1 : 0) : (($seed % 5) + ($day % 3 === 0 ? 1 : 0));
                $dayValues[] = $techIncentiveType === 'team' ? min(12, $value + 3) : min(7, $value);
            }

            $totalUnits = array_sum($dayValues);
            $techIncentiveRows[] = [
                'name' => $name,
                'days' => $dayValues,
                'incentive' => $totalUnits * (int)$techIncentiveAdjustment['rate'],
            ];
        }
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Individual Per Installer Monthly Sales Incentives Report</h1>
                <p class="text-xs text-slate-600">Monthly incentive monitoring by product, installer, and incentive type.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isTechIncentiveProductActive = $selectedTechIncentiveProductKey === $product['key'];
                        $techIncentiveProductUrl = '?section=technician-incentive&product=' . rawurlencode((string)$product['key'])
                            . '&type=' . rawurlencode($techIncentiveType)
                            . '&month=' . rawurlencode($techIncentiveMonth)
                            . '&installer=' . rawurlencode($techIncentiveSearch);
                    ?>
                    <a href="<?= htmlspecialchars($techIncentiveProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isTechIncentiveProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isTechIncentiveProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isTechIncentiveProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Incentive</p>
                            </div>
                            <?php if ($isTechIncentiveProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-3">
                    <div class="flex flex-wrap gap-2" role="tablist" aria-label="Technician incentive type">
                        <?php foreach (['individual' => 'Individual Incentive', 'team' => 'Team Incentive'] as $typeKey => $typeLabel): ?>
                            <?php
                                $typeUrl = '?section=technician-incentive&product=' . rawurlencode($selectedTechIncentiveProductKey)
                                    . '&type=' . rawurlencode($typeKey)
                                    . '&month=' . rawurlencode($techIncentiveMonth)
                                    . '&installer=' . rawurlencode($techIncentiveSearch);
                                $isTypeActive = $techIncentiveType === $typeKey;
                            ?>
                            <a href="<?= htmlspecialchars($typeUrl) ?>"
                               class="inline-flex h-9 items-center justify-center rounded-lg border px-4 text-xs font-extrabold transition-colors <?= $isTypeActive ? 'border-primary-500 bg-primary-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700' ?>">
                                <?= htmlspecialchars($typeLabel) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <form method="GET" class="flex flex-wrap items-end gap-2 xl:justify-end">
                        <input type="hidden" name="section" value="technician-incentive">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($selectedTechIncentiveProductKey) ?>">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($techIncentiveType) ?>">
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Filter Installer:</span>
                            <input type="search" name="installer" value="<?= htmlspecialchars($techIncentiveSearch) ?>" placeholder="Search Installer..." class="h-9 w-48 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                        </label>
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Filter Month:</span>
                            <input type="month" name="month" value="<?= htmlspecialchars($techIncentiveMonth) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                        </label>
                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Filter</button>
                        <button type="button" id="tech-incentive-export" class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700 transition-colors">Export</button>
                    </form>
                </div>

                <div>
                    <h2 class="text-sm font-extrabold uppercase text-primary-700">
                        <?= $techIncentiveType === 'team' ? 'Team' : 'Daily Count' ?> Incentive - <?= htmlspecialchars(strtoupper($techIncentiveMonthLabel)) ?>
                    </h2>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table id="tech-incentive-table" class="min-w-[1380px] w-full border-collapse text-[10px]">
                        <thead class="text-white">
                            <tr class="bg-primary-700">
                                <th class="border border-primary-600 px-3 py-3 text-center">INSTALLER</th>
                                <?php foreach ($techIncentiveDays as $day): ?>
                                    <th class="border border-primary-600 px-2 py-3 text-center"><?= (int)$day ?></th>
                                <?php endforeach; ?>
                                <th class="border border-primary-600 px-3 py-3 text-center">TOTAL INCENTIVE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($techIncentiveRows as $row): ?>
                                <tr class="bg-white text-slate-700 hover:bg-cyan-50/50 transition-colors">
                                    <td class="border border-slate-200 px-3 py-2.5 text-center text-[10px] font-bold uppercase leading-tight text-slate-800"><?= htmlspecialchars($row['name']) ?></td>
                                    <?php foreach ($row['days'] as $value): ?>
                                        <td class="border border-slate-200 px-2 py-2.5 text-center font-semibold"><?= (int)$value ?></td>
                                    <?php endforeach; ?>
                                    <td class="border border-slate-200 bg-primary-50 px-3 py-2.5 text-center font-extrabold text-primary-700">PHP<?= number_format((int)$row['incentive']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary-700 text-white font-extrabold">
                                <td class="border border-primary-600 px-3 py-3 text-center">GRAND TOTAL</td>
                                <?php foreach ($techIncentiveDays as $dayIndex => $day): ?>
                                    <?php $dayTotal = array_sum(array_map(static fn(array $row): int => (int)$row['days'][$dayIndex], $techIncentiveRows)); ?>
                                    <td class="border border-primary-600 px-2 py-3 text-center"><?= (int)$dayTotal ?></td>
                                <?php endforeach; ?>
                                <td class="border border-primary-600 px-3 py-3 text-center">PHP<?= number_format(array_sum(array_map(static fn(array $row): int => (int)$row['incentive'], $techIncentiveRows))) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const exportButton = document.getElementById('tech-incentive-export');
                const table = document.getElementById('tech-incentive-table');
                const monthLabel = <?= json_encode($techIncentiveMonthLabel) ?>;
                const product = <?= json_encode(strtoupper($selectedTechIncentiveProductKey)) ?>;

                exportButton?.addEventListener('click', () => {
                    if (!table) return;
                    const csvRows = [
                        ['Technician Incentive', product, monthLabel],
                        [],
                        ...Array.from(table.querySelectorAll('tr')).map((row) => (
                            Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                        )),
                    ];
                    const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `technician-incentive-${product.toLowerCase()}-${monthLabel.toLowerCase().replace(/\s+/g, '-')}.csv`;
                    link.click();
                    URL.revokeObjectURL(link.href);
                });
            });
        </script>
        <?php
        return;
    }

    if ($activeRoute === 'tech_team_activation') {
        $selectedTeamActivationProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $teamActivationProductKeys = array_column($products, 'key');
        if (!in_array($selectedTeamActivationProductKey, $teamActivationProductKeys, true)) {
            $selectedTeamActivationProductKey = 's2s';
        }

        $teamActivationMonth = (string)($_GET['month'] ?? '2026-01');
        $teamActivationMonthLabel = date('F Y', strtotime($teamActivationMonth . '-01')) ?: 'January 2026';
        $teamActivationDays = range(1, 31);
        $teamActivationProductShift = [
            's2s' => 0,
            'fiberx' => 1,
            'bida' => 2,
            'sme' => 3,
        ][$selectedTeamActivationProductKey] ?? 0;

        $buildTeamActivationReport = static function (int $teamSeed, int $activationSeed, int $teamMax, int $activationMax) use ($teamActivationDays, $teamActivationProductShift): array {
            $teamValues = [];
            $activationValues = [];
            foreach ($teamActivationDays as $day) {
                $teamBase = ($day + $teamSeed + $teamActivationProductShift) % 9;
                $activationBase = ($day + $activationSeed + ($teamActivationProductShift * 2)) % 11;
                $teamValues[] = $day > 29 ? 0 : max(0, min($teamMax, $teamBase + ($day % 5 === 0 ? 2 : 0)));
                $activationValues[] = $day > 29 ? 0 : max(0, min($activationMax, ($activationBase * 4) + ($day % 4 === 0 ? 9 : 0)));
            }

            return [
                'team' => $teamValues,
                'activation' => $activationValues,
            ];
        };

        $teamActivationReports = [
            [
                'title' => 'Installer Monthly Team Activation',
                'subtitle' => 'Green Pasture',
                'rows' => $buildTeamActivationReport(4, 7, 19, 35),
            ],
            [
                'title' => 'Installer Monthly Team Activation',
                'subtitle' => 'Organic Paragon',
                'rows' => $buildTeamActivationReport(8, 3, 28, 68),
            ],
        ];

        $grandTeamValues = [];
        $grandActivationValues = [];
        foreach ($teamActivationDays as $dayIndex => $day) {
            $grandTeamValues[] = array_sum(array_map(static fn(array $report): int => (int)$report['rows']['team'][$dayIndex], $teamActivationReports));
            $grandActivationValues[] = array_sum(array_map(static fn(array $report): int => (int)$report['rows']['activation'][$dayIndex], $teamActivationReports));
        }
        $teamActivationReports[] = [
            'title' => 'Installer Monthly Grand Total (GP + OP)',
            'subtitle' => 'Total (GP + OP)',
            'rows' => [
                'team' => $grandTeamValues,
                'activation' => $grandActivationValues,
                'grand_total' => array_map(static fn(int $team, int $activation): int => $team + $activation, $grandTeamValues, $grandActivationValues),
            ],
            'is_grand' => true,
        ];
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Installer Per Team Activation Report</h1>
                <p class="text-xs text-slate-600">Monthly activation monitoring by product, team group, and day.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isTeamActivationProductActive = $selectedTeamActivationProductKey === $product['key'];
                        $teamActivationProductUrl = '?section=tech-team-activation&product=' . rawurlencode((string)$product['key'])
                            . '&month=' . rawurlencode($teamActivationMonth);
                    ?>
                    <a href="<?= htmlspecialchars($teamActivationProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isTeamActivationProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isTeamActivationProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isTeamActivationProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Team Activation</p>
                            </div>
                            <?php if ($isTeamActivationProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-5">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="section" value="tech-team-activation">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($selectedTeamActivationProductKey) ?>">
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Filter Month:</span>
                            <input type="month" name="month" value="<?= htmlspecialchars($teamActivationMonth) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                        </label>
                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Filter</button>
                    </form>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400"><?= htmlspecialchars($teamActivationMonthLabel) ?></p>
                </div>

                <?php foreach ($teamActivationReports as $reportIndex => $report): ?>
                    <?php
                        $isGrandReport = !empty($report['is_grand']);
                        $reportTableId = 'team-activation-table-' . $reportIndex;
                        $teamTotal = array_sum($report['rows']['team']);
                        $activationTotal = array_sum($report['rows']['activation']);
                        $teamAverage = count($teamActivationDays) > 0 ? number_format($teamTotal / count($teamActivationDays), 2) : '0.00';
                        $activationAverage = count($teamActivationDays) > 0 ? number_format($activationTotal / count($teamActivationDays), 2) : '0.00';
                    ?>
                    <section class="space-y-2">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-2">
                            <div>
                                <h2 class="text-sm font-extrabold uppercase text-primary-700"><?= htmlspecialchars($report['title']) ?></h2>
                                <p class="text-xs font-bold uppercase text-primary-800"><?= htmlspecialchars($report['subtitle']) ?></p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="team-activation-copy inline-flex h-8 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-[11px] font-bold text-primary-700 hover:border-primary-300 hover:bg-cyan-50" data-table-id="<?= htmlspecialchars($reportTableId) ?>">Copy Table (TSV)</button>
                                <button type="button" class="team-activation-export inline-flex h-8 items-center justify-center rounded-lg bg-primary-600 px-3 text-[11px] font-bold text-white hover:bg-primary-700" data-table-id="<?= htmlspecialchars($reportTableId) ?>" data-title="<?= htmlspecialchars($report['subtitle']) ?>">Export CSV</button>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                            <table id="<?= htmlspecialchars($reportTableId) ?>" class="min-w-[1480px] w-full border-collapse text-[10px]">
                                <thead class="text-white">
                                    <tr class="bg-primary-700">
                                        <th class="border border-primary-600 px-3 py-3 text-center">CATEGORY</th>
                                        <?php foreach ($teamActivationDays as $day): ?>
                                            <th class="border border-primary-600 px-2 py-3 text-center"><?= (int)$day ?></th>
                                        <?php endforeach; ?>
                                        <th class="border border-primary-600 px-3 py-3 text-center">TOTAL</th>
                                        <th class="border border-primary-600 px-3 py-3 text-center">AVERAGE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-slate-700 hover:bg-cyan-50/50 transition-colors">
                                        <td class="border border-slate-200 px-3 py-2.5 text-center font-extrabold uppercase text-slate-800">Team</td>
                                        <?php foreach ($report['rows']['team'] as $value): ?>
                                            <td class="border border-slate-200 px-2 py-2.5 text-center font-semibold"><?= (int)$value ?></td>
                                        <?php endforeach; ?>
                                        <td class="border border-slate-200 bg-primary-50 px-3 py-2.5 text-center font-extrabold text-primary-700"><?= (int)$teamTotal ?></td>
                                        <td class="border border-slate-200 bg-primary-50 px-3 py-2.5 text-center font-extrabold text-primary-700"><?= htmlspecialchars($teamAverage) ?></td>
                                    </tr>
                                    <tr class="bg-cyan-50/50 text-slate-700 hover:bg-cyan-50 transition-colors">
                                        <td class="border border-slate-200 px-3 py-2.5 text-center font-extrabold uppercase text-slate-800">Activation</td>
                                        <?php foreach ($report['rows']['activation'] as $value): ?>
                                            <td class="border border-slate-200 px-2 py-2.5 text-center font-semibold"><?= (int)$value ?></td>
                                        <?php endforeach; ?>
                                        <td class="border border-slate-200 bg-primary-50 px-3 py-2.5 text-center font-extrabold text-primary-700"><?= (int)$activationTotal ?></td>
                                        <td class="border border-slate-200 bg-primary-50 px-3 py-2.5 text-center font-extrabold text-primary-700"><?= htmlspecialchars($activationAverage) ?></td>
                                    </tr>
                                    <?php if ($isGrandReport): ?>
                                        <?php
                                            $grandTotal = array_sum($report['rows']['grand_total']);
                                            $grandAverage = count($teamActivationDays) > 0 ? number_format($grandTotal / count($teamActivationDays), 2) : '0.00';
                                        ?>
                                        <tr class="bg-primary-700 text-white font-extrabold">
                                            <td class="border border-primary-600 px-3 py-3 text-center">Grand Total</td>
                                            <?php foreach ($report['rows']['grand_total'] as $value): ?>
                                                <td class="border border-primary-600 px-2 py-3 text-center"><?= (int)$value ?></td>
                                            <?php endforeach; ?>
                                            <td class="border border-primary-600 px-3 py-3 text-center"><?= (int)$grandTotal ?></td>
                                            <td class="border border-primary-600 px-3 py-3 text-center"><?= htmlspecialchars($grandAverage) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const tableToRows = (table) => Array.from(table.querySelectorAll('tr')).map((row) => (
                    Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                ));

                document.querySelectorAll('.team-activation-copy').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const table = document.getElementById(button.dataset.tableId || '');
                        if (!table) return;
                        const tsv = tableToRows(table).map((row) => row.join('\t')).join('\n');
                        await navigator.clipboard?.writeText(tsv);
                    });
                });

                document.querySelectorAll('.team-activation-export').forEach((button) => {
                    button.addEventListener('click', () => {
                        const table = document.getElementById(button.dataset.tableId || '');
                        if (!table) return;
                        const csvRows = tableToRows(table);
                        const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = `tech-team-activation-${(button.dataset.title || 'report').toLowerCase().replace(/\s+/g, '-')}.csv`;
                        link.click();
                        URL.revokeObjectURL(link.href);
                    });
                });
            });
        </script>
        <?php
        return;
    }

    if ($activeRoute === 'technician_per_soc') {
        $selectedSocProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $socProductKeys = array_column($products, 'key');
        if (!in_array($selectedSocProductKey, $socProductKeys, true)) {
            $selectedSocProductKey = 's2s';
        }

        $socMonth = (string)($_GET['month'] ?? 'January');
        $socYear = (string)($_GET['year'] ?? '2026');
        $socMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        if (!in_array($socMonth, $socMonths, true)) {
            $socMonth = 'January';
        }
        $socYears = ['2026', '2025', '2024'];
        if (!in_array($socYear, $socYears, true)) {
            $socYear = '2026';
        }

        $socProductShift = [
            's2s' => 0,
            'fiberx' => 2,
            'bida' => 4,
            'sme' => 6,
        ][$selectedSocProductKey] ?? 0;

        $socAreas = [
            ['area' => 'CAMANAVA', 'team' => 13, 'base' => 774],
            ['area' => 'MANILA', 'team' => 6, 'base' => 361],
            ['area' => 'PARANAQUE', 'team' => 6, 'base' => 441],
            ['area' => 'QUEZON CITY', 'team' => 10, 'base' => 434],
        ];
        $socDays = 31;
        foreach ($socAreas as $areaIndex => &$socArea) {
            $socArea['team'] = max(1, (int)$socArea['team'] + ($socProductShift % 3) - ($areaIndex % 2));
            $socArea['total_activation'] = max(0, (int)$socArea['base'] + ($socProductShift * 23) - ($areaIndex * 11));
            $socArea['daily_avg'] = $socArea['team'] > 0 ? $socArea['total_activation'] / $socDays / $socArea['team'] : 0;
            $socArea['eom_forecast'] = $socArea['daily_avg'] * $socDays;
        }
        unset($socArea);

        $socGrandTeam = array_sum(array_column($socAreas, 'team'));
        $socGrandActivation = array_sum(array_column($socAreas, 'total_activation'));
        $socGrandDailyAvg = array_sum(array_column($socAreas, 'daily_avg'));
        $socGrandForecast = array_sum(array_column($socAreas, 'eom_forecast'));
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Individual Per Installer Monthly Sales Incentives Report</h1>
                <p class="text-xs text-slate-600">SOC monthly coverage by product, team count, activation total, and EOM forecast.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isSocProductActive = $selectedSocProductKey === $product['key'];
                        $socProductUrl = '?section=technician-per-soc&product=' . rawurlencode((string)$product['key'])
                            . '&month=' . rawurlencode($socMonth)
                            . '&year=' . rawurlencode($socYear);
                    ?>
                    <a href="<?= htmlspecialchars($socProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isSocProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isSocProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isSocProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> SOC</p>
                            </div>
                            <?php if ($isSocProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-5">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                    <form method="GET" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="section" value="technician-per-soc">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($selectedSocProductKey) ?>">
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Select Month:</span>
                            <select name="month" class="h-9 w-44 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                                <?php foreach ($socMonths as $month): ?>
                                    <option value="<?= htmlspecialchars($month) ?>" <?= $socMonth === $month ? 'selected' : '' ?>><?= htmlspecialchars($month) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Select Year:</span>
                            <select name="year" class="h-9 w-36 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                                <?php foreach ($socYears as $year): ?>
                                    <option value="<?= htmlspecialchars($year) ?>" <?= $socYear === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-8 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Filter</button>
                    </form>

                    <div class="flex flex-wrap gap-2 xl:justify-end" role="tablist" aria-label="Technician per SOC view">
                        <a href="<?= htmlspecialchars('?section=technician-per-soc&product=' . rawurlencode($selectedSocProductKey) . '&month=' . rawurlencode($socMonth) . '&year=' . rawurlencode($socYear) . '&view=individual') ?>"
                           class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-extrabold text-slate-600 transition-colors hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700">
                            Individual Productivity
                        </a>
                        <a href="<?= htmlspecialchars('?section=technician-per-soc&product=' . rawurlencode($selectedSocProductKey) . '&month=' . rawurlencode($socMonth) . '&year=' . rawurlencode($socYear) . '&view=monthly') ?>"
                           class="inline-flex h-9 items-center justify-center rounded-lg border border-primary-500 bg-primary-600 px-4 text-xs font-extrabold text-white shadow-sm transition-colors">
                            Monthly Team SOC
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table class="min-w-[980px] w-full border-collapse text-[11px]">
                        <thead class="text-white">
                            <tr class="bg-primary-700">
                                <th colspan="5" class="border border-primary-600 px-3 py-3 text-center uppercase tracking-wide">
                                    Team SOC Monthly Report Coverage: <?= htmlspecialchars(strtoupper($socMonth . ' ' . $socYear)) ?>
                                </th>
                            </tr>
                            <tr class="bg-primary-600">
                                <th class="border border-primary-500 px-3 py-3 text-center">SOC (AREA)</th>
                                <th class="border border-primary-500 px-3 py-3 text-center">TEAM</th>
                                <th class="border border-primary-500 px-3 py-3 text-center">TOTAL ACTIVATION</th>
                                <th class="border border-primary-500 px-3 py-3 text-center">DAILY AVG (TOTAL / DAYS / TEAM)</th>
                                <th class="border border-primary-500 px-3 py-3 text-center">EOM FORECAST (AVG * DAYS)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($socAreas as $socArea): ?>
                                <tr class="bg-white text-slate-700 hover:bg-cyan-50/50 transition-colors">
                                    <td class="border border-slate-200 bg-primary-50 px-3 py-3 text-center font-extrabold uppercase text-primary-700"><?= htmlspecialchars($socArea['area']) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-semibold"><?= (int)$socArea['team'] ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-semibold"><?= (int)$socArea['total_activation'] ?></td>
                                    <td class="border border-slate-200 bg-cyan-50/60 px-3 py-3 text-center font-extrabold text-primary-700"><?= number_format((float)$socArea['daily_avg'], 2) ?></td>
                                    <td class="border border-slate-200 bg-cyan-50/60 px-3 py-3 text-center font-extrabold text-primary-700"><?= number_format((float)$socArea['eom_forecast'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary-700 text-white font-extrabold">
                                <td class="border border-primary-600 px-3 py-3 text-center">Grand Total</td>
                                <td class="border border-primary-600 px-3 py-3 text-center"><?= (int)$socGrandTeam ?></td>
                                <td class="border border-primary-600 px-3 py-3 text-center"><?= (int)$socGrandActivation ?></td>
                                <td class="border border-primary-600 px-3 py-3 text-center"><?= number_format((float)$socGrandDailyAvg, 2) ?></td>
                                <td class="border border-primary-600 px-3 py-3 text-center"><?= number_format((float)$socGrandForecast, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php
        return;
    }

    if ($activeRoute === 'productivity_per_area') {
        $selectedAreaProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $areaProductKeys = array_column($products, 'key');
        if (!in_array($selectedAreaProductKey, $areaProductKeys, true)) {
            $selectedAreaProductKey = 's2s';
        }

        $areaMonth = (string)($_GET['month'] ?? 'January');
        $areaYear = (string)($_GET['year'] ?? '2026');
        $areaMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        if (!in_array($areaMonth, $areaMonths, true)) {
            $areaMonth = 'January';
        }
        $areaYears = ['2026', '2025', '2024'];
        if (!in_array($areaYear, $areaYears, true)) {
            $areaYear = '2026';
        }

        $areaDays = range(1, 31);
        $areaProductShift = [
            's2s' => 0,
            'fiberx' => 2,
            'bida' => 4,
            'sme' => 6,
        ][$selectedAreaProductKey] ?? 0;
        $areaProductThemes = [
            's2s' => [
                'header' => 'bg-red-600',
                'subheader' => 'bg-red-500',
                'header_border' => 'border-red-400',
                'sticky' => 'bg-red-600',
                'accent' => 'bg-red-50',
                'accent_text' => 'text-red-700',
                'soft' => 'bg-red-50/70',
                'footer' => 'bg-red-600',
                'footer_border' => 'border-red-400',
            ],
            'fiberx' => [
                'header' => 'bg-indigo-700',
                'subheader' => 'bg-indigo-600',
                'header_border' => 'border-indigo-500',
                'sticky' => 'bg-indigo-700',
                'accent' => 'bg-indigo-50',
                'accent_text' => 'text-indigo-700',
                'soft' => 'bg-blue-50/70',
                'footer' => 'bg-indigo-700',
                'footer_border' => 'border-indigo-500',
            ],
            'bida' => [
                'header' => 'bg-rose-600',
                'subheader' => 'bg-rose-500',
                'header_border' => 'border-rose-400',
                'sticky' => 'bg-rose-600',
                'accent' => 'bg-rose-50',
                'accent_text' => 'text-rose-700',
                'soft' => 'bg-rose-50/70',
                'footer' => 'bg-rose-600',
                'footer_border' => 'border-rose-400',
            ],
            'sme' => [
                'header' => 'bg-emerald-700',
                'subheader' => 'bg-emerald-600',
                'header_border' => 'border-emerald-500',
                'sticky' => 'bg-emerald-700',
                'accent' => 'bg-emerald-50',
                'accent_text' => 'text-emerald-700',
                'soft' => 'bg-emerald-50/70',
                'footer' => 'bg-emerald-700',
                'footer_border' => 'border-emerald-500',
            ],
        ];
        $areaTheme = $areaProductThemes[$selectedAreaProductKey] ?? $areaProductThemes['s2s'];
        $areaRows = [
            ['municipality' => 'TAGUIG', 'base' => 528, 'seed' => 14],
            ['municipality' => 'MANILA', 'base' => 419, 'seed' => 9],
            ['municipality' => 'QUEZON CITY', 'base' => 312, 'seed' => 5],
            ['municipality' => 'SOUTH CALOOCAN', 'base' => 218, 'seed' => 5],
            ['municipality' => 'PARANAQUE', 'base' => 139, 'seed' => 6],
            ['municipality' => 'MALABON', 'base' => 137, 'seed' => 4],
            ['municipality' => 'MUNTINLUPA', 'base' => 120, 'seed' => 3],
            ['municipality' => 'VALENZUELA', 'base' => 111, 'seed' => 3],
            ['municipality' => 'NAVOTAS', 'base' => 109, 'seed' => 2],
            ['municipality' => 'PASAY', 'base' => 86, 'seed' => 2],
            ['municipality' => 'NORTH CALOOCAN', 'base' => 76, 'seed' => 1],
            ['municipality' => 'PASIG', 'base' => 72, 'seed' => 1],
        ];

        foreach ($areaRows as $rowIndex => &$areaRow) {
            $dailyValues = [];
            foreach ($areaDays as $day) {
                $value = (($day + $rowIndex + $areaProductShift) % 8) + (int)floor((int)$areaRow['seed'] / 4);
                if ($day > 29) {
                    $value = 0;
                }
                if ($rowIndex < 3 && $day % 7 === 0) {
                    $value += 8;
                }
                $dailyValues[] = max(0, $value);
            }
            $areaRow['daily'] = $dailyValues;
            $areaRow['total_activation'] = max(0, (int)$areaRow['base'] + ($areaProductShift * 18) - ($rowIndex * 4));
            $areaRow['average'] = $areaRow['total_activation'] / 22;
            $areaRow['eom_forecast'] = $areaRow['average'] * 31;
        }
        unset($areaRow);
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Activation Per Area Report</h1>
                <p class="text-xs text-slate-600">Productivity area monitoring by product, municipality, month, and daily activation.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isAreaProductActive = $selectedAreaProductKey === $product['key'];
                        $areaProductUrl = '?section=productivity-per-area&product=' . rawurlencode((string)$product['key'])
                            . '&month=' . rawurlencode($areaMonth)
                            . '&year=' . rawurlencode($areaYear);
                    ?>
                    <a href="<?= htmlspecialchars($areaProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isAreaProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isAreaProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isAreaProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Area Report</p>
                            </div>
                            <?php if ($isAreaProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="section" value="productivity-per-area">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($selectedAreaProductKey) ?>">
                        <select name="month" class="h-9 w-40 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400" aria-label="Select month">
                            <?php foreach ($areaMonths as $month): ?>
                                <option value="<?= htmlspecialchars($month) ?>" <?= $areaMonth === $month ? 'selected' : '' ?>><?= htmlspecialchars($month) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="year" class="h-9 w-28 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400" aria-label="Select year">
                            <?php foreach ($areaYears as $year): ?>
                                <option value="<?= htmlspecialchars($year) ?>" <?= $areaYear === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Go</button>
                    </form>
                    <button type="button" id="productivity-area-export" class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700 transition-colors">Export</button>
                </div>

                <div class="overflow-auto max-h-[620px] rounded-xl border border-slate-200 shadow-sm">
                    <table id="productivity-area-table" class="min-w-[1680px] w-full border-collapse text-[10px]">
                        <thead class="text-white">
                            <tr class="<?= htmlspecialchars($areaTheme['header']) ?>">
                                <th class="sticky left-0 z-20 border <?= htmlspecialchars($areaTheme['header_border']) ?> <?= htmlspecialchars($areaTheme['sticky']) ?> px-3 py-3 text-center">MUNICIPALITY</th>
                                <th class="border <?= htmlspecialchars($areaTheme['header_border']) ?> px-3 py-3 text-center">TOTAL ACTIVATION</th>
                                <th class="border <?= htmlspecialchars($areaTheme['header_border']) ?> px-3 py-3 text-center">AVERAGE</th>
                                <th class="border <?= htmlspecialchars($areaTheme['header_border']) ?> px-3 py-3 text-center">EOM FORECAST</th>
                                <?php foreach ($areaDays as $day): ?>
                                    <th class="border <?= htmlspecialchars($areaTheme['header_border']) ?> px-2 py-3 text-center"><?= (int)$day ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($areaRows as $areaRow): ?>
                                <tr class="bg-white text-slate-700 hover:bg-cyan-50/50 transition-colors">
                                    <td class="sticky left-0 z-10 border border-slate-200 bg-white px-3 py-2.5 text-center font-extrabold uppercase text-slate-800"><?= htmlspecialchars($areaRow['municipality']) ?></td>
                                    <td class="border border-slate-200 <?= htmlspecialchars($areaTheme['accent']) ?> px-3 py-2.5 text-center font-extrabold <?= htmlspecialchars($areaTheme['accent_text']) ?>"><?= (int)$areaRow['total_activation'] ?></td>
                                    <td class="border border-slate-200 <?= htmlspecialchars($areaTheme['soft']) ?> px-3 py-2.5 text-center font-extrabold <?= htmlspecialchars($areaTheme['accent_text']) ?>"><?= number_format((float)$areaRow['average'], 2) ?></td>
                                    <td class="border border-slate-200 <?= htmlspecialchars($areaTheme['soft']) ?> px-3 py-2.5 text-center font-extrabold <?= htmlspecialchars($areaTheme['accent_text']) ?>"><?= number_format((float)$areaRow['eom_forecast'], 2) ?></td>
                                    <?php foreach ($areaRow['daily'] as $value): ?>
                                        <td class="border border-slate-200 px-2 py-2.5 text-center font-semibold"><?= (int)$value ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="<?= htmlspecialchars($areaTheme['footer']) ?> text-white font-extrabold">
                                <td class="sticky left-0 z-20 border <?= htmlspecialchars($areaTheme['footer_border']) ?> <?= htmlspecialchars($areaTheme['footer']) ?> px-3 py-3 text-center">Grand Total</td>
                                <td class="border <?= htmlspecialchars($areaTheme['footer_border']) ?> px-3 py-3 text-center"><?= array_sum(array_column($areaRows, 'total_activation')) ?></td>
                                <td class="border <?= htmlspecialchars($areaTheme['footer_border']) ?> px-3 py-3 text-center"><?= number_format(array_sum(array_column($areaRows, 'average')), 2) ?></td>
                                <td class="border <?= htmlspecialchars($areaTheme['footer_border']) ?> px-3 py-3 text-center"><?= number_format(array_sum(array_column($areaRows, 'eom_forecast')), 2) ?></td>
                                <?php foreach ($areaDays as $dayIndex => $day): ?>
                                    <?php $dayTotal = array_sum(array_map(static fn(array $row): int => (int)$row['daily'][$dayIndex], $areaRows)); ?>
                                    <td class="border <?= htmlspecialchars($areaTheme['footer_border']) ?> px-2 py-3 text-center"><?= (int)$dayTotal ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const exportButton = document.getElementById('productivity-area-export');
                const table = document.getElementById('productivity-area-table');
                exportButton?.addEventListener('click', () => {
                    if (!table) return;
                    const csvRows = Array.from(table.querySelectorAll('tr')).map((row) => (
                        Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                    ));
                    const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'productivity-per-area-<?= htmlspecialchars(strtolower($selectedAreaProductKey . '-' . $areaMonth . '-' . $areaYear)) ?>.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
                });
            });
        </script>
        <?php
        return;
    }

    if ($activeRoute === 'pending_job_order') {
        $selectedPendingProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $pendingProductKeys = array_column($products, 'key');
        if (!in_array($selectedPendingProductKey, $pendingProductKeys, true)) {
            $selectedPendingProductKey = 's2s';
        }

        $pendingRegion = (string)($_GET['region'] ?? 'all');
        $pendingMonth = (string)($_GET['month'] ?? 'January');
        $pendingMonths = ['January', 'February', 'March', 'April', 'May', 'June'];
        if (!in_array($pendingMonth, $pendingMonths, true)) {
            $pendingMonth = 'January';
        }
        $pendingRegions = [
            'all' => '-- All Regions --',
            'ncr' => 'NCR',
            'region-iv-a' => 'Region IV-A',
            'nclz' => 'NCLZ',
        ];
        if (!isset($pendingRegions[$pendingRegion])) {
            $pendingRegion = 'all';
        }

        $pendingShift = [
            's2s' => 0,
            'fiberx' => 1,
            'bida' => 2,
            'sme' => 3,
        ][$selectedPendingProductKey] ?? 0;
        $pendingRows = [
            ['region' => 'nclz', 'municipality' => 'ANGAT', 'schedule' => 6, 'validation' => 0, 'installation' => 0, 'msa' => 0, 'reschedule' => 0, 'resched_msa' => 0],
            ['region' => 'region-iv-a', 'municipality' => 'ANGONO', 'schedule' => 5, 'validation' => 0, 'installation' => 0, 'msa' => 0, 'reschedule' => 0, 'resched_msa' => 0],
            ['region' => 'region-iv-a', 'municipality' => 'ANTIPOLO', 'schedule' => 15, 'validation' => 1, 'installation' => 0, 'msa' => 7, 'reschedule' => 2, 'resched_msa' => 1],
            ['region' => 'region-iv-a', 'municipality' => 'ATIMONAN', 'schedule' => 1, 'validation' => 0, 'installation' => 0, 'msa' => 0, 'reschedule' => 0, 'resched_msa' => 0],
            ['region' => 'region-iv-a', 'municipality' => 'BACOOR', 'schedule' => 4, 'validation' => 0, 'installation' => 0, 'msa' => 0, 'reschedule' => 0, 'resched_msa' => 0],
            ['region' => 'nclz', 'municipality' => 'TIGAON', 'schedule' => 2, 'validation' => 0, 'installation' => 0, 'msa' => 0, 'reschedule' => 0, 'resched_msa' => 0],
            ['region' => 'region-iv-a', 'municipality' => 'TRECE MARTIRES', 'schedule' => 2, 'validation' => 0, 'installation' => 0, 'msa' => 0, 'reschedule' => 0, 'resched_msa' => 0],
            ['region' => 'ncr', 'municipality' => 'VALENZUELA', 'schedule' => 0, 'validation' => 0, 'installation' => 5, 'msa' => 41, 'reschedule' => 6, 'resched_msa' => 0],
        ];
        foreach ($pendingRows as &$pendingRow) {
            foreach (['schedule', 'validation', 'installation', 'msa', 'reschedule', 'resched_msa'] as $pendingKey) {
                $pendingRow[$pendingKey] = max(0, (int)$pendingRow[$pendingKey] + ($pendingShift > 0 && $pendingKey !== 'validation' ? $pendingShift : 0));
            }
        }
        unset($pendingRow);
        if ($pendingRegion !== 'all') {
            $pendingRows = array_values(array_filter($pendingRows, static fn(array $row): bool => $row['region'] === $pendingRegion));
        }
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Pending Job Order Report</h1>
                <p class="text-xs text-slate-600">Pending job order monitoring by product, region, month, and installation status.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isPendingProductActive = $selectedPendingProductKey === $product['key'];
                        $pendingProductUrl = '?section=pending-job-order&product=' . rawurlencode((string)$product['key'])
                            . '&region=' . rawurlencode($pendingRegion)
                            . '&month=' . rawurlencode($pendingMonth);
                    ?>
                    <a href="<?= htmlspecialchars($pendingProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isPendingProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isPendingProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isPendingProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Pending JO</p>
                            </div>
                            <?php if ($isPendingProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <div class="rounded-xl border border-blue-200 bg-white p-4 space-y-5">
                    <div class="text-left">
                        <h2 class="text-base font-extrabold text-primary-700">Friday, January 30, 2026</h2>
                    </div>

                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="section" value="pending-job-order">
                            <input type="hidden" name="product" value="<?= htmlspecialchars($selectedPendingProductKey) ?>">
                            <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                                <span class="whitespace-nowrap">Filter by Region:</span>
                                <select name="region" class="h-9 w-44 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                                    <?php foreach ($pendingRegions as $regionKey => $regionLabel): ?>
                                        <option value="<?= htmlspecialchars($regionKey) ?>" <?= $pendingRegion === $regionKey ? 'selected' : '' ?>><?= htmlspecialchars($regionLabel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                                <span class="whitespace-nowrap">Filter by Month:</span>
                                <select name="month" class="h-9 w-36 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                                    <?php foreach ($pendingMonths as $month): ?>
                                        <option value="<?= htmlspecialchars($month) ?>" <?= $pendingMonth === $month ? 'selected' : '' ?>><?= htmlspecialchars($month) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Apply</button>
                            <a href="?section=pending-job-order&product=<?= htmlspecialchars(rawurlencode($selectedPendingProductKey)) ?>" class="inline-flex h-9 items-center justify-center rounded-lg bg-red-500 px-4 text-xs font-bold text-white hover:bg-red-600 transition-colors">Reset</a>
                        </form>
                        <button type="button" id="pending-job-export" class="inline-flex h-9 items-center justify-center rounded-lg bg-green-600 px-4 text-xs font-bold text-white hover:bg-green-700 transition-colors">Export</button>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                        <table id="pending-job-table" class="min-w-[980px] w-full border-collapse text-[11px]">
                            <thead class="text-white">
                                <tr class="bg-orange-600">
                                    <th class="border border-orange-400 px-3 py-3 text-center">MUNICIPALITY</th>
                                    <th class="border border-orange-400 px-3 py-3 text-center">FOR SCHEDULE OF INSTALLATION</th>
                                    <th class="border border-orange-400 px-3 py-3 text-center">FOR VALIDATION</th>
                                    <th class="border border-orange-400 px-3 py-3 text-center">FOR INSTALLATION</th>
                                    <th class="border border-orange-400 px-3 py-3 text-center">FOR MSA INSTALLATION</th>
                                    <th class="border border-orange-400 px-3 py-3 text-center">RE-SCHEDULE</th>
                                    <th class="border border-orange-400 px-3 py-3 text-center">RESCHED BY MSA</th>
                                    <th class="border border-orange-400 px-3 py-3 text-center">GRAND TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingRows as $row): ?>
                                    <?php $pendingTotal = $row['schedule'] + $row['validation'] + $row['installation'] + $row['msa'] + $row['reschedule'] + $row['resched_msa']; ?>
                                    <tr class="bg-orange-100 text-slate-700 hover:bg-orange-50 transition-colors">
                                        <td class="border border-orange-200 px-3 py-3 text-center font-bold uppercase text-slate-800"><?= htmlspecialchars($row['municipality']) ?></td>
                                        <td class="border border-orange-200 px-3 py-3 text-center font-semibold"><?= (int)$row['schedule'] ?></td>
                                        <td class="border border-orange-200 px-3 py-3 text-center font-semibold"><?= (int)$row['validation'] ?></td>
                                        <td class="border border-orange-200 px-3 py-3 text-center font-semibold"><?= (int)$row['installation'] ?></td>
                                        <td class="border border-orange-200 px-3 py-3 text-center font-semibold"><?= (int)$row['msa'] ?></td>
                                        <td class="border border-orange-200 px-3 py-3 text-center font-semibold"><?= (int)$row['reschedule'] ?></td>
                                        <td class="border border-orange-200 px-3 py-3 text-center font-semibold"><?= (int)$row['resched_msa'] ?></td>
                                        <td class="border border-orange-200 bg-orange-50 px-3 py-3 text-center font-extrabold text-orange-700"><?= (int)$pendingTotal ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-orange-600 text-white font-extrabold">
                                    <td class="border border-orange-400 px-3 py-3 text-center">Total</td>
                                    <?php foreach (['schedule', 'validation', 'installation', 'msa', 'reschedule', 'resched_msa'] as $pendingKey): ?>
                                        <td class="border border-orange-400 px-3 py-3 text-center"><?= array_sum(array_column($pendingRows, $pendingKey)) ?></td>
                                    <?php endforeach; ?>
                                    <td class="border border-orange-400 px-3 py-3 text-center"><?= array_sum(array_map(static fn(array $row): int => (int)$row['schedule'] + (int)$row['validation'] + (int)$row['installation'] + (int)$row['msa'] + (int)$row['reschedule'] + (int)$row['resched_msa'], $pendingRows)) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const exportButton = document.getElementById('pending-job-export');
                const table = document.getElementById('pending-job-table');
                exportButton?.addEventListener('click', () => {
                    if (!table) return;
                    const csvRows = Array.from(table.querySelectorAll('tr')).map((row) => (
                        Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                    ));
                    const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'pending-job-order-<?= htmlspecialchars(strtolower($selectedPendingProductKey . '-' . $pendingMonth)) ?>.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
                });
            });
        </script>
        <?php
        return;
    }

    if ($activeRoute === 'daily_flow_thru') {
        $selectedFlowProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $flowProductKeys = array_column($products, 'key');
        if (!in_array($selectedFlowProductKey, $flowProductKeys, true)) {
            $selectedFlowProductKey = 's2s';
        }

        $flowRegion = (string)($_GET['region'] ?? 'all');
        $flowYear = (string)($_GET['year'] ?? '2026');
        $flowMonth = (string)($_GET['month'] ?? 'January');
        $flowRegions = ['all' => 'ALL REGIONS', 'ncr' => 'NCR', 'region-iv-a' => 'REGION IV-A', 'nclz' => 'NCLZ'];
        $flowYears = ['2026', '2025', '2024'];
        $flowMonths = ['January', 'February', 'March', 'April', 'May', 'June'];
        if (!isset($flowRegions[$flowRegion])) {
            $flowRegion = 'all';
        }
        if (!in_array($flowYear, $flowYears, true)) {
            $flowYear = '2026';
        }
        if (!in_array($flowMonth, $flowMonths, true)) {
            $flowMonth = 'January';
        }

        $flowShift = [
            's2s' => 0,
            'fiberx' => 2,
            'bida' => 4,
            'sme' => 6,
        ][$selectedFlowProductKey] ?? 0;
        $flowProductThemes = [
            's2s' => [
                'header' => 'bg-red-50',
                'header_text' => 'text-red-700',
                'border' => 'border-red-200',
                'primary_label' => 'bg-red-50 text-red-700',
                'secondary_label' => 'bg-red-100/70 text-red-800',
                'value_hover' => 'hover:bg-red-50/60',
            ],
            'fiberx' => [
                'header' => 'bg-indigo-50',
                'header_text' => 'text-indigo-700',
                'border' => 'border-indigo-200',
                'primary_label' => 'bg-indigo-50 text-indigo-700',
                'secondary_label' => 'bg-blue-100/70 text-blue-800',
                'value_hover' => 'hover:bg-indigo-50/60',
            ],
            'bida' => [
                'header' => 'bg-rose-50',
                'header_text' => 'text-rose-700',
                'border' => 'border-rose-200',
                'primary_label' => 'bg-rose-50 text-rose-700',
                'secondary_label' => 'bg-rose-100/70 text-rose-800',
                'value_hover' => 'hover:bg-rose-50/60',
            ],
            'sme' => [
                'header' => 'bg-emerald-50',
                'header_text' => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'primary_label' => 'bg-emerald-50 text-emerald-700',
                'secondary_label' => 'bg-emerald-100/70 text-emerald-800',
                'value_hover' => 'hover:bg-emerald-50/60',
            ],
        ];
        $flowTheme = $flowProductThemes[$selectedFlowProductKey] ?? $flowProductThemes['s2s'];
        $flowDays = range(1, 31);
        $flowStatuses = [
            ['name' => 'BEGINNING JO', 'tone' => $flowTheme['primary_label'] . ' font-extrabold', 'seed' => 15, 'max' => 62],
            ['name' => 'NEW JO', 'tone' => $flowTheme['primary_label'] . ' font-extrabold', 'seed' => 62, 'max' => 260],
            ['name' => 'DISPATCH JO', 'tone' => $flowTheme['primary_label'] . ' font-extrabold', 'seed' => 2, 'max' => 240],
            ['name' => 'ACTIVATED', 'tone' => 'bg-slate-50 text-slate-800 font-semibold', 'seed' => 2, 'max' => 150],
            ['name' => 'CANCELLED', 'tone' => 'bg-slate-50 text-slate-800 font-semibold', 'seed' => 0, 'max' => 3],
            ['name' => 'DOUBLE ENTRY', 'tone' => 'bg-slate-50 text-slate-800 font-semibold', 'seed' => 0, 'max' => 4],
            ['name' => 'FOR ACTIVATION', 'tone' => 'bg-slate-50 text-slate-800 font-semibold', 'seed' => 0, 'max' => 2],
            ['name' => 'RJO UNISTALLABLE', 'tone' => 'bg-slate-50 text-slate-800 font-semibold', 'seed' => 11, 'max' => 34],
            ['name' => 'ON-HOLD INSTALLATION', 'tone' => 'bg-slate-50 text-slate-800 font-semibold', 'seed' => 0, 'max' => 48],
            ['name' => 'REMAINING PENDING', 'tone' => $flowTheme['secondary_label'] . ' font-extrabold', 'seed' => 15, 'max' => 62],
            ['name' => 'RE-SCHEDULE', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 17],
            ['name' => 'FOR INSTALLATION', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 1, 'max' => 16],
            ['name' => 'FOR SCHEDULE OF INSTALLATION', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 13],
            ['name' => 'FOR MSA INSTALLATION', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 12],
            ['name' => 'ON-HOLD INSTALLATION', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 14, 'max' => 40],
            ['name' => 'RESCHED BY MSA', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 1],
            ['name' => 'FOR VALIDATION', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 11],
            ['name' => 'FOR REVALIDATION', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 1],
            ['name' => 'FOR COMPLIANCE', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 5],
            ['name' => 'OMD VALIDATED', 'tone' => 'bg-white text-slate-700 font-semibold', 'seed' => 0, 'max' => 0],
        ];
        foreach ($flowStatuses as &$flowStatus) {
            $configuredStatus = $dispatchStatusLookup[$normalizeDispatchStatus((string)$flowStatus['name'])] ?? null;
            if ($configuredStatus !== null) {
                $flowStatus['color'] = $configuredStatus['color'];
                $flowStatus['text_color'] = $configuredStatus['text_color'];
                $flowStatus['tone'] = 'font-extrabold';
            }
        }
        unset($flowStatus);
        foreach ($flowStatuses as $statusIndex => &$flowStatus) {
            $values = [];
            foreach ($flowDays as $day) {
                if ($day > 29) {
                    $value = 0;
                } elseif ($flowStatus['seed'] === 0) {
                    $value = (($day + $statusIndex + $flowShift) % 11 === 0) ? min((int)$flowStatus['max'], 1 + $flowShift) : 0;
                } else {
                    $value = (int)$flowStatus['seed'] + (($day * ($statusIndex + 3 + $flowShift)) % max(1, (int)$flowStatus['max']));
                    $value = min((int)$flowStatus['max'], $value);
                }
                $values[] = $value;
            }
            $flowStatus['values'] = $values;
        }
        unset($flowStatus);
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Daily Flow Thru of Every Job Order Update</h1>
                <p class="text-xs text-slate-600">Daily job order flow monitoring by product, region, year, month, and status.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isFlowProductActive = $selectedFlowProductKey === $product['key'];
                        $flowProductUrl = '?section=daily-flow-thru&product=' . rawurlencode((string)$product['key'])
                            . '&region=' . rawurlencode($flowRegion)
                            . '&year=' . rawurlencode($flowYear)
                            . '&month=' . rawurlencode($flowMonth);
                    ?>
                    <a href="<?= htmlspecialchars($flowProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isFlowProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isFlowProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isFlowProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Flow Thru</p>
                            </div>
                            <?php if ($isFlowProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <input type="hidden" name="section" value="daily-flow-thru">
                    <input type="hidden" name="product" value="<?= htmlspecialchars($selectedFlowProductKey) ?>">
                    <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                        <span class="whitespace-nowrap">Region:</span>
                        <select name="region" class="h-9 w-44 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400" aria-label="Select region">
                            <?php foreach ($flowRegions as $regionKey => $regionLabel): ?>
                                <option value="<?= htmlspecialchars($regionKey) ?>" <?= $flowRegion === $regionKey ? 'selected' : '' ?>><?= htmlspecialchars($regionLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                        <span class="whitespace-nowrap">Year:</span>
                        <select name="year" class="h-9 w-28 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400" aria-label="Select year">
                            <?php foreach ($flowYears as $year): ?>
                                <option value="<?= htmlspecialchars($year) ?>" <?= $flowYear === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                        <span class="whitespace-nowrap">Month:</span>
                        <select name="month" class="h-9 w-36 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400" aria-label="Select month">
                            <?php foreach ($flowMonths as $month): ?>
                                <option value="<?= htmlspecialchars($month) ?>" <?= $flowMonth === $month ? 'selected' : '' ?>><?= htmlspecialchars($month) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Filter</button>
                </form>

                <div class="overflow-auto max-h-[620px] rounded-xl border <?= htmlspecialchars($flowTheme['border']) ?> bg-white/80 shadow-sm backdrop-blur-sm">
                    <table id="daily-flow-table" class="min-w-[1480px] w-full border-collapse text-[10px]">
                        <thead>
                            <tr class="<?= htmlspecialchars($flowTheme['header']) ?> <?= htmlspecialchars($flowTheme['header_text']) ?>">
                                <th class="sticky left-0 z-20 border <?= htmlspecialchars($flowTheme['border']) ?> <?= htmlspecialchars($flowTheme['header']) ?> px-3 py-3 text-left font-extrabold <?= htmlspecialchars($flowTheme['header_text']) ?>">Month of: <?= htmlspecialchars($flowMonth . ' ' . $flowYear) ?></th>
                                <?php foreach ($flowDays as $day): ?>
                                    <th class="border <?= htmlspecialchars($flowTheme['border']) ?> px-2 py-3 text-center font-bold <?= htmlspecialchars($flowTheme['header_text']) ?>"><?= (int)$day ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($flowStatuses as $status): ?>
                                <tr>
                                    <td class="sticky left-0 z-10 border <?= htmlspecialchars($flowTheme['border']) ?> px-3 py-2.5 text-left uppercase shadow-[1px_0_0_#e5e7eb] <?= htmlspecialchars($status['tone']) ?>" <?php if (!empty($status['color'])): ?>style="background-color: <?= htmlspecialchars($status['color']) ?>; color: <?= htmlspecialchars($status['text_color']) ?>;"<?php endif; ?>><?= htmlspecialchars($status['name']) ?></td>
                                    <?php foreach ($status['values'] as $value): ?>
                                        <td class="border <?= htmlspecialchars($flowTheme['border']) ?> bg-white/85 px-2 py-2.5 text-center font-semibold text-slate-700 <?= htmlspecialchars($flowTheme['value_hover']) ?>"><?= (int)$value ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        return;
    }

    if ($activeRoute === 'sales_turn_ins') {
        $selectedTurninsProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $turninsProductKeys = array_column($products, 'key');
        if (!in_array($selectedTurninsProductKey, $turninsProductKeys, true)) {
            $selectedTurninsProductKey = 's2s';
        }

        $turninsFrom = (string)($_GET['from'] ?? '2026-01-30');
        $turninsTo = (string)($_GET['to'] ?? '2026-01-30');
        $turninsView = strtolower(trim((string)($_GET['view'] ?? 'turn-ins')));
        if (!in_array($turninsView, ['turn-ins', 'partners-count'], true)) {
            $turninsView = 'turn-ins';
        }
        $turninsManager = strtolower(trim((string)($_GET['manager'] ?? 'all')));
        $turninsManagers = [
            'all' => 'Show All',
            'ace-villardo' => 'ACE VILLARDO',
            'inhouse-page' => 'INHOUSE PAGE',
            'margaret-cunanan' => 'MARGARET CUNANAN',
            'paul-daniel-cabundoc' => 'PAUL DANIEL CABUNDOC',
            'vincent-john-villena' => 'VINCENT JOHN VILLENA',
        ];
        if (!isset($turninsManagers[$turninsManager])) {
            $turninsManager = 'all';
        }

        $turninsShift = [
            's2s' => 0,
            'fiberx' => 1,
            'bida' => 2,
            'sme' => 3,
        ][$selectedTurninsProductKey] ?? 0;
        $turninsRows = [
            ['manager' => 'vincent-john-villena', 'category' => 'VINCENT JOHN VILLENA', 'total' => 9, 'activated' => 3, 'schedule' => 0, 'installation' => 2, 'msa' => 0, 'double_entry' => 0, 'reschedule' => 0, 'on_hold' => 2, 'validation' => 0, 'revalidation' => 0],
            ['manager' => 'ace-villardo', 'category' => 'ACE VILLARDO', 'total' => 2, 'activated' => 1, 'schedule' => 0, 'installation' => 0, 'msa' => 0, 'double_entry' => 0, 'reschedule' => 0, 'on_hold' => 0, 'validation' => 0, 'revalidation' => 0],
            ['manager' => 'inhouse-page', 'category' => 'INHOUSE PAGE', 'total' => 3, 'activated' => 0, 'schedule' => 0, 'installation' => 2, 'msa' => 0, 'double_entry' => 0, 'reschedule' => 1, 'on_hold' => 0, 'validation' => 0, 'revalidation' => 0],
            ['manager' => 'paul-daniel-cabundoc', 'category' => 'PAUL DANIEL CABUNDOC', 'total' => 26, 'activated' => 0, 'schedule' => 1, 'installation' => 5, 'msa' => 6, 'double_entry' => 7, 'reschedule' => 1, 'on_hold' => 0, 'validation' => 4, 'revalidation' => 2],
            ['manager' => 'margaret-cunanan', 'category' => 'MARGARET CUNANAN', 'total' => 18, 'activated' => 2, 'schedule' => 1, 'installation' => 6, 'msa' => 3, 'double_entry' => 2, 'reschedule' => 1, 'on_hold' => 1, 'validation' => 2, 'revalidation' => 0],
            ['manager' => 'vincent-john-villena', 'category' => 'VILLENA PARTNERS', 'total' => 15, 'activated' => 1, 'schedule' => 1, 'installation' => 3, 'msa' => 1, 'double_entry' => 6, 'reschedule' => 1, 'on_hold' => 0, 'validation' => 1, 'revalidation' => 1],
        ];
        foreach ($turninsRows as &$turninsRow) {
            foreach (['total', 'activated', 'schedule', 'installation', 'msa', 'double_entry', 'reschedule', 'on_hold', 'validation', 'revalidation'] as $turninsKey) {
                $increment = $turninsKey === 'total' ? $turninsShift * 3 : ($turninsShift > 0 && $turninsKey !== 'on_hold' ? 1 : 0);
                $turninsRow[$turninsKey] = max(0, (int)$turninsRow[$turninsKey] + $increment);
            }
        }
        unset($turninsRow);
        if ($turninsManager !== 'all') {
            $turninsRows = array_values(array_filter($turninsRows, static fn(array $row): bool => $row['manager'] === $turninsManager));
        }
        if ($turninsView === 'partners-count') {
            foreach ($turninsRows as &$turninsRow) {
                $turninsRow['total'] = max(0, (int)ceil($turninsRow['total'] / 2));
                foreach (['activated', 'schedule', 'installation', 'msa', 'double_entry', 'reschedule', 'on_hold', 'validation', 'revalidation'] as $turninsKey) {
                    $turninsRow[$turninsKey] = max(0, (int)floor($turninsRow[$turninsKey] / 2));
                }
            }
            unset($turninsRow);
        }
        $turninsStatusColumns = [];
        foreach ($dispatchStatusRows as $statusRow) {
            $statusLabel = strtoupper(trim((string)($statusRow['dispatch_status'] ?? '')));
            $statusMapKey = $normalizeDispatchStatus($statusLabel);
            $statusDataKey = $dispatchStatusKeyMap[$statusMapKey] ?? null;
            if ($statusDataKey === null || !array_key_exists($statusDataKey, $turninsRows[0] ?? [])) {
                continue;
            }

            $turninsStatusColumns[$statusDataKey] = [
                'key' => $statusDataKey,
                'label' => $statusLabel,
                'color' => (string)($statusRow['color'] ?? '#e2e8f0'),
                'text_color' => $dispatchStatusTextColor((string)($statusRow['color'] ?? '#e2e8f0')),
            ];
        }
        foreach ([
            'activated' => 'ACTIVATED',
            'schedule' => 'FOR SCHEDULE OF INSTALLATION',
            'installation' => 'FOR INSTALLATION',
            'msa' => 'FOR MSA INSTALLATION',
            'double_entry' => 'DOUBLE ENTRY',
            'reschedule' => 'RE-SCHEDULE',
            'on_hold' => 'ON-HOLD INSTALLATION',
            'validation' => 'FOR VALIDATION',
            'revalidation' => 'FOR REVALIDATION',
        ] as $fallbackKey => $fallbackLabel) {
            if (!isset($turninsStatusColumns[$fallbackKey])) {
                $configuredStatus = $dispatchStatusLookup[$normalizeDispatchStatus($fallbackLabel)] ?? null;
                $fallbackColor = $configuredStatus['color'] ?? '#e2e8f0';
                $turninsStatusColumns[$fallbackKey] = [
                    'key' => $fallbackKey,
                    'label' => $configuredStatus['label'] ?? $fallbackLabel,
                    'color' => $fallbackColor,
                    'text_color' => $configuredStatus['text_color'] ?? $dispatchStatusTextColor($fallbackColor),
                ];
            }
        }
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">Sales Turn-ins Report</h1>
                <p class="text-xs text-slate-600">Sales turn-ins monitoring by product, date range, manager, and job order status.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isTurninsProductActive = $selectedTurninsProductKey === $product['key'];
                        $turninsProductUrl = '?section=sales-turn-ins&product=' . rawurlencode((string)$product['key'])
                            . '&from=' . rawurlencode($turninsFrom)
                            . '&to=' . rawurlencode($turninsTo)
                            . '&view=' . rawurlencode($turninsView)
                            . '&manager=' . rawurlencode($turninsManager);
                    ?>
                    <a href="<?= htmlspecialchars($turninsProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isTurninsProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isTurninsProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isTurninsProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Turn-ins</p>
                            </div>
                            <?php if ($isTurninsProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                    <form method="GET" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="section" value="sales-turn-ins">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($selectedTurninsProductKey) ?>">
                        <input type="hidden" name="view" value="<?= htmlspecialchars($turninsView) ?>">
                        <input type="hidden" name="manager" value="<?= htmlspecialchars($turninsManager) ?>">
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Starting From:</span>
                            <input type="date" name="from" value="<?= htmlspecialchars($turninsFrom) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                        </label>
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">End To:</span>
                            <input type="date" name="to" value="<?= htmlspecialchars($turninsTo) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                        </label>
                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-5 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Filter</button>
                    </form>

                    <div class="flex flex-wrap gap-2 xl:justify-end" role="tablist" aria-label="Sales turn-ins view">
                        <?php foreach (['turn-ins' => 'Turn-ins', 'partners-count' => 'Partners Count'] as $viewKey => $viewLabel): ?>
                            <?php
                                $viewUrl = '?section=sales-turn-ins&product=' . rawurlencode($selectedTurninsProductKey)
                                    . '&from=' . rawurlencode($turninsFrom)
                                    . '&to=' . rawurlencode($turninsTo)
                                    . '&view=' . rawurlencode($viewKey)
                                    . '&manager=' . rawurlencode($turninsManager);
                                $isViewActive = $turninsView === $viewKey;
                            ?>
                            <a href="<?= htmlspecialchars($viewUrl) ?>"
                               class="inline-flex h-9 items-center justify-center rounded-lg border px-4 text-xs font-extrabold transition-colors <?= $isViewActive ? 'border-primary-500 bg-primary-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700' ?>">
                                <?= htmlspecialchars($viewLabel) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 class="text-sm font-semibold uppercase text-slate-700">Sales Manager Toggle (All Types)</h2>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($turninsManagers as $managerKey => $managerLabel): ?>
                            <?php
                                $managerUrl = '?section=sales-turn-ins&product=' . rawurlencode($selectedTurninsProductKey)
                                    . '&from=' . rawurlencode($turninsFrom)
                                    . '&to=' . rawurlencode($turninsTo)
                                    . '&view=' . rawurlencode($turninsView)
                                    . '&manager=' . rawurlencode($managerKey);
                                $isManagerActive = $turninsManager === $managerKey;
                            ?>
                            <a href="<?= htmlspecialchars($managerUrl) ?>"
                               class="inline-flex h-9 items-center justify-center rounded-lg border px-4 text-xs font-bold transition-colors <?= $isManagerActive ? 'border-green-500 bg-green-500 text-white shadow-sm' : 'border-primary-200 bg-white text-primary-700 hover:border-primary-300 hover:bg-cyan-50' ?>">
                                <?= htmlspecialchars($managerLabel) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="overflow-auto max-h-[520px] rounded-xl border border-slate-200 shadow-sm">
                    <table id="sales-turnins-table" class="min-w-[1180px] w-full border-collapse text-[11px]">
                        <thead class="text-white">
                            <tr class="bg-primary-700">
                                <th class="sticky left-0 z-20 border border-primary-600 bg-primary-700 px-3 py-3 text-center">SALES CATEGORY</th>
                                <th class="border border-primary-600 px-3 py-3 text-center">TOTAL</th>
                                <?php foreach ($turninsStatusColumns as $status): ?>
                                    <th class="border border-primary-600 px-3 py-3 text-center" style="background-color: <?= htmlspecialchars($status['color']) ?>; color: <?= htmlspecialchars($status['text_color']) ?>;">
                                        <?= htmlspecialchars($status['label']) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($turninsRows as $row): ?>
                                <tr class="bg-white text-slate-700 hover:bg-cyan-50/50 transition-colors">
                                    <td class="sticky left-0 z-10 border border-slate-200 bg-white px-3 py-3 text-left text-[10px] font-extrabold uppercase leading-tight text-primary-700"><?= htmlspecialchars($row['category']) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-extrabold bg-primary-50 text-primary-700"><?= (int)$row['total'] ?></td>
                                    <?php foreach ($turninsStatusColumns as $status): ?>
                                        <?php $turninsKey = $status['key']; ?>
                                        <td class="border border-slate-200 px-3 py-3 text-center font-semibold <?= $turninsKey === 'total' ? 'bg-primary-50 text-primary-700 font-extrabold' : '' ?>"><?= (int)$row[$turninsKey] ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-100 text-slate-900 font-extrabold">
                                <td class="sticky left-0 z-20 border border-blue-200 bg-blue-100 px-3 py-3 text-center text-[10px] uppercase">Over All Total</td>
                                <td class="border border-blue-200 px-3 py-3 text-center"><?= array_sum(array_column($turninsRows, 'total')) ?></td>
                                <?php foreach ($turninsStatusColumns as $status): ?>
                                    <?php $turninsKey = $status['key']; ?>
                                    <td class="border border-blue-200 px-3 py-3 text-center"><?= array_sum(array_column($turninsRows, $turninsKey)) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php
        return;
    }

    if ($activeRoute === 'partners_report') {
        $selectedPartnersProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $partnersProductKeys = array_column($products, 'key');
        if (!in_array($selectedPartnersProductKey, $partnersProductKeys, true)) {
            $selectedPartnersProductKey = 's2s';
        }

        $partnersYear = (string)($_GET['year'] ?? '2026');
        $partnersYears = ['2026', '2025', '2024'];
        if (!in_array($partnersYear, $partnersYears, true)) {
            $partnersYear = '2026';
        }

        $partnersScope = strtolower(trim((string)($_GET['scope'] ?? 'all')));
        if (!in_array($partnersScope, ['all', 'ncr', 'regional'], true)) {
            $partnersScope = 'all';
        }

        $partnersShift = [
            's2s' => 0,
            'fiberx' => 1,
            'bida' => 2,
            'sme' => 3,
        ][$selectedPartnersProductKey] ?? 0;
        $partnerRows = [
            ['partner' => '3DM INTERNET INSTALLATION SERVICES', 'ncr' => 229, 'regional' => 0],
            ['partner' => '5 JHMD INTERNET INSTALLATION SERVICES', 'ncr' => 28, 'regional' => 36],
            ['partner' => 'AJL INTERNET INSTALLATION SERVICES', 'ncr' => 0, 'regional' => 148],
            ['partner' => 'ALLYS 88 CORPORATION', 'ncr' => 138, 'regional' => 14],
            ['partner' => 'ARGIN NETWORK SERVICES INC', 'ncr' => 156, 'regional' => 60],
            ['partner' => 'AYM2 INTERNET INSTALLATION SERVICES', 'ncr' => 0, 'regional' => 12],
            ['partner' => 'DIGITECH', 'ncr' => 0, 'regional' => 82],
            ['partner' => 'FASTLINES INFORMATION SERVICES', 'ncr' => 323, 'regional' => 6],
            ['partner' => 'FERNANDO ROGER INTERNET INSTALLATION SERVICES', 'ncr' => 0, 'regional' => 21],
        ];
        foreach ($partnerRows as &$partnerRow) {
            $partnerRow['ncr'] = max(0, (int)$partnerRow['ncr'] + ($partnersShift * 7));
            $partnerRow['regional'] = max(0, (int)$partnerRow['regional'] + ($partnersShift * 5));
            $partnerRow['total'] = (int)$partnerRow['ncr'] + (int)$partnerRow['regional'];
        }
        unset($partnerRow);
        if ($partnersScope === 'ncr') {
            $partnerRows = array_values(array_filter($partnerRows, static fn(array $row): bool => (int)$row['ncr'] > 0));
        } elseif ($partnersScope === 'regional') {
            $partnerRows = array_values(array_filter($partnerRows, static fn(array $row): bool => (int)$row['regional'] > 0));
        }
        $partnerMonths = ['January', 'February', 'March'];
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">NCR & Regional Partners Monthly Sales Activation Report</h1>
                <p class="text-xs text-slate-600">Partner activation monitoring by product, year, scope, and monthly NCR/regional count.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isPartnersProductActive = $selectedPartnersProductKey === $product['key'];
                        $partnersProductUrl = '?section=partners-report&product=' . rawurlencode((string)$product['key'])
                            . '&year=' . rawurlencode($partnersYear)
                            . '&scope=' . rawurlencode($partnersScope);
                    ?>
                    <a href="<?= htmlspecialchars($partnersProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isPartnersProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isPartnersProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isPartnersProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> Partners</p>
                            </div>
                            <?php if ($isPartnersProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="section" value="partners-report">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($selectedPartnersProductKey) ?>">
                        <label class="flex h-9 items-center gap-2 text-xs font-bold text-slate-600">
                            <span class="whitespace-nowrap">Select Year:</span>
                            <select name="year" class="h-9 w-28 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                                <?php foreach ($partnersYears as $year): ?>
                                    <option value="<?= htmlspecialchars($year) ?>" <?= $partnersYear === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <?php foreach (['all' => 'ALL', 'ncr' => 'NCR', 'regional' => 'REGIONAL'] as $scopeKey => $scopeLabel): ?>
                            <button type="submit" name="scope" value="<?= htmlspecialchars($scopeKey) ?>" class="inline-flex h-9 items-center justify-center rounded-lg border px-4 text-xs font-extrabold transition-colors <?= $partnersScope === $scopeKey ? 'border-primary-500 bg-primary-600 text-white shadow-sm' : 'border-primary-200 bg-white text-primary-700 hover:border-primary-300 hover:bg-cyan-50' ?>">
                                <?= htmlspecialchars($scopeLabel) ?>
                            </button>
                        <?php endforeach; ?>
                    </form>
                    <button type="button" id="partners-report-export" class="inline-flex h-9 items-center justify-center rounded-lg bg-green-600 px-4 text-xs font-bold text-white hover:bg-green-700 transition-colors">Export</button>
                </div>

                <div class="overflow-auto max-h-[620px] rounded-xl border border-slate-200 shadow-sm">
                    <table id="partners-report-table" class="min-w-[1280px] w-full border-collapse text-[11px]">
                        <thead class="text-white">
                            <tr class="bg-primary-700">
                                <th rowspan="2" class="sticky left-0 z-20 border border-primary-600 bg-primary-700 px-3 py-3 text-center">PARTNER</th>
                                <th rowspan="2" class="border border-primary-600 px-3 py-3 text-center">TOTAL</th>
                                <th rowspan="2" class="border border-primary-600 px-3 py-3 text-center">TOTAL NCR</th>
                                <th rowspan="2" class="border border-primary-600 px-3 py-3 text-center">TOTAL REGIONAL</th>
                                <?php foreach ($partnerMonths as $month): ?>
                                    <th colspan="2" class="border border-primary-600 px-3 py-3 text-center uppercase"><?= htmlspecialchars($month) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <tr class="bg-primary-600">
                                <?php foreach ($partnerMonths as $month): ?>
                                    <th class="border border-primary-500 px-3 py-2.5 text-center">NCR</th>
                                    <th class="border border-primary-500 px-3 py-2.5 text-center">REGIONAL</th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($partnerRows as $rowIndex => $row): ?>
                                <tr class="<?= $rowIndex % 2 === 0 ? 'bg-slate-100' : 'bg-white' ?> text-slate-700 hover:bg-cyan-50/50 transition-colors">
                                    <td class="sticky left-0 z-10 border border-slate-200 <?= $rowIndex % 2 === 0 ? 'bg-slate-100' : 'bg-white' ?> px-3 py-3 text-center font-bold uppercase text-slate-800"><?= htmlspecialchars($row['partner']) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-extrabold text-slate-900"><?= (int)$row['total'] ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-extrabold text-slate-900"><?= (int)$row['ncr'] ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-extrabold text-slate-900"><?= (int)$row['regional'] ?></td>
                                    <?php foreach ($partnerMonths as $monthIndex => $month): ?>
                                        <td class="border border-slate-200 px-3 py-3 text-center font-semibold"><?= $monthIndex === 0 ? (int)$row['ncr'] : 0 ?></td>
                                        <td class="border border-slate-200 px-3 py-3 text-center font-semibold"><?= $monthIndex === 0 ? (int)$row['regional'] : 0 ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary-700 text-white font-extrabold">
                                <td class="sticky left-0 z-20 border border-primary-600 bg-primary-700 px-3 py-3 text-center">Grand Total</td>
                                <td class="border border-primary-600 px-3 py-3 text-center"><?= array_sum(array_column($partnerRows, 'total')) ?></td>
                                <td class="border border-primary-600 px-3 py-3 text-center"><?= array_sum(array_column($partnerRows, 'ncr')) ?></td>
                                <td class="border border-primary-600 px-3 py-3 text-center"><?= array_sum(array_column($partnerRows, 'regional')) ?></td>
                                <?php foreach ($partnerMonths as $monthIndex => $month): ?>
                                    <td class="border border-primary-600 px-3 py-3 text-center"><?= $monthIndex === 0 ? array_sum(array_column($partnerRows, 'ncr')) : 0 ?></td>
                                    <td class="border border-primary-600 px-3 py-3 text-center"><?= $monthIndex === 0 ? array_sum(array_column($partnerRows, 'regional')) : 0 ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const exportButton = document.getElementById('partners-report-export');
                const table = document.getElementById('partners-report-table');
                exportButton?.addEventListener('click', () => {
                    if (!table) return;
                    const csvRows = Array.from(table.querySelectorAll('tr')).map((row) => (
                        Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                    ));
                    const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'partners-report-<?= htmlspecialchars(strtolower($selectedPartnersProductKey . '-' . $partnersYear . '-' . $partnersScope)) ?>.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
                });
            });
        </script>
        <?php
        return;
    }

    if ($activeRoute === 'tat_activation') {
        $selectedTatProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $tatProductKeys = array_column($products, 'key');
        if (!in_array($selectedTatProductKey, $tatProductKeys, true)) {
            $selectedTatProductKey = 's2s';
        }

        $tatStartDate = (string)($_GET['start_date'] ?? '2026-01-01');
        $tatEndDate = (string)($_GET['end_date'] ?? '2026-01-30');
        $tatShift = [
            's2s' => 0,
            'fiberx' => 1,
            'bida' => 2,
            'sme' => 3,
        ][$selectedTatProductKey] ?? 0;
        $tatRows = [
            ['label' => 'INHOUSE PAGE', '0_days' => 20, '1_3_days' => 11, '4_7_days' => 1, '8_14_days' => 0, '15_30_days' => 0, 'over_30_days' => 0],
            ['label' => 'PARTNERS', '0_days' => 1098, '1_3_days' => 416, '4_7_days' => 54, '8_14_days' => 12, '15_30_days' => 2, 'over_30_days' => 0],
            ['label' => 'TEAM ACE', '0_days' => 122, '1_3_days' => 82, '4_7_days' => 10, '8_14_days' => 1, '15_30_days' => 0, 'over_30_days' => 0],
            ['label' => 'TEAM MARGA', '0_days' => 344, '1_3_days' => 282, '4_7_days' => 38, '8_14_days' => 5, '15_30_days' => 3, 'over_30_days' => 0],
            ['label' => 'TEAM PADDY', '0_days' => 62, '1_3_days' => 264, '4_7_days' => 26, '8_14_days' => 6, '15_30_days' => 2, 'over_30_days' => 0],
            ['label' => 'TEAM VINCENT', '0_days' => 227, '1_3_days' => 118, '4_7_days' => 10, '8_14_days' => 0, '15_30_days' => 0, 'over_30_days' => 0],
        ];
        foreach ($tatRows as &$tatRow) {
            foreach (['0_days', '1_3_days', '4_7_days', '8_14_days', '15_30_days', 'over_30_days'] as $tatKey) {
                $tatRow[$tatKey] = max(0, (int)$tatRow[$tatKey] + ($tatShift * ($tatKey === '0_days' ? 18 : 3)));
            }
            $tatRow['grand_total'] = array_sum(array_intersect_key($tatRow, array_flip(['0_days', '1_3_days', '4_7_days', '8_14_days', '15_30_days', 'over_30_days'])));
        }
        unset($tatRow);
        $tatKeys = ['0_days', '1_3_days', '4_7_days', '8_14_days', '15_30_days', 'over_30_days'];
        $tatTotals = [];
        foreach ($tatKeys as $tatKey) {
            $tatTotals[$tatKey] = array_sum(array_column($tatRows, $tatKey));
        }
        $tatGrandTotal = array_sum($tatTotals);
        ?>

        <div class="space-y-5">
            <div class="rounded-2xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-slate-50 px-4 py-3">
                <h1 class="text-lg md:text-xl font-extrabold text-primary-700 tracking-tight">TAT Activation Report</h1>
                <p class="text-xs text-slate-600">Activation turnaround monitoring by product, date range, channel, and aging bucket.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <?php foreach ($products as $product): ?>
                    <?php
                        $isTatProductActive = $selectedTatProductKey === $product['key'];
                        $tatProductUrl = '?section=tat-activation&product=' . rawurlencode((string)$product['key'])
                            . '&start_date=' . rawurlencode($tatStartDate)
                            . '&end_date=' . rawurlencode($tatEndDate);
                    ?>
                    <a href="<?= htmlspecialchars($tatProductUrl) ?>"
                       class="group rounded-xl border p-3 text-center transition-all duration-200 <?= $isTatProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="w-full h-20 rounded-lg border <?= $isTatProductActive ? 'border-primary-100 bg-white' : 'border-slate-100 bg-white' ?> p-2 mb-2 flex items-center justify-center overflow-hidden dashboard-product-logo-frame <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo-frame--light' : '' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-w-full max-h-full object-contain dashboard-product-logo">
                        </div>
                        <div class="flex items-center justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-bold <?= $isTatProductActive ? 'text-primary-700' : 'text-slate-800 group-hover:text-primary-700' ?> truncate"><?= htmlspecialchars($product['name']) ?></p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?> TAT</p>
                            </div>
                            <?php if ($isTatProductActive): ?>
                                <span class="shrink-0 rounded-full bg-primary-600 px-2 py-1 text-[10px] font-bold text-white">Active</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto_auto] gap-3">
                    <input type="hidden" name="section" value="tat-activation">
                    <input type="hidden" name="product" value="<?= htmlspecialchars($selectedTatProductKey) ?>">
                    <label class="flex flex-col gap-1 text-xs font-bold text-slate-600">
                        Start Date
                        <input type="date" name="start_date" value="<?= htmlspecialchars($tatStartDate) ?>" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                    </label>
                    <label class="flex flex-col gap-1 text-xs font-bold text-slate-600">
                        End Date
                        <input type="date" name="end_date" value="<?= htmlspecialchars($tatEndDate) ?>" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400">
                    </label>
                    <button type="submit" class="inline-flex h-10 self-end items-center justify-center rounded-lg bg-primary-600 px-8 text-xs font-bold text-white hover:bg-primary-700 transition-colors">Filter</button>
                    <a href="?section=tat-activation&product=<?= htmlspecialchars(rawurlencode($selectedTatProductKey)) ?>" class="inline-flex h-10 self-end items-center justify-center rounded-lg border border-slate-200 bg-white px-8 text-xs font-bold text-slate-600 hover:border-primary-300 hover:bg-cyan-50 hover:text-primary-700 transition-colors">Reset</a>
                </form>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <?php
                        $tatKpis = [
                            ['label' => 'Same Day (0 Days)', 'value' => $tatTotals['0_days'], 'tone' => 'border-l-4 border-green-500'],
                            ['label' => '1-3 Days', 'value' => $tatTotals['1_3_days'], 'tone' => 'border-l-4 border-yellow-400'],
                            ['label' => '4-7 Days', 'value' => $tatTotals['4_7_days'], 'tone' => 'border-l-4 border-orange-400'],
                            ['label' => 'Over 7 Days', 'value' => $tatTotals['8_14_days'] + $tatTotals['15_30_days'] + $tatTotals['over_30_days'], 'tone' => 'border-l-4 border-red-500'],
                        ];
                    ?>
                    <?php foreach ($tatKpis as $kpi): ?>
                        <?php $percent = $tatGrandTotal > 0 ? number_format(((int)$kpi['value'] / $tatGrandTotal) * 100, 2) : '0.00'; ?>
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm <?= htmlspecialchars($kpi['tone']) ?>">
                            <p class="text-[10px] uppercase tracking-wide text-slate-500 font-extrabold"><?= htmlspecialchars($kpi['label']) ?></p>
                            <p class="mt-2 text-2xl font-extrabold text-slate-900"><?= number_format((int)$kpi['value']) ?></p>
                            <p class="text-xs text-slate-500"><?= htmlspecialchars($percent) ?>% of total</p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="rounded-xl border border-amber-100 bg-amber-50/70 p-3">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-3">
                        <h2 class="text-sm font-extrabold text-slate-900">Detailed TAT Breakdown</h2>
                        <div class="flex gap-2">
                            <button type="button" id="tat-copy-table" class="inline-flex h-8 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-[11px] font-bold text-slate-600 hover:bg-slate-50">Copy Table</button>
                            <button type="button" id="tat-export-csv" class="inline-flex h-8 items-center justify-center rounded-lg bg-primary-600 px-3 text-[11px] font-bold text-white hover:bg-primary-700">Export CSV</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                        <table id="tat-activation-table" class="min-w-[980px] w-full border-collapse text-[11px]">
                            <thead>
                                <tr class="bg-amber-50 text-slate-700">
                                    <th class="border border-slate-200 px-3 py-3 text-left">Row Labels</th>
                                    <th class="border border-slate-200 px-3 py-3 text-center">0 Days</th>
                                    <th class="border border-slate-200 px-3 py-3 text-center">1-3 Days</th>
                                    <th class="border border-slate-200 px-3 py-3 text-center">4-7 Days</th>
                                    <th class="border border-slate-200 px-3 py-3 text-center">8-14 Days</th>
                                    <th class="border border-slate-200 px-3 py-3 text-center">15-30 Days</th>
                                    <th class="border border-slate-200 px-3 py-3 text-center">&gt;30 Days</th>
                                    <th class="border border-slate-200 px-3 py-3 text-center">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tatRows as $row): ?>
                                    <tr class="bg-white text-slate-700 hover:bg-amber-50/40 transition-colors">
                                        <td class="border border-slate-200 px-3 py-3 font-bold uppercase text-slate-800"><?= htmlspecialchars($row['label']) ?></td>
                                        <?php foreach ($tatKeys as $tatKey): ?>
                                            <td class="border border-slate-200 px-3 py-3 text-center font-semibold"><?= (int)$row[$tatKey] ?></td>
                                        <?php endforeach; ?>
                                        <td class="border border-slate-200 bg-amber-50 px-3 py-3 text-center font-extrabold text-slate-900"><?= (int)$row['grand_total'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-amber-100 text-slate-900 font-extrabold">
                                    <td class="border border-amber-200 px-3 py-3">Grand Total</td>
                                    <?php foreach ($tatKeys as $tatKey): ?>
                                        <td class="border border-amber-200 px-3 py-3 text-center"><?= (int)$tatTotals[$tatKey] ?></td>
                                    <?php endforeach; ?>
                                    <td class="border border-amber-200 px-3 py-3 text-center"><?= (int)$tatGrandTotal ?></td>
                                </tr>
                                <tr class="bg-white text-slate-700 font-bold">
                                    <td class="border border-slate-200 px-3 py-3">Percentage</td>
                                    <?php foreach ($tatKeys as $tatKey): ?>
                                        <td class="border border-slate-200 px-3 py-3 text-center"><?= $tatGrandTotal > 0 ? number_format(($tatTotals[$tatKey] / $tatGrandTotal) * 100, 2) : '0.00' ?>%</td>
                                    <?php endforeach; ?>
                                    <td class="border border-slate-200 px-3 py-3 text-center">100.00%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="mt-3 text-[11px] text-slate-500">* 0 Days includes same-day activation and pre-input activations.</p>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const table = document.getElementById('tat-activation-table');
                const tableRows = () => Array.from(table?.querySelectorAll('tr') || []).map((row) => (
                    Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                ));

                document.getElementById('tat-copy-table')?.addEventListener('click', async () => {
                    const tsv = tableRows().map((row) => row.join('\t')).join('\n');
                    await navigator.clipboard?.writeText(tsv);
                });

                document.getElementById('tat-export-csv')?.addEventListener('click', () => {
                    const csv = tableRows().map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'tat-activation-<?= htmlspecialchars(strtolower($selectedTatProductKey . '-' . $tatStartDate . '-' . $tatEndDate)) ?>.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
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

    <?php
        $dashboardMonthlyProductKey = strtolower(trim((string)($_GET['product'] ?? 'fiberx')));
        $dashboardMonthlyProductKeys = array_column($products, 'key');
        if (!in_array($dashboardMonthlyProductKey, $dashboardMonthlyProductKeys, true)) {
            $dashboardMonthlyProductKey = 'fiberx';
        }
        $dashboardMonthlyYear = (string)($_GET['year'] ?? '');
    ?>

    <div class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <?php foreach ($products as $product): ?>
                <?php $summaryUrl = '?section=summary-report&product=' . rawurlencode((string)$product['key']); ?>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="flex min-h-[118px] items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-500"><?= htmlspecialchars($product['name']) ?></p>
                            <p class="mt-1 text-2xl font-extrabold leading-tight text-slate-950"><?= htmlspecialchars($product['value']) ?></p>
                            <p class="mt-3 text-sm text-slate-500">
                                <span class="<?= $product['delta_positive'] ? 'text-emerald-600' : 'text-red-600' ?> font-semibold"><?= htmlspecialchars($product['delta']) ?></span>
                                <span>vs last month</span>
                            </p>
                        </div>
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-100 bg-white p-1.5 shadow-sm">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                        </div>
                    </div>
                    <a href="<?= htmlspecialchars($summaryUrl) ?>" class="group flex h-12 items-center justify-between border-t border-slate-100 px-5 text-sm font-medium text-slate-950 transition-colors hover:bg-slate-50" aria-label="View <?= htmlspecialchars($product['name']) ?> summary report details">
                        <span>View Details</span>
                        <svg class="h-5 w-5 text-slate-500 transition-transform group-hover:translate-x-1 group-hover:text-slate-800" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-3 sm:flex-row sm:items-center sm:justify-between" style="min-height: 74px;">
                    <label class="relative w-full shrink-0 sm:w-auto" style="max-width: 100%; width: 278px;" aria-label="Select monthly activation product">
                        <select id="head-manager-monthly-product" class="block h-12 w-full rounded-lg border border-slate-200 bg-white px-4 pr-10 text-sm font-medium text-slate-950 shadow-sm outline-none transition-colors focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15" style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background: #fff;">
                            <?php foreach ($products as $product): ?>
                                <option value="<?= htmlspecialchars($product['key']) ?>" <?= $dashboardMonthlyProductKey === $product['key'] ? 'selected' : '' ?>><?= htmlspecialchars($product['name']) ?> Monthly Activations</option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="pointer-events-none absolute right-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-950" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <label class="relative w-full shrink-0 sm:w-auto" style="max-width: calc(100% - 14px); width: 198px; margin-right: 14px;" aria-label="Select monthly activation year">
                        <select id="head-manager-monthly-year" class="block h-12 w-full rounded-full border border-slate-200 bg-white px-5 pr-10 text-sm font-medium text-slate-950 shadow-sm outline-none transition-colors focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15" style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background: #fff;">
                            <option value="" <?= $dashboardMonthlyYear === '' ? 'selected' : '' ?>>Please select year</option>
                            <?php foreach (['2026', '2025', '2024'] as $year): ?>
                                <option value="<?= htmlspecialchars($year) ?>" <?= $dashboardMonthlyYear === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="pointer-events-none absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-950" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </label>
                </div>
                <div class="px-5 py-6">
                    <div style="height: 324px; position: relative;">
                        <canvas id="head-manager-monthly-chart" style="display: block; height: 100%; width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-950">Daily Sales Activation</h2>
                    <div class="inline-flex h-10 items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-medium text-slate-900 shadow-sm">
                        Daily
                    </div>
                </div>
                <div class="border-t border-slate-100 px-5 py-6">
                    <div class="mx-auto h-64 max-w-[270px]">
                        <canvas id="head-manager-daily-chart"></canvas>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-x-8 gap-y-3 text-sm sm:grid-cols-2">
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 rounded-full bg-[#5b5cf6]"></span>S2S Activation</span>
                            <strong class="font-semibold text-slate-950">35.2%</strong>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 rounded-full bg-[#08c956]"></span>SME</span>
                            <strong class="font-semibold text-slate-950">30%</strong>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 rounded-full bg-[#f4b000]"></span>FiberX</span>
                            <strong class="font-semibold text-slate-950">9.3%</strong>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 rounded-full bg-[#8b4cf6]"></span>Bida</span>
                            <strong class="font-semibold text-slate-950">7.2%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') return;

            const monthlyCanvas = document.getElementById('head-manager-monthly-chart');
            if (monthlyCanvas) {
                const monthlyProductSelect = document.getElementById('head-manager-monthly-product');
                const monthlyYearSelect = document.getElementById('head-manager-monthly-year');
                const monthlyProducts = {
                    s2s: { label: 'Surf2Sawa Monthly Activations', color: '#ef4444', fill: 'rgba(239, 68, 68, .24)', data: [118, 127, 142, 154, 169, 181, 196, 210, 225, 242, 258, 276] },
                    fiberx: { label: 'FiberX Monthly Activations', color: '#4f46e5', fill: 'rgba(79, 70, 229, .24)', data: [96, 98, 105, 118, 137, 163, 188, 209, 226, 241, 257, 274] },
                    bida: { label: 'Bida Monthly Activations', color: '#f97316', fill: 'rgba(249, 115, 22, .24)', data: [82, 88, 94, 101, 113, 126, 139, 151, 164, 177, 189, 203] },
                    sme: { label: 'SME Solutions Monthly Activations', color: '#10b981', fill: 'rgba(16, 185, 129, .24)', data: [64, 69, 75, 82, 91, 104, 116, 129, 141, 154, 168, 182] },
                };
                const buildMonthlyDataset = () => {
                    const selectedKey = monthlyProductSelect?.value || 'fiberx';
                    const source = monthlyProducts[selectedKey] || monthlyProducts.fiberx;
                    const gradient = monthlyCanvas.getContext('2d').createLinearGradient(0, 0, 0, monthlyCanvas.height || 350);
                    gradient.addColorStop(0, source.fill);
                    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

                    return {
                        label: source.label,
                        data: source.data,
                        borderColor: source.color,
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderWidth: 2,
                        tension: 0.42,
                        fill: true,
                    };
                };

                const monthlyChart = new Chart(monthlyCanvas, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [buildMonthlyDataset()],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { top: 0, right: 14, bottom: 4, left: 4 },
                        },
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
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: '#737373', font: { size: 14 }, padding: 18 },
                            },
                            y: {
                                beginAtZero: true,
                                suggestedMax: 300,
                                border: { display: false },
                                grid: { color: 'rgba(148, 163, 184, .28)', borderDash: [6, 8], drawTicks: false },
                                ticks: { color: '#737373', font: { size: 14 }, stepSize: 50, padding: 14 },
                            },
                        },
                    },
                });

                monthlyProductSelect?.addEventListener('change', () => {
                    monthlyChart.data.datasets = [buildMonthlyDataset()];
                    monthlyChart.update();
                });

                monthlyYearSelect?.addEventListener('change', () => {
                    monthlyChart.update();
                });
            }

            const dailyCanvas = document.getElementById('head-manager-daily-chart');
            if (dailyCanvas) {
                const centerTextPlugin = {
                    id: 'dailyCenterText',
                    afterDraw(chart) {
                        const { ctx, chartArea } = chart;
                        const x = (chartArea.left + chartArea.right) / 2;
                        const y = (chartArea.top + chartArea.bottom) / 2;

                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = '#020617';
                        ctx.font = '700 34px Onest, sans-serif';
                        ctx.fillText('3.5K', x, y - 8);
                        ctx.fillStyle = '#737373';
                        ctx.font = '400 14px Onest, sans-serif';
                        ctx.fillText('This month', x, y + 28);
                        ctx.restore();
                    },
                };

                new Chart(dailyCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['S2S Activation', 'SME', 'FiberX', 'Bida', 'Unassigned'],
                        datasets: [{
                            data: [35.2, 30, 9.3, 7.2, 18.3],
                            backgroundColor: ['#5b5cf6', '#08c956', '#f4b000', '#8b4cf6', '#e5e5e5'],
                            borderColor: '#ffffff',
                            borderWidth: 5,
                            hoverOffset: 5,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '58%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#f8fafc',
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: true,
                            },
                        },
                    },
                    plugins: [centerTextPlugin],
                });
            }
        });
    </script>
    <?php
    return;
}
?>

<?php if (in_array($dashboardSlug, ['asm-manager', 'asm-area-sales-manager'], true)): ?>
    <?php
        $asmProducts = [
            ['key' => 's2s', 'name' => 'Surf2Sawa', 'short' => 'S2S', 'value' => '123,452', 'delta' => '+2343', 'delta_positive' => true, 'image' => 'images/s2s.jpg', 'badge' => 'bg-rose-100 text-rose-600', 'color' => '#ef4444'],
            ['key' => 'fiberx', 'name' => 'FiberX', 'short' => 'FIBERX', 'value' => '20,535', 'delta' => '+1,340', 'delta_positive' => true, 'image' => 'images/fiberx.png', 'badge' => 'bg-indigo-100 text-indigo-600', 'color' => '#5b5cf6'],
            ['key' => 'bida', 'name' => 'Bida', 'short' => 'BIDA', 'value' => '7,637', 'delta' => '-116', 'delta_positive' => false, 'image' => 'images/bida.jpg', 'badge' => 'bg-orange-100 text-orange-600', 'color' => '#ff6b35'],
            ['key' => 'sme', 'name' => 'SME Solutions', 'short' => 'SME', 'value' => '12,423', 'delta' => '+4.2', 'delta_positive' => true, 'image' => 'images/sme.png', 'badge' => 'bg-emerald-100 text-emerald-600', 'color' => '#08c956'],
        ];
        $asmSelectedProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
        $asmSelectedProduct = $asmProducts[0];
        foreach ($asmProducts as $product) {
            if ($product['key'] === $asmSelectedProductKey) {
                $asmSelectedProduct = $product;
                break;
            }
        }
        $asmAssigningRows = [
            ['TEAM MARGA_PINCAS KARRISA', 'TAGUIG', 'PINAGSAMA'],
            ['TEAM MARGA_PINCAS KARRISA', 'TAGUIG', 'LIGID TIPAS'],
            ['TEAM MARGA_PINCAS KARRISA', 'TAGUIG', 'IBAYO TIPAS'],
            ['TEAM MARGA_PINCAS KARRISA', 'TAGUIG', 'NAPINDAN'],
            ['TEAM MARGA_MARICEL DOLETE', 'RODRIGUEZ', 'SAN JOSE'],
            ['TEAM MARGA_JESSICA REGACHUELO', 'QUEZON CITY', 'COMMONWEALTH'],
        ];
        $asmAssigningSearch = trim((string)($_GET['search'] ?? ''));
        if ($asmAssigningSearch !== '') {
            $asmAssigningRows = array_values(array_filter($asmAssigningRows, static function (array $row) use ($asmAssigningSearch): bool {
                return stripos(implode(' ', $row), $asmAssigningSearch) !== false;
            }));
        }
        $asmSubAgentReportType = strtolower(trim((string)($_GET['type'] ?? 'summary')));
        if (!in_array($asmSubAgentReportType, ['summary', 'inhouse', 'partners'], true)) {
            $asmSubAgentReportType = 'summary';
        }
        $asmSubAgentRows = [
            ['HENRY DOLIENTE', 'PSM-225-2', 1, '0.05', '1.36', [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], false],
            ['TEAM MARGA_MARICEL DOLETE', '', 1, '0.05', '1.36', [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], true],
            ['COLE, ARNOLD', 'PSM-236-4', 23, '1.05', '31.36', [0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 2, 2, 0, 3, 0, 1], false],
            ['TEAM MARGA_JONNALYN COLE', '', 23, '1.05', '31.36', [0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 2, 2, 0, 3, 0, 1], true],
            ['PINCAS KIM', 'PSM-226-2', 336, '15.27', '458.18', [0, 8, 7, 21, 12, 7, 12, 13, 18, 18, 10, 12, 13, 8, 13, 17, 15], false],
            ['TEAM MARGA_PINCAS KARRISA', '', 336, '15.27', '458.18', [0, 8, 7, 21, 12, 7, 12, 13, 18, 18, 10, 12, 13, 8, 13, 17, 15], true],
            ['RENELYN CONTANTE', 'PSM-232-2', 91, '4.14', '124.09', [0, 4, 2, 2, 4, 0, 1, 3, 3, 1, 8, 2, 3, 4, 3, 0, 2], false],
            ['TEAM MARGA_JESSICA REGACHUELO', '', 91, '4.14', '124.09', [0, 4, 2, 2, 4, 0, 1, 3, 3, 1, 8, 2, 3, 4, 3, 0, 2], true],
            ['CRISELLE MEDIA', 'PSM-230-2', 32, '1.45', '43.64', [0, 4, 3, 2, 5, 1, 1, 1, 3, 0, 0, 1, 0, 1, 0, 0, 1], false],
            ['TEAM MARGA_CHRISTOPHER NENA', '', 32, '1.45', '43.64', [0, 4, 3, 2, 5, 1, 1, 1, 3, 0, 0, 1, 0, 1, 0, 0, 1], true],
            ['ALLEN DIAZ', 'PSM-216-8', 88, '4.00', '120.00', [0, 2, 2, 5, 6, 3, 1, 3, 4, 5, 5, 0, 0, 1, 1, 2, 0], false],
            ['TEAM MARGA_JAMES DIAZ', '', 88, '4.00', '120.00', [0, 2, 2, 5, 6, 3, 1, 3, 4, 5, 5, 0, 0, 1, 1, 2, 0], true],
        ];
    ?>

    <?php if (($activeRoute ?? 'dashboard') === 'dashboard'): ?>
        <div class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <?php foreach ($asmProducts as $product): ?>
                <?php $detailsUrl = '?section=sales-status&product=' . rawurlencode((string)$product['key']); ?>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="flex min-h-[118px] items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-500"><?= htmlspecialchars($product['name']) ?></p>
                            <p class="mt-1 text-2xl font-extrabold leading-tight text-slate-950"><?= htmlspecialchars($product['value']) ?></p>
                            <p class="mt-2 text-xs text-slate-500">
                                <span class="<?= $product['delta_positive'] ? 'text-emerald-600' : 'text-red-600' ?> font-semibold"><?= htmlspecialchars($product['delta']) ?></span>
                                this month
                            </p>
                        </div>
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-100 bg-white p-1.5 shadow-sm">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                        </div>
                    </div>
                    <a href="<?= htmlspecialchars($detailsUrl) ?>" class="group flex h-12 items-center justify-between border-t border-slate-100 px-5 text-sm font-medium text-slate-950 transition-colors hover:bg-slate-50" aria-label="View <?= htmlspecialchars($product['name']) ?> sales status details">
                        <span>View Details</span>
                        <svg class="h-5 w-5 text-slate-500 transition-transform group-hover:translate-x-1 group-hover:text-slate-800" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 items-stretch gap-4 lg:grid-cols-2 xl:grid-cols-3">
            <div class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2" style="min-height: clamp(440px, 52vh, 560px);">
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center">
                    <h2 class="inline-block whitespace-nowrap text-base font-semibold text-slate-950">Team Leader Monitoring</h2>
                    <div class="relative ml-auto flex w-full items-center justify-end gap-2 sm:w-auto">
                        <button type="button" id="asm-team-date-toggle" class="inline-flex h-10 shrink-0 items-center justify-center gap-3 rounded-full border border-slate-200 bg-white px-4 text-sm font-medium text-slate-900 shadow-sm" aria-expanded="false" aria-controls="asm-team-date-panel">
                            <span id="asm-team-date-label">Jan 10, 2026 - Feb 10, 2026</span>
                            <svg class="h-4 w-4 text-slate-950" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button type="button" id="asm-team-menu-toggle" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-950 shadow-sm" aria-label="More team leader monitoring options" aria-expanded="false" aria-controls="asm-team-menu">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 12h.01M18 12h.01M6 12h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div id="asm-team-date-panel" class="absolute right-0 top-12 z-20 hidden max-w-full rounded-xl border border-slate-200 bg-white p-3 shadow-lg" style="width: min(22rem, calc(100vw - 2rem));">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-500">
                                    From
                                    <input id="asm-team-date-from" type="date" value="2026-01-10" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-900 outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15">
                                </label>
                                <label class="flex flex-col gap-1 text-xs font-semibold text-slate-500">
                                    To
                                    <input id="asm-team-date-to" type="date" value="2026-02-10" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-900 outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15">
                                </label>
                            </div>
                            <div class="mt-3 flex justify-end gap-2">
                                <button type="button" id="asm-team-date-cancel" class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                                <button type="button" id="asm-team-date-apply" class="inline-flex h-9 items-center rounded-lg bg-primary-600 px-4 text-xs font-semibold text-white hover:bg-primary-700">Apply</button>
                            </div>
                        </div>
                        <div id="asm-team-menu" class="absolute right-0 top-12 z-20 hidden w-44 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 shadow-lg">
                            <button type="button" id="asm-team-reset" class="block w-full px-4 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-50">Reset range</button>
                            <button type="button" id="asm-team-export" class="block w-full px-4 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-50">Download CSV</button>
                        </div>
                    </div>
                </div>
                <div class="flex-1 border-t border-slate-100 px-5 pb-6 pt-12 sm:pt-14">
                    <div class="h-full" style="min-height: 330px;">
                        <canvas id="asm-team-leader-monitoring-chart"></canvas>
                    </div>
                </div>
            </div>

            <div class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" style="min-height: clamp(440px, 52vh, 560px);">
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-base font-semibold text-slate-950">Daily Sales Activation</h2>
                    <div class="inline-flex h-10 w-fit items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-medium text-slate-900 shadow-sm">
                        Daily
                    </div>
                </div>
                <div class="flex-1 border-t border-slate-100 px-5 py-6">
                    <div class="mx-auto h-56 w-full max-w-[250px] sm:h-64 sm:max-w-[270px]">
                        <canvas id="asm-manager-daily-sales-chart"></canvas>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-x-8 gap-y-3 text-sm sm:grid-cols-2">
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex min-w-0 items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#5b5cf6]"></span><span class="truncate">S2S Activation</span></span>
                            <strong class="shrink-0 font-semibold text-slate-950">35.2%</strong>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex min-w-0 items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#08c956]"></span><span class="truncate">SME</span></span>
                            <strong class="shrink-0 font-semibold text-slate-950">30%</strong>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex min-w-0 items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#f4b000]"></span><span class="truncate">FiberX</span></span>
                            <strong class="shrink-0 font-semibold text-slate-950">9.3%</strong>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex min-w-0 items-center gap-2 text-slate-500"><span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#8b4cf6]"></span><span class="truncate">Bida</span></span>
                            <strong class="shrink-0 font-semibold text-slate-950">7.2%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                if (typeof Chart === 'undefined') return;

                const teamLeaderCanvas = document.getElementById('asm-team-leader-monitoring-chart');
                if (teamLeaderCanvas) {
                    const teamLeaderLabels = ['Diaz', 'Mendoza', 'Olaco', 'Olaco', 'Mendoza', 'Mendoza', 'Mendoza', 'Mendoza', 'Manila'];
                    const teamLeaderRanges = {
                        default: {
                            label: 'Jan 10, 2026 - Feb 10, 2026',
                            from: '2026-01-10',
                            to: '2026-02-10',
                            data: [4.9, 4, 3.1, 2.75, 2.3, 1.65, 4.9, 4, 4.3],
                        },
                        january: {
                            label: 'Jan 1, 2026 - Jan 31, 2026',
                            from: '2026-01-01',
                            to: '2026-01-31',
                            data: [4.4, 3.7, 2.8, 3.2, 2.1, 1.9, 4.6, 3.6, 4.1],
                        },
                        february: {
                            label: 'Feb 1, 2026 - Feb 28, 2026',
                            from: '2026-02-01',
                            to: '2026-02-28',
                            data: [4.8, 4.2, 3.4, 2.5, 2.6, 1.7, 4.9, 4.4, 3.8],
                        },
                    };
                    const teamDateToggle = document.getElementById('asm-team-date-toggle');
                    const teamDatePanel = document.getElementById('asm-team-date-panel');
                    const teamDateLabel = document.getElementById('asm-team-date-label');
                    const teamDateFrom = document.getElementById('asm-team-date-from');
                    const teamDateTo = document.getElementById('asm-team-date-to');
                    const teamDateApply = document.getElementById('asm-team-date-apply');
                    const teamDateCancel = document.getElementById('asm-team-date-cancel');
                    const teamMenuToggle = document.getElementById('asm-team-menu-toggle');
                    const teamMenu = document.getElementById('asm-team-menu');
                    const teamReset = document.getElementById('asm-team-reset');
                    const teamExport = document.getElementById('asm-team-export');
                    const formatRangeLabel = (from, to) => {
                        const options = { month: 'short', day: 'numeric', year: 'numeric' };
                        const fromDate = new Date(`${from}T00:00:00`);
                        const toDate = new Date(`${to}T00:00:00`);
                        if (Number.isNaN(fromDate.getTime()) || Number.isNaN(toDate.getTime())) {
                            return teamLeaderRanges.default.label;
                        }
                        return `${fromDate.toLocaleDateString('en-US', options)} - ${toDate.toLocaleDateString('en-US', options)}`;
                    };
                    const pickRangeData = (from, to) => {
                        if (from === teamLeaderRanges.january.from && to === teamLeaderRanges.january.to) return teamLeaderRanges.january.data;
                        if (from === teamLeaderRanges.february.from && to === teamLeaderRanges.february.to) return teamLeaderRanges.february.data;
                        return teamLeaderRanges.default.data;
                    };
                    const closeTeamPanels = () => {
                        teamDatePanel?.classList.add('hidden');
                        teamMenu?.classList.add('hidden');
                        teamDateToggle?.setAttribute('aria-expanded', 'false');
                        teamMenuToggle?.setAttribute('aria-expanded', 'false');
                    };

                    const teamLeaderChart = new Chart(teamLeaderCanvas, {
                        type: 'bar',
                        data: {
                            labels: teamLeaderLabels,
                            datasets: [{
                                data: teamLeaderRanges.default.data,
                                backgroundColor: ['#1f5cf5', '#6f7cff', '#1f5cf5', '#6f7cff', '#1f5cf5', '#6f7cff', '#1f5cf5', '#6f7cff', '#6f7cff'],
                                borderRadius: 10,
                                borderSkipped: false,
                                maxBarThickness: 52,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#ffffff',
                                    titleColor: '#737373',
                                    bodyColor: '#020617',
                                    borderColor: '#e5e7eb',
                                    borderWidth: 1,
                                    padding: 12,
                                    cornerRadius: 12,
                                    displayColors: true,
                                },
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    border: { display: false },
                                    ticks: { color: '#737373', font: { size: 12 }, padding: 14, maxRotation: 0 },
                                },
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: 5,
                                    border: { display: false },
                                    grid: { color: 'rgba(148, 163, 184, .28)', borderDash: [6, 8], drawTicks: false },
                                    ticks: { color: '#737373', font: { size: 12 }, stepSize: 1, padding: 10 },
                                },
                            },
                        },
                    });

                    teamDateToggle?.addEventListener('click', () => {
                        const willOpen = teamDatePanel?.classList.contains('hidden');
                        closeTeamPanels();
                        if (willOpen) {
                            teamDatePanel?.classList.remove('hidden');
                            teamDateToggle.setAttribute('aria-expanded', 'true');
                        }
                    });

                    teamMenuToggle?.addEventListener('click', () => {
                        const willOpen = teamMenu?.classList.contains('hidden');
                        closeTeamPanels();
                        if (willOpen) {
                            teamMenu?.classList.remove('hidden');
                            teamMenuToggle.setAttribute('aria-expanded', 'true');
                        }
                    });

                    teamDateCancel?.addEventListener('click', closeTeamPanels);

                    teamDateApply?.addEventListener('click', () => {
                        const from = teamDateFrom?.value || teamLeaderRanges.default.from;
                        const to = teamDateTo?.value || teamLeaderRanges.default.to;
                        teamLeaderChart.data.datasets[0].data = pickRangeData(from, to);
                        if (teamDateLabel) teamDateLabel.textContent = formatRangeLabel(from, to);
                        teamLeaderChart.update();
                        closeTeamPanels();
                    });

                    teamReset?.addEventListener('click', () => {
                        if (teamDateFrom) teamDateFrom.value = teamLeaderRanges.default.from;
                        if (teamDateTo) teamDateTo.value = teamLeaderRanges.default.to;
                        if (teamDateLabel) teamDateLabel.textContent = teamLeaderRanges.default.label;
                        teamLeaderChart.data.datasets[0].data = teamLeaderRanges.default.data;
                        teamLeaderChart.update();
                        closeTeamPanels();
                    });

                    teamExport?.addEventListener('click', () => {
                        const rows = [['Team Leader', 'Activation'], ...teamLeaderLabels.map((label, index) => [label, teamLeaderChart.data.datasets[0].data[index]])];
                        const csv = rows.map((row) => row.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'team-leader-monitoring.csv';
                        link.click();
                        URL.revokeObjectURL(link.href);
                        closeTeamPanels();
                    });

                    document.addEventListener('click', (event) => {
                        const target = event.target;
                        if (!(target instanceof Node)) return;
                        if (!teamDatePanel?.contains(target) && !teamDateToggle?.contains(target) && !teamMenu?.contains(target) && !teamMenuToggle?.contains(target)) {
                            closeTeamPanels();
                        }
                    });
                }

                const dailyCanvas = document.getElementById('asm-manager-daily-sales-chart');
                if (!dailyCanvas) return;

                const centerTextPlugin = {
                    id: 'asmManagerDailyCenterText',
                    afterDraw(chart) {
                        const { ctx, chartArea } = chart;
                        const x = (chartArea.left + chartArea.right) / 2;
                        const y = (chartArea.top + chartArea.bottom) / 2;

                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = '#020617';
                        ctx.font = '700 34px Onest, sans-serif';
                        ctx.fillText('3.5K', x, y - 8);
                        ctx.fillStyle = '#737373';
                        ctx.font = '400 14px Onest, sans-serif';
                        ctx.fillText('This month', x, y + 28);
                        ctx.restore();
                    },
                };

                new Chart(dailyCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['S2S Activation', 'SME', 'FiberX', 'Bida', 'Unassigned'],
                        datasets: [{
                            data: [35.2, 30, 9.3, 7.2, 18.3],
                            backgroundColor: ['#5b5cf6', '#08c956', '#f4b000', '#8b4cf6', '#e5e5e5'],
                            borderColor: '#ffffff',
                            borderWidth: 5,
                            hoverOffset: 5,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '58%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#f8fafc',
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: true,
                            },
                        },
                    },
                    plugins: [centerTextPlugin],
                });
            });
        </script>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'productivity_per_area'): ?>
        <?php
            $asmAreaProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
            $asmAreaProductKeys = array_column($asmProducts, 'key');
            if (!in_array($asmAreaProductKey, $asmAreaProductKeys, true)) {
                $asmAreaProductKey = 's2s';
            }
            $asmAreaMonth = (string)($_GET['month'] ?? 'January');
            $asmAreaYear = (string)($_GET['year'] ?? '2026');
            $asmAreaRegion = strtolower(trim((string)($_GET['region'] ?? 'ncrr')));
            $asmAreaMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $asmAreaYears = ['2026', '2025', '2024'];
            $asmAreaRegions = [
                'nclz' => 'NCLZ',
                'ncrr' => 'NCRR',
                'region-iv-a' => 'REGION IV-A',
                'slb' => 'SLB',
            ];
            if (!in_array($asmAreaMonth, $asmAreaMonths, true)) {
                $asmAreaMonth = 'January';
            }
            if (!in_array($asmAreaYear, $asmAreaYears, true)) {
                $asmAreaYear = '2026';
            }
            if (!isset($asmAreaRegions[$asmAreaRegion])) {
                $asmAreaRegion = 'ncrr';
            }
            $asmAreaProduct = $asmProducts[0];
            foreach ($asmProducts as $product) {
                if ($product['key'] === $asmAreaProductKey) {
                    $asmAreaProduct = $product;
                    break;
                }
            }
            $asmAreaChartRows = [
                ['TAGUIG', 484],
                ['PANGASINAN', 121],
                ['MUNTINLUPA', 113],
                ['LAS PINAS', 50],
                ['PASIG', 38],
                ['MANILA', 36],
                ['QUEZON CITY', 29],
                ['PASAY', 27],
                ['NORTH CALOOCAN', 14],
                ['ANTIPOLO', 12],
                ['OTHERS', 54],
            ];
            $asmAreaProductOffset = [
                's2s' => 0,
                'fiberx' => -42,
                'bida' => -68,
                'sme' => -84,
            ][$asmAreaProductKey] ?? 0;
            $asmAreaRegionOffset = [
                'nclz' => -26,
                'ncrr' => 0,
                'region-iv-a' => -58,
                'slb' => -74,
            ][$asmAreaRegion] ?? 0;
            foreach ($asmAreaChartRows as &$asmAreaChartRow) {
                $asmAreaChartRow[1] = max(6, (int)$asmAreaChartRow[1] + $asmAreaProductOffset + $asmAreaRegionOffset);
            }
            unset($asmAreaChartRow);
            $asmAreaTotal = array_sum(array_column($asmAreaChartRows, 1));
            $asmAreaMunicipalityCount = 18;
            $asmAreaAverage = (int)round($asmAreaTotal / $asmAreaMunicipalityCount);
        ?>

        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-3" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
                    <?php foreach ($asmProducts as $product): ?>
                        <?php
                            $isAreaProductActive = $asmAreaProductKey === $product['key'];
                            $areaProductUrl = '?section=productivity-per-area&product=' . rawurlencode((string)$product['key'])
                                . '&month=' . rawurlencode($asmAreaMonth)
                                . '&year=' . rawurlencode($asmAreaYear)
                                . '&region=' . rawurlencode($asmAreaRegion);
                        ?>
                        <a href="<?= htmlspecialchars($areaProductUrl) ?>"
                           class="group rounded-xl border p-4 transition-all duration-200 <?= $isAreaProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="flex h-24 items-center justify-center rounded-lg border bg-white p-5 <?= $isAreaProductActive ? 'border-primary-100' : 'border-slate-100' ?>">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-medium text-primary-700"><?= htmlspecialchars($asmAreaRegions[$asmAreaRegion]) ?> - <?= htmlspecialchars($asmAreaMonth) ?> <?= htmlspecialchars($asmAreaYear) ?> (<?= (int)$asmAreaMunicipalityCount ?> municipalities)</h2>
                    <div class="mt-4 flex flex-wrap items-center gap-3" style="justify-content: space-between;">
                        <div class="flex flex-wrap items-center gap-2">
                            <?php foreach ($asmAreaRegions as $regionKey => $regionLabel): ?>
                                <?php
                                    $regionUrl = '?section=productivity-per-area&product=' . rawurlencode($asmAreaProductKey)
                                        . '&month=' . rawurlencode($asmAreaMonth)
                                        . '&year=' . rawurlencode($asmAreaYear)
                                        . '&region=' . rawurlencode($regionKey);
                                ?>
                                <a href="<?= htmlspecialchars($regionUrl) ?>"
                                   class="inline-flex h-9 items-center justify-center rounded-lg border px-4 text-xs font-medium transition-colors <?= $asmAreaRegion === $regionKey ? 'border-primary-500 bg-primary-600 text-white shadow-sm' : 'border-primary-200 bg-white text-primary-700 hover:bg-cyan-50' ?>">
                                    <?= htmlspecialchars($regionLabel) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="section" value="productivity-per-area">
                            <input type="hidden" name="product" value="<?= htmlspecialchars($asmAreaProductKey) ?>">
                            <input type="hidden" name="region" value="<?= htmlspecialchars($asmAreaRegion) ?>">
                            <select name="month" class="h-9 w-40 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400" aria-label="Select month">
                                <?php foreach ($asmAreaMonths as $month): ?>
                                    <option value="<?= htmlspecialchars($month) ?>" <?= $asmAreaMonth === $month ? 'selected' : '' ?>><?= htmlspecialchars($month) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="year" class="h-9 w-28 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400" aria-label="Select year">
                                <?php foreach ($asmAreaYears as $year): ?>
                                    <option value="<?= htmlspecialchars($year) ?>" <?= $asmAreaYear === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-medium text-white transition-colors hover:bg-primary-700">Go</button>
                        </form>
                    </div>
                </div>

                <div class="space-y-4 px-5 py-6">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 md:p-4">
                        <div style="height: 420px;">
                            <canvas id="asm-productivity-area-chart"></canvas>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Total Orders</p>
                            <p class="mt-2 text-2xl font-medium text-blue-600"><?= number_format($asmAreaTotal) ?></p>
                            <p class="text-xs text-slate-500">Activated job orders</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Municipalities</p>
                            <p class="mt-2 text-2xl font-medium text-slate-900"><?= (int)$asmAreaMunicipalityCount ?></p>
                            <p class="text-xs text-slate-500">Tracked areas</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Average Per Municipality</p>
                            <p class="mt-2 text-2xl font-medium text-emerald-600"><?= number_format($asmAreaAverage) ?></p>
                            <p class="text-xs text-slate-500">Activated orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                if (typeof Chart === 'undefined') return;
                const areaCanvas = document.getElementById('asm-productivity-area-chart');
                if (!areaCanvas) return;
                const asmAreaAccent = '#2b7af4';
                const asmAreaGradient = areaCanvas.getContext('2d').createLinearGradient(0, 0, 0, 420);
                asmAreaGradient.addColorStop(0, asmAreaAccent);
                asmAreaGradient.addColorStop(1, 'rgba(14, 116, 144, 0.45)');
                new Chart(areaCanvas, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_column($asmAreaChartRows, 0)) ?>,
                        datasets: [{
                            label: 'Activated Job Orders',
                            data: <?= json_encode(array_column($asmAreaChartRows, 1)) ?>,
                            backgroundColor: asmAreaGradient,
                            borderColor: asmAreaAccent,
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
            });
        </script>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'assigning_area'): ?>
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <?php foreach ($asmProducts as $product): ?>
                        <?php
                            $isSelectedProduct = $asmSelectedProduct['key'] === $product['key'];
                            $productUrl = '?section=assigning-area&product=' . rawurlencode((string)$product['key']);
                        ?>
                        <a href="<?= htmlspecialchars($productUrl) ?>"
                           class="group rounded-xl border p-4 transition-all duration-200 <?= $isSelectedProduct ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="flex h-24 items-center justify-center rounded-lg border bg-white p-5 <?= $isSelectedProduct ? 'border-primary-100' : 'border-slate-100' ?>">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-28 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white p-2 shadow-sm">
                                <img src="<?= App\Config\App::url($asmSelectedProduct['image']) ?>" alt="<?= htmlspecialchars($asmSelectedProduct['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($asmSelectedProduct['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Selected Product</p>
                                <h2 class="text-lg font-bold text-slate-950"><?= htmlspecialchars($asmSelectedProduct['name']) ?> Assigning Area</h2>
                            </div>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-right shadow-sm">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Assigned Areas</p>
                            <p class="text-xl font-bold text-slate-950"><?= count($asmAssigningRows) ?></p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <form method="GET" class="flex items-center gap-2 text-xs font-medium text-slate-600" style="width: min(360px, 100%);">
                            <input type="hidden" name="section" value="assigning-area">
                            <input type="hidden" name="product" value="<?= htmlspecialchars($asmSelectedProduct['key']) ?>">
                            <label for="asm-assigning-search" class="shrink-0">Search:</label>
                            <input id="asm-assigning-search" type="search" name="search" value="<?= htmlspecialchars($asmAssigningSearch) ?>" class="h-9 min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-900 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15">
                        </form>
                        <button type="button" class="inline-flex h-9 w-fit shrink-0 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 text-xs font-bold text-white shadow-sm transition-colors hover:bg-primary-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"></path>
                            </svg>
                            Assign Area
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                        <table class="min-w-[900px] w-full border-collapse text-[11px]">
                            <thead class="bg-slate-50 text-left text-[10px] font-extrabold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <?php foreach (['Sales Category', 'Municipality', 'Barangay', 'Action'] as $heading): ?>
                                        <th class="border-b border-slate-200 px-4 py-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <span><?= htmlspecialchars($heading) ?></span>
                                                <span class="text-[10px] font-bold text-slate-400">↑↓</span>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($asmAssigningRows)): ?>
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">No assigned areas found.</td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($asmAssigningRows as $rowIndex => $row): ?>
                                    <tr class="<?= $rowIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50' ?> text-slate-700 transition-colors hover:bg-cyan-50/50">
                                        <td class="border-b border-slate-100 px-4 py-3 font-bold uppercase text-slate-800"><?= htmlspecialchars($row[0]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold uppercase"><?= htmlspecialchars($row[1]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold uppercase"><?= htmlspecialchars($row[2]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-primary-600 transition-colors hover:bg-primary-50" aria-label="Edit assigned area">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M13.5 6.5 17.5 10.5M4 20h4.5L19 9.5a2.12 2.12 0 0 0-3-3L5.5 17V20Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-red-500 transition-colors hover:bg-red-50" aria-label="Delete assigned area">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'sub_agent_report'): ?>
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <?php foreach ($asmProducts as $product): ?>
                        <?php
                            $isSelectedProduct = $asmSelectedProduct['key'] === $product['key'];
                            $productUrl = '?section=sub-agent-report&product=' . rawurlencode((string)$product['key']) . '&type=' . rawurlencode($asmSubAgentReportType);
                        ?>
                        <a href="<?= htmlspecialchars($productUrl) ?>"
                           class="group rounded-xl border p-4 transition-all duration-200 <?= $isSelectedProduct ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="flex h-24 items-center justify-center rounded-lg border bg-white p-5 <?= $isSelectedProduct ? 'border-primary-100' : 'border-slate-100' ?>">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-28 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white p-2 shadow-sm">
                                <img src="<?= App\Config\App::url($asmSelectedProduct['image']) ?>" alt="<?= htmlspecialchars($asmSelectedProduct['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($asmSelectedProduct['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Selected Product</p>
                                <h2 class="text-lg font-bold text-slate-950"><?= htmlspecialchars($asmSelectedProduct['name']) ?> Sub Agent Report</h2>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                            <div class="flex flex-wrap items-center justify-start gap-2 lg:justify-end">
                                <a href="?section=sub-agent-report&product=<?= htmlspecialchars(rawurlencode($asmSelectedProduct['key'])) ?>&type=inhouse" class="inline-flex h-9 items-center rounded-lg border px-3 text-xs font-semibold transition-colors <?= $asmSubAgentReportType === 'inhouse' ? 'border-primary-300 bg-cyan-50 text-primary-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' ?>">In house count</a>
                                <a href="?section=sub-agent-report&product=<?= htmlspecialchars(rawurlencode($asmSelectedProduct['key'])) ?>&type=partners" class="inline-flex h-9 items-center rounded-lg border px-3 text-xs font-semibold transition-colors <?= $asmSubAgentReportType === 'partners' ? 'border-primary-300 bg-cyan-50 text-primary-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' ?>">Partners count</a>
                                <a href="?section=sub-agent-report&product=<?= htmlspecialchars(rawurlencode($asmSelectedProduct['key'])) ?>&type=summary" class="inline-flex h-9 items-center rounded-lg border px-3 text-xs font-semibold transition-colors <?= $asmSubAgentReportType === 'summary' ? 'border-primary-300 bg-cyan-50 text-primary-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' ?>">Summary count</a>
                            </div>

                            <form method="GET" class="flex flex-wrap items-center justify-start gap-2 lg:justify-end">
                                <input type="hidden" name="section" value="sub-agent-report">
                                <input type="hidden" name="product" value="<?= htmlspecialchars($asmSelectedProduct['key']) ?>">
                                <input type="hidden" name="type" value="<?= htmlspecialchars($asmSubAgentReportType) ?>">
                                <select name="month" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:outline-none">
                                    <?php foreach (['January', 'February', 'March', 'April', 'May', 'June'] as $month): ?>
                                        <option value="<?= htmlspecialchars($month) ?>" <?= (($_GET['month'] ?? 'January') === $month) ? 'selected' : '' ?>><?= htmlspecialchars($month) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="year" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:outline-none">
                                    <?php foreach (['2026', '2025', '2024'] as $year): ?>
                                        <option value="<?= htmlspecialchars($year) ?>" <?= (($_GET['year'] ?? '2026') === $year) ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="inline-flex h-9 items-center rounded-lg bg-green-600 px-4 text-xs font-bold text-white transition-colors hover:bg-green-700">Export</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto p-4">
                    <table class="min-w-[1320px] w-full border-collapse text-[11px]">
                        <thead class="bg-primary-700 text-white">
                            <tr>
                                <th class="border border-primary-600 px-3 py-3 text-left">Agent Name</th>
                                <th class="border border-primary-600 px-3 py-3 text-center">Agent Code</th>
                                <th class="border border-primary-600 px-3 py-3 text-center">Total Activation</th>
                                <th class="border border-primary-600 px-3 py-3 text-center">Average</th>
                                <th class="border border-primary-600 px-3 py-3 text-center">EOM Forecast</th>
                                <?php for ($day = 1; $day <= 17; $day++): ?>
                                    <th class="border border-primary-600 px-2 py-3 text-center"><?= $day ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asmSubAgentRows as $row): ?>
                                <tr class="<?= $row[7] ? 'bg-blue-600 text-white font-bold' : 'bg-white text-slate-700 hover:bg-slate-50' ?>">
                                    <td class="border border-slate-200 px-3 py-3 <?= $row[7] ? 'border-blue-500' : '' ?>"><?= htmlspecialchars($row[0]) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center <?= $row[7] ? 'border-blue-500' : '' ?>"><?= htmlspecialchars($row[1]) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center <?= $row[7] ? 'border-blue-500' : 'font-semibold text-slate-950' ?>"><?= htmlspecialchars((string)$row[2]) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center <?= $row[7] ? 'border-blue-500' : '' ?>"><?= htmlspecialchars($row[3]) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center <?= $row[7] ? 'border-blue-500' : '' ?>"><?= htmlspecialchars($row[4]) ?></td>
                                    <?php foreach ($row[5] as $value): ?>
                                        <td class="border border-slate-200 px-2 py-3 text-center <?= $row[7] ? 'border-blue-500' : '' ?>"><?= (int)$value ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'sales_status'): ?>
        <?php
            $asmSalesStatusMode = strtolower(trim((string)($_GET['mode'] ?? 'tl')));
            if (!in_array($asmSalesStatusMode, ['inhouse', 'partners', 'tl'], true)) {
                $asmSalesStatusMode = 'tl';
            }
            $asmSalesStatusTables = [
                'inhouse' => [
                    ['TEAM MARGA_MARICEL DOLETE', 'Cedric Daryl', 'In House', 'Active'],
                    ['TEAM MARGA_JONNALYN COLE', 'Cedric Daryl', 'In House', 'Active'],
                    ['TEAM MARGA_PINCAS KARRISA', 'Cedric Daryl', 'In House', 'Active'],
                    ['TEAM MARGA_JESSICA REGACHUELO', 'Cedric Daryl', 'In House', 'Active'],
                    ['TEAM MARGA_JAMES DIAZ', 'Cedric Daryl', 'In House', 'Active'],
                ],
                'partners' => [
                    ['MBCMGT Incorporated', 'Partner', 'Partner', 'Active'],
                    ['5 JHMD Internet Installation Services', 'Partner', 'Partner', 'Active'],
                    ['ALLY5 68 Corporation', 'Partner', 'Regional Partner', 'Active'],
                    ['ARGIN Network Services Inc', 'Partner', 'Partner', 'Active'],
                    ['MBCMGT Incorporated', 'Partner', 'Partner', 'Active'],
                ],
                'tl' => [
                    ['MBCMGT Incorporated', 'Partner', 'Partner', 'Active'],
                    ['MBCMGT Incorporated', 'Partner', 'Partner', 'Active'],
                    ['MBCMGT Incorporated', 'Partner', 'Partner', 'Active'],
                    ['MBCMGT Incorporated', 'Partner', 'Partner', 'Active'],
                    ['MBCMGT Incorporated', 'Partner', 'Partner', 'Active'],
                ],
            ];
            $asmSalesStatusRows = $asmSalesStatusTables[$asmSalesStatusMode];
        ?>
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <?php foreach ($asmProducts as $product): ?>
                    <?php
                        $isSelectedProduct = $asmSelectedProduct['key'] === $product['key'];
                        $productUrl = '?section=sales-status&product=' . rawurlencode((string)$product['key']) . '&mode=' . rawurlencode($asmSalesStatusMode);
                    ?>
                    <a href="<?= htmlspecialchars($productUrl) ?>"
                       class="group rounded-xl border p-4 transition-all duration-200 <?= $isSelectedProduct ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="flex h-24 items-center justify-center rounded-lg border bg-white p-5 <?= $isSelectedProduct ? 'border-primary-100' : 'border-slate-100' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                        </div>
                    </a>
                <?php endforeach; ?>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between px-5 py-4">
                    <div class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M7 12h10m-7 6h4"/>
                        </svg>
                        <span>Filters</span>
                    </div>
                    <a href="?section=sales-status&product=<?= htmlspecialchars(rawurlencode($asmSelectedProduct['key'])) ?>" class="text-xs font-medium text-slate-500 hover:text-slate-800">Clear all</a>
                </div>
                <form method="GET" class="sales-status-filter-row flex flex-col gap-3 border-t border-slate-100 px-5 py-4 lg:flex-row lg:items-center">
                    <input type="hidden" name="section" value="sales-status">
                    <input type="hidden" name="product" value="<?= htmlspecialchars($asmSelectedProduct['key']) ?>">
                    <input type="hidden" name="mode" value="<?= htmlspecialchars($asmSalesStatusMode) ?>">
                    <label class="sales-status-search-control relative w-full">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="search" name="search" value="<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" placeholder="Search..." class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                    </label>
                    <label class="sales-status-filter-control relative w-full">
                        <input type="month" name="month" value="<?= htmlspecialchars((string)($_GET['month'] ?? '2026-01')) ?>" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:outline-none">
                    </label>
                    <select name="type" class="sales-status-filter-control h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-500 focus:outline-none">
                        <option>All types</option>
                        <option>Partner</option>
                        <option>In House</option>
                    </select>
                    <select name="status" class="sales-status-filter-control h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-500 focus:outline-none">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </form>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-sm font-semibold text-slate-500">Manage TL Status</h2>
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <a href="?section=sales-status&product=<?= htmlspecialchars(rawurlencode($asmSelectedProduct['key'])) ?>&mode=inhouse"
                           class="inline-flex h-9 items-center rounded-lg px-4 text-xs font-bold text-white transition-colors"
                           style="background-color: <?= $asmSalesStatusMode === 'inhouse' ? '#00bf63' : '#cfcfcf' ?>;">In House</a>
                        <a href="?section=sales-status&product=<?= htmlspecialchars(rawurlencode($asmSelectedProduct['key'])) ?>&mode=partners"
                           class="inline-flex h-9 items-center rounded-lg px-4 text-xs font-bold text-white transition-colors"
                           style="background-color: <?= $asmSalesStatusMode === 'partners' ? '#00bf63' : '#cfcfcf' ?>;">Partners/Regional</a>
                        <a href="?section=sales-status&product=<?= htmlspecialchars(rawurlencode($asmSelectedProduct['key'])) ?>&mode=tl"
                           class="inline-flex h-9 items-center rounded-lg px-4 text-xs font-bold text-white transition-colors"
                           style="background-color: <?= $asmSalesStatusMode === 'tl' ? '#00bf63' : '#cfcfcf' ?>;">TL Status</a>
                    </div>
                </div>
                <div class="overflow-x-auto p-4">
                    <table class="min-w-[900px] w-full border-collapse text-[11px]">
                        <thead class="bg-primary-700 text-left text-white">
                            <tr>
                                <th class="border border-primary-600 px-4 py-3">Sales Category</th>
                                <th class="border border-primary-600 px-4 py-3">Sales Manager</th>
                                <th class="border border-primary-600 px-4 py-3">Type of Sales</th>
                                <th class="border border-primary-600 px-4 py-3 text-center">TL Status</th>
                                <th class="border border-primary-600 px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asmSalesStatusRows as $row): ?>
                                <tr class="bg-white text-slate-700 hover:bg-slate-50">
                                    <td class="border border-slate-200 px-4 py-3"><?= htmlspecialchars($row[0]) ?></td>
                                    <td class="border border-slate-200 px-4 py-3"><?= htmlspecialchars($row[1]) ?></td>
                                    <td class="border border-slate-200 px-4 py-3"><?= htmlspecialchars($row[2]) ?></td>
                                    <td class="border border-slate-200 px-4 py-3 text-center">
                                        <span class="inline-flex rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700"><?= htmlspecialchars($row[3]) ?></span>
                                    </td>
                                    <td class="border border-slate-200 px-4 py-3 text-center">
                                        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-800" aria-label="Open actions">
                                            ...
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'daily_sales_activation'): ?>
        <?php
            $asmDailyProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
            $asmDailyProductKeys = array_column($asmProducts, 'key');
            if (!in_array($asmDailyProductKey, $asmDailyProductKeys, true)) {
                $asmDailyProductKey = 's2s';
            }
            $asmDailyFrom = trim((string)($_GET['from'] ?? '2026-01-30'));
            $asmDailyTo = trim((string)($_GET['to'] ?? '2026-01-30'));
            $asmDailyShow = max(5, min(50, (int)($_GET['show'] ?? 10)));
            $asmDailySearch = trim((string)($_GET['search'] ?? ''));
            $asmDailyRows = [
                ['PCC-5JHMD-001', 'DOLETE, MARICEL', '5 JHMD INTERNET INSTALLATION SERVICES', 'SUB-JHMD', 'RACHEL ANN MACALANDA CAMANGIAN', 'IP1997839', 'January 30, 2026', 'BLOCK 9 LOT 6A, PHASE 1B KASIGLAHAN VILLAGE, SAN JOSE, RODRIGUEZ, RIZAL SLB', 'RODRIGUEZ', 'ACTIVATED'],
                ['PCC-5JHMD-001', 'DOLETE, MARICEL', '5 JHMD INTERNET INSTALLATION SERVICES', 'SUB-JHMD', 'DANIEL JR OLERMO CICAT', 'IP1989290', 'January 30, 2026', '17C, MT APO, PINAGSAMA, TAGUIG, METRO MANILA NCRR', 'TAGUIG', 'ACTIVATED'],
                ['PCC-5JHMD-001', 'DOLETE, MARICEL', '5 JHMD INTERNET INSTALLATION SERVICES', 'SUB-JHMD', 'RAQUEL CAMANGIAN DELGADO', 'IP1998250', 'January 30, 2026', 'BLOCK 6 LOT 76, PHASE 1D KASIGLAHAN VILLAGE, SAN JOSE, RODRIGUEZ, RIZAL SLB', 'RODRIGUEZ', 'ACTIVATED'],
                ['PCC-5JHMD-001', 'DOLETE, MARICEL', '5 JHMD INTERNET INSTALLATION SERVICES', 'SUB-JHMD', 'LILIBETH ALMONTE GRASPARIL', 'IP1998608', 'January 30, 2026', '31, KADAYUNAN ST SITIO MAISLAP, SAN ISIDRO, RODRIGUEZ, RIZAL SLB', 'RODRIGUEZ', 'ACTIVATED'],
                ['PCC-ALLYS88-234', 'ROZEL PANTO', 'ALLYS 88 CORPORATION', 'TRADIO/DE GUZMAN/BUCO', 'GEMMA ALINSUNURIN CANGCOO', 'IP1997617', 'January 30, 2026', '3801, CUENCA ST., PALANAN, MAKATI', 'MAKATI', 'ACTIVATED'],
                ['PCC-ARGIN-117', 'SANTOS, MARIA', 'ARGIN NETWORK SERVICES INC', 'SUB-ARGIN', 'JONATHAN REYES DELA CRUZ', 'IP1997442', 'January 30, 2026', 'LOT 18 BLOCK 4, BAGONG SILANG, CALOOCAN CITY NCRR', 'CALOOCAN', 'ACTIVATED'],
                ['PCC-DIGI-086', 'CRUZ, RAMON', 'DIGITECH', 'SUB-DIGITECH', 'PATRICIA MANALO SORIANO', 'IP1997188', 'January 30, 2026', '142 KATIPUNAN AVE, PROJECT 4, QUEZON CITY NCRR', 'QUEZON CITY', 'ACTIVATED'],
                ['PCC-VITA-240', 'REYES, ANGEL', 'VITA-LIZE DISTRIBUTIONS INC', 'SUB-VITA', 'MELANIE DACANAY FLORES', 'IP1997045', 'January 30, 2026', '52 DONA SOLEDAD AVE, BETTER LIVING, PARANAQUE NCRR', 'PARANAQUE', 'ACTIVATED'],
            ];
            if ($asmDailySearch !== '') {
                $asmDailyRows = array_values(array_filter($asmDailyRows, static function ($row) use ($asmDailySearch) {
                    return stripos(implode(' ', $row), $asmDailySearch) !== false;
                }));
            }
            $asmDailyRows = array_slice($asmDailyRows, 0, $asmDailyShow);
        ?>

        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-3" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
                    <?php foreach ($asmProducts as $product): ?>
                        <?php
                            $isDailyProductActive = $asmDailyProductKey === $product['key'];
                            $dailyProductUrl = '?section=daily-sales-activation&product=' . rawurlencode((string)$product['key'])
                                . '&from=' . rawurlencode($asmDailyFrom)
                                . '&to=' . rawurlencode($asmDailyTo)
                                . '&show=' . rawurlencode((string)$asmDailyShow)
                                . '&search=' . rawurlencode($asmDailySearch);
                        ?>
                        <a href="<?= htmlspecialchars($dailyProductUrl) ?>"
                           class="group rounded-xl border p-4 transition-all duration-200 <?= $isDailyProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="flex h-24 items-center justify-center rounded-lg border bg-white p-5 <?= $isDailyProductActive ? 'border-primary-100' : 'border-slate-100' ?>">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-medium text-primary-700">Daily Sales Activation</h2>
                    <div class="mt-4 flex flex-wrap items-center gap-3" style="justify-content: flex-end;">
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="section" value="daily-sales-activation">
                            <input type="hidden" name="product" value="<?= htmlspecialchars($asmDailyProductKey) ?>">
                            <input type="hidden" name="show" value="<?= (int)$asmDailyShow ?>">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($asmDailySearch) ?>">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                                From
                                <input type="date" name="from" value="<?= htmlspecialchars($asmDailyFrom) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-900 outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15">
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                                To
                                <input type="date" name="to" value="<?= htmlspecialchars($asmDailyTo) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-900 outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15">
                            </label>
                            <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-medium text-white transition-colors hover:bg-primary-700">Go</button>
                            <button type="button" id="asm-daily-sales-export" class="inline-flex h-9 items-center justify-center rounded-lg bg-green-600 px-4 text-xs font-medium text-white transition-colors hover:bg-green-700">Export</button>
                        </form>
                    </div>
                </div>

                <div class="px-5 py-6">
                    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <form method="GET" class="flex items-center gap-2 text-xs font-medium text-slate-600">
                            <input type="hidden" name="section" value="daily-sales-activation">
                            <input type="hidden" name="product" value="<?= htmlspecialchars($asmDailyProductKey) ?>">
                            <input type="hidden" name="from" value="<?= htmlspecialchars($asmDailyFrom) ?>">
                            <input type="hidden" name="to" value="<?= htmlspecialchars($asmDailyTo) ?>">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($asmDailySearch) ?>">
                            <span>Show</span>
                            <select name="show" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-900 outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15" onchange="this.form.submit()">
                                <?php foreach ([10, 25, 50] as $showOption): ?>
                                    <option value="<?= (int)$showOption ?>" <?= $asmDailyShow === $showOption ? 'selected' : '' ?>><?= (int)$showOption ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span>entries</span>
                        </form>

                        <form method="GET" class="flex items-center justify-end gap-2 text-xs font-medium text-slate-600">
                            <input type="hidden" name="section" value="daily-sales-activation">
                            <input type="hidden" name="product" value="<?= htmlspecialchars($asmDailyProductKey) ?>">
                            <input type="hidden" name="from" value="<?= htmlspecialchars($asmDailyFrom) ?>">
                            <input type="hidden" name="to" value="<?= htmlspecialchars($asmDailyTo) ?>">
                            <input type="hidden" name="show" value="<?= (int)$asmDailyShow ?>">
                            <label for="asm-daily-sales-search">Search:</label>
                            <input id="asm-daily-sales-search" type="search" name="search" value="<?= htmlspecialchars($asmDailySearch) ?>" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-900 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15 md:w-56">
                        </form>
                    </div>

                    <div class="overflow-auto rounded-xl border border-slate-200 bg-white shadow-sm" style="max-height: 620px;">
                        <table id="asm-daily-sales-table" class="min-w-[1320px] w-full border-collapse text-[11px]">
                            <thead class="bg-slate-50 text-left text-[10px] font-medium uppercase tracking-wide text-slate-500">
                                <tr>
                                    <?php foreach (['Agent Code', 'Agent Name', 'Sales Category', 'Installer', 'Account Name', 'Job Order', 'Date Activated', 'Address', 'Municipality', 'Status'] as $heading): ?>
                                        <th class="border-b border-slate-200 px-4 py-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <span><?= htmlspecialchars($heading) ?></span>
                                                <span class="text-[10px] font-medium text-slate-400">↑↓</span>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($asmDailyRows)): ?>
                                    <tr>
                                        <td colspan="10" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">No activations found.</td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($asmDailyRows as $rowIndex => $row): ?>
                                    <tr class="<?= $rowIndex % 2 === 0 ? 'bg-white' : 'bg-slate-50' ?> text-slate-700 transition-colors hover:bg-cyan-50/50">
                                        <td class="border-b border-slate-100 px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($row[0]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold uppercase"><?= htmlspecialchars($row[1]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold uppercase"><?= htmlspecialchars($row[2]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold uppercase"><?= htmlspecialchars($row[3]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold uppercase"><?= htmlspecialchars($row[4]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold"><?= htmlspecialchars($row[5]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold"><?= htmlspecialchars($row[6]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-semibold uppercase leading-snug"><?= htmlspecialchars($row[7]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3 font-medium uppercase text-slate-800"><?= htmlspecialchars($row[8]) ?></td>
                                        <td class="border-b border-slate-100 px-4 py-3">
                                            <span class="inline-flex rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-[11px] font-semibold uppercase text-emerald-700"><?= htmlspecialchars($row[9]) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const exportButton = document.getElementById('asm-daily-sales-export');
                const table = document.getElementById('asm-daily-sales-table');
                exportButton?.addEventListener('click', () => {
                    if (!table) return;
                    const csvRows = Array.from(table.querySelectorAll('tr')).map((row) => (
                        Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                    ));
                    const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'daily-sales-activation-<?= htmlspecialchars(strtolower($asmDailyProductKey . '-' . $asmDailyFrom . '-' . $asmDailyTo)) ?>.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
                });
            });
        </script>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'partners_report'): ?>
        <?php
            $asmPartnersProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
            $asmPartnersProductKeys = array_column($asmProducts, 'key');
            if (!in_array($asmPartnersProductKey, $asmPartnersProductKeys, true)) {
                $asmPartnersProductKey = 's2s';
            }
            $asmPartnersYear = (string)($_GET['year'] ?? '2026');
            $asmPartnersYears = ['2026', '2025', '2024'];
            if (!in_array($asmPartnersYear, $asmPartnersYears, true)) {
                $asmPartnersYear = '2026';
            }
            $asmPartnersScope = strtolower(trim((string)($_GET['scope'] ?? 'all')));
            if (!in_array($asmPartnersScope, ['all', 'ncr', 'regional'], true)) {
                $asmPartnersScope = 'all';
            }
            $asmPartnersShift = [
                's2s' => 0,
                'fiberx' => 7,
                'bida' => 11,
                'sme' => 15,
            ][$asmPartnersProductKey] ?? 0;
            $asmPartnerRows = [
                ['partner' => 'ALLYS 88 CORPORATION', 'ncr' => 143, 'regional' => 15],
                ['partner' => 'ARGIN NETWORK SERVICES INC', 'ncr' => 167, 'regional' => 61],
                ['partner' => 'AYM2 INTERNET INSTALLATION SERVICES', 'ncr' => 0, 'regional' => 12],
                ['partner' => 'DIGITECH', 'ncr' => 0, 'regional' => 86],
                ['partner' => 'FERNANDO ROGER INTERNET INSTALLATION SERVICES', 'ncr' => 0, 'regional' => 21],
                ['partner' => 'VITA-LIZE DISTRIBUTIONS INC', 'ncr' => 1, 'regional' => 239],
            ];
            foreach ($asmPartnerRows as &$asmPartnerRow) {
                $asmPartnerRow['ncr'] = max(0, (int)$asmPartnerRow['ncr'] + $asmPartnersShift);
                $asmPartnerRow['regional'] = max(0, (int)$asmPartnerRow['regional'] + (int)round($asmPartnersShift / 2));
                $asmPartnerRow['total'] = (int)$asmPartnerRow['ncr'] + (int)$asmPartnerRow['regional'];
            }
            unset($asmPartnerRow);
            if ($asmPartnersScope === 'ncr') {
                $asmPartnerRows = array_values(array_filter($asmPartnerRows, static fn(array $row): bool => (int)$row['ncr'] > 0));
            } elseif ($asmPartnersScope === 'regional') {
                $asmPartnerRows = array_values(array_filter($asmPartnerRows, static fn(array $row): bool => (int)$row['regional'] > 0));
            }
            $asmPartnerMonths = ['January', 'February', 'March'];
            $asmPartnerTotals = [
                'total' => array_sum(array_column($asmPartnerRows, 'total')),
                'ncr' => array_sum(array_column($asmPartnerRows, 'ncr')),
                'regional' => array_sum(array_column($asmPartnerRows, 'regional')),
            ];
        ?>

        <div class="space-y-5">
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <?php foreach ($asmProducts as $product): ?>
                    <?php
                        $isPartnersProductActive = $asmPartnersProductKey === $product['key'];
                        $partnersProductUrl = '?section=partners-report&product=' . rawurlencode((string)$product['key'])
                            . '&year=' . rawurlencode($asmPartnersYear)
                            . '&scope=' . rawurlencode($asmPartnersScope);
                    ?>
                    <a href="<?= htmlspecialchars($partnersProductUrl) ?>"
                       class="group rounded-xl border p-3 transition-all duration-200 <?= $isPartnersProductActive ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                        <div class="flex h-20 items-center justify-center rounded-lg border bg-white p-3 <?= $isPartnersProductActive ? 'border-primary-100' : 'border-slate-100' ?>">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <form method="GET" class="flex flex-wrap items-end gap-2">
                        <input type="hidden" name="section" value="partners-report">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($asmPartnersProductKey) ?>">
                        <input type="hidden" name="year" value="<?= htmlspecialchars($asmPartnersYear) ?>">
                        <?php foreach (['all' => 'ALL', 'ncr' => 'NCR', 'regional' => 'REGIONAL'] as $scopeKey => $scopeLabel): ?>
                            <button type="submit" name="scope" value="<?= htmlspecialchars($scopeKey) ?>" class="inline-flex h-10 items-center justify-center rounded-lg border px-5 text-xs font-extrabold transition-colors <?= $asmPartnersScope === $scopeKey ? 'border-primary-500 bg-primary-600 text-white shadow-sm' : 'border-primary-200 bg-white text-primary-700 hover:bg-cyan-50' ?>">
                                <?= htmlspecialchars($scopeLabel) ?>
                            </button>
                        <?php endforeach; ?>
                    </form>

                    <form method="GET" class="flex flex-wrap items-end justify-end gap-2">
                        <input type="hidden" name="section" value="partners-report">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($asmPartnersProductKey) ?>">
                        <input type="hidden" name="scope" value="<?= htmlspecialchars($asmPartnersScope) ?>">
                        <label class="flex flex-col gap-1 text-xs font-semibold text-slate-500">
                            Select Year
                            <select name="year" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-900 outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15" onchange="this.form.submit()">
                                <?php foreach ($asmPartnersYears as $year): ?>
                                    <option value="<?= htmlspecialchars($year) ?>" <?= $asmPartnersYear === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="button" id="asm-partners-report-export" class="inline-flex h-10 items-center justify-center rounded-lg bg-green-600 px-5 text-xs font-bold text-white transition-colors hover:bg-green-700">Export</button>
                    </form>
                </div>

                <div class="overflow-auto rounded-xl border border-slate-200 shadow-sm" style="max-height: 620px;">
                    <table id="asm-partners-report-table" class="min-w-[1280px] w-full border-collapse text-[11px]">
                        <thead class="text-white">
                            <tr class="bg-primary-700">
                                <th rowspan="2" class="sticky left-0 z-20 border border-primary-600 bg-primary-700 px-3 py-3 text-center">PARTNER</th>
                                <th rowspan="2" class="border border-primary-600 px-3 py-3 text-center">TOTAL</th>
                                <th rowspan="2" class="border border-primary-600 px-3 py-3 text-center">TOTAL NCR</th>
                                <th rowspan="2" class="border border-primary-600 px-3 py-3 text-center">TOTAL REGIONAL</th>
                                <?php foreach ($asmPartnerMonths as $month): ?>
                                    <th colspan="2" class="border border-primary-600 px-3 py-3 text-center uppercase"><?= htmlspecialchars($month) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <tr class="bg-primary-600">
                                <?php foreach ($asmPartnerMonths as $month): ?>
                                    <th class="border border-primary-500 px-3 py-2.5 text-center">NCR</th>
                                    <th class="border border-primary-500 px-3 py-2.5 text-center">REGIONAL</th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asmPartnerRows as $rowIndex => $row): ?>
                                <tr class="<?= $rowIndex % 2 === 0 ? 'bg-slate-100' : 'bg-white' ?> text-slate-700 hover:bg-cyan-50/50 transition-colors">
                                    <td class="sticky left-0 z-10 border border-slate-200 <?= $rowIndex % 2 === 0 ? 'bg-slate-100' : 'bg-white' ?> px-3 py-3 text-center font-bold uppercase text-slate-800"><?= htmlspecialchars($row['partner']) ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-extrabold text-slate-950"><?= (int)$row['total'] ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-extrabold text-slate-950"><?= (int)$row['ncr'] ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center font-extrabold text-slate-950"><?= (int)$row['regional'] ?></td>
                                    <?php foreach ($asmPartnerMonths as $monthIndex => $month): ?>
                                        <td class="border border-slate-200 px-3 py-3 text-center"><?= $monthIndex === 0 ? (int)$row['ncr'] : 0 ?></td>
                                        <td class="border border-slate-200 px-3 py-3 text-center"><?= $monthIndex === 0 ? (int)$row['regional'] : 0 ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-100 font-extrabold text-slate-950">
                                <td class="sticky left-0 z-20 border border-slate-200 bg-slate-100 px-3 py-3 text-center uppercase">Grand Total</td>
                                <td class="border border-slate-200 px-3 py-3 text-center"><?= (int)$asmPartnerTotals['total'] ?></td>
                                <td class="border border-slate-200 px-3 py-3 text-center"><?= (int)$asmPartnerTotals['ncr'] ?></td>
                                <td class="border border-slate-200 px-3 py-3 text-center"><?= (int)$asmPartnerTotals['regional'] ?></td>
                                <?php foreach ($asmPartnerMonths as $monthIndex => $month): ?>
                                    <td class="border border-slate-200 px-3 py-3 text-center"><?= $monthIndex === 0 ? (int)$asmPartnerTotals['ncr'] : 0 ?></td>
                                    <td class="border border-slate-200 px-3 py-3 text-center"><?= $monthIndex === 0 ? (int)$asmPartnerTotals['regional'] : 0 ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const exportButton = document.getElementById('asm-partners-report-export');
                const table = document.getElementById('asm-partners-report-table');
                exportButton?.addEventListener('click', () => {
                    if (!table) return;
                    const csvRows = Array.from(table.querySelectorAll('tr')).map((row) => (
                        Array.from(row.children).map((cell) => cell.innerText.replace(/\s+/g, ' ').trim())
                    ));
                    const csv = csvRows.map((line) => line.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'partners-report-<?= htmlspecialchars(strtolower($asmPartnersProductKey . '-' . $asmPartnersYear . '-' . $asmPartnersScope)) ?>.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
                });
            });
        </script>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'sales_turn_ins'): ?>
        <?php
            $asmTurninsRows = [
                ['5 JHMD INTERNET INSTALLATION SERVICES', 0, 1, 1, 0, 0, 0, 0, 0, 2],
                ['ALLY5 68 CORPORATION', 0, 1, 2, 0, 3, 1, 0, 1, 8],
                ['ARGIN NETWORK SERVICES INC', 0, 2, 2, 0, 0, 1, 0, 0, 5],
                ['TEAM MARGA_CHRISTOPHER NEDIA', 0, 0, 0, 0, 1, 0, 1, 1, 3],
                ['TEAM MARGA_ELEUTERIO MENDOZA', 0, 1, 1, 0, 3, 1, 0, 0, 6],
                ['TEAM MARGA_JAMES DIAZ', 0, 1, 1, 0, 0, 0, 0, 0, 2],
                ['TEAM MARGA_JESSICA REGACHUELO', 1, 1, 0, 0, 1, 0, 0, 0, 3],
                ['TEAM MARGA_JIMMY GOMBAO', 0, 0, 0, 1, 2, 0, 0, 0, 3],
                ['TEAM MARGA_KATRINA MAE DERUJE', 0, 2, 0, 1, 0, 0, 0, 0, 3],
                ['TEAM MARGA_PINCAS KARRISA', 0, 1, 2, 0, 4, 0, 0, 2, 9],
            ];
            $asmTurninsTotals = array_fill(0, 9, 0);
            foreach ($asmTurninsRows as $row) {
                foreach (array_slice($row, 1) as $index => $value) {
                    $asmTurninsTotals[$index] += (int)$value;
                }
            }
        ?>
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <?php foreach ($asmProducts as $product): ?>
                        <?php
                            $isSelectedProduct = $asmSelectedProduct['key'] === $product['key'];
                            $productUrl = '?section=sales-turn-ins&product=' . rawurlencode((string)$product['key']);
                        ?>
                        <a href="<?= htmlspecialchars($productUrl) ?>"
                           class="group rounded-xl border p-4 transition-all duration-200 <?= $isSelectedProduct ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="flex h-24 items-center justify-center rounded-lg border bg-white p-5 <?= $isSelectedProduct ? 'border-primary-100' : 'border-slate-100' ?>">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-blue-300 bg-white shadow-sm">
                <div class="flex items-center justify-end border-b border-blue-100 px-5 py-4">
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="section" value="sales-turn-ins">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($asmSelectedProduct['key']) ?>">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            Dispatch Date:
                            <input type="date" name="dispatch_date" value="<?= htmlspecialchars((string)($_GET['dispatch_date'] ?? '2026-01-30')) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:outline-none">
                        </label>
                        <button type="button" class="inline-flex h-9 items-center rounded-lg bg-green-600 px-4 text-xs font-bold text-white transition-colors hover:bg-green-700">Export</button>
                    </form>
                </div>

                <div class="overflow-x-auto p-4">
                    <table class="sales-turnins-table min-w-[1180px] w-full border-collapse text-[11px]">
                        <thead>
                            <tr>
                                <th>Sales Category</th>
                                <th>For Schedule of Installation</th>
                                <th>Activated</th>
                                <th>Double Entry</th>
                                <th>For Compliance</th>
                                <th>For Installation</th>
                                <th>For MSA Installation</th>
                                <th>For Revalidation</th>
                                <th>Re-schedule</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asmTurninsRows as $row): ?>
                                <tr>
                                    <td class="sales-turnins-category"><?= htmlspecialchars($row[0]) ?></td>
                                    <?php foreach (array_slice($row, 1, 8) as $value): ?>
                                        <td><?= (int)$value ?></td>
                                    <?php endforeach; ?>
                                    <td class="sales-turnins-total"><?= (int)$row[9] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="sales-turnins-grand-total">
                                <td>Grand Total</td>
                                <?php foreach ($asmTurninsTotals as $value): ?>
                                    <td><?= (int)$value ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'pending_job_order'): ?>
        <?php
            $asmPendingRows = [
                ['ANTIPOLO', 1, 0, 5, 6],
                ['BARAS', 0, 1, 0, 1],
                ['BAY', 0, 0, 2, 2],
                ['BINAN', 0, 0, 1, 1],
                ['CABUYAO', 0, 0, 1, 1],
                ['CAINTA', 1, 0, 6, 7],
                ['CALAMBA', 0, 0, 2, 2],
                ['CALAUAN', 0, 0, 1, 1],
                ['RODRIGUEZ', 0, 0, 1, 1],
                ['SAN MATEO', 0, 1, 0, 1],
                ['SAN PEDRO', 0, 0, 1, 1],
                ['SANTA ROSA', 0, 0, 2, 2],
                ['TAGUIG', 0, 7, 344, 351],
                ['TANAY', 1, 0, 0, 1],
                ['TAYTAY', 0, 0, 1, 1],
            ];
            $asmPendingTotals = array_fill(0, 4, 0);
            foreach ($asmPendingRows as $row) {
                foreach (array_slice($row, 1) as $index => $value) {
                    $asmPendingTotals[$index] += (int)$value;
                }
            }
            $asmPendingDate = (string)($_GET['pending_date'] ?? '2026-01-30');
            $asmPendingDateLabel = date('l, F j, Y', strtotime($asmPendingDate) ?: strtotime('2026-01-30'));
        ?>
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <?php foreach ($asmProducts as $product): ?>
                        <?php
                            $isSelectedProduct = $asmSelectedProduct['key'] === $product['key'];
                            $productUrl = '?section=pending-job-order&product=' . rawurlencode((string)$product['key']);
                        ?>
                        <a href="<?= htmlspecialchars($productUrl) ?>"
                           class="group rounded-xl border p-4 transition-all duration-200 <?= $isSelectedProduct ? 'border-primary-400 bg-cyan-50 shadow-sm ring-2 ring-primary-500/10' : 'border-slate-200 bg-white hover:border-primary-200 hover:bg-slate-50 hover:shadow-sm' ?>">
                            <div class="flex h-24 items-center justify-center rounded-lg border bg-white p-5 <?= $isSelectedProduct ? 'border-primary-100' : 'border-slate-100' ?>">
                                <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-medium text-primary-700"><?= htmlspecialchars($asmPendingDateLabel) ?></h2>
                    <div class="mt-4 flex flex-wrap items-center gap-3" style="justify-content: flex-end;">
                        <form method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="section" value="pending-job-order">
                            <input type="hidden" name="product" value="<?= htmlspecialchars($asmSelectedProduct['key']) ?>">
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                Date:
                                <input type="date" name="pending_date" value="<?= htmlspecialchars($asmPendingDate) ?>" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-700 focus:outline-none">
                            </label>
                            <button type="button" class="inline-flex h-9 items-center rounded-lg bg-green-600 px-4 text-xs font-medium text-white transition-colors hover:bg-green-700">Export</button>
                        </form>
                    </div>
                </div>
                <div class="px-5 py-6">
                    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                        <table class="pending-job-table min-w-[920px] w-full border-collapse text-[11px]">
                            <thead>
                                <tr>
                                    <th>Municipality</th>
                                    <th>Resched by MSA</th>
                                    <th>For Installation</th>
                                    <th>For MSA Installation</th>
                                    <th>Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asmPendingRows as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row[0]) ?></td>
                                        <td><?= (int)$row[1] ?></td>
                                        <td><?= (int)$row[2] ?></td>
                                        <td><?= (int)$row[3] ?></td>
                                        <td><?= (int)$row[4] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="pending-job-total">
                                    <td>Total</td>
                                    <?php foreach ($asmPendingTotals as $value): ?>
                                        <td><?= (int)$value ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php return; ?>
    <?php endif; ?>

    <?php if (($activeRoute ?? 'dashboard') === 'faq'): ?>
        <?php
            $asmFaqItems = [
                [
                    'question' => 'How do I switch between product reports?',
                    'answer' => 'Use the product cards at the top of each report section. Selecting S2S, FiberX, BIDA, or SME reloads the current tab with data and filters preserved for that product whenever possible.',
                ],
                [
                    'question' => 'What does Productivity Per Area show?',
                    'answer' => 'Productivity Per Area summarizes activated job orders by municipality and region. Area Sales Managers can compare total activations, tracked municipalities, and average activation performance for the selected month and product.',
                ],
                [
                    'question' => 'How should I use Pending Job Order?',
                    'answer' => 'Pending Job Order helps monitor municipalities with orders still waiting for reschedule, installation, or MSA installation. Use the date filter to check the current queue for a specific day, then export the table if you need a report copy.',
                ],
                [
                    'question' => 'What is Daily Sales Activation for?',
                    'answer' => 'Daily Sales Activation lists activated accounts within the selected date range, including agent code, agent name, sales category, installer, job order, address, municipality, and activation status.',
                ],
                [
                    'question' => 'Can I search and export table records?',
                    'answer' => 'Yes. Sections with tables include search, date, show-entry, or export controls depending on the report. Use search for quick filtering and Export when you need a CSV copy of the visible report data.',
                ],
                [
                    'question' => 'Why do some reports change when I select a date, month, or region?',
                    'answer' => 'The dashboard reads the selected filters from the page URL. Changing a filter refreshes the report so the table, chart, and totals match the selected product, date range, month, year, or region.',
                ],
            ];
        ?>
        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-xl font-medium text-slate-950">Help & Center</h1>
                    <p class="mt-1 text-xs font-medium text-slate-500">Quick answers for using the Area Sales Manager dashboard.</p>
                </div>
                <button type="button" class="inline-flex h-10 w-fit items-center justify-center rounded-lg bg-primary-600 px-4 text-xs font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    Add Question
                </button>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm font-semibold text-slate-600">Help and Center</p>
                    <label class="relative w-full md:w-64">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input id="asm-faq-search" type="search" placeholder="Search" class="h-9 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-xs font-medium text-slate-900 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-500/15">
                    </label>
                </div>

                <div id="asm-faq-list" class="space-y-3 p-5">
                    <?php foreach ($asmFaqItems as $item): ?>
                        <details class="asm-faq-item group rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm" open>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-medium text-slate-950">
                                <span><?= htmlspecialchars($item['question']) ?></span>
                                <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m6 15 6-6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </summary>
                            <p class="mt-2 text-sm leading-6 text-slate-500"><?= htmlspecialchars($item['answer']) ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const search = document.getElementById('asm-faq-search');
                const items = Array.from(document.querySelectorAll('.asm-faq-item'));
                search?.addEventListener('input', () => {
                    const query = search.value.trim().toLowerCase();
                    items.forEach((item) => {
                        item.style.display = item.innerText.toLowerCase().includes(query) ? '' : 'none';
                    });
                });
            });
        </script>
        <?php return; ?>
    <?php endif; ?>

    <div class="space-y-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <?php foreach ($asmProducts as $product): ?>
                <?php $detailsUrl = '?section=sales-status&product=' . rawurlencode((string)$product['key']); ?>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="flex min-h-[118px] items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-500"><?= htmlspecialchars($product['name']) ?></p>
                            <p class="mt-1 text-2xl font-bold leading-tight text-slate-950"><?= htmlspecialchars($product['value']) ?></p>
                            <p class="mt-3 text-sm text-slate-500">
                                <span class="<?= $product['delta_positive'] ? 'text-emerald-600' : 'text-red-600' ?> font-semibold"><?= htmlspecialchars($product['delta']) ?></span>
                                <span>vs last month</span>
                            </p>
                        </div>
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-100 bg-white p-1.5 shadow-sm">
                            <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain <?= in_array($product['short'], ['S2S', 'BIDA'], true) ? 'dashboard-product-logo' : '' ?>">
                        </div>
                    </div>
                    <a href="<?= htmlspecialchars($detailsUrl) ?>" class="group flex h-12 items-center justify-between border-t border-slate-100 px-5 text-sm font-medium text-slate-950 transition-colors hover:bg-slate-50" aria-label="View <?= htmlspecialchars($product['name']) ?> sales status details">
                        <span>View Details</span>
                        <svg class="h-5 w-5 text-slate-500 transition-transform group-hover:translate-x-1 group-hover:text-slate-800" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
    <?php return; ?>
<?php endif; ?>

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
