<?php
/**
 * Güvenlik Fonksiyonları
 *
 * OWASP en iyi pratiklerine uygun güvenlik katmanları:
 * - XSS (Cross-Site Scripting) koruması
 * - CSRF (Cross-Site Request Forgery) koruması
 * - Input validation ve sanitization
 * - SQL Injection koruması (PDO ile)
 */

/**
 * XSS Koruması: HTML Escape
 *
 * Kullanıcı girdilerini HTML'e basarken MUTLAKA bu fonksiyonu kullan
 *
 * @param mixed $data Escape edilecek veri
 * @return string|array Escape edilmiş veri
 */
function escapeHtml($data) {
    if (is_array($data)) {
        return array_map('escapeHtml', $data);
    }

    if (is_string($data)) {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return $data;
}

/**
 * CSRF Token Oluştur
 *
 * Her form submission için unique token oluşturur
 *
 * @return string CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time']) ||
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }

    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token Doğrula
 *
 * POST/PUT/DELETE isteklerinde MUTLAKA kontrol edilmeli
 *
 * @param string $token Kontrol edilecek token
 * @return bool Token geçerli mi?
 */
function validateCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    // Token süresi dolmuş mu?
    if (!empty($_SESSION['csrf_token_time']) &&
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
        return false;
    }

    // Timing attack'e karşı hash_equals kullan
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * CSRF Token HTML Input
 *
 * Formlara eklenecek hidden input HTML'i döndürür
 *
 * @return string HTML input tag
 */
function csrfTokenField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Input Sanitization: String
 *
 * Tehlikeli karakterleri temizler
 *
 * @param string $input Temizlenecek string
 * @return string Temizlenmiş string
 */
function sanitizeString($input) {
    if (!is_string($input)) {
        return '';
    }

    // Trim whitespace
    $input = trim($input);

    // Remove null bytes
    $input = str_replace("\0", '', $input);

    // Strip tags
    $input = strip_tags($input);

    return $input;
}

/**
 * Input Validation: Email
 *
 * Email formatını kontrol eder
 *
 * @param string $email Kontrol edilecek email
 * @return bool Geçerli mi?
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Input Validation: Integer
 *
 * Integer olup olmadığını kontrol eder
 *
 * @param mixed $value Kontrol edilecek değer
 * @return bool Geçerli mi?
 */
function validateInteger($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

/**
 * Input Validation: Float
 *
 * Float/decimal olup olmadığını kontrol eder
 *
 * @param mixed $value Kontrol edilecek değer
 * @return bool Geçerli mi?
 */
function validateFloat($value) {
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}

/**
 * Input Validation: Date
 *
 * Tarih formatını kontrol eder (YYYY-MM-DD)
 *
 * @param string $date Kontrol edilecek tarih
 * @return bool Geçerli mi?
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Password Hash Oluştur
 *
 * Bcrypt ile güvenli password hash
 *
 * @param string $password Hashlenecek şifre
 * @return string Hashlenmiş şifre
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Password Doğrula
 *
 * Hash ile karşılaştırma yapar
 *
 * @param string $password Girilen şifre
 * @param string $hash Veritabanındaki hash
 * @return bool Şifre doğru mu?
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Güvenli Random String Üret
 *
 * Token, API key vs. için kullanılabilir
 *
 * @param int $length String uzunluğu
 * @return string Random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * IP Adresi Al
 *
 * Gerçek kullanıcı IP adresini döndürür (proxy arkasında bile)
 *
 * @return string IP adresi
 */
function getUserIpAddress() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
               'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

/**
 * Rate Limiting: Basit implementasyon
 *
 * Brute force saldırılarına karşı koruma
 *
 * @param string $identifier Unique identifier (örn: IP, email)
 * @param int $maxAttempts Maksimum deneme sayısı
 * @param int $timeWindow Zaman penceresi (saniye)
 * @return bool Rate limit aşıldı mı?
 */
function isRateLimited($identifier, $maxAttempts = 5, $timeWindow = 300) {
    $key = 'rate_limit_' . md5($identifier);

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }

    $data = $_SESSION[$key];

    // Zaman penceresi dolmuşsa sıfırla
    if ((time() - $data['first_attempt']) > $timeWindow) {
        $_SESSION[$key] = ['count' => 1, 'first_attempt' => time()];
        return false;
    }

    // Deneme sayısını artır
    $_SESSION[$key]['count']++;

    // Limit aşıldı mı?
    return $_SESSION[$key]['count'] > $maxAttempts;
}

/**
 * Rate Limit Sıfırla
 *
 * Başarılı işlemden sonra sayacı sıfırla
 *
 * @param string $identifier Unique identifier
 */
function resetRateLimit($identifier) {
    $key = 'rate_limit_' . md5($identifier);
    unset($_SESSION[$key]);
}

/**
 * Güvenli File Upload Kontrolü
 *
 * Dosya yükleme güvenlik kontrolleri
 *
 * @param array $file $_FILES array element
 * @return array ['success' => bool, 'message' => string]
 */
function validateFileUpload($file) {
    // Dosya yüklendi mi?
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Geçersiz dosya'];
    }

    // Upload hatası var mı?
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Dosya yükleme hatası'];
    }

    // Dosya boyutu kontrolü
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Dosya çok büyük (Max: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)'];
    }

    // Dosya uzantısı kontrolü
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'İzin verilmeyen dosya tipi'];
    }

    // MIME type kontrolü (ek güvenlik)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    $allowedMimes = [
        'image/jpeg', 'image/jpg', 'image/png',
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];

    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'message' => 'Geçersiz dosya formatı'];
    }

    return ['success' => true, 'message' => 'OK'];
}

/**
 * Log Güvenlik Olayları
 *
 * Şüpheli aktiviteleri logla
 *
 * @param string $event Olay açıklaması
 * @param array $context Ekstra bilgi
 */
function logSecurityEvent($event, $context = []) {
    $logFile = __DIR__ . '/../../logs/security.log';

    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'ip' => getUserIpAddress(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'user_id' => $_SESSION['user_id'] ?? null,
        'context' => $context
    ];

    $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL;

    error_log($logLine, 3, $logFile);
}
