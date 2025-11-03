<?php
// 1. Sertakan file koneksi database
include '../../config/koneksi.php';

// 2. Set header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, DELETE'); // Mendukung berbagai metode untuk ID

// 3. Ambil ID produk. Prioritas GET, lalu POST, lalu dari input body (untuk DELETE method)
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];
} elseif (isset($_POST['id'])) {
    $id_produk = $_POST['id'];
} else {
    // Coba ambil dari body input (biasa digunakan pada metode DELETE)
    $data = json_decode(file_get_contents("php://input"), true);
    $id_produk = $data['id'] ?? null;
}

// 4. Validasi ID
if (empty($id_produk)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'ID Produk harus disertakan.']);
    $koneksi->close();
    exit;
}

$id_produk = (int)$id_produk;

// 5. Query SQL dengan Prepared Statement
$sql = "DELETE FROM produk WHERE id_produk = ?";
$stmt = $koneksi->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query: ' . $koneksi->error]);
    $koneksi->close();
    exit;
}

// Bind parameter: "i" berarti integer (id_produk)
$stmt->bind_param("i", $id_produk);

// 6. Eksekusi query
if ($stmt->execute()) {
    // Cek apakah ada baris yang benar-benar dihapus
    if ($stmt->affected_rows > 0) {
        $response = [
            'status' => 'success',
            'message' => 'Produk dengan ID ' . $id_produk . ' berhasil dihapus.'
        ];
    } else {
        http_response_code(404); // Not Found
        $response = [
            'status' => 'error',
            'message' => 'Gagal menghapus. ID Produk ' . $id_produk . ' tidak ditemukan.'
        ];
    }
} else {
    http_response_code(500);
    $response = [
        'status' => 'error',
        'message' => 'Gagal menghapus produk: ' . $stmt->error
    ];
}

// 7. Tampilkan response JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// 8. Tutup statement dan koneksi
$stmt->close();
$koneksi->close();
?>