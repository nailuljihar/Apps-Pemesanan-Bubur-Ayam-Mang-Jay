<?php
// Header agar bisa diakses dari frontend (CORS & JSON)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../../config/koneksi.php';

// Cek apakah request method benar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode tidak diizinkan"]);
    exit;
}

// Ambil data dari form (FormData di frontend mengirim $_POST dan $_FILES)
$nama_produk = $_POST['nama_produk'] ?? '';
$harga = $_POST['harga'] ?? '';
$status_aktif = 1; // Default aktif

// Validasi Input Sederhana
if (empty($nama_produk) || empty($harga)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Nama dan Harga wajib diisi!"]);
    exit;
}

// LOGIKA UPLOAD GAMBAR
$nama_gambar_baru = 'bubur-ayam1.jpg'; // Default jika tidak ada upload

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
    $target_dir = "../../../frontend/assets/images/";
    
    // Pastikan folder ada, jika tidak buat dulu
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($file_extension, $allowed_ext)) {
        // Generate nama file unik biar gak bentrok (cth: img_6578a9b12.jpg)
        $nama_gambar_baru = "img_" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $nama_gambar_baru;

        if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            echo json_encode(["status" => "error", "message" => "Gagal upload gambar ke folder assets"]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Format gambar harus JPG, JPEG, PNG, atau WEBP"]);
        exit;
    }
}

// INSERT KE DATABASE
// Pastikan kolom sesuai dengan tabel produk Anda: nama_produk, harga, status_aktif, gambar
$sql = "INSERT INTO produk (nama_produk, harga, status_aktif, gambar) VALUES (?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);

if ($stmt) {
    $stmt->bind_param("siis", $nama_produk, $harga, $status_aktif, $nama_gambar_baru);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Produk berhasil ditambahkan!"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "Gagal simpan database: " . $stmt->error]);
    }
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Query Error: " . $koneksi->error]);
}
?>