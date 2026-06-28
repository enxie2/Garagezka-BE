<?php
/**
 * GET /api/layanan.php
 * Ambil semua layanan servis (PUBLIK - tidak perlu token)
 *
 * Response:
 * {
 *   "status": "success",
 *   "total": 8,
 *   "data": [ { "id": 1, "nama_layanan": "Ganti Oli", ... } ]
 * }
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    apiError('Method tidak diizinkan. Gunakan GET.', 405);
}

$conn   = getConnection();
$result = $conn->query("SELECT id, nama_layanan, deskripsi, harga, estimasi_waktu, icon FROM layanan WHERE is_active = 1 ORDER BY id ASC");
$data   = $result->fetch_all(MYSQLI_ASSOC);

// Format harga sebagai integer
foreach ($data as &$row) {
    $row['harga'] = (int) $row['harga'];
}

apiResponse([
    'status' => 'success',
    'total'  => count($data),
    'data'   => $data
]);
?>
