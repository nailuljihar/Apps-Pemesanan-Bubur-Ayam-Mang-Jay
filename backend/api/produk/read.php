<?php
// 1. Sertakan file koneksi database
include '../../config/koneksi.php';

// 2. Set header untuk memberitahu klien bahwa respon adalah JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Izinkan akses dari mana saja (sesuaikan untuk produksi)

// 3. Query SQL untuk mengambil semua produk yang aktif
$sql = "SELECT id_produk, nama_produk, harga FROM produk WHERE status_aktif = 1 ORDER BY id_produk ASC";
$result = $koneksi->query($sql);

$data_produk = array();

if ($result->num_rows > 0) {
    // 4. Ambil setiap baris data dan masukkan ke dalam array
    while($row = $result->fetch_assoc()) {
        $data_produk[] = $row;
    }

    // 5. Buat response sukses dalam format JSON
    $response = array(
        'status' => 'success',
        'message' => 'Data produk berhasil diambil.',
        'data' => $data_produk
    );
} else {
    // 6. Buat response jika data tidak ditemukan
    $response = array(
        'status' => 'error',
        'message' => 'Tidak ada produk ditemukan.',
        'data' => null
    );
}

// 7. Tampilkan response JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// 8. Tutup koneksi database
$koneksi->close();

?>