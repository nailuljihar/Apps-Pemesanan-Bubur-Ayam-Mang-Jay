<?php
// File: backend/api/callback/midtrans.php
// Pastikan path koneksi ini benar
require_once '../../config/koneksi.php';

// 1. Ambil data JSON mentah dari Midtrans
$json_result = file_get_contents('php://input');
$result = json_decode($json_result, true);

// 2. Cek validitas data
if (!$result) {
    die("Akses ditolak: Data tidak valid.");
}

// 3. Ambil variabel penting
$order_id = $result['order_id'];
$transaction_status = $result['transaction_status'];
$fraud_status = $result['fraud_status'];

// 4. Logika Penentuan Status
$status_baru = "";

if ($transaction_status == 'capture') {
    if ($fraud_status == 'challenge') {
        $status_baru = 'Pending';
    } else {
        $status_baru = 'Lunas';
    }
} else if ($transaction_status == 'settlement') {
    $status_baru = 'Lunas';
} else if ($transaction_status == 'pending') {
    $status_baru = 'Pending';
} else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
    $status_baru = 'Batal';
}

// 5. Update Database (User & Admin otomatis melihat ini)
if ($status_baru != "") {
    $stmt = $koneksi->prepare("UPDATE transaksi SET status = ? WHERE order_id = ?");
    $stmt->bind_param("ss", $status_baru, $order_id);
    
    if ($stmt->execute()) {
        echo "Sukses update status $order_id jadi $status_baru";
    } else {
        echo "Gagal update: " . $koneksi->error;
    }
}
?>