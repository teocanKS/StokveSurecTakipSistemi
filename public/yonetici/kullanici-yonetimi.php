<?php
/**
 * Yönetici - Kullanıcı Yönetimi
 *
 * Kullanıcı listesi ve rol yönetimi
 */

$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
require_once __DIR__ . '/../../src/auth/middleware.php';

setPageTitle('Kullanıcı Yönetimi', 'Sistem kullanıcılarını yönetin');

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

                <a href="gecmis.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Geçmiş</span>
                </a>

                <div class="border-t border-white/10 my-4"></div>

                <a href="kullanici-yonetimi.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-500 text-white font-medium">
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
                            <label class="text-sm font-medium text-slate-700">Rol:</label>
                            <select id="filterRole" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <option value="">Tümü</option>
                                <option value="yonetici">Yönetici</option>
                                <option value="personel">Personel</option>
                            </select>
                        </div>

                        <button onclick="loadUsers()" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Yenile</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Kullanıcı Tablosu -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Ad Soyad</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">E-posta</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Rol</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody" class="divide-y divide-slate-200">
                                <!-- Loading state -->
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
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

                <!-- Bilgilendirme -->
                <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Kullanıcı Yönetimi Bilgilendirme</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Kullanıcıların rollerini değiştirebilirsiniz (yönetici ↔ personel)</li>
                                <li>Kendi rolünüzü değiştiremezsiniz</li>
                                <li>Sadece iki rol vardır: <strong>yonetici</strong> ve <strong>personel</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/common.js"></script>
    <script>
        let allUsers = [];
        const currentUserId = <?php echo $currentUser['id']; ?>;

        // Kullanıcıları yükle
        async function loadUsers() {
            try {
                const roleFilter = document.getElementById('filterRole').value;
                const params = {};
                if (roleFilter) params.role = roleFilter;

                const response = await apiGet('users.php', params);
                allUsers = response.data;

                renderUsers(allUsers);

            } catch (error) {
                console.error('Kullanıcılar yükleme hatası:', error);
                document.getElementById('usersTableBody').innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-red-600">
                            Hata: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Tabloyu render et
        function renderUsers(data) {
            const tbody = document.getElementById('usersTableBody');

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
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

            const rowsHTML = data.map(user => {
                const isCurrentUser = user.Users_ID === currentUserId;
                const roleBadge = user.role === 'yonetici' ?
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Yönetici</span>' :
                    '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Personel</span>';

                return `
                    <tr class="hover:bg-slate-50 transition ${isCurrentUser ? 'bg-orange-50/30' : ''}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="font-medium text-slate-900">${user.full_name || '-'}</span>
                                ${isCurrentUser ? '<span class="ml-2 text-xs text-orange-600 font-medium">(Siz)</span>' : ''}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-700">${user.Email || '-'}</td>
                        <td class="px-6 py-4">${roleBadge}</td>
                        <td class="px-6 py-4 text-center">
                            ${isCurrentUser ?
                                '<span class="text-xs text-slate-500">-</span>' :
                                `
                                <select onchange="changeUserRole(${user.Users_ID}, this.value, '${user.full_name}')" class="px-3 py-1 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500">
                                    <option value="">Rol Değiştir...</option>
                                    <option value="yonetici" ${user.role === 'yonetici' ? 'disabled' : ''}>Yönetici Yap</option>
                                    <option value="personel" ${user.role === 'personel' ? 'disabled' : ''}>Personel Yap</option>
                                </select>
                                `
                            }
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rowsHTML;
        }

        // Kullanıcı rolü değiştir
        async function changeUserRole(userId, newRole, userName) {
            if (!newRole) return;

            const roleText = newRole === 'yonetici' ? 'Yönetici' : 'Personel';

            if (!confirm(`${userName} kullanıcısının rolünü "${roleText}" olarak değiştirmek istediğinizden emin misiniz?`)) {
                loadUsers(); // Reset select
                return;
            }

            try {
                showLoading(true);

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                const response = await fetch('../src/api/users.php', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        role: newRole,
                        csrf_token: csrfToken
                    })
                });

                const data = await response.json();

                showLoading(false);

                if (data.success) {
                    showNotification(data.message, 'success');
                    loadUsers(); // Reload
                } else {
                    showNotification(data.message || 'Rol güncelleme başarısız', 'error');
                    loadUsers(); // Reset
                }

            } catch (error) {
                showLoading(false);
                console.error('Rol güncelleme hatası:', error);
                showNotification('Rol güncellenirken hata oluştu', 'error');
                loadUsers(); // Reset
            }
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();

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
