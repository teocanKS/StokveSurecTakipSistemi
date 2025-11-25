<?php
/**
 * Yönetici - Aktif İşler
 *
 * Devam eden tüm operasyonlar (Müşteri işlemleri + Tedarikçi alışları)
 * Düzeltilmiş gün hesaplama mantığı ile
 */

$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
require_once __DIR__ . '/../../src/auth/middleware.php';

setPageTitle('Aktif İşler', 'Devam eden alış ve satış işlemleri');

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
        .operation-card { transition: transform 0.2s, box-shadow 0.2s; }
        .operation-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
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

                <a href="aktif-isler.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-500 text-white font-medium">
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
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-slate-700">Tip:</label>
                            <select id="filterTip" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <option value="">Tümü</option>
                                <option value="satis">Satış</option>
                                <option value="alis">Alış</option>
                            </select>
                        </div>

                        <div class="flex-1">
                            <input type="text" id="searchInput" placeholder="Firma veya ürün ara..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>

                        <button onclick="loadOperations()" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span>Ara</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Operations Grid -->
                <div id="operationsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Loading state -->
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-3/4 mb-4"></div>
                        <div class="h-8 bg-slate-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/common.js"></script>
    <script>
        let allOperations = [];

        // İşlemleri yükle
        async function loadOperations() {
            try {
                const response = await apiGet('operations.php', { filter: 'aktif' });
                allOperations = response.data;

                const tipFilter = document.getElementById('filterTip').value;
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();

                let filtered = allOperations;

                if (tipFilter) {
                    filtered = filtered.filter(op => op.tip === tipFilter);
                }

                if (searchTerm) {
                    filtered = filtered.filter(op =>
                        (op.firma || '').toLowerCase().includes(searchTerm) ||
                        (op.urun || '').toLowerCase().includes(searchTerm)
                    );
                }

                renderOperations(filtered);

            } catch (error) {
                console.error('İşlemler yükleme hatası:', error);
            }
        }

        // İşlemleri render et
        function renderOperations(data) {
            const container = document.getElementById('operationsContainer');

            if (data.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <svg class="w-20 h-20 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-lg font-medium text-slate-600">Aktif işlem bulunamadı</p>
                    </div>
                `;
                return;
            }

            const cardsHTML = data.map(op => {
                // Gün hesaplama (düzeltilmiş)
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const opDate = new Date(op.tarih);
                opDate.setHours(0, 0, 0, 0);

                const diffTime = today - opDate;
                const gunSayisi = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                let gunBadgeClass = 'bg-green-100 text-green-800';
                if (gunSayisi >= 30) {
                    gunBadgeClass = 'bg-red-100 text-red-800';
                } else if (gunSayisi >= 7) {
                    gunBadgeClass = 'bg-orange-100 text-orange-800';
                }

                const tipIcon = op.tip === 'satis' ?
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>' :
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>';

                const tipColor = op.tip === 'satis' ? 'bg-green-500' : 'bg-blue-500';

                return `
                    <div class="operation-card bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 ${tipColor} rounded-lg flex items-center justify-center text-white">
                                ${tipIcon}
                            </div>
                            <span class="text-xs font-semibold px-3 py-1 rounded-full ${gunBadgeClass}">
                                ${gunSayisi} gün
                            </span>
                        </div>

                        <h3 class="font-bold text-lg text-slate-900 mb-2">${op.firma || '-'}</h3>
                        <p class="text-sm text-slate-600 mb-4">${op.urun || '-'}</p>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Miktar:</span>
                                <span class="font-semibold">${formatSayi(op.adet || 0)} adet</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Tutar:</span>
                                <span class="font-bold text-slate-900">${formatPara(op.tutar || 0)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Tarih:</span>
                                <span class="text-slate-700">${formatTarih(op.tarih)}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = cardsHTML;
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadOperations();

            // Search debounce
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(loadOperations, 300);
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
