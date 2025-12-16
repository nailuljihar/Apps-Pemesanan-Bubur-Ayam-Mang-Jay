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

// Logika Best Seller: Produk yang paling banyak terjual di detail_transaksi
// Kita pake subquery biar otomatis 'realtime' ngitungnya
$query = "SELECT p.*, 
          (SELECT COALESCE(SUM(jumlah), 0) FROM detail_transaksi dt WHERE dt.id_produk = p.id_produk) as total_terjual 
          FROM produk p 
          ORDER BY total_terjual DESC"; 

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
                    <span>Selamat Datang, <b class="user-name"><?= $_SESSION['nama_lengkap'] ?></b></span>
                    <br>
                    <div style="margin-top: 5px; font-size: 0.9em;">
                        <a href="profile.php" style="margin-right: 10px; color: #333;"><i class="fa fa-user-edit"></i> Edit Profil</a>
                        <a href="riwayat.php" style="margin-right: 10px; color: #333;"><i class="fa fa-history"></i> Riwayat</a>
                        <a href="../../../index.php" style="color: red; text-decoration: none;">Logout</a>
                    </div>
                </div>
                        <a href="keranjang.php" class="floating-cart"> <i class="fa-solid fa-cart-shopping"></i>
                        </a>
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

            <div class="product-listing-grid">
    <?php 
    $rank = 0; // Buat nentuin top 3
    while($row = $result->fetch_assoc()): 
        $rank++;
        $is_best_seller = ($rank <= 3 && $row['total_terjual'] > 0); // Top 3 dan pernah terjual
    ?>
        <div class="product-card" style="position: relative;">
            
            <?php if($is_best_seller): ?>
                <div style="position: absolute; top: 10px; right: 10px; background: #ff9800; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.7em; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    <i class="fa-solid fa-crown"></i> BEST SELLER
                </div>
            <?php endif; ?>

            <img src="../../assets/images/<?= !empty($row['gambar']) ? $row['gambar'] : 'bubur-ayam1.jpg' ?>" alt="<?= $row['nama_produk'] ?>">
            
            <p class="product-name"><?= $row['nama_produk'] ?></p>
            <p class="product-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
            
            <form action="tambah_keranjang.php" method="POST">
                <input type="hidden" name="id_produk" value="<?= $row['id_produk'] ?>"> <input type="hidden" name="nama_produk" value="<?= $row['nama_produk'] ?>">
                <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                <button type="submit" class="btn-beli-mini"><i class="fa fa-shopping-cart"></i> Beli</button>
            </form>
        </div>
    <?php endwhile; ?>
    </div>

            </section>
        </main>
    </div>

    <a href="keranjang.php" class="floating-cart">
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