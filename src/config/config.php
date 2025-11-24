<?php
/**
 * Genel Uygulama Konfigürasyonu
 *
 * Güvenlik, session ve uygulama ayarları
 */

// Hata raporlama (Production'da kapatılmalı!)
error_reporting(E_ALL);
ini_set('display_errors', '1'); // Production'da '0' yapılmalı

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Character encoding
mb_internal_encoding('UTF-8');

// Session konfigürasyonu - GÜVENLİK KRİTİK
ini_set('session.cookie_httponly', '1');      // XSS koruması: JavaScript ile erişimi engelle
ini_set('session.use_only_cookies', '1');     // Sadece cookie kullan, URL'de session ID yok
ini_set('session.cookie_samesite', 'Strict'); // CSRF koruması
ini_set('session.cookie_secure', '0');        // HTTPS zorunluluğu (local dev için 0, prod'da 1)
ini_set('session.gc_maxlifetime', '3600');    // Session ömrü: 1 saat
ini_set('session.use_strict_mode', '1');      // Session hijacking koruması

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_name('DOGU_SESSION'); // Custom session name (güvenlik)
    session_start();

    // Session fixation saldırısı koruması
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }

    // Session timeout kontrolü (30 dakika inaktivite)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

// Uygulama sabitleri
define('APP_NAME', 'Doğu AŞ - Envanter ve Süreç Takip');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // 'production' veya 'development'

// Base URL (web root)
define('BASE_URL', '/');

// Kullanıcı rolleri
define('ROLE_PERSONEL', 'personel');
define('ROLE_YONETICI', 'yonetici');

// CSRF Token timeout (saniye cinsinden)
define('CSRF_TOKEN_EXPIRE', 3600); // 1 saat

// Sayfalama sabitleri
define('ITEMS_PER_PAGE', 20);

// Dosya yükleme (eğer kullanılacaksa)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'xlsx', 'csv']);

/**
 * Güvenlik Header'ları Ayarla
 *
 * OWASP önerileri doğrultusunda güvenlik header'ları
 */
function setSecurityHeaders() {
    // XSS koruması
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');

    // Content Security Policy (XSS koruması)
    // NOT: Tailwind CDN kullanıyoruz, bu yüzden CDN'leri whitelist'e ekliyoruz
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; img-src 'self' data:; font-src 'self' data:;");

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // HTTPS zorlama (Production'da)
    if (APP_ENV === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Her sayfada güvenlik header'larını ayarla
setSecurityHeaders();

/**
 * Uygulama başlatıldı log'u
 */
if (APP_ENV === 'development') {
    error_log('[' . date('Y-m-d H:i:s') . '] Application initialized');
}
