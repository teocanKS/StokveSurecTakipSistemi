<?php
/**
 * Stok API Endpoint
 *
 * GET  /src/api/stok.php - Stok listesi
 * POST /src/api/stok.php - Yeni stok ekleme (Yönetici)
 */

// Middleware
$requireAuth = true;
$apiMode = true;
require_once __DIR__ . '/../auth/middleware.php';

try {
    $pdo = getDbConnection();

    // GET: Stok Listesi
    if (isMethod('GET')) {
        $filters = [];
        $params = [];

        // Filtreleme parametreleri
        $kritikMi = getParam('kritik'); // 'true' ise sadece kritik stoklar

        $query = '
            SELECT
                us."Stok_ID",
                us."Urun_ID",
                u."Urun_Adi",
                u."Birim",
                u."kategori",
                us."Toplam_Stok",
                us."Referans_Degeri"
            FROM "Urun_Stok" us
            INNER JOIN "Urun" u ON us."Urun_ID" = u."Urun_ID"
        ';

        // Kritik stok filtresi
        if ($kritikMi === 'true') {
            $query .= ' WHERE us."Toplam_Stok" <= us."Referans_Degeri"';
        }

        $query .= ' ORDER BY us."Toplam_Stok" ASC';

        $stmt = $pdo->query($query);
        $stoklar = $stmt->fetchAll();

        // Her stok için kritik durumu ekle
        $stoklar = array_map(function($stok) {
            $stok['kritik_mi'] = isKritikStok($stok['Toplam_Stok'], $stok['Referans_Degeri']);
            $stok['durum_class'] = stokDurumClass($stok['Toplam_Stok'], $stok['Referans_Degeri']);
            return $stok;
        }, $stoklar);

        successResponse($stoklar, 'Stok listesi başarıyla alındı');
    }

    // POST: Yeni Stok Ekleme (Sadece Yönetici)
    elseif (isMethod('POST')) {
        // Yönetici kontrolü
        requireRole(ROLE_YONETICI, true);

        // CSRF kontrolü
        $input = getJsonInput();
        if (!isset($input['csrf_token']) || !validateCsrfToken($input['csrf_token'])) {
            errorResponse('Geçersiz CSRF token', 403);
        }

        // Input validation
        $urun_id = $input['urun_id'] ?? null;
        $toplam_stok = $input['toplam_stok'] ?? 0;
        $referans_degeri = $input['referans_degeri'] ?? 0;

        $errors = [];

        if (empty($urun_id) || !validateInteger($urun_id)) {
            $errors['urun_id'] = 'Geçerli bir ürün seçin';
        }

        if (!validateInteger($toplam_stok) || $toplam_stok < 0) {
            $errors['toplam_stok'] = 'Geçerli bir stok miktarı girin';
        }

        if (!validateInteger($referans_degeri) || $referans_degeri < 0) {
            $errors['referans_degeri'] = 'Geçerli bir referans değeri girin';
        }

        if (!empty($errors)) {
            validationErrorResponse($errors);
        }

        // Ürün var mı kontrol et
        $stmt = $pdo->prepare('SELECT "Urun_ID" FROM "Urun" WHERE "Urun_ID" = :urun_id');
        $stmt->execute(['urun_id' => $urun_id]);
        if (!$stmt->fetch()) {
            errorResponse('Ürün bulunamadı', 404);
        }

        // Bu ürün için zaten stok kaydı var mı?
        $stmt = $pdo->prepare('SELECT "Stok_ID" FROM "Urun_Stok" WHERE "Urun_ID" = :urun_id');
        $stmt->execute(['urun_id' => $urun_id]);
        if ($stmt->fetch()) {
            errorResponse('Bu ürün için stok kaydı zaten mevcut', 400);
        }

        // Stok ekle
        $stmt = $pdo->prepare('
            INSERT INTO "Urun_Stok" ("Urun_ID", "Toplam_Stok", "Referans_Degeri")
            VALUES (:urun_id, :toplam_stok, :referans_degeri)
            RETURNING "Stok_ID"
        ');

        $stmt->execute([
            'urun_id' => $urun_id,
            'toplam_stok' => $toplam_stok,
            'referans_degeri' => $referans_degeri
        ]);

        $newStok = $stmt->fetch();

        logSecurityEvent('Yeni stok eklendi', [
            'stok_id' => $newStok['Stok_ID'],
            'urun_id' => $urun_id
        ]);

        createdResponse($newStok, 'Stok başarıyla eklendi');
    }

    else {
        errorResponse('Geçersiz istek metodu', 405);
    }

} catch (Exception $e) {
    error_log('Stok API error: ' . $e->getMessage());
    serverErrorResponse('Stok işlemi sırasında bir hata oluştu');
}
