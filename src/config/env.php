<?php
/**
 * Environment Variables Loader
 *
 * .env dosyasından environment variables'ları yükler
 */

function loadEnv($path = null) {
    if ($path === null) {
        $path = __DIR__ . '/../../.env';
    }

    if (!file_exists($path)) {
        // .env yoksa, hardcoded values kullan (backward compatibility)
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Yorum satırlarını atla
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // KEY=VALUE formatını parse et
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Tırnak işaretlerini kaldır
            $value = trim($value, '"\'');

            // Environment variable olarak set et
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    return true;
}

function env($key, $default = null) {
    $value = getenv($key);

    if ($value === false) {
        $value = $_ENV[$key] ?? $default;
    }

    // Boolean değerleri dönüştür
    if (is_string($value)) {
        $lower = strtolower($value);
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
        if ($lower === 'null') return null;
    }

    return $value;
}

// .env dosyasını yükle
loadEnv();
