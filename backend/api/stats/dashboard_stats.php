<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/koneksi.php';

// 1. Hitung Pesanan Hari Ini
$query_orders = "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()";
$res_orders = $koneksi->query($query_orders)->fetch_assoc();

// 2. Hitung Pendapatan Hari Ini (Hanya status sukses/lunas/dikemas/diantar/selesai, abaikan Batal/Pending jika mau)
// Di sini kita ambil semua kecuali 'Batal' dan 'Pending' agar pendapatan riil
$query_income = "SELECT SUM(total_pendapatan) as total FROM transaksi 
                 WHERE DATE(tanggal) = CURDATE() AND status NOT IN ('Batal', 'Pending')";
$res_income = $koneksi->query($query_income)->fetch_assoc();

// 3. Hitung Total Produk Aktif
$query_produk = "SELECT COUNT(*) as total FROM produk WHERE status_aktif = 1";
$res_produk = $koneksi->query($query_produk)->fetch_assoc();

// 4. Hitung Total Pelanggan (User)
$query_users = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$res_users = $koneksi->query($query_users)->fetch_assoc();

// Kirim Data JSON
echo json_encode([
    "orders_today" => $res_orders['total'] ?? 0,
    "income_today" => $res_income['total'] ?? 0,
    "active_products" => $res_produk['total'] ?? 0,
    "total_users" => $res_users['total'] ?? 0
]);
?>