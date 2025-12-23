<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");

require_once '../../config/koneksi.php';

// Ambil ID dari Parameter URL (?id=1) atau Body POST
$id_produk = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id_produk) {
    echo json_encode(["status" => "error", "message" => "ID Produk tidak ditemukan"]);
    exit;
}

// Hapus dari Database
$stmt = $koneksi->prepare("DELETE FROM produk WHERE id_produk = ?");
$stmt->bind_param("i", $id_produk);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Produk berhasil dihapus"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menghapus: " . $koneksi->error]);
}
?>