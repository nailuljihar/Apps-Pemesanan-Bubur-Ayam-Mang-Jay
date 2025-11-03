<?php
// 1. Sertakan file koneksi database
include '../../config/koneksi.php';

// 2. Set header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');

// 3. Pastikan request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Hanya metode POST yang diizinkan.']);
    exit;
}

// 4. Ambil data dari body request (diasumsikan JSON)
$data = json_decode(file_get_contents("php://input"), true);

// 5. Validasi data yang dibutuhkan
if (!isset($data['nama_produk']) || !isset($data['harga'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Nama produk dan Harga harus diisi.']);
    $koneksi->close();
    exit;
}

$nama_produk = $data['nama_produk'];
$harga = (int)$data['harga'];
// status_aktif otomatis 1 (sesuai definisi tabel)

// 6. Query SQL dengan Prepared Statement untuk mencegah SQL Injection
$sql = "INSERT INTO produk (nama_produk, harga) VALUES (?, ?)";
$stmt = $koneksi->prepare($sql);

// Cek jika prepared statement gagal
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query: ' . $koneksi->error]);
    $koneksi->close();
    exit;
}

// Bind parameter: "si" berarti string (nama_produk), integer (harga)
$stmt->bind_param("si", $nama_produk, $harga);

// 7. Eksekusi query
if ($stmt->execute()) {
    http_response_code(201); // Created
    $response = [
        'status' => 'success',
        'message' => 'Produk baru berhasil ditambahkan.',
        'id_produk' => $koneksi->insert_id // Mengambil ID produk yang baru dibuat
    ];
} else {
    http_response_code(500); // Internal Server Error
    $response = [
        'status' => 'error',
        'message' => 'Gagal menambahkan produk: ' . $stmt->error
    ];
}

// 8. Tampilkan response JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// 9. Tutup statement dan koneksi
$stmt->close();
$koneksi->close();
?>