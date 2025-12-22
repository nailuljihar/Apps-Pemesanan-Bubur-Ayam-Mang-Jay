<?php
include '../../config/koneksi.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Perbaikan: Hapus 'WHERE status_aktif = 1' biar Admin bisa lihat menu yang disembunyikan
// Tambah kolom 'gambar' dan 'status_aktif'
$sql = "SELECT id_produk, nama_produk, harga, status_aktif, gambar FROM produk ORDER BY id_produk ASC";
$result = $koneksi->query($sql);

$data_produk = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data_produk[] = $row;
    }
    $response = [
        'status' => 'success', 
        'data' => $data_produk
    ];
} else {
    $response = [
        'status' => 'success', // Tetap success biar JS gak error, tapi datanya kosong
        'data' => [] 
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
$koneksi->close();
?>