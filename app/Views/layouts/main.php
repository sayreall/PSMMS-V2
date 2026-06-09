<?php use App\Helpers\Auth; ?>
<?php
$authUser = Auth::user();
$sidebarResolver = new \App\Models\DashboardRouteResolver();
// Resolve the signed-in user's dashboard path so the sidebar can match their actual role/position.
$sidebarDashboardPath = $sidebarResolver->resolvePath($authUser ?? []);
$sidebarDashboardSlug = basename($sidebarDashboardPath);
$sidebarDisplayRole = $sidebarResolver->resolveDisplayRole($authUser ?? []);
// Keep dashboard section URLs consistent for role dashboards that use /dashboard/{role}/{section}.
$dashboardSectionUrl = static function (string $dashboardPath, string $section): string {
    return App\Config\App::url(trim($dashboardPath, '/') . '/' . trim($section, '/'));
};
$sidebarSectionTitle = 'Main Menu';
$currentUserRole = strtolower(trim((string)($authUser['role'] ?? '')));
// These flags decide which role-specific sidebar menu should be rendered below.
$isSuperAdminSidebar = ($currentUserRole === 'super_admin' && $sidebarDashboardSlug === 'super-admin');
$isAdminSidebar = (
    in_array($sidebarDashboardSlug, ['admin', 'accounting'], true)
    || strpos($sidebarDashboardSlug, 'admin-') === 0
);
$isAsmSidebar = in_array($sidebarDashboardSlug, ['asm-manager', 'asm-super-manager', 'asm-area-sales-manager'], true);
$mainHeaderTitle = 'Dashboard';
$mainHeaderSection = '';
$headerProductOptions = [
    's2s' => ['name' => 'Surf2Sawa', 'short' => 'S2S', 'image' => 'images/icons/surf2sawa.png'],
    'fiberx' => ['name' => 'FiberX', 'short' => 'FIBERX', 'image' => 'images/icons/fiberx.png'],
    'bida' => ['name' => 'Bida', 'short' => 'BIDA', 'image' => 'images/icons/bida.png'],
    'sme' => ['name' => 'Converge SME', 'short' => 'SME', 'image' => 'images/icons/converge.png'],
];
$productPickerDashboardSlugs = ['admin-dispatcher', 'head-manager', 'asm-manager', 'asm-area-sales-manager'];
$showHeaderProductPicker = in_array($sidebarDashboardSlug, $productPickerDashboardSlugs, true);
$selectedHeaderProductKey = strtolower(trim((string)($_GET['product'] ?? 's2s')));
if (!isset($headerProductOptions[$selectedHeaderProductKey])) {
    $selectedHeaderProductKey = 's2s';
}
$selectedHeaderProduct = $headerProductOptions[$selectedHeaderProductKey];
$headerProductUrl = static function (string $productKey): string {
    $params = $_GET;
    $params['product'] = $productKey;
    $path = strtok((string)($_SERVER['REQUEST_URI'] ?? ''), '?') ?: '';
    return $path . '?' . http_build_query($params);
};
if ($sidebarDashboardSlug === 'asm-area-sales-manager') {
    // Area Sales Manager section titles shown in the top dashboard header.
    $asmHeaderSectionTitles = [
        '' => 'Overview',
        'assigning-area' => 'Assigning Area',
        'sub-agent-report' => 'Sub Agent Report',
        'sales-status' => 'Sales Status',
        'productivity-per-area' => 'Productivity Per Area',
        'eod-jo-area' => 'EOD JO Area',
        'sales-turn-ins' => 'Sales Turn-ins',
        'pending-job-order' => 'Pending Job Order',
        'partners-report' => 'Partners Report',
        'daily-sales-activation' => 'Daily Sales Activation',
        'tat-activation' => 'TAT Activation',
        'faq' => 'FAQ',
    ];
    $mainHeaderSection = strtolower(trim((string)($_GET['section'] ?? '')));
    $mainHeaderTitle = $asmHeaderSectionTitles[$mainHeaderSection] ?? $mainHeaderTitle;
} elseif ($sidebarDashboardSlug === 'head-manager') {
    // Head Manager section titles shown in the top dashboard header.
    $headManagerHeaderSectionTitles = [
        '' => 'Overview',
        'summary-report' => 'Summary Report',
        'monthly-sales-report' => 'Monthly Sales Report',
        'daily-tech-productivity' => 'Daily Tech Productivity',
        'technician-incentive' => 'Technician Incentive',
        'tech-team-activation' => 'Tech Team Activation',
        'technician-per-soc' => 'Technician Per SOC',
        'productivity-per-area' => 'Productivity Per Area',
        'pending-job-order' => 'Pending Job Order',
        'sales-turn-ins' => 'Sales Turn-ins',
        'daily-flow-thru' => 'Daily Flow Thru',
        'partners-report' => 'Partners Report',
        'daily-sales-activation' => 'Daily Sales Activation',
        'tat-activation' => 'TAT Activation',
        'faq' => 'FAQ',
    ];
    $mainHeaderSection = strtolower(trim((string)($_GET['section'] ?? '')));
    $mainHeaderTitle = $headManagerHeaderSectionTitles[$mainHeaderSection] ?? $mainHeaderTitle;
} elseif ($sidebarDashboardSlug === 'admin-dispatcher') {
    // Dispatcher-only section titles for the monitoring tabs under the dispatcher dashboard.
    $dispatcherHeaderSectionTitles = [
        '' => 'Operations Dashboard',
        'omd-monitoring' => 'OMD Monitoring',
        'dispatcher-monitoring' => 'Dispatch Monitoring',
        'assign-installer' => 'Assign Technician',
        'assign-technician' => 'Assign Technician',
        'regional-monitoring' => 'Regional Monitoring',
        'productivity-per-area' => 'Productivity Per Area',
        'daily-tech-productivity' => 'Daily Tech Productivity',
        'sales-turn-ins' => 'Sales Turn-ins',
        'daily-flow-thru' => 'Daily Flow Thru',
        'daily-sales-activation' => 'Daily Sales Activation',
        'pending-job-order' => 'Pending JO',
        'faq' => 'FAQ',
    ];
    $mainHeaderSection = strtolower(trim((string)($_GET['section'] ?? '')));
    $mainHeaderTitle = $dispatcherHeaderSectionTitles[$mainHeaderSection] ?? $mainHeaderTitle;
}
$hideMainHeaderTitle = $sidebarDashboardSlug === 'admin-dispatcher' && $mainHeaderSection !== '';

$menuItems = [];
if ($isSuperAdminSidebar) {
    // Super Admin gets full maintenance menus for users, sales records, addresses, dispatch records, and products.
    $menuItems[] = ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => App\Config\App::url($sidebarDashboardPath), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>' ];
    $menuItems[] = ['key' => 'admins', 'label' => 'Admins', 'url' => App\Config\App::url('admins'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>' ];
    $menuItems[] = ['key' => 'managers', 'label' => 'Managers', 'url' => App\Config\App::url('managers'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>' ];
    $menuItems[] = ['key' => 'inhouse', 'label' => 'In-House', 'url' => App\Config\App::url('inhouse'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>' ];
    $menuItems[] = ['key' => 'partners', 'label' => 'MSA Partners', 'url' => App\Config\App::url('partners'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>' ];
    $menuItems[] = ['key' => 'sme_sales', 'label' => 'SME Sales', 'url' => App\Config\App::url('sme-sales'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>' ];
    $menuItems[] = [
        'key' => 'asm_area_sales_manager',
        'label' => 'Area Sales Manager',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        'children' => [
            ['key' => 'asm_name', 'label' => 'ASM Name', 'url' => App\Config\App::url('asm/name'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>' ],
            ['key' => 'asm_per_area', 'label' => 'ASM Per Area', 'url' => App\Config\App::url('asm/per-area'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2l6 3 5.447-2.724A1 1 0 0121 5.382v10.764a1 1 0 01-.553.894L15 20l-6-3z"/>' ],
        ],
    ];
    $menuItems[] = [
        'key' => 'sales_record',
        'label' => 'Sales Record',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'children' => [
            ['key' => 'sales_category', 'label' => 'Sales Category', 'url' => App\Config\App::url('sales-category'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h12M3 17h8"/>' ],
            ['key' => 'sales_agent', 'label' => 'Sales Agent', 'url' => App\Config\App::url('sales-agent'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A10.97 10.97 0 0012 20c2.5 0 4.847-.83 6.879-2.196M15 11a3 3 0 10-6 0 3 3 0 006 0z"/>' ],
            ['key' => 'agent_code', 'label' => 'Agent Code', 'url' => App\Config\App::url('agent-code'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>' ],
        ],
    ];
    $menuItems[] = [
        'key' => 'address',
        'label' => 'Address',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'children' => [
            ['key' => 'address_region', 'label' => 'Region', 'url' => App\Config\App::url('address/region'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>' ],
            ['key' => 'address_province', 'label' => 'Province', 'url' => App\Config\App::url('address/province'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7"/>' ],
            ['key' => 'address_municipalities', 'label' => 'Municipalities', 'url' => App\Config\App::url('address/municipalities'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-6 9 6M5 10v9a1 1 0 001 1h4m4 0h4a1 1 0 001-1v-9"/>' ],
        ],
    ];
    $menuItems[] = [
        'key' => 'installers',
        'label' => 'Installers',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'children' => [
            ['key' => 'tech_data', 'label' => 'Tech Data', 'url' => App\Config\App::url('installers/tech-data'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m2 4H7a2 2 0 01-2-2V7a2 2 0 012-2h3l2 2h5a2 2 0 012 2v10a2 2 0 01-2 2z"/>' ],
            ['key' => 'tech_team_area', 'label' => 'Tech Team Area', 'url' => App\Config\App::url('installers/tech-team-area'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>' ],
        ],
    ];
    $menuItems[] = ['key' => 'plans', 'label' => 'Plans', 'url' => App\Config\App::url('plan'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>' ];
    $menuItems[] = [
        'key' => 'dispatch_record',
        'label' => 'Dispatch Record',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V5a2 2 0 012-2h9a2 2 0 012 2v12a2 2 0 01-2 2h-9a2 2 0 01-2-2zM9 17H5a2 2 0 01-2-2V9a2 2 0 012-2h4m0 10h4"/>' ,
        'children' => [
            ['key' => 'dispatch_status', 'label' => 'Dispatch Status', 'url' => App\Config\App::url('dispatch-record/dispatch-status'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' ],
            ['key' => 'dispatch_remarks', 'label' => 'Dispatch Remarks', 'url' => App\Config\App::url('dispatch-record/dispatch-remarks'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6M5 20l2-4h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12z"/>' ],
        ],
    ];
    $menuItems[] = ['key' => 'products', 'label' => 'Products', 'url' => App\Config\App::url('products'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>' ];
    $menuItems[] = ['key' => 'users', 'label' => 'User Management', 'url' => App\Config\App::url('users'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>' ];
} elseif (in_array($sidebarDashboardSlug, ['asm-manager', 'asm-area-sales-manager'], true)) {
    // ASM roles use report-style component tabs under their dashboard route.
    $sidebarSectionTitle = 'Components';
    $areaSalesManagerDashboardUrl = App\Config\App::url($sidebarDashboardPath);
    $menuItems[] = ['key' => 'dashboard', 'label' => 'Overview', 'url' => $areaSalesManagerDashboardUrl, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11.5L12 4l9 7.5M5 10v10h5v-6h4v6h5V10"/>'];
    $menuItems[] = ['key' => 'assigning_area', 'label' => 'Assigning Area', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'assigning-area'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2" stroke-width="2"/>'];
    $menuItems[] = ['key' => 'sub_agent_report', 'label' => 'Sub Agent Report', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'sub-agent-report'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 012-2z"/>'];
    $menuItems[] = ['key' => 'sales_status', 'label' => 'Sales Status', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'sales-status'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>'];
    $menuItems[] = ['key' => 'productivity_per_area', 'label' => 'Productivity Per Area', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'productivity-per-area'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>'];
    $menuItems[] = ['key' => 'eod_jo_area', 'label' => 'EOD JO Area', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'eod-jo-area'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17V9m4 8V5m4 12v-6M5 21h14"/>'];
    $menuItems[] = ['key' => 'sales_turn_ins', 'label' => 'Sales Turn-ins', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'sales-turn-ins'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'];
    $menuItems[] = ['key' => 'pending_job_order', 'label' => 'Pending Job Order', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'pending-job-order'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>'];
    $menuItems[] = ['key' => 'partners_report', 'label' => 'Partners Report', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'partners-report'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l2 13h11l2-9H7m3 13a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>'];
    $menuItems[] = ['key' => 'daily_sales_activation', 'label' => 'Daily Sales Activation', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'daily-sales-activation'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h11m0 0l-3-3m3 3l-3 3M20 17H9m0 0l3-3m-3 3l3 3"/>'];
    $menuItems[] = ['key' => 'tat_activation', 'label' => 'TAT Activation', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'tat-activation'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7m11-7l-4 4-2-2"/>'];
    $menuItems[] = ['key' => 'faq', 'label' => 'FAQ', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'faq'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5M4 6h16M4 18h16"/>'];
} elseif ($sidebarDashboardSlug === 'head-manager') {
    // Head Manager uses the wider management report set, including installer and productivity reports.
    $sidebarSectionTitle = 'Components';
    $headManagerDashboardUrl = App\Config\App::url($sidebarDashboardPath);
    $menuItems[] = ['key' => 'dashboard', 'label' => 'Overview', 'url' => $headManagerDashboardUrl, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11.5L12 4l9 7.5M5 10v10h5v-6h4v6h5V10"/>'];
    $menuItems[] = ['key' => 'summary_report', 'label' => 'Summary Report', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'summary-report'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>'];
    $menuItems[] = ['key' => 'monthly_sales_report', 'label' => 'Monthly Sales Report', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'monthly-sales-report'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>'];
    $menuItems[] = [
        'key' => 'installer_report',
        'label' => 'Installer Report',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m2 4H7a2 2 0 01-2-2V7a2 2 0 012-2h3l2 2h5a2 2 0 012 2v10a2 2 0 01-2 2z"/>',
        'children' => [
            ['key' => 'daily_tech_productivity', 'label' => 'Daily Tech Productivity', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'daily-tech-productivity'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01"/>'],
            ['key' => 'technician_incentive', 'label' => 'Technician Incentive', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'technician-incentive'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2"/>'],
            ['key' => 'tech_team_activation', 'label' => 'Tech Team Activation', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'tech-team-activation'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
            ['key' => 'technician_per_soc', 'label' => 'Technician Per SOC', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'technician-per-soc'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7m11-7l-4 4-2-2"/>'],
        ],
    ];
    $menuItems[] = ['key' => 'productivity_per_area', 'label' => 'Productivity Per Area', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'productivity-per-area'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2" stroke-width="2"/>'];
    $menuItems[] = ['key' => 'pending_job_order', 'label' => 'Pending Job Order', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'pending-job-order'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>'];
    $menuItems[] = ['key' => 'sales_turn_ins', 'label' => 'Sales Turn-ins', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'sales-turn-ins'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l2 13h11l2-9H7m3 13a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>'];
    $menuItems[] = ['key' => 'daily_flow_thru', 'label' => 'Daily Flow Thru', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'daily-flow-thru'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h11m0 0l-3-3m3 3l-3 3M20 17H9m0 0l3-3m-3 3l3 3"/>'];
    $menuItems[] = ['key' => 'partners_report', 'label' => 'Partners Report', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'partners-report'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M12 12a4 4 0 100-8 4 4 0 000 8z"/>'];
    $menuItems[] = ['key' => 'daily_sales_activation', 'label' => 'Daily Sales Activation', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'daily-sales-activation'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7m11-7l-4 4-2-2"/>'];
    $menuItems[] = ['key' => 'tat_activation', 'label' => 'TAT Activation', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'tat-activation'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'];
    $menuItems[] = ['key' => 'faq', 'label' => 'FAQ', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'faq'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5M4 6h16M4 18h16"/>'];
} elseif ($isAdminSidebar) {
    // Admin-position users share the operations dashboard entry, then receive position-specific tabs.
    $menuItems[] = ['key' => 'dashboard', 'label' => 'Operations Dashboard', 'url' => App\Config\App::url($sidebarDashboardPath), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>' ];
    if ($sidebarDashboardSlug === 'admin-dispatcher') {
        // Dispatcher-specific tabs live under /dashboard/admin-dispatcher/{section}.
        $menuItems[] = ['key' => 'omd_monitoring', 'label' => 'OMD Monitoring', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'omd-monitoring'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/>'];
        $menuItems[] = ['key' => 'dispatcher_monitoring', 'label' => 'Dispatch Monitoring', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'dispatcher-monitoring'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16M4 12h16M4 19h16M8 5v14M16 5v14"/>'];
        $menuItems[] = ['key' => 'assign_technician', 'label' => 'Assign Technician', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'assign-technician'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c4.418 0 8 1.343 8 3s-3.582 3-8 3-8-1.343-8-3 3.582-3 8-3zM4 6v6c0 1.657 3.582 3 8 3s8-1.343 8-3V6M4 12v6c0 1.657 3.582 3 8 3s8-1.343 8-3v-6"/>'];
        $menuItems[] = ['key' => 'regional_monitoring', 'label' => 'Regional Monitoring', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'regional-monitoring'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>'];
        $menuItems[] = ['key' => 'productivity_per_area', 'label' => 'Productivity Per Area', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'productivity-per-area'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17V9m4 8V5m4 12v-6M5 21h14"/>'];
        $menuItems[] = ['key' => 'daily_flow_thru', 'label' => 'Daily Flow Thru', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'daily-flow-thru'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'];
        $menuItems[] = ['key' => 'daily_sales_activation', 'label' => 'Daily Sales Activation', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'daily-sales-activation'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>'];
        $menuItems[] = ['key' => 'sales_turn_ins', 'label' => 'Sales Turn-ins', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'sales-turn-ins'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l2 13h11l2-9H7m3 13a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>'];
        $menuItems[] = ['key' => 'pending_job_order', 'label' => 'Pending JO', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'pending-job-order'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h13m-3-3l4 3-4 3M4 17h13m-3-3l4 3-4 3"/>'];
        $menuItems[] = ['key' => 'daily_tech_productivity', 'label' => 'Daily Tech Productivity', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'daily-tech-productivity'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'];
        $menuItems[] = ['key' => 'faq', 'label' => 'FAQ', 'url' => $dashboardSectionUrl($sidebarDashboardPath, 'faq'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M8 6v12"/>'];
    }
} elseif ($isAsmSidebar) {
    // Fallback ASM sidebar for ASM dashboard slugs that do not use the component-tab layout above.
    $menuItems[] = ['key' => 'dashboard', 'label' => $sidebarDisplayRole . ' Dashboard', 'url' => App\Config\App::url($sidebarDashboardPath), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>' ];
} else {
    // General fallback for roles without a custom sidebar definition.
    $menuItems[] = ['key' => 'dashboard', 'label' => 'Overview', 'url' => App\Config\App::url($sidebarDashboardPath), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>' ];
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? 'PSMMS Dashboard' ?> | PSMMS</title>
    <meta name="description" content="PSMMS Dashboard System">
    <meta name="theme-color" content="#0f766e">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? '') ?>">

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@100..900&display=swap" rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="<?= App\Config\App::url('css/app.css') ?>">

    <!-- Alpine.js for reactive UI -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Toast Notifications CSS -->
    <link rel="stylesheet" href="<?= App\Config\App::url('css/toast.css') ?>">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@1.13.8/css/jquery.dataTables.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= App\Config\App::url('images/small%20icon%201.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= App\Config\App::url('images/small%20icon%201.png') ?>">

    <?= $styles ?? '' ?>
</head>
<body class="bg-slate-50 text-slate-900 antialiased font-sans" x-data="appState()" data-base-url="<?= App\Config\App::url('') ?>">

    <!-- Ambient Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-primary-200/40 blur-3xl"></div>
        <div class="absolute top-1/3 -left-40 h-96 w-96 rounded-full bg-cyan-200/30 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 h-72 w-72 rounded-full bg-slate-200/40 blur-3xl"></div>
    </div>

    <!-- Page Loader -->
    <div id="page-loader" class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-300">
        <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-primary-600 font-medium text-sm">Loading...</span>
        </div>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-72 h-screen transition-transform duration-300 lg:translate-x-0 -translate-x-full bg-slate-50 border-r border-slate-200 flex flex-col">

        <!-- Logo -->
        <div class="sidebar-header h-20 border-b border-slate-200 px-4 flex items-center justify-between">
            <button type="button" class="sidebar-logo-button" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <img src="<?= App\Config\App::url('images/paragon.png') ?>" alt="Paragon Communications Corp." class="sidebar-logo h-14 w-auto max-w-[190px] object-contain" />
            </button>
            <button class="sidebar-collapse-button p-2 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" onclick="toggleSidebar()" aria-label="Collapse sidebar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav flex-1 overflow-y-auto py-4 px-3 space-y-2">
            <div class="sidebar-search px-1 mb-3">
                <div class="relative group">
                    <input type="text" placeholder="Search"
                           class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg bg-white text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-shadow">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Main Section -->
            <p class="sidebar-section-title text-[10px] font-semibold text-slate-400 uppercase tracking-[0.14em] px-3 mb-1"><?= htmlspecialchars($sidebarSectionTitle) ?></p>

            <?php foreach ($menuItems as $item): ?>
                <?php if (!empty($item['children']) && is_array($item['children'])): ?>
                    <?php
                        $childKeys = array_map(static fn(array $child): string => (string)($child['key'] ?? ''), $item['children']);
                        $isChildActive = in_array((string)($activeRoute ?? ''), $childKeys, true);
                    ?>
                    <details class="group" <?= $isChildActive ? 'open' : '' ?>>
                        <summary class="list-none cursor-pointer nav-link has-children flex items-center justify-between gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-700 rounded-lg hover:bg-slate-100 transition-colors duration-200 <?= $isChildActive ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' ?>"
                                 data-label="<?= htmlspecialchars($item['label']) ?>">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
                                <span class="nav-label"><?= htmlspecialchars($item['label']) ?></span>
                            </span>
                            <svg class="submenu-chevron w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="submenu-panel mt-1 ml-7 flex flex-col gap-1">
                            <?php foreach ($item['children'] as $child): ?>
                                          <a href="<?= htmlspecialchars($child['url']) ?>"
                                              title="<?= htmlspecialchars($child['label'] ?? '') ?>"
                                              data-label="<?= htmlspecialchars($child['label'] ?? '') ?>"
                                   class="nav-link px-3 py-2 text-[12px] font-medium rounded-lg transition-colors duration-200 <?= ($activeRoute ?? '') === ($child['key'] ?? '') ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'text-slate-600 hover:bg-slate-100' ?>">
                                    <span class="submenu-icon" aria-hidden="true">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?= $child['icon'] ?? '<circle cx="12" cy="12" r="3" stroke-width="2" />' ?>
                                        </svg>
                                    </span>
                                    <span class="nav-label"><?= htmlspecialchars($child['label'] ?? '') ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php else: ?>
                          <a href="<?= htmlspecialchars($item['url']) ?>"
                              data-label="<?= htmlspecialchars($item['label']) ?>"
                              class="nav-link flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-700 rounded-lg hover:bg-slate-100 transition-colors duration-200 <?= ($activeRoute ?? '') === $item['key'] ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
                        <span class="nav-label"><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>

        </nav>

        <div class="border-t border-slate-200 p-3">
            <form method="POST" action="<?= App\Config\App::url('logout') ?>" id="logout-form" class="contents">
                <?= \App\Helpers\Csrf::field(); ?>
                     <button type="submit"
                   onclick="return confirmLogout(event)"
                         data-label="Logout"
                         class="w-full flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-700 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="nav-label">Logout</span>
                </button>
            </form>
        </div>
    </aside>
    <div id="sidebar-tooltip" class="sidebar-tooltip" role="tooltip" aria-hidden="true"></div>

    <!-- Main Content -->
    <div class="main-content lg:pl-72 min-h-screen flex flex-col">

        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
            <div class="flex h-20 items-center justify-between px-4 md:px-6">
                <!-- Mobile Menu Button -->
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex min-w-0 flex-1 items-center">
                    <?php if (!$isSuperAdminSidebar): ?>
                        <div class="flex min-w-0 items-center gap-3">
                            <?php if (!$hideMainHeaderTitle): ?>
                                <h1 class="truncate text-2xl font-semibold leading-none text-slate-950 md:text-[28px]"><?= htmlspecialchars($mainHeaderTitle) ?></h1>
                            <?php endif; ?>
                            <?php if ($showHeaderProductPicker): ?>
                                <details class="group relative shrink-0">
                                    <summary style="list-style: none; width: 128px; height: 54px; overflow: hidden;" class="flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-transparent px-1 py-0 transition-colors hover:bg-slate-50/70 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                                        <span class="flex min-w-0 flex-1 items-center justify-center overflow-hidden" style="height: 50px;">
                                            <img src="<?= App\Config\App::url($selectedHeaderProduct['image']) ?>" alt="<?= htmlspecialchars($selectedHeaderProduct['name']) ?>" style="display: block; max-width: 96px; max-height: 46px; width: auto; height: auto; object-fit: contain;">
                                        </span>
                                        <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </summary>
                                    <div class="absolute left-0 top-full z-40 mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 shadow-xl">
                                        <?php foreach ($headerProductOptions as $productKey => $product): ?>
                                            <?php $isSelectedHeaderProduct = $selectedHeaderProductKey === $productKey; ?>
                                            <a href="<?= htmlspecialchars($headerProductUrl($productKey)) ?>"
                                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold transition-colors <?= $isSelectedHeaderProduct ? 'bg-cyan-50 text-primary-700' : 'text-slate-700 hover:bg-slate-50' ?>">
                                                <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg border <?= $isSelectedHeaderProduct ? 'border-primary-100 bg-white' : 'border-slate-100 bg-slate-50' ?> p-1.5">
                                                    <img src="<?= App\Config\App::url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain">
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="block truncate"><?= htmlspecialchars($product['name']) ?></span>
                                                    <span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400"><?= htmlspecialchars($product['short']) ?></span>
                                                </span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </details>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="toggleTheme()" class="hidden md:inline-flex items-center justify-center w-9 h-9 rounded-full border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Toggle theme">
                        <svg id="theme-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    <button class="relative inline-flex items-center justify-center w-9 h-9 rounded-full border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors" onclick="toggleNotifications()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                    </button>

                    <?php
                        $firstName = trim((string)($authUser['first_name'] ?? ''));
                        $lastName = trim((string)($authUser['last_name'] ?? ''));
                        $fullName = trim($firstName . ' ' . $lastName);
                        $fallbackName = trim((string)($authUser['name'] ?? $authUser['email'] ?? 'User'));
                        $profileName = $fullName !== '' ? $fullName : $fallbackName;
                        $profileInitial = strtoupper(substr($profileName !== '' ? $profileName : 'U', 0, 1));
                        $profileRole = $sidebarResolver->resolveDisplayRole($authUser ?? []);
                    ?>
                    <div class="profile-menu" data-profile-menu>
                        <button type="button" class="profile-trigger" data-profile-trigger aria-haspopup="true" aria-expanded="false">
                            <span class="profile-avatar">
                                <img src="<?= App\Config\App::url('images/profile%20pic.png') ?>" alt="Profile" class="w-full h-full object-cover">
                            </span>
                            <span class="profile-summary">
                                <span class="profile-name"><?= htmlspecialchars($profileName !== '' ? $profileName : 'User') ?></span>
                                <span class="profile-role"><?= htmlspecialchars($profileRole) ?></span>
                            </span>
                        </button>
                        <div class="profile-dropdown" role="menu" aria-label="Profile menu">
                            <div class="profile-dropdown-header">
                                <span class="profile-dropdown-name"><?= htmlspecialchars($profileName !== '' ? $profileName : 'User') ?></span>
                                <span class="profile-dropdown-role"><?= htmlspecialchars($profileRole) ?></span>
                            </div>
                            <a href="<?= App\Config\App::url('dashboard/profile/view') ?>" class="profile-dropdown-item" role="menuitem">View profile</a>
                            <form method="POST" action="<?= App\Config\App::url('logout') ?>" class="profile-dropdown-form" role="menuitem">
                                <?= \App\Helpers\Csrf::field(); ?>
                                <button type="submit" class="profile-dropdown-item profile-dropdown-logout" onclick="return confirmLogout(event)">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex h-14 items-center border-t border-slate-200/80 px-4 md:px-6">
                <a href="<?= App\Config\App::url($sidebarDashboardPath) ?>"
                   class="inline-flex h-9 items-center gap-2 rounded-md border border-slate-200 bg-white px-3 text-xs font-medium text-slate-600 shadow-sm transition-colors hover:bg-slate-50 hover:text-slate-800 <?= ($activeRoute ?? '') === 'dashboard' ? 'border-slate-200 bg-slate-50 text-slate-700' : '' ?>">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M4 19h16M6 7v10m12-10v10"/>
                    </svg>
                    <span>Overview</span>
                </a>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 p-4 md:p-6 lg:p-8">
            <?php echo $content ?? ''; ?>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

    <!-- Modal Container -->
    <div id="modal-container" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div id="modal-content" class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto transform transition-all scale-95 opacity-0"></div>
    </div>

    <!-- Confirmation Dialog -->
    <div id="confirm-dialog" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeConfirm()"></div>
        <div class="confirm-card bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-slate-900 mb-2" id="confirm-title">Confirm</h3>
            <p class="text-slate-500 text-sm mb-6" id="confirm-message">Are you sure?</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeConfirm()" class="confirm-cancel px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">Cancel</button>
                <button id="confirm-btn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="<?= App\Config\App::url('js/app.js') ?>"></script>
    <script src="<?= App\Config\App::url('js/toast.js') ?>"></script>
    <script src="<?= App\Config\App::url('js/modal.js') ?>"></script>
    <script src="<?= App\Config\App::url('js/search.js') ?>"></script>

    <?= $scripts ?? '' ?>

    <?php if (!empty($flash['message'])): ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof showToast === 'function') {
                showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']) ?>);
            }
        });
    </script>
    <?php endif; ?>

    <script>
        // Auto-hide loader
        window.addEventListener('load', () => {
            document.getElementById('page-loader').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('page-loader').classList.add('hidden');
            }, 300);
        });
    </script>
</body>
</html>
