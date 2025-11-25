<?php
/**
 * Yönetici - Stok Yönetimi
 *
 * Tüm stok listesi, kritik stok uyarıları ve yönetim özellikleri
 */

$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
require_once __DIR__ . '/../../src/auth/middleware.php';

setPageTitle('Stok Yönetimi', 'Ürün stok seviyeleri ve yönetimi');

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrfToken; ?>">
    <title><?php echo getPageTitle(); ?> | <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar-gradient { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
    </style>
</head>
<body class="min-h-screen bg-slate-100">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar (Yönetici - Koyu Tema) -->
        <aside id="sidebar" class="w-64 sidebar-gradient text-white flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 z-30">
            <div class="px-6 py-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">Doğu AŞ</h1>
                        <p class="text-xs text-slate-300">Yönetici Panel</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Anasayfa</span>
                </a>

                <a href="stok.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-500 text-white font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Stok Yönetimi</span>
                </a>

                <a href="aktif-isler.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Aktif İşler</span>
                </a>

                <a href="gecmis.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Geçmiş</span>
                </a>

                <div class="border-t border-white/10 my-4"></div>

                <a href="kullanici-yonetimi.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Kullanıcı Yönetimi</span>
                </a>
            </nav>

            <div class="px-6 py-4 border-t border-white/10">
                <div class="flex items-center justify-between text-sm text-slate-400">
                    <span>v1.0.0</span>
                    <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="3"/></svg>
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
                        <button data-sidebar-toggle class="lg:hidden text-slate-600 hover:text-slate-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800"><?php echo getPageTitle(); ?></h2>
                            <p class="text-sm text-slate-600"><?php echo getPageSubtitle(); ?></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="hidden md:flex items-center space-x-2 text-sm text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="currentTime"><?php echo date('H:i'); ?></span>
                        </div>

                        <div class="flex items-center space-x-3 pl-4 border-l border-slate-200">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-semibold text-slate-800"><?php echo escapeHtml($currentUser['full_name']); ?></p>
                                <p class="text-xs text-orange-600 capitalize font-medium"><?php echo escapeHtml($currentUser['role']); ?></p>
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

                <!-- Filter Bar -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-slate-700">Göster:</label>
                            <select id="filterKritik" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">Tüm Stoklar</option>
                                <option value="true">Sadece Kritik Stoklar</option>
                            </select>
                        </div>

                        <div class="flex-1">
                            <input type="text" id="searchInput" placeholder="Ürün adı ile ara..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>

                        <button onclick="loadStokList()" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span>Ara</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="stokSummary">
                    <!-- Loading states -->
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

                <!-- Stok Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Ürün Adı</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Birim</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Mevcut Stok</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Kritik Seviye</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Durum</th>
                                </tr>
                            </thead>
                            <tbody id="stokTableBody" class="divide-y divide-slate-200">
                                <!-- Loading state -->
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-orange-500 border-t-transparent"></div>
                                            <p class="text-slate-600">Stok listesi yükleniyor...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/common.js"></script>
    <script>
        let allStokData = [];

        // Stok özet istatistikleri
        async function loadStokSummary() {
            try {
                const response = await apiGet('stats.php');
                const stats = response.data;

                const summaryHTML = `
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mb-1">Toplam Ürün</p>
                        <p class="text-3xl font-bold text-slate-900">${formatSayi(stats.stok.toplam_urun)}</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mb-1">Toplam Stok Adedi</p>
                        <p class="text-3xl font-bold text-slate-900">${formatSayi(stats.stok.toplam_stok_adedi)}</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mb-1">Kritik Stok</p>
                        <p class="text-3xl font-bold text-red-600">${formatSayi(stats.kritik_stok)}</p>
                    </div>
                `;

                document.getElementById('stokSummary').innerHTML = summaryHTML;

            } catch (error) {
                console.error('Stok özet yükleme hatası:', error);
            }
        }

        // Stok listesini yükle
        async function loadStokList() {
            try {
                const kritikFilter = document.getElementById('filterKritik').value;
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();

                const params = {};
                if (kritikFilter) params.kritik = kritikFilter;

                const response = await apiGet('stok.php', params);
                allStokData = response.data;

                // Arama filtresi (frontend)
                let filteredData = allStokData;
                if (searchTerm) {
                    filteredData = allStokData.filter(stok =>
                        (stok.Urun_Adi || '').toLowerCase().includes(searchTerm) ||
                        (stok.kategori || '').toLowerCase().includes(searchTerm)
                    );
                }

                renderStokTable(filteredData);

            } catch (error) {
                console.error('Stok liste yükleme hatası:', error);
                document.getElementById('stokTableBody').innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-red-600">
                            Stok listesi yüklenirken hata oluştu: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Tabloyu render et
        function renderStokTable(data) {
            const tbody = document.getElementById('stokTableBody');

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-4 text-slate-500">
                                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-lg font-medium">Kayıt bulunamadı</p>
                                <p class="text-sm">Arama kriterlerinizi değiştirip tekrar deneyin</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            const rowsHTML = data.map(stok => {
                const toplam = parseInt(stok.Toplam_Stok) || 0;
                const referans = parseInt(stok.Referans_Degeri) || 0;
                const kritikMi = toplam <= referans;

                // Durum badge
                let durumClass = 'bg-green-100 text-green-800';
                let durumText = 'Normal';
                let durumIcon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';

                if (toplam === 0) {
                    durumClass = 'bg-red-100 text-red-800';
                    durumText = 'Tükendi';
                    durumIcon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                } else if (kritikMi) {
                    durumClass = 'bg-orange-100 text-orange-800';
                    durumText = 'Kritik';
                    durumIcon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                } else if (toplam <= (referans * 1.5)) {
                    durumClass = 'bg-yellow-100 text-yellow-800';
                    durumText = 'Düşük';
                    durumIcon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                }

                return `
                    <tr class="hover:bg-slate-50 transition ${kritikMi ? 'bg-red-50/30' : ''}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                ${kritikMi ? '<svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>' : ''}
                                <span class="font-medium text-slate-900">${stok.Urun_Adi || '-'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">${stok.kategori || '-'}</td>
                        <td class="px-6 py-4 text-slate-600">${stok.Birim || '-'}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-semibold ${kritikMi ? 'text-red-600' : 'text-slate-900'}">${formatSayi(toplam)}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-slate-600">${formatSayi(referans)}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-medium ${durumClass}">
                                ${durumIcon}
                                <span>${durumText}</span>
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rowsHTML;
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadStokSummary();
            loadStokList();

            // Arama inputu - debounce ile
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(loadStokList, 300);
            });

            // Saat güncelle
            setInterval(() => {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                document.getElementById('currentTime').textContent = `${hours}:${minutes}`;
            }, 60000);
        });
    </script>
</body>
</html>
