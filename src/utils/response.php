<?php
/**
 * HTTP Response Helper Functions
 *
 * API endpoint'leri için JSON response helper'ları
 */

/**
 * JSON Response Gönder
 *
 * @param mixed $data Response data
 * @param int $statusCode HTTP status code
 * @param array $headers Ekstra header'lar
 */
function jsonResponse($data, $statusCode = 200, $headers = []) {
    // HTTP status code ayarla
    http_response_code($statusCode);

    // Header'ları ayarla
    header('Content-Type: application/json; charset=utf-8');

    foreach ($headers as $key => $value) {
        header("$key: $value");
    }

    // JSON encode et ve gönder
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Success Response
 *
 * @param mixed $data Response data
 * @param string $message Başarı mesajı
 * @param int $statusCode HTTP status code
 */
function successResponse($data = null, $message = 'İşlem başarılı', $statusCode = 200) {
    $response = [
        'success' => true,
        'message' => $message,
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    jsonResponse($response, $statusCode);
}

/**
 * Error Response
 *
 * @param string $message Hata mesajı
 * @param int $statusCode HTTP status code
 * @param array $errors Detaylı hata listesi (opsiyonel)
 */
function errorResponse($message = 'Bir hata oluştu', $statusCode = 400, $errors = []) {
    $response = [
        'success' => false,
        'message' => $message,
    ];

    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    jsonResponse($response, $statusCode);
}

/**
 * Validation Error Response
 *
 * @param array $errors Validation hataları ['field' => 'error message']
 * @param string $message Genel hata mesajı
 */
function validationErrorResponse($errors, $message = 'Doğrulama hatası') {
    errorResponse($message, 422, $errors);
}

/**
 * Unauthorized Response (401)
 *
 * @param string $message Hata mesajı
 */
function unauthorizedResponse($message = 'Yetkisiz erişim') {
    errorResponse($message, 401);
}

/**
 * Forbidden Response (403)
 *
 * @param string $message Hata mesajı
 */
function forbiddenResponse($message = 'Bu işlem için yetkiniz yok') {
    errorResponse($message, 403);
}

/**
 * Not Found Response (404)
 *
 * @param string $message Hata mesajı
 */
function notFoundResponse($message = 'Kayıt bulunamadı') {
    errorResponse($message, 404);
}

/**
 * Server Error Response (500)
 *
 * @param string $message Hata mesajı
 */
function serverErrorResponse($message = 'Sunucu hatası') {
    // Detaylı hata production'da gösterilmemeli
    if (APP_ENV === 'development') {
        errorResponse($message, 500);
    } else {
        errorResponse('Bir hata oluştu. Lütfen daha sonra tekrar deneyin.', 500);
    }
}

/**
 * Created Response (201)
 *
 * Yeni kayıt oluşturulduğunda
 *
 * @param mixed $data Oluşturulan kayıt
 * @param string $message Başarı mesajı
 */
function createdResponse($data = null, $message = 'Kayıt oluşturuldu') {
    successResponse($data, $message, 201);
}

/**
 * No Content Response (204)
 *
 * İşlem başarılı ama döndürülecek data yok
 */
function noContentResponse() {
    http_response_code(204);
    exit;
}

/**
 * Paginated Response
 *
 * Sayfalanmış data için
 *
 * @param array $items Data array
 * @param int $total Toplam kayıt sayısı
 * @param int $page Mevcut sayfa
 * @param int $perPage Sayfa başına kayıt
 */
function paginatedResponse($items, $total, $page = 1, $perPage = 20) {
    $totalPages = ceil($total / $perPage);

    $response = [
        'success' => true,
        'data' => $items,
        'pagination' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ]
    ];

    jsonResponse($response);
}

/**
 * API İsteği mi Kontrolü
 *
 * @return bool API isteği mi?
 */
function isApiRequest() {
    // Content-Type: application/json
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        return true;
    }

    // Accept: application/json
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept, 'application/json') !== false) {
        return true;
    }

    // URL'de /api/ var mı?
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requestUri, '/api/') !== false) {
        return true;
    }

    return false;
}

/**
 * JSON Input Al
 *
 * POST body'deki JSON'ı parse et
 *
 * @return array|null Parse edilmiş data
 */
function getJsonInput() {
    $input = file_get_contents('php://input');

    if (empty($input)) {
        return null;
    }

    return json_decode($input, true);
}

/**
 * Request Method Kontrolü
 *
 * @param string $method Beklenen method (GET, POST, PUT, DELETE)
 * @return bool Method eşleşiyor mu?
 */
function isMethod($method) {
    return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
}

/**
 * Request Method Zorunlu Tut
 *
 * Method eşleşmiyorsa 405 döndür
 *
 * @param string|array $allowedMethods İzin verilen method(lar)
 */
function requireMethod($allowedMethods) {
    $allowedMethods = (array) $allowedMethods;
    $currentMethod = $_SERVER['REQUEST_METHOD'];

    if (!in_array($currentMethod, $allowedMethods)) {
        http_response_code(405);
        header('Allow: ' . implode(', ', $allowedMethods));
        errorResponse('Method not allowed', 405);
    }
}

/**
 * CORS Header'ları Ayarla
 *
 * @param array $allowedOrigins İzin verilen origin'ler
 * @param array $allowedMethods İzin verilen methodlar
 */
function setCorsHeaders($allowedOrigins = ['*'], $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE']) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

    if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
    }

    header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400'); // 24 saat

    // OPTIONS (preflight) isteği
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}
