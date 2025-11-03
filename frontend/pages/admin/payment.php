<?php 
// Logika Sidebar Aktif: Cek nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran</title>
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
                <div class="pembayaran-wrapper" style="max-width: 500px; margin: 0 auto; box-shadow: none;">
                    <div class="modal-header-detail">
                        METODE PEMBAYARAN
                    </div>
                    <div class="modal-body-pembayaran">
                        <p class="scan-text">Scan untuk membayar</p>
                        
                        <div class="qr-code-box">
                            <img src="../../assets/images/qr_code_placeholder.png" alt="QR Code Pembayaran" class="qr-code-img">
                        </div>

                        <div class="pembayaran-actions">
                            <button class="btn-kembali" onclick="history.back()">KEMBALI</button>
                            <button class="btn-download-qr">DOWNLOAD QR CODE</button>
                            <button class="btn-selesai">SELESAI</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>