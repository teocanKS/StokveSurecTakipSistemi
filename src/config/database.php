<?php
/**
 * Supabase Database Configuration
 *
 * Bu dosya Supabase PostgreSQL bağlantısını yönetir.
 * Environment variables (.env) veya hardcoded values kullanır.
 *
 * Güvenlik notu: Service role key sadece backend'de kullanılır.
 * Frontend'e ASLA göndermeyin!
 */

// Environment variables yükle
require_once __DIR__ . '/env.php';

// Supabase Project URL
define('SUPABASE_URL', env('SUPABASE_URL', 'https://kyceuinsjetbbyfleyfq.supabase.co'));

// Supabase Service Role Key (Backend için - RLS bypass)
// CRITICAL: Bu key asla frontend'e gönderilmemeli!
define('SUPABASE_SERVICE_KEY', env('SUPABASE_SERVICE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imt5Y2V1aW5zamV0YmJ5ZmxleWZxIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2MTE5OTMyNywiZXhwIjoyMDc2Nzc1MzI3fQ.pawxMfbc8key4mucABkkBnSY4rKAjSooOr0hSpTsPKQ'));

// Supabase Anon Key (Frontend için - public kullanım)
define('SUPABASE_ANON_KEY', env('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imt5Y2V1aW5zamV0YmJ5ZmxleWZxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjExOTkzMjcsImV4cCI6MjA3Njc3NTMyN30.Wsdd0bcVnwcnl6el5tgAlwmUKwn-Qymy6jgHvHYCV3M'));

// PostgreSQL Direct Connection (PHP PDO için)
define('DB_HOST', env('DB_HOST', 'db.kyceuinsjetbbyfleyfq.supabase.co'));
define('DB_PORT', env('DB_PORT', '5432'));
define('DB_NAME', env('DB_NAME', 'postgres'));
define('DB_USER', env('DB_USER', 'postgres'));
define('DB_PASS', env('DB_PASSWORD', 'TeoYagmurDenizMali19031905'));

// PDO DSN (Data Source Name)
define('DB_DSN', sprintf(
    'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
    DB_HOST,
    DB_PORT,
    DB_NAME
));

/**
 * PDO Bağlantısı Oluştur
 *
 * Güvenli PostgreSQL bağlantısı (SSL zorunlu)
 * Prepared statements kullanımı için optimize edilmiş
 *
 * @return PDO
 * @throws PDOException
 */
function getDbConnection() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            // PDO options - güvenlik ve performans için
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Hataları exception olarak fırlat
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Associative array olarak fetch et
                PDO::ATTR_EMULATE_PREPARES   => false,                   // Gerçek prepared statements kullan
                PDO::ATTR_STRINGIFY_FETCHES  => false,                   // Tip dönüşümünü devre dışı bırak
                PDO::ATTR_PERSISTENT         => false,                   // Persistent connection kullanma (güvenlik)
            ];

            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            // Güvenlik: Hata mesajlarında hassas bilgi gösterme
            error_log('Database connection error: ' . $e->getMessage());
            throw new Exception('Veritabanı bağlantısı kurulamadı. Lütfen sistem yöneticisine başvurun.');
        }
    }

    return $pdo;
}

/**
 * Supabase REST API ile GET isteği
 *
 * @param string $table Tablo adı (örn: "Users", "Urun_Stok")
 * @param array $filters Filtreler (optional)
 * @return array
 */
function supabaseGet($table, $filters = []) {
    $url = SUPABASE_URL . '/rest/v1/' . $table;

    // Query string oluştur
    if (!empty($filters)) {
        $queryParams = [];
        foreach ($filters as $key => $value) {
            $queryParams[] = $key . '=eq.' . urlencode($value);
        }
        $url .= '?' . implode('&', $queryParams);
    }

    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Supabase GET error: HTTP $httpCode - $response");
        return [];
    }

    return json_decode($response, true) ?: [];
}

/**
 * Supabase REST API ile POST isteği
 *
 * @param string $table Tablo adı
 * @param array $data Eklenecek veri
 * @return array|false
 */
function supabasePost($table, $data) {
    $url = SUPABASE_URL . '/rest/v1/' . $table;

    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'  // Eklenen veriyi döndür
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 201) {
        error_log("Supabase POST error: HTTP $httpCode - $response");
        return false;
    }

    return json_decode($response, true) ?: [];
}
