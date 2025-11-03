<?php
// Pengaturan Koneksi Database
$host = 'localhost'; // Ganti jika host database Anda berbeda
$user = 'root';      // Ganti dengan username database Anda
$pass = '';          // Ganti dengan password database Anda
$db_name = 'bubu_db';

// Membuat koneksi
$koneksi = new mysqli($host, $user, $pass, $db_name);

// Mengecek koneksi
if ($koneksi->connect_error) {
    // Keluar dan menampilkan pesan error jika koneksi gagal
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Opsional: Mengatur header untuk mencegah caching
header("Cache-Control: no-cache, must-revalidate"); 
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 

// Koneksi berhasil
// echo "Koneksi database berhasil!"; 
?>