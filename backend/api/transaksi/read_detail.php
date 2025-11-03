<?php
// 1. Sertakan file koneksi database
include '../../config/koneksi.php';

// 2. Set header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');

// 3. Ambil ID Transaksi dari parameter GET
$id_transaksi = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 4. Validasi ID
if ($id_transaksi === 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'ID Transaksi harus disertakan.']);
    $koneksi->close();
    exit;
}

// 5. Query SQL dengan Prepared Statement
// Menggunakan JOIN untuk mengambil data dari 3 tabel sekaligus
$sql = "
    SELECT 
        t.id_transaksi,
        t.tanggal AS tanggal_transaksi,
        t.jenis_transaksi,
        t.total_pendapatan AS total_transaksi,
        t.metode_pembayaran,
        t.catatan,
        dt.jumlah,
        dt.subtotal,
        p.nama_produk,
        p.harga AS harga_satuan
    FROM 
        transaksi t
    JOIN 
        detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
    JOIN 
        produk p ON dt.id_produk = p.id_produk
    WHERE 
        t.id_transaksi = ?
    ORDER BY 
        dt.id_detail ASC;
";

$stmt = $koneksi->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query: ' . $koneksi->error]);
    $koneksi->close();
    exit;
}

// Bind parameter: "i" berarti integer (id_transaksi)
$stmt->bind_param("i", $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();

$transaksi = null;
$items = [];

if ($result->num_rows > 0) {
    // Loop untuk mengumpulkan semua detail
    while ($row = $result->fetch_assoc()) {
        // Hanya set informasi transaksi utama sekali saja
        if ($transaksi === null) {
            $transaksi = [
                'id_transaksi' => $row['id_transaksi'],
                'tanggal' => $row['tanggal_transaksi'],
                'jenis_transaksi' => $row['jenis_transaksi'],
                'metode_pembayaran' => $row['metode_pembayaran'],
                'total_transaksi' => $row['total_transaksi'],
                'catatan' => $row['catatan'],
                'detail_produk' => [] // Array untuk detail produk
            ];
        }

        // Kumpulkan detail produk/item
        $items[] = [
            'nama_produk' => $row['nama_produk'],
            'harga_satuan' => $row['harga_satuan'],
            'jumlah' => $row['jumlah'],
            'subtotal' => $row['subtotal']
        ];
    }
    
    // Gabungkan detail item ke dalam data transaksi utama
    $transaksi['detail_produk'] = $items;

    $response = [
        'status' => 'success',
        'message' => 'Detail transaksi berhasil diambil.',
        'data' => $transaksi
    ];
} else {
    http_response_code(404); // Not Found
    $response = [
        'status' => 'error',
        'message' => 'Transaksi dengan ID ' . $id_transaksi . ' tidak ditemukan.'
    ];
}

// 7. Tampilkan response JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// 8. Tutup statement dan koneksi
$stmt->close();
$koneksi->close();
?>