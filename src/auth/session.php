<?php
/**
 * Session Yönetim Fonksiyonları
 *
 * Güvenli session işlemleri için helper fonksiyonlar
 */

/**
 * Kullanıcı Login Yap
 *
 * Session'a kullanıcı bilgilerini kaydet
 *
 * @param array $user Kullanıcı bilgileri (Users tablosundan)
 */
function loginUser($user) {
    // Session fixation koruması
    session_regenerate_id(true);

    // Kullanıcı bilgilerini session'a kaydet
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['Users_ID'];
    $_SESSION['user_name'] = $user['Name'];
    $_SESSION['user_surname'] = $user['Surname'] ?? '';
    $_SESSION['user_email'] = $user['Email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();

    // Güvenlik: IP ve User Agent kaydet (session hijacking tespiti için)
    $_SESSION['user_ip'] = getUserIpAddress();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Login activity log
    logSecurityEvent('User login', [
        'user_id' => $user['Users_ID'],
        'email' => $user['Email'],
        'role' => $user['role']
    ]);
}

/**
 * Kullanıcı Logout Yap
 *
 * Session'ı temizle ve yok et
 */
function logoutUser() {
    // Logout activity log (önce logla, sonra session yok et)
    if (isset($_SESSION['user_id'])) {
        logSecurityEvent('User logout', [
            'user_id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'] ?? 'unknown'
        ]);
    }

    // Session verilerini temizle
    $_SESSION = [];

    // Session cookie'sini sil
    if (isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Session'ı yok et
    session_destroy();
}

/**
 * Kullanıcı Login Olmuş mu?
 *
 * @return bool Login durumu
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Mevcut Kullanıcıyı Al
 *
 * @return array|null Kullanıcı bilgileri
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? '',
        'surname' => $_SESSION['user_surname'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? '',
        'full_name' => trim(($_SESSION['user_name'] ?? '') . ' ' . ($_SESSION['user_surname'] ?? ''))
    ];
}

/**
 * Session Hijacking Kontrolü
 *
 * IP ve User Agent değişmiş mi?
 *
 * @return bool Session güvenli mi?
 */
function isSessionValid() {
    if (!isLoggedIn()) {
        return false;
    }

    // IP kontrolü
    $currentIp = getUserIpAddress();
    $sessionIp = $_SESSION['user_ip'] ?? '';

    if ($sessionIp !== $currentIp) {
        logSecurityEvent('Session hijacking attempt detected - IP mismatch', [
            'session_ip' => $sessionIp,
            'current_ip' => $currentIp,
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
        return false;
    }

    // User Agent kontrolü
    $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $sessionUserAgent = $_SESSION['user_agent'] ?? '';

    if ($sessionUserAgent !== $currentUserAgent) {
        logSecurityEvent('Session hijacking attempt detected - User Agent mismatch', [
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
        return false;
    }

    return true;
}

/**
 * Login Gerekli (Middleware)
 *
 * Kullanıcı login değilse login sayfasına yönlendir
 *
 * @param bool $apiMode API modu (JSON response döner)
 */
function requireLogin($apiMode = false) {
    if (!isLoggedIn() || !isSessionValid()) {
        if ($apiMode) {
            unauthorizedResponse('Lütfen giriş yapın');
        } else {
            // Session'ı temizle
            session_unset();

            // Return URL'i kaydet (login sonrası dönmek için)
            $_SESSION['return_url'] = $_SERVER['REQUEST_URI'] ?? '';

            // Login sayfasına yönlendir
            redirect(BASE_URL . 'public/index.php');
        }
    }

    // Activity timestamp'i güncelle
    $_SESSION['last_activity'] = time();
}

/**
 * Rol Kontrolü (Middleware)
 *
 * Kullanıcının belirtilen role sahip olup olmadığını kontrol et
 *
 * @param string|array $allowedRoles İzin verilen rol(ler)
 * @param bool $apiMode API modu
 */
function requireRole($allowedRoles, $apiMode = false) {
    // Önce login kontrolü
    requireLogin($apiMode);

    $allowedRoles = (array) $allowedRoles;
    $userRole = $_SESSION['user_role'] ?? '';

    // Yönetici her şeye erişebilir
    if ($userRole === ROLE_YONETICI) {
        return;
    }

    // Rol kontrolü
    if (!in_array($userRole, $allowedRoles)) {
        logSecurityEvent('Unauthorized role access attempt', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_role' => $userRole,
            'required_roles' => $allowedRoles,
            'requested_url' => $_SERVER['REQUEST_URI'] ?? ''
        ]);

        if ($apiMode) {
            forbiddenResponse('Bu işlem için yetkiniz yok');
        } else {
            // Kullanıcıyı kendi dashboard'una yönlendir
            $dashboardUrl = ($userRole === ROLE_PERSONEL)
                ? BASE_URL . 'public/personel/dashboard.php'
                : BASE_URL . 'public/yonetici/dashboard.php';

            redirect($dashboardUrl);
        }
    }
}

/**
 * Yönetici mi Kontrolü
 *
 * @return bool Yönetici mi?
 */
function isYonetici() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === ROLE_YONETICI;
}

/**
 * Personel mi Kontrolü
 *
 * @return bool Personel mi?
 */
function isPersonel() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === ROLE_PERSONEL;
}

/**
 * Session Bilgilerini Göster (Debug)
 *
 * Sadece development'ta çalışır
 */
function debugSession() {
    if (APP_ENV === 'development' && isLoggedIn()) {
        echo '<div style="position:fixed;bottom:0;left:0;background:#000;color:#0f0;padding:10px;font-family:monospace;font-size:11px;z-index:9999;">';
        echo '<strong>Session Debug:</strong><br>';
        echo 'User ID: ' . ($_SESSION['user_id'] ?? 'N/A') . '<br>';
        echo 'Email: ' . ($_SESSION['user_email'] ?? 'N/A') . '<br>';
        echo 'Role: ' . ($_SESSION['user_role'] ?? 'N/A') . '<br>';
        echo 'IP: ' . ($_SESSION['user_ip'] ?? 'N/A') . '<br>';
        echo 'Last Activity: ' . date('Y-m-d H:i:s', $_SESSION['last_activity'] ?? 0) . '<br>';
        echo '</div>';
    }
}
