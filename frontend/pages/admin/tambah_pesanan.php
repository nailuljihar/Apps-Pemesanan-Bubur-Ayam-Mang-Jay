<?php
session_start();
include '../../../backend/config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../index.php");
    exit();
}

// Proses Simpan Pesanan
if (isset($_POST['simpan_pesanan'])) {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $produk_items = $_POST['produk']; // Array ID Produk
    $qty_items = $_POST['qty'];       // Array Jumlah
    
    // Hitung Total & Validasi Stok (Simple)
    $total_transaksi = 0;
    $detail_list = [];

    foreach ($produk_items as $key => $id_produk) {
        $jumlah = (int)$qty_items[$key];
        if ($jumlah > 0) {
            // Ambil harga produk
            $q_prod = $koneksi->query("SELECT harga FROM produk WHERE id_produk = '$id_produk'");
            $d_prod = $q_prod->fetch_assoc();
            $subtotal = $d_prod['harga'] * $jumlah;
            
            $total_transaksi += $subtotal;
            $detail_list[] = [
                'id_produk' => $id_produk,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal
            ];
        }
    }

    if ($total_transaksi > 0) {
        // 1. Insert Header Transaksi (Offline = Cash & Sukses Langsung)
        // Note: id_users NULL karena offline (tamu), order_id kita buat format OFF-
        $order_id = "OFF-" . time();
        
        $sql_trx = "INSERT INTO transaksi 
                    (id_users, order_id, tanggal, jenis_transaksi, total_pendapatan, ongkir, nama_penerima, status, metode_pembayaran, catatan) 
                    VALUES 
                    (NULL, '$order_id', NOW(), 'offline', '$total_transaksi', 0, '$nama_pelanggan', 'Lunas', 'cash', 'Pembelian di Kasir')";
        
        if ($koneksi->query($sql_trx)) {
            $id_trx_baru = $koneksi->insert_id;

            // 2. Insert Detail
            foreach ($detail_list as $item) {
                $sql_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal, tanggal) 
                               VALUES ('$id_trx_baru', '{$item['id_produk']}', '{$item['jumlah']}', '{$item['subtotal']}', NOW())";
                $koneksi->query($sql_detail);
            }

            echo "<script>alert('Pesanan Offline Berhasil Disimpan!'); window.location='data_pesanan.php';</script>";
        } else {
            echo "<script>alert('Gagal simpan transaksi: " . $koneksi->error . "');</script>";
        }
    } else {
        echo "<script>alert('Pilih minimal 1 produk!');</script>";
    }
}

// Ambil Data Produk untuk Dropdown
$data_produk = $koneksi->query("SELECT * FROM produk WHERE status_aktif = 1");
$produk_arr = [];
while ($p = $data_produk->fetch_assoc()) {
    $produk_arr[] = $p;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pesanan Offline</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .row-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .row-item select { flex: 2; padding: 10px; }
        .row-item input { flex: 1; padding: 10px; }
        .btn-add-row { background: #27ae60; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .btn-remove-row { background: #c0392b; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
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
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Input Pesanan Offline (Kasir)</h1>
        </div>

        <div class="form-container">
            <form action="" method="POST">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Nama Pelanggan / Meja</label>
                    <input type="text" name="nama_pelanggan" class="input-field" placeholder="Contoh: Meja 5 / Bpk. Budi" required style="width:100%; padding:10px;">
                </div>

                <label>Pilih Menu</label>
                <div id="items-container">
                    <div class="row-item">
                        <select name="produk[]" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($produk_arr as $prod): ?>
                                <option value="<?= $prod['id_produk'] ?>">
                                    <?= $prod['nama_produk'] ?> - Rp <?= number_format($prod['harga'],0,',','.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="qty[]" placeholder="Jml" min="1" value="1" required>
                        <button type="button" class="btn-add-row" onclick="addRow()"><i class="fa fa-plus"></i></button>
                    </div>
                </div>

                <div style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px;">
                    <button type="submit" name="simpan_pesanan" class="tombol-biru" style="width: 100%; padding: 12px; font-size: 1.1em;">
                        <i class="fa fa-save"></i> PROSES PEMBAYARAN (CASH)
                    </button>
                    <a href="data_pesanan.php" style="display:block; text-align:center; margin-top:10px; color:#666;">Batal</a>
                </div>

            </form>
        </div>
    </main>

    <script>
        // Script sederhana untuk tambah baris menu
        function addRow() {
            const container = document.getElementById('items-container');
            const row = document.createElement('div');
            row.className = 'row-item';
            row.innerHTML = `
                <select name="produk[]" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach ($produk_arr as $prod): ?>
                        <option value="<?= $prod['id_produk'] ?>">
                            <?= $prod['nama_produk'] ?> - Rp <?= number_format($prod['harga'],0,',','.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="qty[]" placeholder="Jml" min="1" value="1" required>
                <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()"><i class="fa fa-trash"></i></button>
            `;
            container.appendChild(row);
        }
    </script>
</body>
</html>