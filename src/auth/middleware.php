<?php
/**
 * Authentication Middleware
 *
 * Her korumalı sayfada include edilmeli
 * Otomatik auth ve role kontrolü yapar
 */

// Config ve utility dosyalarını yükle
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/security.php';
require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/session.php';

/**
 * Sayfa bazlı auth kontrolü
 *
 * Her sayfa kendi gerekliliğini belirtir:
 * - $requireAuth: Login gerekli mi? (default: true)
 * - $allowedRoles: İzin verilen roller (default: tüm roller)
 *
 * Kullanım:
 * ```php
 * $requireAuth = true;
 * $allowedRoles = [ROLE_YONETICI]; // Sadece yönetici
 * require_once __DIR__ . '/../../src/auth/middleware.php';
 * ```
 */

// Varsayılan değerler (sayfa önceden set etmemişse)
if (!isset($requireAuth)) {
    $requireAuth = true; // Varsayılan: Auth gerekli
}

if (!isset($allowedRoles)) {
    $allowedRoles = [ROLE_PERSONEL, ROLE_YONETICI]; // Varsayılan: Tüm roller
}

if (!isset($apiMode)) {
    $apiMode = false; // Varsayılan: Web sayfası modu
}

// Auth kontrolü
if ($requireAuth) {
    requireLogin($apiMode);

    // Rol kontrolü (eğer belirtilmişse)
    if (!empty($allowedRoles)) {
        requireRole($allowedRoles, $apiMode);
    }
}

/**
 * Helper: Mevcut kullanıcıyı global olarak kullanılabilir yap
 */
$currentUser = getCurrentUser();

/**
 * Helper: Sidebar'da aktif sayfa belirlemek için
 *
 * @param string $page Sayfa adı (örn: 'dashboard', 'stok')
 * @return string 'active' veya ''
 */
function isActivePage($page) {
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    return ($currentPage === $page) ? 'active' : '';
}

/**
 * Helper: Layout başlığını belirle
 *
 * @param string $pageTitle Sayfa başlığı
 * @param string $subtitle Alt başlık (opsiyonel)
 */
function setPageTitle($pageTitle, $subtitle = '') {
    $GLOBALS['page_title'] = $pageTitle;
    $GLOBALS['page_subtitle'] = $subtitle;
}

/**
 * Helper: Sayfa başlığını al
 *
 * @return string Sayfa başlığı
 */
function getPageTitle() {
    return $GLOBALS['page_title'] ?? APP_NAME;
}

/**
 * Helper: Alt başlığı al
 *
 * @return string Alt başlık
 */
function getPageSubtitle() {
    return $GLOBALS['page_subtitle'] ?? '';
}
