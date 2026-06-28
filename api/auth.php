<?php
/**
 * POST /api/auth.php
 * Login dan dapatkan API Token
 *
 * Request Body (JSON):
 * {
 *   "email": "admin@garagezka.com",
 *   "password": "admin123"
 * }
 *
 * Response:
 * {
 *   "status": "success",
 *   "token": "xxxxx",
 *   "user": { ... }
 * }
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiError('Method tidak diizinkan. Gunakan POST.', 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$email    = trim($body['email'] ?? '');
$password = trim($body['password'] ?? '');

if (empty($email) || empty($password)) {
    apiError('Email dan password wajib diisi.');
}

$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    apiError('Email atau password salah.', 401);
}

// Generate token unik jika belum ada
$token = $user['api_token'];
if (empty($token)) {
    $token = bin2hex(random_bytes(32));
    $stmtToken = $conn->prepare("UPDATE users SET api_token = ? WHERE id = ?");
    $stmtToken->bind_param("si", $token, $user['id']);
    $stmtToken->execute();
}

apiResponse([
    'status'  => 'success',
    'message' => 'Login berhasil',
    'token'   => $token,
    'user'    => [
        'id'          => $user['id'],
        'nama'        => $user['nama_lengkap'],
        'email'       => $user['email'],
        'role'        => $user['role'],
        'no_hp'       => $user['no_hp'],
    ]
]);
?>
