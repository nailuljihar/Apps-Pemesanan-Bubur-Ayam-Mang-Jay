<?php
session_start();
require_once '../../../backend/config/koneksi.php';

if (empty($_SESSION['keranjang'])) {
    header("Location: index.php");
    exit;
}

// --- LOGIKA ONGKIR ---
$ongkir = 12000; // Contoh Tarif Flat Ongkir (bisa dibuat dinamis jika ada data jarak)
$total_barang = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total_barang += ($item['harga'] * $item['jumlah']);
}
$grand_total = $total_barang + $ongkir;

// Ambil data user untuk autofill form
$id_user = $_SESSION['id_users'];
$sql_user = "SELECT * FROM users WHERE id_users = '$id_user'";
$u = $koneksi->query($sql_user)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout & Pembayaran</title>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-p03qwXbZBJ7PooX6"></script>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .checkout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        @media (max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } }
        .summary-box { background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
        .row-total { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1.1em; }
        .grand-total { font-weight: bold; color: #2c3e50; font-size: 1.3em; border-top: 2px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <nav class="admin-sidebar">
        <div class="sidebar-header"><h3>MANG JAY<br><small>Checkout</small></h3></div>
        <ul class="sidebar-menu">
            <li><a href="keranjang.php"><i class="fa-solid fa-arrow-left"></i> <span>Kembali ke Keranjang</span></a></li>
        </ul>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Konfirmasi & Pembayaran</h1>
        </div>

        <div class="checkout-grid">
            <div class="form-section">
                <div class="summary-box">
                    <h3><i class="fa fa-map-marker-alt"></i> Informasi Pengiriman</h3>
                    <br>
                    <div class="form-group" style="margin-bottom:15px;">
                        <label>Nama Penerima</label>
                        <input type="text" id="nama_penerima" class="input-field" style="width:100%; padding:10px;" value="<?= $u['nama_lengkap'] ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom:15px;">
                        <label>Nomor Telepon / WA</label>
                        <input type="text" id="no_hp" class="input-field" style="width:100%; padding:10px;" value="<?= $u['no_hp'] ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom:15px;">
                        <label>Alamat Lengkap Pengiriman</label>
                        <textarea id="alamat" class="input-field" rows="4" style="width:100%; padding:10px;" required><?= $u['alamat'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Catatan Pesanan (Opsional)</label>
                        <input type="text" id="catatan" class="input-field" style="width:100%; padding:10px;" placeholder="Contoh: Jangan pakai seledri">
                    </div>
                </div>
            </div>

            <div class="summary-section">
                <div class="summary-box">
                    <h3><i class="fa fa-receipt"></i> Rincian Pesanan</h3>
                    <br>
                    <ul style="list-style:none; padding:0; margin-bottom:20px;">
                        <?php foreach($_SESSION['keranjang'] as $itm): ?>
                        <li style="border-bottom:1px dashed #ccc; padding:5px 0; display:flex; justify-content:space-between;">
                            <span><?= $itm['nama'] ?> (x<?= $itm['jumlah'] ?>)</span>
                            <span>Rp <?= number_format($itm['harga'] * $itm['jumlah'], 0, ',', '.') ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="row-total">
                        <span>Subtotal Barang</span>
                        <span>Rp <?= number_format($total_barang, 0, ',', '.') ?></span>
                    </div>
                    <div class="row-total" style="color: #27ae60;">
                        <span>Biaya Ongkir (Flat)</span>
                        <span>Rp <?= number_format($ongkir, 0, ',', '.') ?></span>
                    </div>
                    <div class="row-total grand-total">
                        <span>Total Bayar</span>
                        <span>Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                    </div>

                    <button id="pay-button" class="tombol-biru" style="width:100%; margin-top:20px; font-size:1.1em; padding:15px;">
                        <i class="fa fa-lock"></i> BAYAR SEKARANG
                    </button>
                    <p style="font-size:0.8em; color:#666; margin-top:10px; text-align:center;">Pembayaran aman via Payment Gateway (Online Only).</p>
                </div>
            </div>
        </div>
    </main>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        
        payButton.addEventListener('click', function () {
            // Validasi Form Dulu
            var nama = document.getElementById('nama_penerima').value;
            var hp = document.getElementById('no_hp').value;
            var alamat = document.getElementById('alamat').value;
            var catatan = document.getElementById('catatan').value;

            if(nama == "" || hp == "" || alamat == ""){
                alert("Mohon lengkapi Nama, Nomor HP, dan Alamat Pengiriman!");
                return;
            }

            payButton.innerHTML = "Memproses...";
            payButton.disabled = true;

            // Persiapkan Data untuk dikirim ke Backend (AJAX)
            var dataPesanan = {
                grand_total: <?= $grand_total ?>,
                ongkir: <?= $ongkir ?>,
                nama_penerima: nama,
                no_hp: hp,
                alamat: alamat,
                catatan: catatan
            };

            fetch('proses_pembayaran.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataPesanan)
            })
            .then(response => response.json())
            .then(data => {
                if(data.token) {
                    window.snap.pay(data.token, {
                        onSuccess: function(result){
                            // Redirect ke riwayat atau sukses
                            window.location.href = "riwayat.php?status=success";
                        },
                        onPending: function(result){
                            window.location.href = "riwayat.php?status=pending";
                        },
                        onError: function(result){
                            alert("Pembayaran Gagal!");
                            payButton.disabled = false;
                            payButton.innerHTML = "BAYAR SEKARANG";
                        },
                        onClose: function(){
                            payButton.disabled = false;
                            payButton.innerHTML = "BAYAR SEKARANG";
                        }
                    });
                } else {
                    alert("Error Token: " + data.error);
                    payButton.disabled = false;
                    payButton.innerHTML = "BAYAR SEKARANG";
                }
            })
            .catch(err => {
                console.error(err);
                alert("Terjadi kesalahan koneksi");
                payButton.disabled = false;
                payButton.innerHTML = "BAYAR SEKARANG";
            });
        });
    </script>
</body>
</html>