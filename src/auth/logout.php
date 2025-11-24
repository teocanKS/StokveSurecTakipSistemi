<?php
/**
 * Logout Handler
 *
 * GET/POST /src/auth/logout.php
 *
 * Kullanıcı çıkışını işler
 */

// Middleware
require_once __DIR__ . '/middleware.php';

// Logout yap
logoutUser();

// Login sayfasına yönlendir
if (isApiRequest()) {
    successResponse(null, 'Çıkış başarılı');
} else {
    $_SESSION['success'] = 'Başarıyla çıkış yaptınız';
    redirect(BASE_URL . 'public/index.php');
}
