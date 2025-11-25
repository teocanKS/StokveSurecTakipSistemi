<?php
/**
 * Yönetici - Geçmiş İşlemler
 *
 * Tüm tamamlanmış/iptal edilmiş işlemler
 * Gelişmiş filtreleme ve sıralama özellikleri
 */

$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
require_once __DIR__ . '/../../src/auth/middleware.php';

setPageTitle('Geçmiş İşlemler', 'Tamamlanmış ve iptal edilmiş işlemler');

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

        <!-- Sidebar (Yönetici) -->
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

                <a href="stok.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/10 transition">
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

                <a href="gecmis.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-500 text-white font-medium">
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
                <!-- Gelişmiş Filtre Paneli -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Filtrele</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Firma</label>
                            <input type="text" id="filterFirma" placeholder="Firma adı..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Ürün</label>
                            <input type="text" id="filterUrun" placeholder="Ürün adı..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Başlangıç Tarihi</label>
                            <input type="date" id="filterBaslangic" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Bitiş Tarihi</label>
                            <input type="date" id="filterBitis" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">İşlem Tipi</label>
                            <select id="filterTip" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <option value="">Tümü</option>
                                <option value="satis">Satış</option>
                                <option value="alis">Alış</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Sıralama</label>
                            <select id="sortBy" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <option value="tarih_desc">Tarih (Yeni → Eski)</option>
                                <option value="tarih_asc">Tarih (Eski → Yeni)</option>
                                <option value="tutar_desc">Tutar (Yüksek → Düşük)</option>
                                <option value="tutar_asc">Tutar (Düşük → Yüksek)</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 flex items-end space-x-2">
                            <button onclick="loadHistory()" class="flex-1 px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <span>Uygula</span>
                                </div>
                            </button>
                            <button onclick="resetFilters()" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-lg">
                                Sıfırla
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Geçmiş Tablosu -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Tip</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Firma</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Ürün</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase">Miktar</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase">Tutar</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Tarih</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody" class="divide-y divide-slate-200">
                                <!-- Loading state -->
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-orange-500 border-t-transparent"></div>
                                            <p class="text-slate-600">Geçmiş yükleniyor...</p>
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
        let allHistory = [];

        // Geçmişi yükle
        async function loadHistory() {
            try {
                const firma = document.getElementById('filterFirma').value;
                const urun = document.getElementById('filterUrun').value;
                const baslangic = document.getElementById('filterBaslangic').value;
                const bitis = document.getElementById('filterBitis').value;
                const tip = document.getElementById('filterTip').value;
                const sortBy = document.getElementById('sortBy').value;

                const params = {
                    firma, urun, baslangic, bitis, tip, sortBy
                };

                // Boş parametreleri temizle
                Object.keys(params).forEach(key => !params[key] && delete params[key]);

                const response = await apiGet('history.php', params);
                allHistory = response.data;

                renderHistory(allHistory);

            } catch (error) {
                console.error('Geçmiş yükleme hatası:', error);
                document.getElementById('historyTableBody').innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-red-600">
                            Hata: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Tabloyu render et
        function renderHistory(data) {
            const tbody = document.getElementById('historyTableBody');

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-4 text-slate-500">
                                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">Kayıt bulunamadı</p>
                                <p class="text-sm">Filtrelerinizi değiştirip tekrar deneyin</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            const rowsHTML = data.map(item => {
                const tipBadge = item.tip === 'satis' ?
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>Satış</span>' :
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>Alış</span>';

                return `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">${tipBadge}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">${item.firma || '-'}</td>
                        <td class="px-6 py-4 text-slate-600">${item.urun || '-'}</td>
                        <td class="px-6 py-4 text-right text-slate-900">${formatSayi(item.adet || 0)} adet</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900">${formatPara(item.tutar || 0)}</td>
                        <td class="px-6 py-4 text-slate-700">${formatTarih(item.tarih)}</td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rowsHTML;
        }

        // Filtreleri sıfırla
        function resetFilters() {
            document.getElementById('filterFirma').value = '';
            document.getElementById('filterUrun').value = '';
            document.getElementById('filterBaslangic').value = '';
            document.getElementById('filterBitis').value = '';
            document.getElementById('filterTip').value = '';
            document.getElementById('sortBy').value = 'tarih_desc';
            loadHistory();
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadHistory();

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
