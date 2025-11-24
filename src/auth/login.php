<?php
/**
 * Login Handler
 *
 * POST /src/auth/login.php
 *
 * Kullanıcı girişini işler
 */

// Middleware (auth gereksiz, çünkü zaten login sayfası)
$requireAuth = false;
require_once __DIR__ . '/middleware.php';

// Sadece POST kabul et
requireMethod('POST');

// CSRF token kontrolü
if (!validateCsrfToken(postParam('csrf_token'))) {
    logSecurityEvent('CSRF token validation failed on login', [
        'ip' => getUserIpAddress()
    ]);

    if (isApiRequest()) {
        errorResponse('Geçersiz token. Lütfen sayfayı yenileyin.', 403);
    } else {
        $_SESSION['error'] = 'Güvenlik hatası. Lütfen tekrar deneyin.';
        redirect(BASE_URL . 'public/index.php');
    }
}

// Input'ları al ve sanitize et
$email = sanitizeString(postParam('email', ''));
$password = postParam('password', '');

// Validation
$errors = [];

if (empty($email)) {
    $errors['email'] = 'E-posta adresi gerekli';
} elseif (!validateEmail($email)) {
    $errors['email'] = 'Geçerli bir e-posta adresi girin';
}

if (empty($password)) {
    $errors['password'] = 'Şifre gerekli';
}

if (!empty($errors)) {
    if (isApiRequest()) {
        validationErrorResponse($errors);
    } else {
        $_SESSION['error'] = 'Lütfen tüm alanları doldurun';
        $_SESSION['old_email'] = $email;
        redirect(BASE_URL . 'public/index.php');
    }
}

// Rate limiting kontrolü (brute force koruması)
$rateLimitKey = 'login_' . getUserIpAddress();

if (isRateLimited($rateLimitKey, 5, 300)) { // 5 deneme / 5 dakika
    logSecurityEvent('Login rate limit exceeded', [
        'ip' => getUserIpAddress(),
        'email' => $email
    ]);

    if (isApiRequest()) {
        errorResponse('Çok fazla başarısız deneme. Lütfen 5 dakika sonra tekrar deneyin.', 429);
    } else {
        $_SESSION['error'] = 'Çok fazla başarısız deneme. Lütfen 5 dakika sonra tekrar deneyin.';
        redirect(BASE_URL . 'public/index.php');
    }
}

try {
    // Veritabanı bağlantısı
    $pdo = getDbConnection();

    // Kullanıcıyı email ile bul
    // NOT: Password'ü de çekiyoruz (hash'lenmiş olarak saklanıyor olmalı)
    $stmt = $pdo->prepare('
        SELECT "Users_ID", "Name", "Surname", "Email", "Password", "role"
        FROM "Users"
        WHERE "Email" = :email
        LIMIT 1
    ');

    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Kullanıcı bulunamadı veya şifre yanlış
    if (!$user || !verifyPassword($password, $user['Password'])) {
        // Güvenlik: Kullanıcı bulunamadı mı yoksa şifre mi yanlış belirtme
        logSecurityEvent('Failed login attempt', [
            'ip' => getUserIpAddress(),
            'email' => $email,
            'reason' => !$user ? 'user_not_found' : 'invalid_password'
        ]);

        if (isApiRequest()) {
            errorResponse('E-posta veya şifre hatalı', 401);
        } else {
            $_SESSION['error'] = 'E-posta veya şifre hatalı';
            $_SESSION['old_email'] = $email;
            redirect(BASE_URL . 'public/index.php');
        }
    }

    // Başarılı login!
    resetRateLimit($rateLimitKey);

    // Session'a kullanıcı bilgilerini kaydet
    loginUser($user);

    // Return URL varsa oraya yönlendir
    $returnUrl = $_SESSION['return_url'] ?? null;
    unset($_SESSION['return_url']);

    // Kullanıcıyı rolüne göre yönlendir
    if ($returnUrl) {
        $redirectUrl = $returnUrl;
    } else {
        $redirectUrl = ($user['role'] === ROLE_YONETICI)
            ? BASE_URL . 'public/yonetici/dashboard.php'
            : BASE_URL . 'public/personel/dashboard.php';
    }

    if (isApiRequest()) {
        successResponse([
            'redirect' => $redirectUrl,
            'user' => getCurrentUser()
        ], 'Giriş başarılı');
    } else {
        $_SESSION['success'] = 'Hoş geldiniz, ' . $user['Name'] . '!';
        redirect($redirectUrl);
    }

} catch (Exception $e) {
    // Hata logla
    error_log('Login error: ' . $e->getMessage());
    logSecurityEvent('Login system error', [
        'error' => $e->getMessage(),
        'email' => $email
    ]);

    if (isApiRequest()) {
        serverErrorResponse('Giriş işlemi sırasında bir hata oluştu');
    } else {
        $_SESSION['error'] = 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
        redirect(BASE_URL . 'public/index.php');
    }
}
