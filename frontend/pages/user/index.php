<?php
session_start();
require_once '../../../backend/config/koneksi.php';

// Cek Sesi User
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../../../index.php");
    exit;
}

// Ambil Data Produk (Menu)
$query = "SELECT * FROM produk WHERE status_aktif = 1 ORDER BY nama_produk ASC";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - Pesan Makan</title>
    <link rel="stylesheet" href="../../css/styles.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h3>MANG JAY<br><small>Pelanggan</small></h3>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fa-solid fa-utensils"></i> <span>Pilih Menu</span></a></li>
            <li>
                <a href="keranjang.php">
                    <i class="fa-solid fa-cart-shopping"></i> <span>Keranjang</span>
                    <?php 
                    $count = isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0;
                    if($count > 0) echo "<span class='badge' style='background:red; padding:2px 6px; border-radius:10px; font-size:0.8em; margin-left:5px;'>$count</span>"; 
                    ?>
                </a>
            </li>
            <li><a href="riwayat.php"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat & Status</span></a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-pen"></i> <span>Edit Profile</span></a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="../../../logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h1>
            <p>Silakan pilih menu favoritmu hari ini.</p>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="user-menu-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="menu-item-card">
                        <img src="../../assets/images/<?= !empty($row['gambar']) ? $row['gambar'] : 'bubur-ayam1.jpg' ?>" alt="<?= $row['nama_produk'] ?>">
                        <div class="menu-details">
                            <h4><?= $row['nama_produk'] ?></h4>
                            <div class="menu-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                            
                            <form action="tambah_keranjang.php" method="POST" style="margin-top:auto;">
                                <input type="hidden" name="id_produk" value="<?= $row['id_produk'] ?>">
                                <input type="hidden" name="nama_produk" value="<?= $row['nama_produk'] ?>">
                                <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                                <button type="submit" class="tombol-biru" style="width:100%; cursor:pointer;">
                                    <i class="fa fa-plus"></i> Tambah
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">Belum ada menu yang tersedia saat ini.</div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>