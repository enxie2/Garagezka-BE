<?php
/**
 * KONFIGURASI DATABASE
 * Salin file ini menjadi config/database.php
 * lalu sesuaikan dengan setting database lokal Anda
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Username MySQL Anda
define('DB_PASS', '');           // Password MySQL Anda (kosong untuk XAMPP default)
define('DB_NAME', 'garagezka');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
