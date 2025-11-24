<?php
/**
 * Yönetici - Kullanıcı Yönetimi
 *
 * Kullanıcı listesi, onaylama ve yönetim
 */

$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
require_once __DIR__ . '/../../src/auth/middleware.php';

setPageTitle('Kullanıcı Yönetimi', 'Kullanıcı onaylama ve yönetim');

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
        .dark-scrollbar::-webkit-scrollbar { width: 6px; }
        .dark-scrollbar::-webkit-scrollbar-track { background: #1e293b; }
        .dark-scrollbar::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
        .dark-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</head>
<body class="min-h-screen bg-slate-50">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar (Yönetici - Koyu Tema) -->
        <aside id="sidebar" class="w-64 sidebar-gradient text-white flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 z-30 dark-scrollbar">
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

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto dark-scrollbar">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/5 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <a href="kullanici-yonetimi.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-500/20 text-white font-medium border border-orange-500/30">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Kullanıcı Yönetimi</span>
                    </a>
                </div>
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

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="userStats">
                    <!-- Loading -->
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

                <!-- Filter Bar -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-slate-700">Durum:</label>
                            <select id="filterStatus" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">Tümü</option>
                                <option value="approved">Onaylandı</option>
                                <option value="pending">Onay Bekliyor</option>
                            </select>
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <input type="text" id="searchInput" placeholder="E-posta veya isim ile ara..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>

                        <button onclick="loadUsers()" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span>Ara</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-800 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Kullanıcı</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">E-posta</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Kayıt Tarihi</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody" class="divide-y divide-slate-200">
                                <!-- Loading state -->
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-orange-500 border-t-transparent"></div>
                                            <p class="text-slate-600">Kullanıcılar yükleniyor...</p>
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
        let allUsers = [];
        const currentUserId = <?php echo $currentUser['Users_ID']; ?>;

        // Kullanıcıları yükle
        async function loadUsers() {
            try {
                showLoading(true);

                const response = await apiGet('users.php');
                allUsers = response.data;

                showLoading(false);

                // İstatistikleri güncelle
                updateStats();

                // Filtreleme uygula
                renderUsers();

            } catch (error) {
                showLoading(false);
                console.error('Kullanıcı yükleme hatası:', error);
                document.getElementById('usersTableBody').innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-red-600">
                            Kullanıcılar yüklenirken hata oluştu: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        // İstatistikleri güncelle
        function updateStats() {
            const totalUsers = allUsers.length;
            const pendingUsers = allUsers.filter(u => !u.is_approved).length;
            const approvedUsers = allUsers.filter(u => u.is_approved).length;

            const statsHTML = `
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 mb-1">Toplam Kullanıcı</p>
                    <p class="text-3xl font-bold text-slate-900">${totalUsers}</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 mb-1">Onaylanmış</p>
                    <p class="text-3xl font-bold text-green-600">${approvedUsers}</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 mb-1">Onay Bekliyor</p>
                    <p class="text-3xl font-bold text-yellow-600">${pendingUsers}</p>
                </div>
            `;

            document.getElementById('userStats').innerHTML = statsHTML;
        }

        // Kullanıcıları render et
        function renderUsers() {
            const statusFilter = document.getElementById('filterStatus').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            // Filtreleme
            let filteredUsers = allUsers;

            if (statusFilter === 'approved') {
                filteredUsers = filteredUsers.filter(u => u.is_approved);
            } else if (statusFilter === 'pending') {
                filteredUsers = filteredUsers.filter(u => !u.is_approved);
            }

            if (searchTerm) {
                filteredUsers = filteredUsers.filter(u =>
                    (u.email || '').toLowerCase().includes(searchTerm) ||
                    (u.full_name || '').toLowerCase().includes(searchTerm)
                );
            }

            const tbody = document.getElementById('usersTableBody');

            if (filteredUsers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-4 text-slate-500">
                                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="text-lg font-medium">Kullanıcı bulunamadı</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            const rowsHTML = filteredUsers.map(user => {
                const isCurrentUser = user.id === currentUserId;
                const statusClass = user.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                const statusText = user.is_approved ? 'Onaylandı' : 'Onay Bekliyor';
                const roleClass = user.role === 'yonetici' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800';

                let actionsHTML = '';
                if (isCurrentUser) {
                    actionsHTML = '<span class="text-xs text-slate-500">Kendi hesabınız</span>';
                } else if (!user.is_approved) {
                    actionsHTML = `
                        <button onclick="updateUserStatus(${user.id}, 'approve')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition">
                            Onayla
                        </button>
                        <button onclick="updateUserStatus(${user.id}, 'reject')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition">
                            Reddet
                        </button>
                    `;
                } else {
                    actionsHTML = `
                        <button onclick="updateUserStatus(${user.id}, 'reject')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition">
                            İptal Et
                        </button>
                    `;
                }

                return `
                    <tr class="hover:bg-slate-50 transition ${isCurrentUser ? 'bg-orange-50/30' : ''}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">${user.full_name || 'İsimsiz'}</p>
                                    ${isCurrentUser ? '<span class="text-xs text-orange-600">Siz</span>' : ''}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">${user.email}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium ${roleClass}">
                                ${user.role_label}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">${formatTarih(user.created_at)}</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            ${actionsHTML}
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rowsHTML;
        }

        // Kullanıcı durumunu güncelle
        async function updateUserStatus(userId, action) {
            const actionText = action === 'approve' ? 'onaylamak' : 'reddetmek';

            if (!confirm(`Bu kullanıcıyı ${actionText} istediğinizden emin misiniz?`)) {
                return;
            }

            try {
                showLoading(true);

                await apiPost('users.php', {
                    user_id: userId,
                    action: action,
                    csrf_token: document.querySelector('meta[name="csrf-token"]').content
                });

                showNotification(action === 'approve' ? 'Kullanıcı onaylandı' : 'Kullanıcı reddedildi', 'success');

                // Listeyi yenile
                await loadUsers();

            } catch (error) {
                console.error('Durum güncelleme hatası:', error);
                showNotification(error.message || 'İşlem başarısız oldu', 'error');
            } finally {
                showLoading(false);
            }
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();

            // Arama inputu - debounce ile
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(renderUsers, 300);
            });

            // Durum filtresi değiştiğinde
            document.getElementById('filterStatus').addEventListener('change', renderUsers);

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
