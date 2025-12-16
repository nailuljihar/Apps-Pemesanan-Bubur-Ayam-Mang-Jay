<?php
session_start();
// Helper fungsi format rupiah
function formatRupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Keranjang Belanja - Bang Jay</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cart-container { max-width: 900px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; }
        .table-cart { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table-cart th, .table-cart td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        .qty-input { width: 50px; padding: 5px; text-align: center; }
        .btn-update { background: #ffc107; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; color: #000; font-size: 0.8em; }
        .btn-delete { background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; text-decoration: none; font-size: 0.9em; }
        .cart-summary { margin-top: 20px; text-align: right; }
        .btn-checkout { background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; }
    </style>
</head>
<body>

<div class="cart-container">
    <h1><i class="fa-solid fa-cart-shopping"></i> Keranjang Anda</h1>

    <?php if (empty($_SESSION['keranjang'])): ?>
        <div style="text-align: center; padding: 50px;">
            <h3>Keranjang masih kosong nih.</h3>
            <p>Yuk pesen bubur dulu!</p>
            <a href="index.php" style="color: blue; text-decoration: underline;">Kembali ke Menu</a>
        </div>
    <?php else: ?>
        
        <table class="table-cart">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_bayar = 0;
                foreach ($_SESSION['keranjang'] as $id_produk => $item): 
                    $subtotal = $item['harga'] * $item['jumlah'];
                    $total_bayar += $subtotal;
                ?>
                <tr>
                    <td><strong><?= $item['nama_produk'] ?></strong></td>
                    <td><?= formatRupiah($item['harga']) ?></td>
                    <td>
                        <form action="update_keranjang.php" method="POST" style="display:flex; gap:5px; align-items:center;">
                            <input type="hidden" name="id_produk" value="<?= $id_produk ?>">
                            <input type="number" name="jumlah" value="<?= $item['jumlah'] ?>" min="1" class="qty-input">
                            <button type="submit" name="update_jumlah" class="btn-update" title="Update Jumlah">
                                <i class="fa-solid fa-sync"></i>
                            </button>
                        </form>
                    </td>
                    <td><?= formatRupiah($subtotal) ?></td>
                    <td>
                        <a href="update_keranjang.php?action=delete&id=<?= $id_produk ?>" class="btn-delete" onclick="return confirm('Hapus menu ini?');">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Total Belanja: <?= formatRupiah($total_bayar) ?></h3>
            <div style="margin-top: 20px;">
                <a href="index.php" style="margin-right: 15px; color: #666; text-decoration: none;">Lanjut Belanja</a>
                <a href="proses_pembayaran.php" class="btn-checkout">Bayar Sekarang <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

    <?php endif; ?>
</div>

</body>
</html>