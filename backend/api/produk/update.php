<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../../config/koneksi.php';

// 1. Ambil Data
$id_produk = $_POST['id_produk'] ?? null;
$nama_produk = $_POST['nama_produk'] ?? '';
$harga = $_POST['harga'] ?? 0;
$status_aktif = $_POST['status_aktif'] ?? 1;

if (!$id_produk) {
    echo json_encode(["status" => "error", "message" => "ID Produk diperlukan"]);
    exit;
}

// 2. Cek Apakah Ada Upload Gambar Baru?
$query_update = "";
$params = [];
$types = "";

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
    // --- LOGIKA UPLOAD GAMBAR BARU ---
    $target_dir = "../../../frontend/assets/images/";
    $ext = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
    $nama_file_baru = "img_" . uniqid() . "." . $ext;
    
    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $nama_file_baru)) {
        // Query update DENGAN gambar
        $query_update = "UPDATE produk SET nama_produk=?, harga=?, status_aktif=?, gambar=? WHERE id_produk=?";
        $params = [$nama_produk, $harga, $status_aktif, $nama_file_baru, $id_produk];
        $types = "siisi";
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal upload gambar"]);
        exit;
    }
} else {
    // Query update TANPA ganti gambar
    $query_update = "UPDATE produk SET nama_produk=?, harga=?, status_aktif=? WHERE id_produk=?";
    $params = [$nama_produk, $harga, $status_aktif, $id_produk];
    $types = "siii";
}

// 3. Eksekusi Query
$stmt = $koneksi->prepare($query_update);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Produk berhasil diperbarui"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}
?>