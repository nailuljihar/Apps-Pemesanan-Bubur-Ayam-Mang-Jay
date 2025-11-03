<?php 
// Logika Sidebar Aktif: Cek nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Menu</title>
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

            <section class="content-area">
                <div class="menu-list-section">
                    <h4 class="content-header-menu">TAMBAH MENU</h4>
                    
                    <div class="menu-item-card">
                        <span class="item-name">Bubur Ayam Jumbo</span>
                        <div class="item-actions">
                            <button class="btn-edit">EDIT</button>
                            <button class="btn-hapus">HAPUS</button>
                        </div>
                    </div>
                    </div>
                
                <hr class="separator">

                <div class="add-menu-form-section">
                    <h3 class="form-title-center">Tambah Menu</h3>
                    <form class="menu-form" method="POST" action="add_menu_process.php">
                        <label for="nama_menu">Nama Menu</label>
                        <input type="text" id="nama_menu" name="nama_menu">
                        
                        <label for="deskripsi_menu">Deskripsi Menu</label>
                        <input type="text" id="deskripsi_menu" name="deskripsi_menu">
                        
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" name="kategori">
                        
                        <label for="harga">Harga</label>
                        <input type="number" id="harga" name="harga">

                        <div class="form-actions-bottom">
                            <button type="reset" class="btn-batal">BATAL</button>
                            <button type="submit" class="btn-tambah">TAMBAH</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</body>
</html>