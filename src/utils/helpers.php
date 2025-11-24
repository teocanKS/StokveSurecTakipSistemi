<?php
/**
 * Yardımcı Fonksiyonlar
 *
 * Genel utility fonksiyonları
 */

/**
 * Tarih Formatla (Türkçe)
 *
 * @param string $date Tarih (Y-m-d format)
 * @param string $format Format tipi ('short', 'long', 'datetime')
 * @return string Formatlanmış tarih
 */
function formatTarih($date, $format = 'short') {
    if (empty($date)) {
        return '-';
    }

    try {
        $dt = new DateTime($date);

        switch ($format) {
            case 'short':
                return $dt->format('d.m.Y');

            case 'long':
                $aylar = [
                    1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
                    5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
                    9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
                ];
                $gun = $dt->format('d');
                $ay = $aylar[(int)$dt->format('m')];
                $yil = $dt->format('Y');
                return "$gun $ay $yil";

            case 'datetime':
                return $dt->format('d.m.Y H:i');

            case 'time':
                return $dt->format('H:i');

            default:
                return $dt->format('d.m.Y');
        }
    } catch (Exception $e) {
        return '-';
    }
}

/**
 * Para Formatla (Türk Lirası)
 *
 * @param float $amount Miktar
 * @param bool $showCurrency Para birimi gösterilsin mi?
 * @return string Formatlanmış para
 */
function formatPara($amount, $showCurrency = true) {
    if (!is_numeric($amount)) {
        return $showCurrency ? '0,00 ₺' : '0,00';
    }

    $formatted = number_format($amount, 2, ',', '.');

    return $showCurrency ? $formatted . ' ₺' : $formatted;
}

/**
 * Sayı Formatla (Türkçe)
 *
 * @param mixed $number Sayı
 * @param int $decimals Ondalık basamak sayısı
 * @return string Formatlanmış sayı
 */
function formatSayi($number, $decimals = 0) {
    if (!is_numeric($number)) {
        return '0';
    }

    return number_format($number, $decimals, ',', '.');
}

/**
 * İki Tarih Arasındaki Gün Farkı
 *
 * @param string $date1 İlk tarih
 * @param string $date2 İkinci tarih (null ise bugün)
 * @return int Gün farkı
 */
function gunFarki($date1, $date2 = null) {
    try {
        $dt1 = new DateTime($date1);
        $dt2 = $date2 ? new DateTime($date2) : new DateTime();

        $diff = $dt1->diff($dt2);
        return (int)$diff->format('%R%a');
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Geçen Süre (Human Readable)
 *
 * Örn: "2 saat önce", "3 gün önce"
 *
 * @param string $datetime Tarih/saat
 * @return string İnsan okunabilir format
 */
function gecenSure($datetime) {
    if (empty($datetime)) {
        return '-';
    }

    try {
        $now = new DateTime();
        $past = new DateTime($datetime);
        $diff = $now->diff($past);

        if ($diff->y > 0) {
            return $diff->y . ' yıl önce';
        } elseif ($diff->m > 0) {
            return $diff->m . ' ay önce';
        } elseif ($diff->d > 0) {
            return $diff->d . ' gün önce';
        } elseif ($diff->h > 0) {
            return $diff->h . ' saat önce';
        } elseif ($diff->i > 0) {
            return $diff->i . ' dakika önce';
        } else {
            return 'Az önce';
        }
    } catch (Exception $e) {
        return '-';
    }
}

/**
 * String'i URL-safe slug'a çevir
 *
 * @param string $text Dönüştürülecek metin
 * @return string Slug
 */
function slugify($text) {
    // Türkçe karakterleri değiştir
    $turkish = ['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü'];
    $english = ['c', 'c', 'g', 'g', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u'];
    $text = str_replace($turkish, $english, $text);

    // Küçük harfe çevir
    $text = strtolower($text);

    // Alfanumerik olmayan karakterleri tire ile değiştir
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    // Baştaki ve sondaki tireleri kaldır
    $text = trim($text, '-');

    return $text;
}

/**
 * Truncate (Metin Kısalt)
 *
 * @param string $text Kısaltılacak metin
 * @param int $length Maksimum uzunluk
 * @param string $suffix Sonuna eklenecek (varsayılan: '...')
 * @return string Kısaltılmış metin
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Array'den belirli key'leri seç
 *
 * @param array $array Kaynak array
 * @param array $keys Seçilecek key'ler
 * @return array Filtrelenmiş array
 */
function arrayOnly($array, $keys) {
    return array_intersect_key($array, array_flip($keys));
}

/**
 * GET parametresi al (güvenli)
 *
 * @param string $key Parametre adı
 * @param mixed $default Varsayılan değer
 * @return mixed Parametre değeri
 */
function getParam($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * POST parametresi al (güvenli)
 *
 * @param string $key Parametre adı
 * @param mixed $default Varsayılan değer
 * @return mixed Parametre değeri
 */
function postParam($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * Redirect (Yönlendirme)
 *
 * @param string $url Hedef URL
 * @param int $statusCode HTTP status code (varsayılan: 302)
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * JSON parse et (güvenli)
 *
 * @param string $json JSON string
 * @param bool $assoc Associative array döndür
 * @return mixed Parse edilmiş data veya null
 */
function jsonDecode($json, $assoc = true) {
    try {
        return json_decode($json, $assoc);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * JSON encode et (güvenli)
 *
 * @param mixed $data Encode edilecek data
 * @param int $options JSON options
 * @return string JSON string
 */
function jsonEncode($data, $options = JSON_UNESCAPED_UNICODE) {
    return json_encode($data, $options);
}

/**
 * Boş değil mi kontrol et
 *
 * @param mixed $value Kontrol edilecek değer
 * @return bool Boş değil mi?
 */
function isNotEmpty($value) {
    return !empty($value) || $value === '0' || $value === 0;
}

/**
 * Dizinin tüm elemanları boş değil mi?
 *
 * @param array $array Kontrol edilecek array
 * @param array $keys Kontrol edilecek key'ler
 * @return bool Tüm elemanlar dolu mu?
 */
function allNotEmpty($array, $keys) {
    foreach ($keys as $key) {
        if (!isset($array[$key]) || empty($array[$key])) {
            return false;
        }
    }
    return true;
}

/**
 * Debug Helper (sadece development'ta çalışır)
 *
 * @param mixed $data Debug edilecek data
 * @param bool $die Script'i durdur
 */
function dd($data, $die = true) {
    if (APP_ENV === 'development') {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';

        if ($die) {
            die();
        }
    }
}

/**
 * Basit Logger
 *
 * @param string $message Log mesajı
 * @param string $level Log seviyesi (INFO, WARNING, ERROR)
 */
function logMessage($message, $level = 'INFO') {
    $logFile = __DIR__ . '/../../logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logLine = "[$timestamp] [$level] $message" . PHP_EOL;

    error_log($logLine, 3, $logFile);
}

/**
 * Rol Kontrolü
 *
 * @param string $requiredRole Gerekli rol
 * @return bool Kullanıcının rolü uygun mu?
 */
function hasRole($requiredRole) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }

    $userRole = $_SESSION['user_role'];

    // Yönetici her şeye erişebilir
    if ($userRole === ROLE_YONETICI) {
        return true;
    }

    // Diğer roller eşleşmeli
    return $userRole === $requiredRole;
}

/**
 * Kritik Stok Kontrolü
 *
 * Stok seviyesi kritik seviyede mi?
 *
 * @param int $toplam_stok Mevcut stok
 * @param int $referans_degeri Kritik seviye
 * @return bool Kritik mi?
 */
function isKritikStok($toplam_stok, $referans_degeri) {
    return $toplam_stok <= $referans_degeri;
}

/**
 * Stok Durum Badge Sınıfı
 *
 * @param int $toplam_stok Mevcut stok
 * @param int $referans_degeri Kritik seviye
 * @return string CSS class
 */
function stokDurumClass($toplam_stok, $referans_degeri) {
    if ($toplam_stok == 0) {
        return 'bg-red-600 text-white'; // Tükendi
    } elseif ($toplam_stok <= $referans_degeri) {
        return 'bg-orange-500 text-white'; // Kritik
    } elseif ($toplam_stok <= ($referans_degeri * 1.5)) {
        return 'bg-yellow-500 text-white'; // Düşük
    } else {
        return 'bg-green-600 text-white'; // Normal
    }
}

/**
 * İşlem Durumu Badge Sınıfı
 *
 * @param string $durum İşlem durumu
 * @return string CSS class
 */
function islemDurumClass($durum) {
    $durum = strtoupper($durum ?? '');

    switch ($durum) {
        case 'TAMAMLANDI':
        case 'TESLIM EDILDI':
            return 'bg-green-600 text-white';

        case 'DEVAM EDIYOR':
        case 'HAZIRLANIYOR':
            return 'bg-blue-600 text-white';

        case 'BEKLEMEDE':
            return 'bg-yellow-500 text-white';

        case 'IPTAL EDILDI':
        case 'IPTAL EDİLDİ':
            return 'bg-red-600 text-white';

        default:
            return 'bg-gray-500 text-white';
    }
}
