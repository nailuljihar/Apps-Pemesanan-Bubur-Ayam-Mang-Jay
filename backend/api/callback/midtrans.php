<?php
// backend/api/callback/midtrans.php
require_once '../../config/koneksi.php';
// require_once '../../../vendor/autoload.php'; // Disable dulu library-nya buat tes manual

// --- MODE TESTING MANUAL (POSTMAN) ---
// Kita ambil data mentah langsung tanpa verifikasi ke server Midtrans
$json_result = file_get_contents('php://input');
$result = json_decode($json_result, true);

// Cek apakah data valid
if (!$result) {
    die("Data JSON tidak valid atau kosong.");
}

// Ambil variabel dari JSON Postman
// Pake operator ?? '' biar gak error kalau datanya kosong
$transaction = $result['transaction_status'] ?? '';
$type = $result['payment_type'] ?? '';
$order_id = $result['order_id'] ?? '';
$fraud = $result['fraud_status'] ?? '';

// Debugging (Opsional: Cek apa yang diterima)
// file_put_contents('debug_log.txt', print_r($result, true));

// Cari transaksi berdasarkan order_id
$stmt = $koneksi->prepare("SELECT status FROM transaksi WHERE order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$query_result = $stmt->get_result(); // Ganti nama variabel biar gak bentrok

if ($query_result->num_rows > 0) {
    $status_baru = "Pending";

    // Logika Status Midtrans
    if ($transaction == 'capture') {
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                $status_baru = 'Pending';
            } else {
                $status_baru = 'Lunas';
            }
        }
    } else if ($transaction == 'settlement') {
        $status_baru = 'Lunas';
    } else if ($transaction == 'pending') {
        $status_baru = 'Pending';
    } else if ($transaction == 'deny') {
        $status_baru = 'Batal';
    } else if ($transaction == 'expire') {
        $status_baru = 'Batal';
    } else if ($transaction == 'cancel') {
        $status_baru = 'Batal';
    }

    // Update Database
    $update = $koneksi->prepare("UPDATE transaksi SET status = ? WHERE order_id = ?");
    $update->bind_param("ss", $status_baru, $order_id);
    
    if ($update->execute()) {
        echo "Status transaksi $order_id berhasil diupdate jadi $status_baru";
    } else {
        echo "Gagal update database: " . $koneksi->error;
    }

} else {
    echo "Order ID '$order_id' tidak ditemukan di database.";
}
?>