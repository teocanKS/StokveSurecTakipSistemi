<?php
/**
 * Users API Endpoint
 *
 * GET /src/api/users.php - Kullanıcı listesi
 * PATCH /src/api/users.php - Kullanıcı rol güncelleme
 *
 * SADECE yönetici erişebilir
 */

// Middleware
$requireAuth = true;
$allowedRoles = [ROLE_YONETICI];
$apiMode = true;
require_once __DIR__ . '/../auth/middleware.php';

try {
    $pdo = getDbConnection();

    // GET: Kullanıcı listesi
    if (isMethod('GET')) {
        // Rol filtresi (opsiyonel)
        $roleFilter = $_GET['role'] ?? null;

        $query = 'SELECT "Users_ID", "Name", "Surname", "Email", "role" FROM "Users"';
        $params = [];

        if ($roleFilter && in_array($roleFilter, [ROLE_YONETICI, ROLE_PERSONEL])) {
            $query .= ' WHERE "role" = :role';
            $params[':role'] = $roleFilter;
        }

        $query .= ' ORDER BY "Name", "Surname"';

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        // Password alanını çıkar (güvenlik)
        $users = array_map(function($user) {
            return [
                'Users_ID' => $user['Users_ID'],
                'Name' => $user['Name'],
                'Surname' => $user['Surname'],
                'Email' => $user['Email'],
                'role' => $user['role'],
                'full_name' => trim(($user['Name'] ?? '') . ' ' . ($user['Surname'] ?? ''))
            ];
        }, $users);

        successResponse($users, 'Kullanıcılar başarıyla alındı');
    }

    // PATCH: Rol güncelleme
    elseif (isMethod('PATCH')) {
        $input = getJsonInput();

        // CSRF token kontrolü
        if (!isset($input['csrf_token']) || !validateCsrfToken($input['csrf_token'])) {
            unauthorizedResponse('Geçersiz CSRF token');
        }

        // Validation
        if (empty($input['user_id']) || !validateInteger($input['user_id'])) {
            validationErrorResponse(['user_id' => 'Geçersiz kullanıcı ID']);
        }

        if (empty($input['role']) || !in_array($input['role'], [ROLE_YONETICI, ROLE_PERSONEL])) {
            validationErrorResponse(['role' => 'Geçersiz rol. Sadece "yonetici" veya "personel" olabilir']);
        }

        $userId = (int)$input['user_id'];
        $newRole = $input['role'];

        // Kullanıcı var mı kontrol et
        $stmt = $pdo->prepare('SELECT "Users_ID", "Name", "Surname", "Email", "role" FROM "Users" WHERE "Users_ID" = :id');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            notFoundResponse('Kullanıcı bulunamadı');
        }

        // Kendi rolünü değiştiremez
        if ($userId === $currentUser['id']) {
            forbiddenResponse('Kendi rolünüzü değiştiremezsiniz');
        }

        // Rol güncelle
        $stmt = $pdo->prepare('UPDATE "Users" SET "role" = :role WHERE "Users_ID" = :id');
        $stmt->execute([
            ':role' => $newRole,
            ':id' => $userId
        ]);

        // Güvenlik log'u
        logSecurityEvent('User role updated', [
            'updated_user_id' => $userId,
            'updated_user_email' => $user['Email'],
            'old_role' => $user['role'],
            'new_role' => $newRole,
            'updated_by' => $currentUser['id']
        ]);

        successResponse([
            'user_id' => $userId,
            'new_role' => $newRole
        ], 'Kullanıcı rolü başarıyla güncellendi');
    }

    // PUT / POST: Yeni kullanıcı oluşturma (opsiyonel, gerekirse eklenebilir)
    // Şu an için kullanıcılar manuel olarak veritabanına ekleniyor

    else {
        // Method not allowed
        requireMethod(['GET', 'PATCH']);
    }

} catch (PDOException $e) {
    error_log('Users API DB error: ' . $e->getMessage());
    serverErrorResponse('Veritabanı hatası');
} catch (Exception $e) {
    error_log('Users API error: ' . $e->getMessage());
    serverErrorResponse('İşlem sırasında hata oluştu');
}
