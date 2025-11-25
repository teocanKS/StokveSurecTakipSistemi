<?php
/**
 * Urunler API Endpoint
 *
 * GET /src/api/urunler.php - Liste tüm ürünler (dropdown için)
 */

// Middleware
$requireAuth = true;
$allowedRoles = [ROLE_PERSONEL, ROLE_YONETICI];
$apiMode = true;
require_once __DIR__ . '/../auth/middleware.php';

try {
    $pdo = getDbConnection();

    // GET: Ürün Listesi
    if (isMethod('GET')) {
        $query = '
            SELECT
                "Urun_ID" as id,
                "Urun_Adi" as name,
                "Birim" as unit,
                "kategori" as category
            FROM "Urun"
            WHERE "Urun_Adi" IS NOT NULL
            ORDER BY "Urun_Adi" ASC
        ';

        $stmt = $pdo->query($query);
        $urunler = $stmt->fetchAll();

        successResponse($urunler, 'Ürünler başarıyla alındı');
    }

    else {
        errorResponse('Geçersiz istek metodu', 405);
    }

} catch (Exception $e) {
    error_log('Urunler API error: ' . $e->getMessage());
    serverErrorResponse('Ürün listesi yüklenirken bir hata oluştu');
}
