<?php
/**
 * Operations API Endpoint
 *
 * GET /src/api/operations.php - Aktif operasyonlar listesi
 *
 * Müşteri işlemleri (satış) ve Tedarikçi alışlarını birleştirir
 */

// Middleware
$requireAuth = true;
$apiMode = true;
require_once __DIR__ . '/../auth/middleware.php';

// Sadece GET kabul et
requireMethod('GET');

try {
    $pdo = getDbConnection();

    $filter = getParam('filter', 'aktif'); // 'aktif' veya 'tamamlanan'
    $operations = [];

    // 1. Müşteri İşlemleri (Satış)
    $musteriQuery = '
        SELECT
            umi."Islem_ID" as id,
            \'satis\' as tip,
            m."Musteri_Adi" as firma,
            u."Urun_Adi" as urun,
            umi."Satilan_Adet" as adet,
            umi."Toplam_Satis_Tutari" as tutar,
            umi."Urun_Satis_Fiyati" as birim_fiyat,
            umi."Islem_Tarihi" as tarih,
            umi."Musteri_Teslimat_Tarihi" as teslimat_tarihi,
            umi."Islemin_Durumu" as durum
        FROM "Urun_Musteri_Islem" umi
        LEFT JOIN "Musteri" m ON umi."Musteri_ID" = m."Musteri_ID"
        LEFT JOIN "Urun" u ON umi."Urun_ID" = u."Urun_ID"
    ';

    // 2. Tedarikçi Alışları
    $tedarikciQuery = '
        SELECT
            uta."Alis_ID" as id,
            \'alis\' as tip,
            t."Tedarikci_Adi" as firma,
            u."Urun_Adi" as urun,
            uta."Alinan_Adet" as adet,
            uta."Toplam_Alis_Tutari" as tutar,
            uta."Urun_Alis_Fiyati" as birim_fiyat,
            uta."Alis_Tarihi" as tarih,
            uta."Tedarikci_Teslimat_Tarihi" as teslimat_tarihi,
            CASE
                WHEN uta."Alis_Durumu" = true THEN \'TAMAMLANDI\'
                WHEN uta."Alis_Durumu" = false THEN \'IPTAL EDILDI\'
                ELSE \'DEVAM EDIYOR\'
            END as durum
        FROM "Urun_Tedarikci_Alis" uta
        LEFT JOIN "Tedarikci" t ON uta."Tedarikci_ID" = t."Tedarikci_ID"
        LEFT JOIN "Urun" u ON uta."Urun_ID" = u."Urun_ID"
    ';

    // Filtreleme
    if ($filter === 'aktif') {
        // Aktif işlemler: tamamlanmamış veya iptal edilmemiş
        $musteriQuery .= ' WHERE (umi."Islemin_Durumu" IS NULL OR umi."Islemin_Durumu" NOT IN (\'IPTAL EDILDI\', \'TAMAMLANDI\'))';
        $tedarikciQuery .= ' WHERE (uta."Alis_Durumu" IS NULL OR uta."Alis_Durumu" != false)';
    } elseif ($filter === 'tamamlanan') {
        $musteriQuery .= ' WHERE umi."Islemin_Durumu" = \'TAMAMLANDI\'';
        $tedarikciQuery .= ' WHERE uta."Alis_Durumu" = true';
    }

    $musteriQuery .= ' ORDER BY umi."Islem_Tarihi" DESC';
    $tedarikciQuery .= ' ORDER BY uta."Alis_Tarihi" DESC';

    // Müşteri işlemlerini al
    $stmt = $pdo->query($musteriQuery);
    $musteriOperations = $stmt->fetchAll();

    // Tedarikçi işlemlerini al
    $stmt = $pdo->query($tedarikciQuery);
    $tedarikciOperations = $stmt->fetchAll();

    // Birleştir
    $operations = array_merge($musteriOperations, $tedarikciOperations);

    // Tarihe göre sırala (en yeni önce)
    usort($operations, function($a, $b) {
        return strtotime($b['tarih']) - strtotime($a['tarih']);
    });

    // Her operasyon için ek bilgiler ekle
    $operations = array_map(function($op) {
        // Gün farkı hesapla
        $op['gun_farki'] = gunFarki($op['tarih']);

        // Durum class
        $op['durum_class'] = islemDurumClass($op['durum']);

        // Teslimat durumu
        if ($op['teslimat_tarihi']) {
            $op['teslimat_gun_farki'] = gunFarki($op['teslimat_tarihi']);
        }

        return $op;
    }, $operations);

    successResponse($operations, 'Operasyonlar başarıyla alındı');

} catch (Exception $e) {
    error_log('Operations API error: ' . $e->getMessage());
    serverErrorResponse('Operasyonlar alınırken bir hata oluştu');
}
