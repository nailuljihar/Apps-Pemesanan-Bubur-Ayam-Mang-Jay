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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pesanan</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Override Warna Sidebar User */
        .admin-sidebar { background-color: #2d3436; }
        .sidebar-header { background-color: #202526; color: #fab1a0; }
        .sidebar-menu li a.active { background-color: #d35400; border-left-color: #fff; }
        
        /* Timeline Status */
        .status-tracker { display: flex; gap: 10px; margin-top: 10px; font-size: 0.8em; }
        .step { padding: 5px 10px; border-radius: 20px; background: #eee; color: #999; }
        .step.active { background: #27ae60; color: white; font-weight: bold; }
    </style>
</head>
<body>

    <nav class="admin-sidebar">
        <div class="sidebar-header"><h3>MANG JAY<br><small>Pelanggan</small></h3></div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fa-solid fa-utensils"></i> <span>Pilih Menu</span></a></li>
            <li><a href="keranjang.php"><i class="fa-solid fa-cart-shopping"></i> <span>Keranjang</span></a></li>
            <li><a href="riwayat.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat</span></a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-pen"></i> <span>Edit Profil</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../../../index.php" class="btn-logout" onclick="return confirm('Keluar?');"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header"><h1>Riwayat Pesanan</h1></div>

        <div class="dashboard-wrapper">
            <?php if ($result->num_rows > 0): ?>
                <?php while($trx = $result->fetch_assoc()): ?>
                    <div class="stat-card" style="display:block; margin-bottom:20px; border-left: 5px solid #d35400;">
                        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding-bottom:10px;">
                            <strong>Order ID: #<?= $trx['order_id'] ?></strong>
                            <span style="color:#666;"><?= date('d M Y H:i', strtotime($trx['tanggal'])) ?></span>
                        </div>
                        
                        <div style="margin:15px 0;">
                            <p>Total Bayar: <b style="color:#d35400;">Rp <?= number_format($trx['total_pendapatan'], 0, ',', '.') ?></b></p>
                            
                            <div class="status-tracker">
                                <span class="step <?= ($trx['status']!='Pending' && $trx['status']!='Batal') ? 'active' : '' ?>">Dibayar</span>
                                <span class="step <?= ($trx['status']=='Dikemas' || $trx['status']=='Dikirim' || $trx['status']=='Selesai') ? 'active' : '' ?>">Dikemas</span>
                                <span class="step <?= ($trx['status']=='Dikirim' || $trx['status']=='Selesai') ? 'active' : '' ?>">Diantar</span>
                                <span class="step <?= ($trx['status']=='Selesai') ? 'active' : '' ?>">Selesai</span>
                            </div>
                            
                            <?php if($trx['status'] == 'Batal'): ?>
                                <p style="color:red; margin-top:5px;">Pesanan Dibatalkan</p>
                            <?php endif; ?>
                        </div>

                        <?php if($trx['status'] == 'Pending' && $trx['jenis_transaksi'] == 'online'): ?>
                            <button id="pay-btn-<?= $trx['order_id'] ?>" class="tombol-biru" style="background:#2980b9;">Bayar Sekarang</button>
                            <script>
                                document.getElementById('pay-btn-<?= $trx['order_id'] ?>').onclick = function(){
                                    snap.pay('<?= $trx['snap_token'] ?>');
                                };
                            </script>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; margin-top:50px; color:#aaa;">Belum ada riwayat transaksi.</p>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-p03qwXbZBJ7PooX6"></script>
</body>
</html>