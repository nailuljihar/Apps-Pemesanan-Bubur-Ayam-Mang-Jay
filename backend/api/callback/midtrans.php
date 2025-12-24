<?php
require_once '../../config/koneksi.php';
require_once '../../../vendor/autoload.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-9nWvHSWmVVj4U90WuCfqJ-67'; // Ganti dengan Server Key Anda
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

try {
    $notif = new \Midtrans\Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $order_id = $notif->order_id;
    $fraud = $notif->fraud_status;

    // Mapping Status Midtrans ke Database Kita
    $status_db = 'Pending';

    if ($transaction == 'capture') {
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                $status_db = 'Pending';
            } else {
                $status_db = 'Lunas'; // Lunas = Siap Dikemas
            }
        }
    } else if ($transaction == 'settlement') {
        $status_db = 'Lunas';
    } else if ($transaction == 'pending') {
        $status_db = 'Pending';
    } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
        $status_db = 'Batal';
    }

    // Update Status di Database
    // Jika status Lunas, kita set juga defaultnya jadi 'Dikemas' agar admin tau
    $status_pesanan = ($status_db == 'Lunas') ? 'Dikemas' : $status_db;

    $stmt = $koneksi->prepare("UPDATE transaksi SET status = ? WHERE order_id = ?");
    $stmt->bind_param("ss", $status_pesanan, $order_id);
    $stmt->execute();

    echo "Status transaksi diperbarui: $status_pesanan";

} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>