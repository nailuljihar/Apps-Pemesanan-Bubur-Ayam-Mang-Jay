<?php
// 1. Sertakan file koneksi database
include '../../config/koneksi.php';

// 2. Set header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');

// 3. Ambil tanggal dari parameter GET. Default ke tanggal hari ini jika tidak ada.
$tanggal_laporan = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// 4. Validasi format tanggal (sederhana)
if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tanggal_laporan)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD.']);
    $koneksi->close();
    exit;
}

// Inisialisasi struktur data laporan
$laporan = [
    'tanggal' => $tanggal_laporan,
    'ringkasan_harian' => null,
    'detail_pembayaran' => [
        'cash' => ['total_pendapatan' => 0, 'jumlah_transaksi' => 0],
        'qris' => ['total_pendapatan' => 0, 'jumlah_transaksi' => 0]
    ],
    'transaksi_list' => []
];

// 5. Query 1: Ambil data ringkasan dari pendapatan_harian
$sql_harian = "SELECT total_transaksi, total_pendapatan, jam FROM pendapatan_harian WHERE tanggal = ?";
$stmt_harian = $koneksi->prepare($sql_harian);
$stmt_harian->bind_param("s", $tanggal_laporan);
$stmt_harian->execute();
$result_harian = $stmt_harian->get_result();

if ($result_harian->num_rows > 0) {
    $laporan['ringkasan_harian'] = $result_harian->fetch_assoc();
}
$stmt_harian->close();

// 6. Query 2: Ambil semua data transaksi pada tanggal tersebut untuk rincian pembayaran
$sql_transaksi = "
    SELECT 
        id_transaksi, 
        jenis_transaksi, 
        total_pendapatan, 
        metode_pembayaran,
        catatan
    FROM 
        transaksi 
    WHERE 
        tanggal = ?
    ORDER BY 
        id_transaksi ASC
";
$stmt_transaksi = $koneksi->prepare($sql_transaksi);
$stmt_transaksi->bind_param("s", $tanggal_laporan);
$stmt_transaksi->execute();
$result_transaksi = $stmt_transaksi->get_result();

if ($result_transaksi->num_rows > 0) {
    while ($row = $result_transaksi->fetch_assoc()) {
        $metode = strtolower($row['metode_pembayaran']);
        
        // A. Kumpulkan rincian per metode pembayaran
        if (isset($laporan['detail_pembayaran'][$metode])) {
            $laporan['detail_pembayaran'][$metode]['total_pendapatan'] += $row['total_pendapatan'];
            $laporan['detail_pembayaran'][$metode]['jumlah_transaksi'] += 1;
        }

        // B. Kumpulkan list transaksi (opsional, untuk front-end detail)
        $laporan['transaksi_list'][] = $row;
    }
}
$stmt_transaksi->close();

// 7. Cek status laporan dan buat response
if ($laporan['ringkasan_harian'] || !empty($laporan['transaksi_list'])) {
    $response = [
        'status' => 'success',
        'message' => "Laporan harian untuk tanggal {$tanggal_laporan} berhasil diambil.",
        'data' => $laporan
    ];
} else {
    http_response_code(404); // Not Found
    $response = [
        'status' => 'error',
        'message' => "Tidak ada data transaksi atau ringkasan harian untuk tanggal {$tanggal_laporan}.",
        'data' => $laporan
    ];
}

// 8. Tampilkan response JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// 9. Tutup koneksi
$koneksi->close();
?>