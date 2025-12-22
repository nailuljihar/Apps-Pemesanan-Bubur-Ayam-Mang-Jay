<?php
session_start();

// --- BARIS INI YANG TADI KURANG ---
require_once '../../../backend/config/koneksi.php'; 
// ----------------------------------

require_once '../../../vendor/autoload.php'; 

// LOGIKA UPDATE STATUS OTOMATIS (KHUSUS LOCALHOST)
if (isset($_GET['order_id']) || isset($_GET['result_data'])) {
    
    // Konfigurasi Midtrans (Samakan dengan file lain)
    \Midtrans\Config::$serverKey = 'SB-Mid-server-9nWvHSWmVVj4U90WuCfqJ-67'; // Server Key Kamu
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    // Ambil Order ID
    $order_id_to_check = $_GET['order_id'] ?? null;

    if ($order_id_to_check) {
        try {
            // Tanya status ke Midtrans
            $status = \Midtrans\Transaction::status($order_id_to_check);
            $transaction_status = $status->transaction_status;
            
            // Tentukan status baru
            $new_status = 'Pending';
            if ($transaction_status == 'settlement' || $transaction_status == 'capture') {
                $new_status = 'Lunas';
            } else if ($transaction_status == 'expire' || $transaction_status == 'cancel' || $transaction_status == 'deny') {
                $new_status = 'Batal';
            }

            // Update Database
            $stmt = $koneksi->prepare("UPDATE transaksi SET status = ? WHERE order_id = ?");
            $stmt->bind_param("ss", $new_status, $order_id_to_check);
            $stmt->execute();
            
        } catch (Exception $e) {
            // Abaikan error kalau koneksi gagal
        }
    }
}

// Cek User Login
if (!isset($_SESSION['id_users'])) {
    header("Location: ../../../index.php");
    exit;
}

$id_user = $_SESSION['id_users'];
// Ambil transaksi user ini
$query = "SELECT * FROM transaksi WHERE id_users = ? ORDER BY tanggal DESC";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pesanan - Bang Jay</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
        <h2>Riwayat Pesanan Kamu</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while($trx = $result->fetch_assoc()): ?>
                <div class="card-riwayat" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: white;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                        <span><strong>#<?= $trx['order_id'] ?></strong></span>
                        <span style="color: #666;"><?= date('d M Y', strtotime($trx['tanggal'])) ?></span>
                    </div>
                    
                    <div>
                        <p>Total: <strong>Rp <?= number_format($trx['total_pendapatan'], 0, ',', '.') ?></strong></p>
                        <p>Metode: <?= strtoupper($trx['metode_pembayaran']) ?></p>
                        
                        <?php 
                        $warna_status = 'orange'; // Default Pending
                        if($trx['status'] == 'Lunas') $warna_status = 'green';
                        if($trx['status'] == 'Batal') $warna_status = 'red';
                        ?>
                        <p>Status: <span style="background: <?= $warna_status ?>; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.9em;"><?= $trx['status'] ?></span></p>
                    </div>

                    <?php if($trx['status'] == 'Pending' && $trx['jenis_transaksi'] == 'online' && !empty($trx['snap_token'])): ?>
                        <button id="pay-button-<?= $trx['order_id'] ?>" style="background: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-top: 10px;">
                            Lanjut Bayar
                        </button>
                        
                        <script type="text/javascript">
                            document.getElementById('pay-button-<?= $trx['order_id'] ?>').onclick = function(){
                                snap.pay('<?= $trx['snap_token'] ?>');
                            };
                        </script>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada riwayat pesanan.</p>
        <?php endif; ?>
        
        <a href="index.php">Kembali ke Menu</a>
    </div>
    
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-p03qwXbZBJ7PooX6"></script>
</body>
</html>