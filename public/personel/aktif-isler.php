<?php
/**
 * Personel - Aktif İşler
 *
 * Devam eden operasyonlar (Müşteri işlemleri + Tedarikçi alışları)
 * Kanban tarzı görünüm - Gün hesaplaması düzeltilmiş
 */

$requireAuth = true;
$allowedRoles = [ROLE_PERSONEL, ROLE_YONETICI];
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
        .sidebar-gradient { background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%); }
        .operation-card { transition: transform 0.2s, box-shadow 0.2s; }
        .operation-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="min-h-screen bg-slate-50">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 sidebar-gradient text-white flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 z-30">
            <div class="px-6 py-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">Doğu AŞ</h1>
                        <p class="text-xs text-blue-200">Personel Panel</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-blue-100 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Anasayfa</span>
                </a>

                <a href="stok.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-blue-100 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Stok Durumu</span>
                </a>

                <a href="aktif-isler.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-white/20 text-white font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Aktif İşler</span>
                </a>

                <a href="gecmis.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-blue-100 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Geçmiş</span>
                </a>
            </nav>

            <div class="px-6 py-4 border-t border-white/10">
                <div class="flex items-center justify-between text-sm text-blue-200">
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
                                <p class="text-xs text-slate-600 capitalize"><?php echo escapeHtml($currentUser['role']); ?></p>
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

                <!-- Filter Tabs -->
                <div class="flex items-center justify-between mb-6">
                    <div class="bg-white rounded-lg shadow-md p-1 flex space-x-1">
                        <button onclick="filterOperations('')" id="btnAll" class="px-6 py-2 rounded-md font-medium text-sm transition bg-blue-600 text-white">
                            Tümü
                        </button>
                        <button onclick="filterOperations('satis')" id="btnSatis" class="px-6 py-2 rounded-md font-medium text-sm transition text-slate-600 hover:bg-slate-100">
                            Satış
                        </button>
                        <button onclick="filterOperations('alis')" id="btnAlis" class="px-6 py-2 rounded-md font-medium text-sm transition text-slate-600 hover:bg-slate-100">
                            Alış
                        </button>
                    </div>

                    <div class="flex items-center space-x-2 text-sm text-slate-600">
                        <span class="font-medium">Toplam:</span>
                        <span id="operationCount" class="font-bold text-blue-600">0</span>
                        <span>işlem</span>
                    </div>
                </div>

                <!-- Operations Grid -->
                <div id="operationsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Loading state -->
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-3/4 mb-4"></div>
                        <div class="h-6 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-4 bg-slate-200 rounded w-full mb-2"></div>
                        <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-3/4 mb-4"></div>
                        <div class="h-6 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-4 bg-slate-200 rounded w-full mb-2"></div>
                        <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-slate-200 rounded w-3/4 mb-4"></div>
                        <div class="h-6 bg-slate-200 rounded w-1/2 mb-4"></div>
                        <div class="h-4 bg-slate-200 rounded w-full mb-2"></div>
                        <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/common.js"></script>
    <script>
        let allOperations = [];
        let currentFilter = '';

        // Operasyonları yükle
        async function loadOperations() {
            try {
                showLoading(true);
                const response = await apiGet('operations.php', { filter: 'aktif' });
                allOperations = response.data;
                showLoading(false);

                filterOperations(currentFilter);

            } catch (error) {
                showLoading(false);
                console.error('Operasyon yükleme hatası:', error);
                document.getElementById('operationsGrid').innerHTML = `
                    <div class="col-span-full text-center py-12 text-red-600">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-lg font-medium">Operasyonlar yüklenirken hata oluştu</p>
                        <p class="text-sm">${error.message}</p>
                    </div>
                `;
            }
        }

        // Filtreleme
        function filterOperations(tip) {
            currentFilter = tip;

            // Button states
            document.getElementById('btnAll').className = tip === ''
                ? 'px-6 py-2 rounded-md font-medium text-sm transition bg-blue-600 text-white'
                : 'px-6 py-2 rounded-md font-medium text-sm transition text-slate-600 hover:bg-slate-100';

            document.getElementById('btnSatis').className = tip === 'satis'
                ? 'px-6 py-2 rounded-md font-medium text-sm transition bg-blue-600 text-white'
                : 'px-6 py-2 rounded-md font-medium text-sm transition text-slate-600 hover:bg-slate-100';

            document.getElementById('btnAlis').className = tip === 'alis'
                ? 'px-6 py-2 rounded-md font-medium text-sm transition bg-blue-600 text-white'
                : 'px-6 py-2 rounded-md font-medium text-sm transition text-slate-600 hover:bg-slate-100';

            // Filter data
            const filtered = tip === ''
                ? allOperations
                : allOperations.filter(op => op.tip === tip);

            document.getElementById('operationCount').textContent = filtered.length;

            renderOperations(filtered);
        }

        // Operasyonları render et
        function renderOperations(data) {
            const grid = document.getElementById('operationsGrid');

            if (data.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-lg font-medium text-slate-600">Aktif işlem bulunamadı</p>
                        <p class="text-sm text-slate-500 mt-2">Henüz devam eden bir operasyon yok</p>
                    </div>
                `;
                return;
            }

            const cardsHTML = data.map(op => {
                // Tip badge
                const tipClass = op.tip === 'satis' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                const tipIcon = op.tip === 'satis'
                    ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>'
                    : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>';

                // Gün hesaplaması - DOĞRU YOL
                const bugun = new Date();
                bugun.setHours(0, 0, 0, 0); // Saat sıfırla

                const islemTarihi = new Date(op.tarih);
                islemTarihi.setHours(0, 0, 0, 0);

                const farkMs = bugun - islemTarihi;
                const gunFarki = Math.floor(farkMs / (1000 * 60 * 60 * 24));

                // Gün badge rengi
                let gunBadgeClass = 'bg-green-100 text-green-800';
                if (gunFarki > 7) gunBadgeClass = 'bg-orange-100 text-orange-800';
                if (gunFarki > 30) gunBadgeClass = 'bg-red-100 text-red-800';

                return `
                    <div class="operation-card bg-white rounded-xl shadow-md p-6 border-t-4 ${op.tip === 'satis' ? 'border-green-500' : 'border-blue-500'}">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-medium ${tipClass}">
                                ${tipIcon}
                                <span>${op.tip === 'satis' ? 'Satış' : 'Alış'}</span>
                            </span>

                            <span class="inline-flex items-center space-x-1 px-2 py-1 rounded-lg text-xs font-semibold ${gunBadgeClass}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                <span>${gunFarki} gün</span>
                            </span>
                        </div>

                        <!-- Content -->
                        <h3 class="text-lg font-bold text-slate-900 mb-2">${op.firma || 'Firma belirtilmemiş'}</h3>

                        <div class="space-y-2 text-sm text-slate-600 mb-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>${op.urun || '-'}</span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span>${op.adet ? formatSayi(op.adet) + ' adet' : '-'}</span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>${formatTarih(op.tarih)}</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-xs text-slate-500">${op.durum || 'Devam ediyor'}</span>
                            <span class="text-lg font-bold ${op.tip === 'satis' ? 'text-green-600' : 'text-blue-600'}">
                                ${formatPara(op.tutar)}
                            </span>
                        </div>
                    </div>
                `;
            }).join('');

            grid.innerHTML = cardsHTML;
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadOperations();

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
