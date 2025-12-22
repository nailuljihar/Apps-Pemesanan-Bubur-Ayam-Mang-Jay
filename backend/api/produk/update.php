<?php
// 1. Sertakan file koneksi database
include '../../config/koneksi.php';

// 2. Set header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, PUT'); // Mendukung POST atau PUT

// 3. Ambil data dari body request
// Karena beberapa client mungkin menggunakan POST untuk update, kita ambil dari php://input
$data = json_decode(file_get_contents("php://input"), true);

// 4. Validasi data yang dibutuhkan
if (!isset($data['id_produk']) || !isset($data['nama_produk']) || !isset($data['harga']) || !isset($data['status_aktif'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'ID Produk, Nama, Harga, dan Status Aktif harus diisi.']);
    $koneksi->close();
    exit;
}

$id_produk = (int)$data['id_produk'];
$nama_produk = $data['nama_produk'];
$harga = (int)$data['harga'];
$status_aktif = (int)$data['status_aktif']; // 0 atau 1

// 5. Query SQL dengan Prepared Statement
$gambar = $data['gambar'];
$sql = "UPDATE produk SET nama_produk = ?, harga = ?, status_aktif = ?, gambar = ? WHERE id_produk = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("siisi", $nama_produk, $harga, $status_aktif, $gambar, $id_produk);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query: ' . $koneksi->error]);
    $koneksi->close();
    exit;
}

// Bind parameter: "siii" berarti string, integer, integer, integer (nama, harga, status, id)
$stmt->bind_param("siii", $nama_produk, $harga, $status_aktif, $id_produk);

// 6. Eksekusi query
if ($stmt->execute()) {
    // Cek apakah ada baris yang benar-benar terpengaruh (diupdate)
    if ($stmt->affected_rows > 0) {
        $response = [
            'status' => 'success',
            'message' => 'Produk dengan ID ' . $id_produk . ' berhasil diperbarui.'
        ];
    } else {
        // Baris ditemukan, tapi data mungkin sama, atau ID tidak ditemukan
        $response = [
            'status' => 'warning',
            'message' => 'Data produk tidak berubah atau ID produk tidak ditemukan.'
        ];
    }
} else {
    http_response_code(500);
    $response = [
        'status' => 'error',
        'message' => 'Gagal memperbarui produk: ' . $stmt->error
    ];
}

// 7. Tampilkan response JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// 8. Tutup statement dan koneksi
$stmt->close();
$koneksi->close();
?>