<?php
/**
 * Users API Endpoint
 *
 * GET  /src/api/users.php - Liste tüm kullanıcılar (Yönetici only)
 * POST /src/api/users.php - Kullanıcı onaylama/güncelleme (Yönetici only)
 */

// Middleware
$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
$apiMode = true;
require_once __DIR__ . '/../auth/middleware.php';

try {
    $pdo = getDbConnection();

    // GET: Kullanıcı Listesi
    if (isMethod('GET')) {
        $query = '
            SELECT
                "Users_ID" as id,
                "Email" as email,
                "full_name" as full_name,
                "role",
                "is_approved",
                "created_at"
            FROM "Users"
            ORDER BY "created_at" DESC
        ';

        $stmt = $pdo->query($query);
        $users = $stmt->fetchAll();

        // Her kullanıcı için ek bilgiler
        $users = array_map(function($user) {
            $user['is_approved'] = (bool) $user['is_approved'];
            $user['role_label'] = $user['role'] === 'yonetici' ? 'Yönetici' : 'Personel';
            return $user;
        }, $users);

        successResponse($users, 'Kullanıcılar başarıyla alındı');
    }

    // POST: Kullanıcı Güncelleme
    elseif (isMethod('POST')) {
        // CSRF kontrolü
        $input = getJsonInput();
        if (!isset($input['csrf_token']) || !validateCsrfToken($input['csrf_token'])) {
            errorResponse('Geçersiz CSRF token', 403);
        }

        // Input validation
        $user_id = $input['user_id'] ?? null;
        $action = $input['action'] ?? null; // 'approve' veya 'reject'

        if (empty($user_id) || !validateInteger($user_id)) {
            errorResponse('Geçerli bir kullanıcı seçin', 400);
        }

        if (!in_array($action, ['approve', 'reject'])) {
            errorResponse('Geçersiz işlem', 400);
        }

        // Kullanıcı var mı kontrol et
        $stmt = $pdo->prepare('SELECT "Users_ID", "Email" FROM "Users" WHERE "Users_ID" = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            errorResponse('Kullanıcı bulunamadı', 404);
        }

        // Kendi kendini reddedemez/onaylayamaz
        if ($user_id == $currentUser['Users_ID']) {
            errorResponse('Kendi hesabınızı güncelleyemezsiniz', 400);
        }

        // Güncelle
        $is_approved = ($action === 'approve');

        $stmt = $pdo->prepare('
            UPDATE "Users"
            SET "is_approved" = :is_approved
            WHERE "Users_ID" = :user_id
        ');

        $stmt->execute([
            'is_approved' => $is_approved,
            'user_id' => $user_id
        ]);

        logSecurityEvent('User status updated', [
            'target_user_id' => $user_id,
            'action' => $action,
            'by_user_id' => $currentUser['Users_ID']
        ]);

        successResponse([
            'user_id' => $user_id,
            'is_approved' => $is_approved
        ], 'Kullanıcı durumu güncellendi');
    }

    else {
        errorResponse('Geçersiz istek metodu', 405);
    }

} catch (Exception $e) {
    error_log('Users API error: ' . $e->getMessage());
    serverErrorResponse('Kullanıcı işlemi sırasında bir hata oluştu');
}
