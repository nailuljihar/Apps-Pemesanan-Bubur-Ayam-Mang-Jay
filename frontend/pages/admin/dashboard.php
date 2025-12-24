<?php
session_start();
require_once '../../../backend/config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../index.php");
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
            <a href="../../../logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Dashboard Overview</h1>
            <div class="user-info">
                <i class="fa-solid fa-user-shield"></i> Halo, Admin
            </div>
        </div>

        <div class="stats-grid">
            
            <div class="stat-card" style="border-left-color: #3498db;">
                <div class="stat-info">
                    <h3 id="stat-orders">0</h3>
                    <p>Pesanan Hari Ini</p>
                </div>
                <div class="stat-icon" style="color: #3498db;">
                    <i class="fa-solid fa-cart-plus"></i>
                </div>
            </div>

            <div class="stat-card" style="border-left-color: #27ae60;">
                <div class="stat-info">
                    <h3 id="stat-income">Rp 0</h3>
                    <p>Pendapatan Hari Ini</p>
                </div>
                <div class="stat-icon" style="color: #27ae60;">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>

            <div class="stat-card" style="border-left-color: #f39c12;">
                <div class="stat-info">
                    <h3 id="stat-products">0</h3>
                    <p>Menu Aktif</p>
                </div>
                <div class="stat-icon" style="color: #f39c12;">
                    <i class="fa-solid fa-bowl-food"></i>
                </div>
            </div>

            <div class="stat-card" style="border-left-color: #9b59b6;">
                <div class="stat-info">
                    <h3 id="stat-users">0</h3>
                    <p>Total Pelanggan</p>
                </div>
                <div class="stat-icon" style="color: #9b59b6;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

        </div>

        <div class="dashboard-wrapper">
            <h3>Aktivitas Terbaru</h3>
            <p style="color:#777;">Data statistik di atas diperbarui secara otomatis setiap 3 detik.</p>
        </div>

    </main>

    <script>
        function updateStats() {
            fetch('../../../backend/api/stats/dashboard_stats.php')
            .then(response => response.json())
            .then(data => {
                // Update Angka di Halaman
                document.getElementById('stat-orders').innerText = data.orders_today;
                document.getElementById('stat-products').innerText = data.active_products;
                document.getElementById('stat-users').innerText = data.total_users;
                
                // Format Rupiah untuk Pendapatan
                let income = parseInt(data.income_today).toLocaleString('id-ID');
                document.getElementById('stat-income').innerText = 'Rp ' + income;
            })
            .catch(error => console.error('Gagal mengambil data stats:', error));
        }

        // Panggil fungsi pertama kali saat load
        updateStats();

        // Panggil ulang setiap 3000ms (3 detik)
        setInterval(updateStats, 3000);
    </script>

</body>
</html>