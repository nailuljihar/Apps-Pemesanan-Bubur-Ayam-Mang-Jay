<?php
// Logika Sidebar Aktif
$current_page = basename($_SERVER['PHP_SELF']);

// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bubu_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Query Pesanan
$sql = "
    SELECT 
        t.id_transaksi,
        t.tanggal,
        t.metode_pembayaran,
        t.total_bayar,
        t.status_pesanan,

        u.nama AS nama_pelanggan
    FROM transaksi t
    LEFT JOIN users u ON t.id_user = u.id_user
    ORDER BY t.tanggal DESC
";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pesanan</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>
<div class="dashboard-wrapper">

    <header class="header-dashboard">
        <div class="logo-admin">BUBUR AYAM BANDUNG BANG JAY</div>
        <div class="status-admin">
            <span class="notification">Selamat Datang</span>
            <span class="admin-name">ADMIN</span>
        </div>
    </header>

    <div class="main-admin-content">

        <!-- SIDEBAR -->
        <aside class="sidebar-nav">
            <h4 class="sidebar-title">DASHBOARD --</h4>
            <ul>
                <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Makanan dan Minuman</a></li>
                <li><a href="pesanan.php" class="active">Data Pesanan</a></li>
                <li><a href="reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>">Laporan</a></li>
                <li><a href="#">Data Pelanggan</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="content-area">
            <div class="laporan-wrapper" style="max-width: 100%; margin: 0; box-shadow: none;">
                <div class="modal-header-detail">DATA PESANAN</div>

                <div class="modal-body-laporan">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Metode Bayar</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($p = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $p['id_transaksi'] ?></td>
                                    <td><?= date('d-m-Y', strtotime($p['tanggal'])) ?></td>
                                    <td><?= $p['nama_pelanggan'] ? $p['nama_pelanggan'] : '-' ?></td>
                                    <td><?= strtoupper($p['metode_pembayaran']) ?></td>
                                    <td><?= number_format($p['total_bayar'], 0, ',', '.') ?></td>

                                    <td>
                                        <span style="
                                            padding:5px 10px;
                                            border-radius:5px;
                                            color:white;
                                            background:<?= $p['status_pesanan'] == 'selesai' ? 'green' : ($p['status_pesanan'] == 'proses' ? 'orange' : 'red'); ?>
                                        ">
                                            <?= strtoupper($p['status_pesanan']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <a href="detail_pesanan.php?id=<?= $p['id_transaksi'] ?>" 
                                           class="btn-tambah" 
                                           style="padding:4px 8px;">
                                           Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">Belum ada pesanan.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>

    </div>

</div>
</body>
</html>
