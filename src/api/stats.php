<?php
/**
 * Stats API Endpoint
 *
 * GET /src/api/stats.php
 *
 * Dashboard KPI kartları için istatistikler döndürür
 */

// Middleware
$requireAuth = true;
$apiMode = true;
require_once __DIR__ . '/../auth/middleware.php';

// Sadece GET kabul et
requireMethod('GET');

try {
    $pdo = getDbConnection();
    $stats = [];

    // 1. Toplam Stok Değeri
    $stmt = $pdo->query('
        SELECT COUNT(*) as toplam_urun,
               SUM("Toplam_Stok") as toplam_stok_adedi
        FROM "Urun_Stok"
    ');
    $stokData = $stmt->fetch();
    $stats['stok'] = [
        'toplam_urun' => (int)($stokData['toplam_urun'] ?? 0),
        'toplam_stok_adedi' => (int)($stokData['toplam_stok_adedi'] ?? 0)
    ];

    // 2. Kritik Stok Sayısı
    // Kritik stok: Toplam_Stok <= Referans_Degeri
    $stmt = $pdo->query('
        SELECT COUNT(*) as kritik_stok_sayisi
        FROM "Urun_Stok"
        WHERE "Toplam_Stok" <= "Referans_Degeri"
    ');
    $kritikData = $stmt->fetch();
    $stats['kritik_stok'] = (int)($kritikData['kritik_stok_sayisi'] ?? 0);

    // 3. Aktif Müşteri İşlemleri (Bugün)
    $stmt = $pdo->query('
        SELECT COUNT(*) as aktif_islem
        FROM "Urun_Musteri_Islem"
        WHERE "Islem_Tarihi" = CURRENT_DATE
        AND ("Islemin_Durumu" IS NULL OR "Islemin_Durumu" NOT IN (\'IPTAL EDILDI\', \'TAMAMLANDI\'))
    ');
    $musteriIslemData = $stmt->fetch();
    $stats['bugunun_islemleri'] = (int)($musteriIslemData['aktif_islem'] ?? 0);

    // 4. Aktif Tedarikçi Alışları (Bugün)
    $stmt = $pdo->query('
        SELECT COUNT(*) as aktif_alis
        FROM "Urun_Tedarikci_Alis"
        WHERE "Alis_Tarihi" = CURRENT_DATE
        AND ("Alis_Durumu" IS NULL OR "Alis_Durumu" = true)
    ');
    $tedarikcİAlisData = $stmt->fetch();
    $stats['bugunun_alislari'] = (int)($tedarikcİAlisData['aktif_alis'] ?? 0);

    // 5. Bu Ayki Toplam Satış (Müşteri İşlemleri)
    $stmt = $pdo->query('
        SELECT COALESCE(SUM("Toplam_Satis_Tutari"), 0) as bu_ay_satis
        FROM "Urun_Musteri_Islem"
        WHERE DATE_TRUNC(\'month\', "Islem_Tarihi") = DATE_TRUNC(\'month\', CURRENT_DATE)
        AND ("Islemin_Durumu" IS NULL OR "Islemin_Durumu" != \'IPTAL EDILDI\')
    ');
    $satisData = $stmt->fetch();
    $stats['bu_ay_satis'] = (float)($satisData['bu_ay_satis'] ?? 0);

    // 6. Bu Ayki Toplam Alış
    $stmt = $pdo->query('
        SELECT COALESCE(SUM("Toplam_Alis_Tutari"), 0) as bu_ay_alis
        FROM "Urun_Tedarikci_Alis"
        WHERE DATE_TRUNC(\'month\', "Alis_Tarihi") = DATE_TRUNC(\'month\', CURRENT_DATE)
        AND ("Alis_Durumu" IS NULL OR "Alis_Durumu" = true)
    ');
    $alisData = $stmt->fetch();
    $stats['bu_ay_alis'] = (float)($alisData['bu_ay_alis'] ?? 0);

    // 7. Toplam Müşteri Sayısı
    $stmt = $pdo->query('SELECT COUNT(*) as toplam_musteri FROM "Musteri"');
    $musteriData = $stmt->fetch();
    $stats['toplam_musteri'] = (int)($musteriData['toplam_musteri'] ?? 0);

    // 8. Toplam Tedarikçi Sayısı
    $stmt = $pdo->query('SELECT COUNT(*) as toplam_tedarikci FROM "Tedarikci"');
    $tedarikciData = $stmt->fetch();
    $stats['toplam_tedarikci'] = (int)($tedarikciData['toplam_tedarikci'] ?? 0);

    // 9. Son 7 Günün Satış Grafiği (günlük)
    $stmt = $pdo->query('
        SELECT
            "Islem_Tarihi" as tarih,
            COUNT(*) as islem_sayisi,
            COALESCE(SUM("Toplam_Satis_Tutari"), 0) as toplam_tutar
        FROM "Urun_Musteri_Islem"
        WHERE "Islem_Tarihi" >= CURRENT_DATE - INTERVAL \'7 days\'
        AND ("Islemin_Durumu" IS NULL OR "Islemin_Durumu" != \'IPTAL EDILDI\')
        GROUP BY "Islem_Tarihi"
        ORDER BY "Islem_Tarihi" ASC
    ');
    $grafikData = $stmt->fetchAll();
    $stats['son_7_gun_satis'] = array_map(function($row) {
        return [
            'tarih' => $row['tarih'],
            'islem_sayisi' => (int)$row['islem_sayisi'],
            'toplam_tutar' => (float)$row['toplam_tutar']
        ];
    }, $grafikData);

    // Başarılı response
    successResponse($stats, 'İstatistikler başarıyla alındı');

} catch (Exception $e) {
    error_log('Stats API error: ' . $e->getMessage());
    serverErrorResponse('İstatistikler alınırken bir hata oluştu');
}
