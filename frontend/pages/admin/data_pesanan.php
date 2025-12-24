<?php
session_start();
include '../../../backend/config/koneksi.php'; 

// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../user/index.php"); 
    exit();
}

// --- LOGIKA HAPUS PESANAN ---
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    // Hapus detail dulu (Foreign Key)
    $koneksi->query("DELETE FROM detail_transaksi WHERE id_transaksi = '$id_hapus'");
    // Hapus transaksi utama
    if($koneksi->query("DELETE FROM transaksi WHERE id_transaksi = '$id_hapus'")){
        echo "<script>alert('Pesanan berhasil dihapus!'); window.location='data_pesanan.php';</script>";
    }
}

// --- LOGIKA UPDATE STATUS PESANAN ---
if (isset($_POST['update_status'])) {
    $id_trx = $_POST['id_transaksi'];
    $status_baru = $_POST['status_baru'];
    
    $stmt = $koneksi->prepare("UPDATE transaksi SET status = ? WHERE id_transaksi = ?");
    $stmt->bind_param("si", $status_baru, $id_trx);
    $stmt->execute();
    echo "<script>alert('Status berhasil diubah menjadi $status_baru'); window.location='data_pesanan.php';</script>";
}

// Query Data Pesanan
$query_pesanan = "SELECT 
                    t.id_transaksi, t.order_id, t.tanggal, t.total_pendapatan, 
                    t.status, t.jenis_transaksi, t.nama_penerima, u.nama_lengkap 
                  FROM transaksi t
                  LEFT JOIN users u ON t.id_users = u.id_users 
                  ORDER BY t.tanggal DESC";
$result_pesanan = $koneksi->query($query_pesanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan - Admin</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styling Badge Status */
        .badge { padding: 5px 10px; border-radius: 15px; color: white; font-weight: bold; font-size: 0.8em; }
        .bg-pending { background-color: #f39c12; } /* Kuning */
        .bg-lunas { background-color: #3498db; }   /* Biru */
        .bg-dikemas { background-color: #9b59b6; } /* Ungu */
        .bg-dikirim { background-color: #e67e22; } /* Oranye */
        .bg-selesai { background-color: #27ae60; } /* Hijau */
        .bg-batal { background-color: #c0392b; }   /* Merah */
    </style>
</head>
<body>

    <nav class="admin-sidebar">
        <div class="sidebar-header"><h2>ADMIN PANEL</h2></div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a></li>
            <li><a href="data_pesanan.php" class="active"><i class="fa-solid fa-cart-shopping"></i> <span>Pesanan</span></a></li>
            <li><a href="produk.php"><i class="fa-solid fa-utensils"></i> <span>Menu Produk</span></a></li>
            <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span>Laporan</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../../../index.php" class="btn-logout" onclick="return confirm('Keluar?');"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Data Pesanan</h1>
            <a href="tambah_pesanan.php" class="tombol-biru"><i class="fa fa-plus"></i> Input Manual (Offline)</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status Saat Ini</th>
                        <th>Ubah Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result_pesanan->fetch_assoc()): ?>
                        <?php 
                            // Tentukan warna badge
                            $s = $row['status'];
                            $cls = 'bg-pending';
                            if($s=='Lunas') $cls='bg-lunas';
                            if($s=='Dikemas') $cls='bg-dikemas';
                            if($s=='Dikirim') $cls='bg-dikirim';
                            if($s=='Selesai') $cls='bg-selesai';
                            if($s=='Batal') $cls='bg-batal';
                        ?>
                        <tr>
                            <td>#<?= $row['id_transaksi'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <?= htmlspecialchars(!empty($row['nama_lengkap']) ? $row['nama_lengkap'] : ($row['nama_penerima'] ?? 'Guest')) ?>
                                <br><small><?= strtoupper($row['jenis_transaksi']) ?></small>
                            </td>
                            <td>Rp <?= number_format($row['total_pendapatan'],0,',','.') ?></td>
                            
                            <td><span class="badge <?= $cls ?>"><?= $s ?></span></td>
                            
                            <td>
                                <form action="" method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                                    <select name="status_baru" style="padding:5px; border-radius:4px;">
                                        <option value="Pending" <?= $s=='Pending'?'selected':'' ?>>Pending</option>
                                        <option value="Lunas" <?= $s=='Lunas'?'selected':'' ?>>Lunas</option>
                                        <option value="Dikemas" <?= $s=='Dikemas'?'selected':'' ?>>Dikemas</option>
                                        <option value="Dikirim" <?= $s=='Dikirim'?'selected':'' ?>>Diantar</option>
                                        <option value="Selesai" <?= $s=='Selesai'?'selected':'' ?>>Selesai</option>
                                        <option value="Batal" <?= $s=='Batal'?'selected':'' ?>>Batal</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-edit" style="border:none; cursor:pointer;" title="Simpan Status"><i class="fa fa-save"></i></button>
                                </form>
                            </td>

                            <td>
                                <a href="data_pesanan.php?hapus=<?= $row['id_transaksi'] ?>" class="btn-delete" onclick="return confirm('Hapus Permanen?')" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>