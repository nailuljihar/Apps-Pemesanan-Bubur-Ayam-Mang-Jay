<?php
session_start();
require_once '../../../backend/config/koneksi.php';

// Cek Login
if (!isset($_SESSION['id_users'])) {
    header("Location: ../../../index.php");
    exit;
}

$id_user = $_SESSION['id_users'];
$query = "SELECT * FROM transaksi WHERE id_users = ? ORDER BY tanggal DESC";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// Hitung Keranjang
$jumlah_keranjang = isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Bang Jay</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h3>MANG JAY<br><small>Pelanggan</small></h3>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fa-solid fa-utensils"></i> <span>Pilih Menu</span></a></li>
            <li>
                <a href="keranjang.php">
                    <i class="fa-solid fa-cart-shopping"></i> 
                    <span>Keranjang</span>
                    <?php if($jumlah_keranjang > 0): ?>
                        <span class="badge-cart"><?= $jumlah_keranjang ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="riwayat.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Pesanan</span></a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-pen"></i> <span>Edit Profil</span></a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="../../../index.php" class="btn-logout" onclick="return confirm('Keluar?');" style="background:#c0392b; color:white; border:none;">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Riwayat Pesanan</h1>
        </div>

        <div class="dashboard-wrapper">
            <?php if ($result->num_rows > 0): ?>
                <?php while($trx = $result->fetch_assoc()): 
                    // Mapping warna status agar konsisten
                    $st = $trx['status'];
                    $cls = 'bg-pending';
                    if($st=='Lunas') $cls='bg-lunas';
                    if($st=='Dikemas') $cls='bg-dikemas';
                    if($st=='Diantar') $cls='bg-diantar';
                    if($st=='Selesai') $cls='bg-selesai';
                    if($st=='Batal') $cls='bg-batal';
                ?>
                    <div class="card-riwayat">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <div>
                                <h3 style="margin:0; color:#2d3436;">Order #<?= htmlspecialchars($trx['order_id']) ?></h3>
                                <small style="color:#7f8c8d;"><?= date('d M Y, H:i', strtotime($trx['tanggal'])) ?></small>
                            </div>
                            <span class="status-badge <?= $cls ?>"><?= $st ?></span>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px;">
                            <div>
                                <span style="display:block; font-size:0.9em; color:#666;">Total Pembayaran</span>
                                <strong style="font-size:1.2em; color:#d35400;">Rp <?= number_format($trx['total_pendapatan'], 0, ',', '.') ?></strong>
                            </div>

                            <?php if($st == 'Pending' && $trx['jenis_transaksi'] == 'online'): ?>
                                <button id="pay-btn-<?= $trx['order_id'] ?>" class="tombol-biru" style="padding:8px 20px; font-size:0.9em;">
                                    Bayar Sekarang
                                </button>
                                <script>
                                    document.getElementById('pay-btn-<?= $trx['order_id'] ?>').onclick = function(){
                                        snap.pay('<?= $trx['snap_token'] ?>');
                                    };
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align:center; padding:50px; color:#aaa;">
                    <i class="fa-solid fa-history" style="font-size:3em; margin-bottom:15px;"></i>
                    <p>Belum ada riwayat pesanan.</p>
                    <a href="index.php" style="color:#d35400; font-weight:bold;">Pesan Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-p03qwXbZBJ7PooX6"></script>
</body>
</html>