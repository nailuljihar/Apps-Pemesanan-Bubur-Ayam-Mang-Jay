<?php
session_start();
// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../user/user-dashboard.php"); // Redirect kalau bukan admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Bang Jay</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h2>ADMIN PANEL</h2>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a></li>
            <li><a href="data_pesanan.php"><i class="fa-solid fa-cart-shopping"></i> <span>Pesanan</span></a></li>
            <li><a href="produk.php"><i class="fa-solid fa-utensils"></i> <span>Menu Produk</span></a></li>
            <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span>Laporan</span></a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="../../../index.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Selamat Datang, Admin!</h1>
            <div class="user-info">
                <i class="fa-solid fa-user-circle"></i> <?= $_SESSION['nama_lengkap'] ?? 'Administrator' ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>150</h3>
                    <p>Pesanan Hari Ini</p>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>Rp 2.5jt</h3>
                    <p>Pendapatan</p>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>12</h3>
                    <p>Menu Aktif</p>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-burger"></i>
                </div>
            </div>
            
            <div class="stat-card" style="border-left-color: #f39c12;">
                <div class="stat-info">
                    <h3>5</h3>
                    <p>Perlu Konfirmasi</p>
                </div>
                <div class="stat-icon" style="color: #f39c12;">
                    <i class="fa-solid fa-bell"></i>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">Pesanan Terbaru</div>
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#ORD-001</td>
                        <td>Budi Santoso</td>
                        <td>Rp 25.000</td>
                        <td><span style="color: green; font-weight: bold;">Selesai</span></td>
                        <td><button style="padding: 5px 10px;">Detail</button></td>
                    </tr>
                    </tbody>
            </table>
        </div>

    </main>

</body>
</html>