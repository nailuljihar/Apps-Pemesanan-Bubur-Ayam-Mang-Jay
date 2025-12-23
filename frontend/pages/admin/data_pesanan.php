<?php
session_start();
include '../../../backend/config/koneksi.php'; 

// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../user/index.php"); 
    exit();
}

// QUERY PERBAIKAN:
// 1. Gunakan LEFT JOIN agar pesanan Offline (yg tidak punya user ID) tetap muncul.
// 2. Ambil 't.nama_penerima' (untuk offline) DAN 'u.nama_lengkap' (untuk online).
// 3. Gunakan alias 't' untuk transaksi dan 'u' untuk users biar rapi.

$query_pesanan = "SELECT 
                    t.id_transaksi,
                    t.order_id,
                    t.tanggal,
                    t.total_pendapatan,
                    t.status,
                    t.jenis_transaksi,
                    t.nama_penerima,  -- Nama dari input manual (Offline/Guest)
                    u.nama_lengkap    -- Nama dari akun user (Member Online)
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
                                <td>
                                    <strong>#<?= htmlspecialchars($row['order_id'] ?? $row['id_transaksi']) ?></strong>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                                
                                <td>
                                    <?php if($row['jenis_transaksi'] == 'offline'): ?>
                                        <span style="background:#95a5a6; color:white; padding:4px 8px; border-radius:4px; font-size:0.8em; font-weight:bold;">OFFLINE</span>
                                    <?php else: ?>
                                        <span style="background:#3498db; color:white; padding:4px 8px; border-radius:4px; font-size:0.8em; font-weight:bold;">ONLINE</span>
                                    <?php endif; ?>
                                </td>

                                <td style="font-weight:bold; color:#2c3e50;">
                                    <?php 
                                        // Prioritas 1: Nama Akun User (Jika ada)
                                        if (!empty($row['nama_lengkap'])) {
                                            echo htmlspecialchars($row['nama_lengkap']);
                                            echo ' <i class="fa-solid fa-circle-check" style="color:#27ae60; margin-left:5px;" title="Member Terdaftar"></i>';
                                        } 
                                        // Prioritas 2: Nama Input Manual (Guest/Offline)
                                        elseif (!empty($row['nama_penerima'])) {
                                            echo htmlspecialchars($row['nama_penerima']);
                                            echo ' <small style="color:#7f8c8d;">(Guest)</small>';
                                        } 
                                        // Fallback: Jika kosong semua
                                        else {
                                            echo "<span style='color:red; font-style:italic;'>Tanpa Nama</span>";
                                        }
                                    ?>
                                </td>
                                
                                <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                                
                                <td>
                                    <?php 
                                        $s = $row['status'];
                                        $warnabel = 'gray';
                                        if($s == 'Pending') $warnabel = '#f39c12'; // Kuning
                                        if($s == 'Sukses' || $s == 'Selesai' || $s == 'Lunas') $warnabel = '#27ae60'; // Hijau
                                        if($s == 'Batal' || $s == 'Gagal') $warnabel = '#c0392b'; // Merah
                                    ?>
                                    <span style="background:<?= $warnabel ?>; color:white; padding:4px 10px; border-radius:15px; font-size:0.8em; font-weight:bold;">
                                        <?= strtoupper($s) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" onclick="alert('Fitur detail sedang dikembangkan')" title="Lihat Detail" class="btn-edit" style="background:#34495e;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px; color:#7f8c8d;">
                                <i class="fa-solid fa-box-open" style="font-size:3em; margin-bottom:10px;"></i><br>
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