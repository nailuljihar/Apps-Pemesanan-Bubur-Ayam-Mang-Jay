<?php
session_start();
include '../../../backend/config/koneksi.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../user/index.php"); // Redirect ke home user jika bukan admin
    exit();
}

// QUERY DIPERBAIKI: Menggunakan LEFT JOIN agar pesanan Offline (yg tidak punya akun user) tetap muncul
// Menggunakan alias t dan u untuk menghindari error 'ambiguous'
$query_pesanan = "SELECT 
                    t.id_transaksi,
                    t.tanggal,
                    t.total_pendapatan,
                    t.status,
                    t.jenis_transaksi,
                    t.nama_penerima,  -- Nama dari input manual (Offline/Online Guest)
                    u.nama_lengkap    -- Nama dari akun user (Online Member)
                  FROM transaksi t
                  LEFT JOIN users u ON t.id_users = u.id_users 
                  ORDER BY t.tanggal DESC";

$result_pesanan = $koneksi->query($query_pesanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan - Admin Bang Jay</title>
    <link rel="stylesheet" href="../../css/admin.css">
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
            <a href="../../../index.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Data Pesanan Masuk</h1>
            
            <a href="tambah_pesanan.php" class="tombol-biru" style="text-decoration:none; font-size:0.9em;">
                <i class="fa fa-plus-circle"></i> Pesanan Baru (Offline)
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    <?php if ($result_pesanan && $result_pesanan->num_rows > 0): ?>
        <?php while($row = $result_pesanan->fetch_assoc()): ?>
            <tr>
                <td>#<?= htmlspecialchars($row['order_id'] ?? $row['id_transaksi']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                
                <td>
                    <?php if($row['jenis_transaksi'] == 'offline'): ?>
                        <span style="background:#7f8c8d; color:white; padding:3px 8px; border-radius:4px; font-size:0.8em;">OFFLINE</span>
                    <?php else: ?>
                        <span style="background:#3498db; color:white; padding:3px 8px; border-radius:4px; font-size:0.8em;">ONLINE</span>
                    <?php endif; ?>
                </td>

                <td style="font-weight:bold; color:#2c3e50;">
                    <?php 
                        if (!empty($row['nama_lengkap'])) {
                            echo htmlspecialchars($row['nama_lengkap']);
                        } else {
                            // Tambahkan '??' untuk menangani nilai NULL menjadi string kosong
                            $nama_guest = $row['nama_penerima'] ?? 'Tanpa Nama'; 
                            echo htmlspecialchars($nama_guest) . " <small>(Guest)</small>";
                        }
                    ?>
                </td>
                
                <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                
                <td>
                    <?php 
                        $s = $row['status'];
                        $warnabel = 'gray';
                        if($s == 'Pending') $warnabel = '#f39c12';
                        if($s == 'Sukses' || $s == 'Selesai' || $s == 'Lunas') $warnabel = '#27ae60';
                        if($s == 'Batal') $warnabel = '#c0392b';
                    ?>
                    <span style="background:<?= $warnabel ?>; color:white; padding:3px 8px; border-radius:4px; font-size:0.8em;">
                        <?= strtoupper($s) ?>
                    </span>
                </td>
                <td>
                    <a href="#" onclick="alert('Fitur detail belum aktif')" title="Lihat Detail">
                        <i class="fa-solid fa-eye" style="color:#2980b9;"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center; padding: 30px; color:gray;">
                Belum ada data transaksi.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </main>
</body>
</html>