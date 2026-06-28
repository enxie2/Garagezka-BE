<?php
/**
 * GARAGEZKA REST API
 * Base URL: http://localhost/garagezka/api/
 *
 * Endpoints:
 * GET  /api/layanan.php          → Daftar semua layanan
 * GET  /api/bookings.php         → Daftar booking (butuh token)
 * POST /api/bookings.php         → Buat booking baru (butuh token)
 * GET  /api/users.php            → Daftar user (butuh token admin)
 * POST /api/auth.php             → Login & dapatkan token
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

echo json_encode([
    'status'  => 'ok',
    'name'    => 'GARAGEZKA API',
    'version' => '1.0.0',
    'endpoints' => [
        'GET  /api/layanan.php'        => 'Daftar semua layanan servis (publik)',
        'POST /api/auth.php'           => 'Login dan dapatkan API token',
        'GET  /api/bookings.php'       => 'Daftar booking user (butuh Authorization header)',
        'POST /api/bookings.php'       => 'Buat booking baru (butuh Authorization header)',
        'GET  /api/users.php'          => 'Daftar semua user - admin only (butuh Authorization header)',
    ],
    'auth' => 'Bearer Token — dapatkan token via POST /api/auth.php'
], JSON_PRETTY_PRINT);
?>
