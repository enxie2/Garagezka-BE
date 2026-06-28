<?php
/**
 * GET /api/users.php
 * Daftar semua user - ADMIN ONLY
 *
 * Header: Authorization: Bearer <token_admin>
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiError('Method tidak diizinkan. Gunakan GET.', 405);
}

$conn = getConnection();
validateAdminToken($conn);

$users = $conn->query("
    SELECT u.id, u.nama_lengkap, u.email, u.no_hp, u.created_at,
           COUNT(DISTINCT k.id) AS total_kendaraan,
           COUNT(DISTINCT b.id) AS total_booking
    FROM users u
    LEFT JOIN kendaraan k ON k.user_id = u.id
    LEFT JOIN booking   b ON b.user_id = u.id
    WHERE u.role = 'user'
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

foreach ($users as &$u) {
    $u['total_kendaraan'] = (int) $u['total_kendaraan'];
    $u['total_booking']   = (int) $u['total_booking'];
}

apiResponse([
    'status' => 'success',
    'total'  => count($users),
    'data'   => $users
]);
?>
