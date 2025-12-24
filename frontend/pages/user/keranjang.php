<?php
session_start();
// Handle Update Jumlah (CRUD: Update)
if(isset($_POST['update_qty'])) {
    $id = $_POST['id_produk'];
    $qty = intval($_POST['jumlah']);
    if($qty > 0) {
        $_SESSION['keranjang'][$id]['jumlah'] = $qty;
    }
}

// Handle Hapus Item (CRUD: Delete)
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
    header("Location: keranjang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="admin-sidebar">
        <div class="sidebar-header"><h3>MANG JAY<br><small>Pelanggan</small></h3></div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fa-solid fa-utensils"></i> <span>Pilih Menu</span></a></li>
            <li><a href="keranjang.php" class="active"><i class="fa-solid fa-cart-shopping"></i> <span>Keranjang</span></a></li>
            <li><a href="riwayat.php"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat</span></a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-pen"></i> <span>Edit Profile</span></a></li>
        </ul>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Keranjang Pesanan</h1>
        </div>

        <div class="table-container">
            <?php if(empty($_SESSION['keranjang'])): ?>
                <div class="alert alert-info">Keranjang kamu masih kosong. <a href="index.php">Pesan sekarang!</a></div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th style="width:150px;">Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_belanja = 0;
                        foreach($_SESSION['keranjang'] as $id => $item): 
                            $subtotal = $item['harga'] * $item['jumlah'];
                            $total_belanja += $subtotal;
                        ?>
                        <tr>
                            <td><?= $item['nama_produk'] ?></td>
                            <td>Rp <?= number_format($item['harga'],0,',','.') ?></td>
                            <td>
                                <form action="" method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="id_produk" value="<?= $id ?>">
                                    <input type="number" name="jumlah" value="<?= $item['jumlah'] ?>" min="1" style="width:60px; padding:5px;">
                                    <button type="submit" name="update_qty" class="btn-qty" title="Update"><i class="fa fa-refresh"></i></button>
                                </form>
                            </td>
                            <td>Rp <?= number_format($subtotal,0,',','.') ?></td>
                            <td>
                                <a href="keranjang.php?hapus=<?= $id ?>" class="btn-delete" onclick="return confirm('Hapus menu ini?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right; font-weight:bold;">Total Sementara:</td>
                            <td colspan="2" style="font-weight:bold;">Rp <?= number_format($total_belanja,0,',','.') ?></td>
                        </tr>
                    </tfoot>
                </table>

                <div style="margin-top:20px; text-align:right;">
                    <a href="index.php" class="tombol-abu" style="padding:10px 20px; text-decoration:none;">Tambah Menu Lain</a>
                    <a href="checkout.php" class="tombol-biru" style="padding:10px 20px; text-decoration:none;">Lanjut ke Pembayaran <i class="fa fa-arrow-right"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>