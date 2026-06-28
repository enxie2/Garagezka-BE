<?php
/**
 * GET  /api/bookings.php        → Daftar booking milik user yang login
 * POST /api/bookings.php        → Buat booking baru
 *
 * Header: Authorization: Bearer <token>
 *
 * POST Body (JSON):
 * {
 *   "kendaraan_id": 1,
 *   "layanan_id": 2,
 *   "tanggal": "2026-07-01",
 *   "jam": "09:00",
 *   "keluhan": "Mesin brebet"
 * }
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();

$conn = getConnection();
$user = validateToken($conn);

// ===================== GET =====================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $uid  = $user['id'];
    $rows = $conn->query("
        SELECT b.id, b.tanggal, b.jam, b.status, b.total_biaya, b.keluhan,
               k.nama_kendaraan, k.nomor_plat,
               l.nama_layanan, l.harga
        FROM booking b
        JOIN kendaraan k ON b.kendaraan_id = k.id
        JOIN layanan   l ON b.layanan_id   = l.id
        WHERE b.user_id = $uid
        ORDER BY b.created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as &$r) {
        $r['total_biaya'] = (int) $r['total_biaya'];
        $r['harga']       = (int) $r['harga'];
    }

    apiResponse([
        'status' => 'success',
        'user'   => $user['nama_lengkap'],
        'total'  => count($rows),
        'data'   => $rows
    ]);
}

// ===================== POST =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body        = json_decode(file_get_contents('php://input'), true);
    $kendaraanId = (int) ($body['kendaraan_id'] ?? 0);
    $layananId   = (int) ($body['layanan_id']   ?? 0);
    $tanggal     = trim($body['tanggal'] ?? '');
    $jam         = trim($body['jam']     ?? '09:00');
    $keluhan     = trim($body['keluhan'] ?? '');

    if (!$kendaraanId || !$layananId || !$tanggal) {
        apiError('Field kendaraan_id, layanan_id, dan tanggal wajib diisi.');
    }

    // Validasi kendaraan milik user ini
    $kend = $conn->query("SELECT id FROM kendaraan WHERE id=$kendaraanId AND user_id={$user['id']}")->fetch_assoc();
    if (!$kend) apiError('Kendaraan tidak ditemukan atau bukan milik Anda.', 404);

    // Ambil harga layanan
    $layan = $conn->query("SELECT harga FROM layanan WHERE id=$layananId AND is_active=1")->fetch_assoc();
    if (!$layan) apiError('Layanan tidak ditemukan.', 404);

    $totalBiaya = $layan['harga'];
    $userId     = $user['id'];

    $stmt = $conn->prepare("INSERT INTO booking (user_id, kendaraan_id, layanan_id, tanggal, jam, keluhan, total_biaya, status) VALUES (?,?,?,?,?,?,?,'pending')");
    $stmt->bind_param("iiisssd", $userId, $kendaraanId, $layananId, $tanggal, $jam, $keluhan, $totalBiaya);

    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        apiResponse([
            'status'     => 'success',
            'message'    => 'Booking berhasil dibuat.',
            'booking_id' => $newId,
            'data'       => [
                'tanggal'     => $tanggal,
                'jam'         => $jam,
                'total_biaya' => $totalBiaya,
                'status'      => 'pending'
            ]
        ], 201);
    } else {
        apiError('Gagal menyimpan booking.', 500);
    }
}

apiError('Method tidak diizinkan.', 405);
?>
