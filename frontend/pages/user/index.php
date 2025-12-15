<?php
session_start();
// Pastikan path ke koneksi benar (sesuaikan naik 3 folder)
require_once '../../../backend/config/koneksi.php';

// Cek Login (Opsional: Hapus jika user tamu boleh lihat menu)
if (!isset($_SESSION['role'])) {
    header("Location: ../../../index.php");
    exit;
}

// Logika Tab Aktif (Default 'bestseller')
$current_tab = isset($_GET['tab']) ? htmlspecialchars($_GET['tab']) : 'bestseller';

// Logika Search Sederhana
$search_keyword = isset($_GET['q']) ? $koneksi->real_escape_string($_GET['q']) : '';
$query = "SELECT * FROM produk";
if (!empty($search_keyword)) {
    $query .= " WHERE nama_produk LIKE '%$search_keyword%'";
}
$query .= " ORDER BY nama_produk ASC";

$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Bubur Ayam Bandung Bang Jay</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Perbaikan kecil agar grid kartu terlihat rapi (timpa style carousel jika perlu) */
        .product-listing-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 10px 0;
            justify-content: center; /* Agar kartu ada di tengah */
        }
        /* Style tambahan untuk form di dalam kartu */
        .btn-beli-mini {
            background-color: var(--warna-primer);
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 5px;
            font-size: 0.9em;
        }
        .btn-beli-mini:hover { background-color: #00695c; }
        
        /* Floating Cart (Opsional, agar tetap bisa checkout) */
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--warna-primer);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            text-decoration: none;
            z-index: 999;
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8em;
        }
    </style>
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
                    <span>Selamat Datang, <b class="user-name"><?= $_SESSION['username'] ?></b></span>
                    <br>
                    <a href="../../../index.php" style="color: red; font-size: 0.8em; text-decoration: none;">LOG OUT</a>
                </div>
            </div>

            <div class="search-bar-container">
                <form action="" method="GET" class="container-search">
                    <div class="search-dropdown">
                        <select aria-label="Semua Kategori">
                            <option>All Categories</option>
                        </select>
                    </div>
                    <input type="search" name="q" placeholder="Cari Produk..." value="<?= htmlspecialchars($search_keyword) ?>">
                    <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </header>
        
        <main class="main-content-toko">
            
            <section class="hero-banner" style="background-color: #f7f0e6; padding: 40px; display: flex; justify-content: space-around; align-items: center; border-radius: 10px; margin-bottom: 30px;">
                <div>
                    <p style="color: #666;">Nikmati Kehangatan</p>
                    <h2 style="font-size: 2.2em; margin: 10px 0; color: #333;">BUBUR AYAM LEZAT<br>KHAS BANDUNG</h2>
                    <a href="#area-menu" class="tombol-form-cta" style="width: auto; text-decoration: none; display: inline-block;">PESAN SEKARANG</a>
                </div>
                <img src="../../assets/images/bubur-ayam1.jpg" alt="Bubur Ayam Banner" style="width: 35%; max-width: 300px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            </section>

            <section id="area-menu" style="padding: 10px 0;">
                <nav class="menu-tabs">
                    <a href="?tab=bestseller" class="<?= $current_tab == 'bestseller' ? 'active' : '' ?>">BEST SELLER</a>
                    <a href="?tab=baru" class="<?= $current_tab == 'baru' ? 'active' : '' ?>">BARU</a>
                    <a href="?tab=popular" class="<?= $current_tab == 'popular' ? 'active' : '' ?>">POPULAR</a>
                    <a href="index.php" class="tampilkan-semua">Tampilkan Semua</a>
                </nav>

                <div class="product-listing-grid">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="product-card">
                                <img src="../../assets/images/<?= !empty($row['gambar']) ? $row['gambar'] : 'bubur-ayam1.jpg' ?>" 
                                     alt="<?= $row['nama_produk'] ?>">
                                
                                <p class="product-name"><?= $row['nama_produk'] ?></p>
                                <p class="product-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                                
                                <form action="tambah_keranjang.php" method="POST">
                                    <input type="hidden" name="id_produk" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="nama_produk" value="<?= $row['nama_produk'] ?>">
                                    <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                                    <button type="submit" class="btn-beli-mini">
                                        <i class="fa fa-shopping-cart"></i> Beli
                                    </button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; width: 100%; color: #888;">Menu tidak ditemukan.</p>
                    <?php endif; ?>
                </div>

            </section>
        </main>
    </div>

    <a href="checkout.php" class="floating-cart">
        <i class="fa-solid fa-cart-shopping"></i>
        <?php 
        $jumlah_item = isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0;
        if($jumlah_item > 0): 
        ?>
            <span class="cart-badge"><?= $jumlah_item ?></span>
        <?php endif; ?>
    </a>

</body>
</html>