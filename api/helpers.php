<?php
/**
 * GARAGEZKA API Helper
 * Fungsi-fungsi pembantu untuk semua endpoint API
 */

require_once __DIR__ . '/../config/database.php';

// Set header JSON & CORS untuk semua endpoint
function setApiHeaders() {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Kirim response JSON
function apiResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Kirim response error
function apiError($message, $statusCode = 400) {
    apiResponse(['status' => 'error', 'message' => $message], $statusCode);
}

// Ambil token dari header Authorization: Bearer <token>
function getBearerToken() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        return $matches[1];
    }
    // Fallback: dari query string ?token=xxx
    return $_GET['token'] ?? null;
}

// Validasi token & kembalikan data user
function validateToken($conn) {
    $token = getBearerToken();
    if (!$token) {
        apiError('Token tidak ditemukan. Gunakan header: Authorization: Bearer <token>', 401);
    }
    $stmt = $conn->prepare("SELECT id, nama_lengkap, email, role FROM users WHERE api_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) {
        apiError('Token tidak valid atau sudah kadaluarsa.', 401);
    }
    return $user;
}

// Validasi token & pastikan role admin
function validateAdminToken($conn) {
    $user = validateToken($conn);
    if ($user['role'] !== 'admin') {
        apiError('Akses ditolak. Endpoint ini hanya untuk admin.', 403);
    }
    return $user;
}
?>
