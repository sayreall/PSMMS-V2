<?php use App\Helpers\Auth; ?>
<?php
$authUser = Auth::user();
$sidebarResolver = new \App\Models\DashboardRouteResolver();
$sidebarDashboardPath = $sidebarResolver->resolvePath($authUser ?? []);
$sidebarDashboardSlug = basename($sidebarDashboardPath);
$currentUserRole = strtolower(trim((string)($authUser['role'] ?? '')));
$isSuperAdminSidebar = ($currentUserRole === 'super_admin' && $sidebarDashboardSlug === 'super-admin');
$isAdminSidebar = in_array($sidebarDashboardSlug, ['admin', 'accounting'], true);
$isAsmSidebar = in_array($sidebarDashboardSlug, ['asm-manager', 'asm-super-manager', 'asm-area-sales-manager', 'asm-head-manager'], true);

$menuItems = [];
if ($isSuperAdminSidebar) {
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
            ['key' => 'asm_name', 'label' => 'ASM Name', 'url' => App\Config\App::url('asm/name')],
            ['key' => 'asm_per_area', 'label' => 'ASM Per Area', 'url' => App\Config\App::url('asm/per-area')],
        ],
    ];
    $menuItems[] = [
        'key' => 'sales_record',
        'label' => 'Sales Record',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'children' => [
            ['key' => 'sales_category', 'label' => 'Sales Category', 'url' => App\Config\App::url('sales-category')],
            ['key' => 'sales_agent', 'label' => 'Sales Agent', 'url' => App\Config\App::url('sales-agent')],
            ['key' => 'agent_code', 'label' => 'Agent Code', 'url' => App\Config\App::url('agent-code')],
        ],
    ];
    $menuItems[] = [
        'key' => 'address',
        'label' => 'Address',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'children' => [
            ['key' => 'address_region', 'label' => 'Region', 'url' => App\Config\App::url('address/region')],
            ['key' => 'address_province', 'label' => 'Province', 'url' => App\Config\App::url('address/province')],
            ['key' => 'address_municipalities', 'label' => 'Municipalities', 'url' => App\Config\App::url('address/municipalities')],
        ],
    ];
    $menuItems[] = [
        'key' => 'installers',
        'label' => 'Installers',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.607 2.296.07 2.572-1.065z"/>',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'children' => [
            ['key' => 'add_installer', 'label' => 'Add Installer', 'url' => App\Config\App::url('installers/create')],
            ['key' => 'tech_data', 'label' => 'Tech Data', 'url' => App\Config\App::url('installers/tech-data')],
            ['key' => 'tech_team_area', 'label' => 'Tech Team Area', 'url' => App\Config\App::url('installers/tech-team-area')],
        ],
    ];
    $menuItems[] = ['key' => 'plans', 'label' => 'Plans', 'url' => App\Config\App::url('plan'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>' ];
    $menuItems[] = [
        'key' => 'dispatch_record',
        'label' => 'Dispatch Record',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V5a2 2 0 012-2h9a2 2 0 012 2v12a2 2 0 01-2 2h-9a2 2 0 01-2-2zM9 17H5a2 2 0 01-2-2V9a2 2 0 012-2h4m0 10h4"/>' ,
        'children' => [
            ['key' => 'dispatch_status', 'label' => 'Dispatch Status', 'url' => App\Config\App::url('dispatch-record/dispatch-status')],
            ['key' => 'dispatch_remarks', 'label' => 'Dispatch Remarks', 'url' => App\Config\App::url('dispatch-record/dispatch-remarks')],
        ],
    ];
    $menuItems[] = ['key' => 'products', 'label' => 'Products', 'url' => App\Config\App::url('products'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>' ];
    $menuItems[] = ['key' => 'users', 'label' => 'User Management', 'url' => App\Config\App::url('users'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>' ];
} elseif ($isAdminSidebar) {
    $menuItems[] = ['key' => 'dashboard', 'label' => 'Operations Dashboard', 'url' => App\Config\App::url($sidebarDashboardPath), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>' ];
} elseif ($isAsmSidebar) {
    $menuItems[] = ['key' => 'dashboard', 'label' => 'ASM Dashboard', 'url' => App\Config\App::url($sidebarDashboardPath), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>' ];
} else {
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: {'50':'#ecfeff','100':'#cffafe','200':'#a5f3fc','300':'#67e8f9','400':'#22d3ee','500':'#06b6d4','600':'#0891b2','700':'#0e7490','800':'#155e75','900':'#164e63'},
                        secondary: {'500':'#0f172a','600':'#0b1324','700':'#0a1220','800':'#111827','900':'#0b1120'},
                    }
                }
            }
        };
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= App\Config\App::url('css/app.css') ?>">

    <!-- Alpine.js for reactive UI -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Toast Notifications CSS -->
    <link rel="stylesheet" href="<?= App\Config\App::url('css/toast.css') ?>">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@1.13.8/css/jquery.dataTables.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= App\Config\App::url('images/favicon.svg') ?>">

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
        <div class="h-16 border-b border-slate-200 px-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-primary-700 font-bold text-sm">P</div>
                <div>
                    <p class="text-[15px] font-extrabold text-primary-700 leading-none tracking-wide">PSMMS</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Dashboard System</p>
                </div>
            </div>
            <button class="p-2 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" onclick="toggleSidebar()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-2">
            <div class="px-1 mb-3">
                <div class="relative group">
                    <input type="text" placeholder="Search"
                           class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg bg-white text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-shadow">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Main Section -->
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.14em] px-3 mb-1">Main Menu</p>

            <?php foreach ($menuItems as $item): ?>
                <?php if (!empty($item['children']) && is_array($item['children'])): ?>
                    <?php
                        $childKeys = array_map(static fn(array $child): string => (string)($child['key'] ?? ''), $item['children']);
                        $isChildActive = in_array((string)($activeRoute ?? ''), $childKeys, true);
                    ?>
                    <details class="group" <?= $isChildActive ? 'open' : '' ?>>
                        <summary class="list-none cursor-pointer nav-link flex items-center justify-between gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-700 rounded-lg hover:bg-slate-100 transition-colors duration-200 <?= $isChildActive ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' ?>">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
                                <?= htmlspecialchars($item['label']) ?>
                            </span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="mt-1 ml-7 flex flex-col gap-1">
                            <?php foreach ($item['children'] as $child): ?>
                                <a href="<?= htmlspecialchars($child['url']) ?>"
                                   class="nav-link px-3 py-2 text-[12px] font-medium rounded-lg transition-colors duration-200 <?= ($activeRoute ?? '') === ($child['key'] ?? '') ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'text-slate-600 hover:bg-slate-100' ?>">
                                    <?= htmlspecialchars($child['label'] ?? '') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       class="nav-link flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-700 rounded-lg hover:bg-slate-100 transition-colors duration-200 <?= ($activeRoute ?? '') === $item['key'] ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Divider -->
            <hr class="my-3 border-slate-200">

            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.14em] px-3 mb-1">Account</p>

            <a href="<?= App\Config\App::url('dashboard/profile') ?>"
               class="nav-link flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-700 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.607 2.296.07 2.572-1.065z"/></svg>
                Settings
            </a>
        </nav>

        <div class="border-t border-slate-200 p-3">
            <form method="POST" action="<?= App\Config\App::url('logout') ?>" id="logout-form" class="contents">
                <?= \App\Helpers\Csrf::field(); ?>
                <button type="submit"
                   onclick="return confirmLogout(event)"
                   class="w-full flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-slate-700 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-72 min-h-screen flex flex-col">

        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
            <div class="flex items-center justify-between px-4 py-3">
                <!-- Mobile Menu Button -->
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex-1 max-w-xl">
                    <div class="relative group">
                        <input type="text" id="global-search" placeholder="Search users, records..."
                            class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-lg bg-white/80 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-shadow"
                            autocomplete="off">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="toggleTheme()" class="hidden md:inline-flex items-center justify-center w-9 h-9 rounded-full border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Toggle theme">
                        <svg id="theme-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    <button class="relative inline-flex items-center justify-center w-9 h-9 rounded-full border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors" onclick="toggleNotifications()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                    </button>

                    <a href="<?= App\Config\App::url('dashboard/profile') ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-full overflow-hidden border border-slate-200 bg-slate-100 hover:ring-2 hover:ring-slate-200 transition-all" title="Profile">
                        <?php if (!empty($authUser['avatar'])): ?>
                            <img src="<?= App\Config\App::url('uploads/' . $authUser['avatar']) ?>" alt="Profile" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="text-xs font-semibold text-slate-700"><?= strtoupper(substr($authUser['name'] ?? 'U', 0, 1)) ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- Mobile Search -->
                    <button class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100" onclick="document.getElementById('global-search').focus()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
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
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-slate-900 mb-2" id="confirm-title">Confirm</h3>
            <p class="text-slate-500 text-sm mb-6" id="confirm-message">Are you sure?</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeConfirm()" class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">Cancel</button>
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
