<?php
session_start();

// 1. INCLUDE KONEKSI
// Pastikan path ini benar (naik 3 folder)
include '../../../backend/config/koneksi.php'; 

// 2. Definisi BASE_URL (Biar CSS gak error)
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/Apps-Pemesanan-Bubur-Ayam-Mang-Jay/');
}

// 3. Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: " . BASE_URL . "frontend/pages/user/login.php");
    exit();
}

// 4. QUERY DATA PESANAN (JOIN TABEL)
// Penjelasan Logika JOIN:
// "Ambil semua data dari tabel transaksi (t), GABUNGKAN dengan tabel users (u)
//  DIMANA kolom 'id_user' di transaksi COCOK dengan 'id' di users."

$query_pesanan = "SELECT 
                    id_transaksi,
                    tanggal,
                    total_pendapatan,
                    status,
                    order_id,
                    nama_lengkap  -- << Ini diambil dari tabel USERS
                  FROM transaksi 
                  JOIN users ON id_user = id_users  -- << CEK DB LO: 'id_user' atau 'user_id'?
                  ORDER BY total_pendapatan DESC";

$result_pesanan = $koneksi->query($query_pesanan);

// Debugging Cepat: Kalau query error, script akan mati dan kasih tau errornya
if (!$result_pesanan) {
    die("<h3>Error Query SQL:</h3>" . $koneksi->error . "<br><br><b>Saran Perbaikan:</b> Coba cek struktur tabel 'transaksi' di phpMyAdmin, apakah nama kolom penyambungnya <b>id_user</b> atau <b>user_id</b>? Ganti di kodingan baris 31 sesuai nama kolom yg benar.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan - Admin Bang Jay</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>frontend/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h2>ADMIN PANEL</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a></li>
            <li><a href="data_pesanan.php" class="active"><i class="fa-solid fa-cart-shopping"></i> <span>Pesanan</span></a></li>
            <li><a href="produk.php"><i class="fa-solid fa-utensils"></i> <span>Menu Produk</span></a></li>
            <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span>Laporan</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>index.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Data Pesanan Masuk</h1>
            <div class="user-info">
                <i class="fa-solid fa-user-tie"></i> Admin
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">Daftar Transaksi</div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_pesanan->num_rows > 0): ?>
                        <?php while($row = $result_pesanan->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $row['id_transaksi'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                                
                                <td style="font-weight:bold; color:#00897b;">
                                    <?= htmlspecialchars($row['nama_lengkap']) ?>
                                </td>
                                
                                <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                                <td>
                                    <?php 
                                        $s = $row['status'];
                                        $warnabel = 'gray';
                                        if($s == 'Pending') $warnabel = '#f39c12'; // Oranye
                                        if($s == 'Sukses' || $s == 'selesai') $warnabel = '#27ae60'; // Hijau
                                        if($s == 'Batal') $warnabel = '#c0392b'; // Merah
                                    ?>
                                    <span style="background:<?= $warnabel ?>; color:white; padding:3px 8px; border-radius:4px; font-size:0.8em;">
                                        <?= strtoupper($s) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail_pesanan.php?id=<?= $row['id_transaksi'] ?>" title="Lihat Detail">
                                        <i class="fa-solid fa-eye" style="color:#2980b9; margin-right:5px;"></i>
                                    </a>
                                    <a href="#" title="Hapus" onclick="alert('Fitur hapus belum aktif')">
                                        <i class="fa-solid fa-trash" style="color:#c0392b;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px; color:gray;">
                                <i class="fa-solid fa-box-open" style="font-size:2em; margin-bottom:10px;"></i><br>
                                Belum ada data transaksi yang masuk.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>