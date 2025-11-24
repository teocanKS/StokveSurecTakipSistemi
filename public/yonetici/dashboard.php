<?php
/**
 * Yönetici Dashboard
 *
 * Anasayfa - Gelişmiş KPI kartları, aktivite akışı, sistem durumu
 */

// Auth kontrolü - Sadece yönetici
$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
require_once __DIR__ . '/../../src/auth/middleware.php';

setPageTitle('Yönetim Paneli', 'Sistem genel bakış ve istatistikler');

// CSRF token
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrfToken; ?>">
    <title><?php echo getPageTitle(); ?> | <?php echo APP_NAME; ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Yönetici Panel - Koyu Tema */
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }

        /* Custom scrollbar for dark theme */
        .dark-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .dark-scrollbar::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .dark-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }
        .dark-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50">

    <!-- Layout Container -->
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar (Yönetici - Koyu Tema) -->
        <aside id="sidebar" class="w-64 sidebar-gradient text-white flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 z-30 dark-scrollbar">
            <!-- Logo & Brand -->
            <div class="px-6 py-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">Doğu AŞ</h1>
                        <p class="text-xs text-orange-300">Yönetici Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto dark-scrollbar">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-500/20 text-white font-medium border border-orange-500/30">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Anasayfa</span>
                </a>

                <a href="stok.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Stok Yönetimi</span>
                </a>

                <a href="aktif-isler.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Aktif İşler</span>
                </a>

                <a href="gecmis.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Geçmiş</span>
                </a>

                <div class="pt-2 border-t border-white/10 mt-2">
                    <a href="kullanici-yonetimi.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Kullanıcı Yönetimi</span>
                    </a>
                </div>
            </nav>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-white/10">
                <div class="flex items-center justify-between text-sm text-slate-400">
                    <span>v1.0.0</span>
                    <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        <span>Çevrimiçi</span>
                    </span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button -->
                        <button data-sidebar-toggle class="lg:hidden text-slate-600 hover:text-slate-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Page title -->
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800"><?php echo getPageTitle(); ?></h2>
                            <?php if (getPageSubtitle()): ?>
                            <p class="text-sm text-slate-600"><?php echo getPageSubtitle(); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Current time -->
                        <div class="hidden md:flex items-center space-x-2 text-sm text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="currentTime"><?php echo date('H:i'); ?></span>
                        </div>

                        <!-- User menu -->
                        <div class="flex items-center space-x-3 pl-4 border-l border-slate-200">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-semibold text-slate-800"><?php echo escapeHtml($currentUser['full_name']); ?></p>
                                <p class="text-xs text-orange-600 font-medium capitalize"><?php echo escapeHtml($currentUser['role']); ?></p>
                            </div>
                            <button data-logout class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-6">
                <!-- Welcome Card - Dark gradient -->
                <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-8 mb-6 text-white shadow-lg border border-orange-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Merhaba, <?php echo escapeHtml($currentUser['name']); ?>! 👋</h3>
                            <p class="text-slate-300">Bugün <?php echo formatTarih(date('Y-m-d'), 'long'); ?></p>
                        </div>
                        <div class="hidden lg:flex items-center space-x-2 bg-orange-500/10 px-4 py-2 rounded-lg border border-orange-500/30">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="text-orange-300 font-medium">Yönetici Yetkisi</span>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards - Enhanced for manager view -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" id="kpiCards">
                    <!-- KPI cards will be loaded here via JavaScript -->
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-8 bg-slate-200 rounded w-3/4"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-8 bg-slate-200 rounded w-3/4"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-8 bg-slate-200 rounded w-3/4"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-8 bg-slate-200 rounded w-3/4"></div>
                    </div>
                </div>

                <!-- Secondary KPI Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="secondaryKpiCards">
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-6 bg-slate-200 rounded w-2/3"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-6 bg-slate-200 rounded w-2/3"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-6 bg-slate-200 rounded w-2/3"></div>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Son İşlemler -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-slate-800">Son İşlemler</h3>
                            <a href="aktif-isler.php" class="text-orange-600 hover:text-orange-700 text-sm font-medium">Tümünü Gör →</a>
                        </div>

                        <div id="recentOperations">
                            <!-- Loading state -->
                            <div class="space-y-3">
                                <div class="animate-pulse flex items-center space-x-4 p-4 bg-slate-50 rounded-lg">
                                    <div class="h-12 w-12 bg-slate-200 rounded-lg"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kritik Stoklar -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-slate-800">Kritik Stoklar</h3>
                            <a href="stok.php?kritik=true" class="text-orange-600 hover:text-orange-700 text-sm font-medium">Tümünü Gör →</a>
                        </div>

                        <div id="kritikStoklar">
                            <!-- Loading state -->
                            <div class="space-y-3">
                                <div class="animate-pulse flex items-center justify-between p-4 bg-slate-50 rounded-lg">
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                                        <div class="h-3 bg-slate-200 rounded w-1/3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="../js/common.js"></script>
    <script>
        // KPI Kartlarını yükle
        async function loadDashboardStats() {
            try {
                const response = await apiGet('stats.php');
                const stats = response.data;

                // Primary KPI Cards
                const kpiCardsHTML = `
                    <!-- Toplam Ürün -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition border-l-4 border-orange-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mb-1">Toplam Ürün</p>
                        <p class="text-3xl font-bold text-slate-900">${formatSayi(stats.stok.toplam_urun)}</p>
                        <p class="text-xs text-slate-500 mt-2">Sistemdeki toplam ürün çeşidi</p>
                    </div>

                    <!-- Kritik Stok -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition border-l-4 border-red-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mb-1">Kritik Stok</p>
                        <p class="text-3xl font-bold text-red-600">${formatSayi(stats.kritik_stok)}</p>
                        <p class="text-xs text-slate-500 mt-2">Acil tedarik gerekebilir</p>
                    </div>

                    <!-- Bugünkü İşlemler -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition border-l-4 border-green-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mb-1">Bugünkü İşlemler</p>
                        <p class="text-3xl font-bold text-slate-900">${formatSayi(stats.bugunun_islemleri)}</p>
                        <p class="text-xs text-slate-500 mt-2">Alış ve satış toplamı</p>
                    </div>

                    <!-- Bu Ay Satış -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition border-l-4 border-purple-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mb-1">Bu Ay Satış</p>
                        <p class="text-2xl font-bold text-slate-900">${formatPara(stats.bu_ay_satis)}</p>
                        <p class="text-xs text-slate-500 mt-2">Aylık satış cirosu</p>
                    </div>
                `;

                document.getElementById('kpiCards').innerHTML = kpiCardsHTML;

                // Secondary KPI Cards
                const secondaryKpiHTML = `
                    <!-- Bu Ay Alış -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-600">Bu Ay Alış</p>
                                <p class="text-xl font-bold text-slate-900">${formatPara(stats.bu_ay_alis)}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Son 7 Gün Satış -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-600">Son 7 Gün Satış</p>
                                <p class="text-xl font-bold text-slate-900">${formatPara(stats.son_7_gun_satis)}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Toplam Stok Değeri -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-600">Toplam Stok</p>
                                <p class="text-xl font-bold text-slate-900">${formatSayi(stats.stok.toplam_stok_miktar)}</p>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('secondaryKpiCards').innerHTML = secondaryKpiHTML;

            } catch (error) {
                console.error('KPI yükleme hatası:', error);
                showNotification('İstatistikler yüklenirken bir hata oluştu', 'error');
            }
        }

        // Son işlemleri yükle
        async function loadRecentOperations() {
            try {
                const response = await apiGet('operations.php', { filter: 'aktif' });
                const operations = response.data.slice(0, 5); // İlk 5 kayıt

                if (operations.length === 0) {
                    document.getElementById('recentOperations').innerHTML = `
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-sm">Henüz aktif işlem bulunmuyor</p>
                        </div>
                    `;
                    return;
                }

                const operationsHTML = operations.map(op => {
                    const tipIcon = op.tip === 'satis' ?
                        '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>' :
                        '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>';

                    return `
                        <div class="flex items-center space-x-4 p-4 hover:bg-slate-50 rounded-lg transition border-l-4 ${op.tip === 'satis' ? 'border-green-500' : 'border-blue-500'}">
                            <div class="w-12 h-12 ${op.tip === 'satis' ? 'bg-green-100' : 'bg-blue-100'} rounded-lg flex items-center justify-center">
                                ${tipIcon}
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">${op.firma || '-'}</p>
                                <p class="text-sm text-slate-600">${op.urun || '-'} · ${op.adet ? formatSayi(op.adet) + ' adet' : '-'}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-900">${formatPara(op.tutar)}</p>
                                <p class="text-xs text-slate-500">${formatTarih(op.tarih)}</p>
                            </div>
                        </div>
                    `;
                }).join('');

                document.getElementById('recentOperations').innerHTML = operationsHTML;

            } catch (error) {
                console.error('Son işlemler yükleme hatası:', error);
            }
        }

        // Kritik stokları yükle
        async function loadKritikStoklar() {
            try {
                const response = await apiGet('stok.php', { kritik: 'true' });
                const stoklar = response.data.slice(0, 5); // İlk 5 kayıt

                if (stoklar.length === 0) {
                    document.getElementById('kritikStoklar').innerHTML = `
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm">Tüm stoklar yeterli seviyede! 🎉</p>
                        </div>
                    `;
                    return;
                }

                const stoklarHTML = stoklar.map(stok => {
                    const yuzde = Math.round((stok.Toplam_Stok / stok.Referans_Degeri) * 100);
                    return `
                        <div class="p-4 hover:bg-slate-50 rounded-lg transition border-l-4 border-red-500">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-slate-800">${stok.Urun_Adi}</p>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Kritik
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <p class="text-slate-600">Stok: <span class="font-semibold">${formatSayi(stok.Toplam_Stok)}</span> / ${formatSayi(stok.Referans_Degeri)}</p>
                                <p class="text-slate-600">${yuzde}%</p>
                            </div>
                            <div class="mt-2 w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: ${Math.min(yuzde, 100)}%"></div>
                            </div>
                        </div>
                    `;
                }).join('');

                document.getElementById('kritikStoklar').innerHTML = stoklarHTML;

            } catch (error) {
                console.error('Kritik stoklar yükleme hatası:', error);
            }
        }

        // Saat güncelle
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('currentTime').textContent = `${hours}:${minutes}`;
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            loadRecentOperations();
            loadKritikStoklar();

            // Saati her dakika güncelle
            setInterval(updateClock, 60000);
        });
    </script>
</body>
</html>
