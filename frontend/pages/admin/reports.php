<?php 
// Logika Sidebar Aktif: Cek nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="../../css/styles.css"> 
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <header class="header-dashboard">
            <div class="logo-admin">BUBUR AYAM BANDUNG BANG JAY</div>
            <div class="status-admin">
                <span class="notification">Melayani Alat Masak/Minuman</span>
                <span class="admin-name">ADMIN</span>
            </div>
        </header>

        <div class="main-admin-content">
            <aside class="sidebar-nav">
                <h4 class="sidebar-title">DASHBOARD --</h4>
                <ul>
                    <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Makanan dan Minuman</a></li>
                    <li><a href="#">Data Pesanan</a></li>
                    <li><a href="reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>">Laporan</a></li>
                    <li><a href="#">Data Pelanggan</a></li>
                </ul>
            </aside>
            <main class="content-area">
                <div class="laporan-wrapper" style="max-width: 100%; margin: 0; box-shadow: none;">
                    <div class="modal-header-detail">
                        LAPORAN PENJUALAN
                    </div>
                    <div class="modal-body-laporan">
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Pesanan</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Total Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>07:30 WIB</td>
                                    <td>Bubur Jumbo</td>
                                    <td>2</td>
                                    <td>10.000</td>
                                    <td>Cash</td>
                                    <td>20.000</td>
                                </tr>
                                <tr>
                                    <td>-- WIB</td> <td>-</td> <td>-</td> <td>-</td> <td>Cash</td> <td>-</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="laporan-actions">
                            <button class="btn-kembali" onclick="history.back()">KEMBALI</button>
                            <button class="btn-download-laporan">DOWNLOAD LAPORAN</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>