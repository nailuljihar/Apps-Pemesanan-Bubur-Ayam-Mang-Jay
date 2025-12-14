<?php 
// 1. Logika Modal Detail Menu menggunakan Parameter URL
$menu_id = isset($_GET['menu']) ? htmlspecialchars($_GET['menu']) : null; 
$show_modal = !empty($menu_id);
$detail = null;

if ($show_modal) {
    // Simulasi data menu yang akan diambil dari database (backend)
    $menu_data = [
        'bubur_jumbo' => [
            'nama' => 'Bubur Ayam Jumbo', 
            'harga' => 'Rp. 10.000', 
            'harga_coret' => 'Rp. 15.000',
            'komposisi' => ['Ayam', 'Kerupuk', 'Kuah'],
            // PASTIKAN JALUR INI BENAR KE FOLDER ASSETS
            'image_path' => '/bubur-ayam-frontend/assets/images/bubur-ayam-jumbo.jpg' 
        ],
        // Tambahkan data menu lain di sini
    ];
    $detail = isset($menu_data[$menu_id]) ? $menu_data[$menu_id] : null;
}
$modal_display_style = $show_modal ? 'display: flex;' : 'display: none;';

// 2. Logika Tab Aktif
$current_tab = isset($_GET['tab']) ? htmlspecialchars($_GET['tab']) : 'bestseller';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Bubur Ayam Bandung Bang Jay</title>
    <link rel="stylesheet" href="../../css/styles.css"> 
</head>
<body>
    <div class="main-wrapper">
        <header class="header-toko">
            <div class="container-header">
                <div class="logo-text">
                    <h3>BUBUR AYAM BANDUNG</h3>
                    <h1>BANG JAY</h1>
                </div>
                <div class="welcome-section">
                    <span>WELCOME</span>
                    <a href="login.php" class="user-name">LOG IN / REGISTER</a>
                </div>
            </div>
            <div class="search-bar-container">
                <div class="container-search">
                    <div class="search-dropdown">
                        <select aria-label="Semua Kategori">
                            <option>All Categories</option>
                        </select>
                    </div>
                    <input type="search" placeholder="Cari Produk...">
                    <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </header>
        
        <main class="main-content-toko">
            <section class="hero-banner" style="background-color: #f7f0e6; padding: 50px; display: flex; justify-content: space-around; align-items: center;">
                <div>
                    <p>Selamat Datang Di</p>
                    <h2 style="font-size: 2.5em; margin: 10px 0;">BUBUR AYAM BANDUNG<br>BANG JAY</h2>
                    <button class="tombol-form-cta" style="width: auto;">BELANJA SEKARANG</button>
                </div>
                <img src="/bubur-ayam-frontend/assets/images/bubur-jumbo-banner.jpg" alt="Bubur Ayam" style="width: 40%; max-width: 400px;">
            </section>

            <section style="padding: 20px 0;">
                <nav class="menu-tabs">
                    <a href="index.php?tab=bestseller" class="<?= $current_tab == 'bestseller' ? 'active' : '' ?>">BEST SELLER</a>
                    <a href="index.php?tab=baru" class="<?= $current_tab == 'baru' ? 'active' : '' ?>">BARU</a>
                    <a href="index.php?tab=popular" class="<?= $current_tab == 'popular' ? 'active' : '' ?>">POPULAR</a>
                    <a href="index.php" class="tampilkan-semua">Tampilkan Semua</a>
                </nav>

                <div class="product-carousel">
                    <div class="product-card">
                        <a href="checkout.php"><img src="/bubur-ayam-frontend/assets/images/bubur-ayam.jpg" alt="Bubur Ayam Jumbo"></a>
                        <p class="product-name">Bubur Ayam Jumbo</p>
                        <p class="product-price">Rp. 10.000</p>
                    </div>
                    </div>
            </section>
        </main>
        
        <div class="modal-backdrop" id="modalDetailMenu" style="<?= $modal_display_style ?>">
            <?php if ($detail): ?>
            <div class="detail-menu-modal">
                <div class="modal-header-detail">DETAIL MENU</div>
                <div class="modal-body-detail">
                    <div class="detail-content-wrapper">
                        <div class="menu-image-section">
                            <img src="<?= $detail['image_path'] ?>" alt="<?= $detail['nama'] ?>">
                            <span class="harga-awal">MULAI DARI</span>
                            <span class="harga-badge"><?= $detail['harga'] ?></span>
                        </div>
                        <div class="menu-info-section">
                            <p class="rating">(5)</p>
                            <h3 class="menu-title"><?= $detail['nama'] ?></h3>
                            <p class="harga-sekarang"><?= $detail['harga'] ?> <span class="harga-coret"><?= $detail['harga_coret'] ?></span></p>
                            <ul class="komposisi">
                                <?php foreach($detail['komposisi'] as $item): ?>
                                    <li><?= $item ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button class="btn-beli-sekarang">BELI SEKARANG</button>
                            <a href="index.php" style="background: none; border: none; color: gray; margin-top: 10px; cursor: pointer; display: block; text-align: right; text-decoration: none;">Tutup Modal</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>