<?php
/**
 * History API Endpoint
 *
 * GET /src/api/history.php - Geçmiş işlemler (gelişmiş filtreleme)
 *
 * Tamamlanmış ve iptal edilmiş tüm işlemleri döndürür
 * Filtreleme: tarih aralığı, firma, ürün, tutar, sıralama
 */

// Middleware
$requireAuth = true;
$apiMode = true;
require_once __DIR__ . '/../auth/middleware.php';

// Sadece GET kabul et
requireMethod('GET');

try {
    $pdo = getDbConnection();

    // Filtreleme parametreleri
    $firma = getParam('firma', '');
    $urun = getParam('urun', '');
    $baslangic_tarihi = getParam('baslangic_tarihi', '');
    $bitis_tarihi = getParam('bitis_tarihi', '');
    $min_tutar = getParam('min_tutar', '');
    $max_tutar = getParam('max_tutar', '');
    $tip = getParam('tip', ''); // 'satis' veya 'alis'
    $sirala = getParam('sirala', 'tarih_desc'); // tarih_desc, tarih_asc, tutar_desc, tutar_asc

    $history = [];

    // 1. Müşteri İşlemleri (Geçmiş)
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
        WHERE (umi."Islemin_Durumu" = \'TAMAMLANDI\' OR umi."Islemin_Durumu" = \'IPTAL EDILDI\')
    ';

    // 2. Tedarikçi Alışları (Geçmiş)
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
        WHERE (uta."Alis_Durumu" = true OR uta."Alis_Durumu" = false)
    ';

    // Filtreleme: Firma
    if (!empty($firma)) {
        $musteriQuery .= ' AND LOWER(m."Musteri_Adi") LIKE :firma';
        $tedarikciQuery .= ' AND LOWER(t."Tedarikci_Adi") LIKE :firma';
    }

    // Filtreleme: Ürün
    if (!empty($urun)) {
        $musteriQuery .= ' AND LOWER(u."Urun_Adi") LIKE :urun';
        $tedarikciQuery .= ' AND LOWER(u."Urun_Adi") LIKE :urun';
    }

    // Filtreleme: Tarih Aralığı
    if (!empty($baslangic_tarihi) && validateDate($baslangic_tarihi)) {
        $musteriQuery .= ' AND umi."Islem_Tarihi" >= :baslangic_tarihi';
        $tedarikciQuery .= ' AND uta."Alis_Tarihi" >= :baslangic_tarihi';
    }

    if (!empty($bitis_tarihi) && validateDate($bitis_tarihi)) {
        $musteriQuery .= ' AND umi."Islem_Tarihi" <= :bitis_tarihi';
        $tedarikciQuery .= ' AND uta."Alis_Tarihi" <= :bitis_tarihi';
    }

    // Filtreleme: Tutar Aralığı
    if (!empty($min_tutar) && validateFloat($min_tutar)) {
        $musteriQuery .= ' AND umi."Toplam_Satis_Tutari" >= :min_tutar';
        $tedarikciQuery .= ' AND uta."Toplam_Alis_Tutari" >= :min_tutar';
    }

    if (!empty($max_tutar) && validateFloat($max_tutar)) {
        $musteriQuery .= ' AND umi."Toplam_Satis_Tutari" <= :max_tutar';
        $tedarikciQuery .= ' AND uta."Toplam_Alis_Tutari" <= :max_tutar';
    }

    // Tip filtresi
    $musteriOperations = [];
    $tedarikciOperations = [];

    if ($tip === '' || $tip === 'satis') {
        // Müşteri işlemlerini al
        $stmt = $pdo->prepare($musteriQuery);

        // Parametreleri bind et
        if (!empty($firma)) $stmt->bindValue(':firma', '%' . strtolower($firma) . '%');
        if (!empty($urun)) $stmt->bindValue(':urun', '%' . strtolower($urun) . '%');
        if (!empty($baslangic_tarihi)) $stmt->bindValue(':baslangic_tarihi', $baslangic_tarihi);
        if (!empty($bitis_tarihi)) $stmt->bindValue(':bitis_tarihi', $bitis_tarihi);
        if (!empty($min_tutar)) $stmt->bindValue(':min_tutar', $min_tutar);
        if (!empty($max_tutar)) $stmt->bindValue(':max_tutar', $max_tutar);

        $stmt->execute();
        $musteriOperations = $stmt->fetchAll();
    }

    if ($tip === '' || $tip === 'alis') {
        // Tedarikçi işlemlerini al
        $stmt = $pdo->prepare($tedarikciQuery);

        // Parametreleri bind et
        if (!empty($firma)) $stmt->bindValue(':firma', '%' . strtolower($firma) . '%');
        if (!empty($urun)) $stmt->bindValue(':urun', '%' . strtolower($urun) . '%');
        if (!empty($baslangic_tarihi)) $stmt->bindValue(':baslangic_tarihi', $baslangic_tarihi);
        if (!empty($bitis_tarihi)) $stmt->bindValue(':bitis_tarihi', $bitis_tarihi);
        if (!empty($min_tutar)) $stmt->bindValue(':min_tutar', $min_tutar);
        if (!empty($max_tutar)) $stmt->bindValue(':max_tutar', $max_tutar);

        $stmt->execute();
        $tedarikciOperations = $stmt->fetchAll();
    }

    // Birleştir
    $history = array_merge($musteriOperations, $tedarikciOperations);

    // Sıralama
    switch ($sirala) {
        case 'tarih_asc':
            usort($history, fn($a, $b) => strtotime($a['tarih']) - strtotime($b['tarih']));
            break;
        case 'tutar_desc':
            usort($history, fn($a, $b) => $b['tutar'] - $a['tutar']);
            break;
        case 'tutar_asc':
            usort($history, fn($a, $b) => $a['tutar'] - $b['tutar']);
            break;
        case 'tarih_desc':
        default:
            usort($history, fn($a, $b) => strtotime($b['tarih']) - strtotime($a['tarih']));
            break;
    }

    // Her kayıt için ek bilgiler
    $history = array_map(function($item) {
        $item['gun_farki'] = gunFarki($item['tarih']);
        $item['durum_class'] = islemDurumClass($item['durum']);
        $item['tarih_formatted'] = formatTarih($item['tarih'], 'short');
        $item['tutar_formatted'] = formatPara($item['tutar']);
        return $item;
    }, $history);

    successResponse($history, 'Geçmiş kayıtlar başarıyla alındı');

} catch (Exception $e) {
    error_log('History API error: ' . $e->getMessage());
    serverErrorResponse('Geçmiş kayıtlar alınırken bir hata oluştu');
}
