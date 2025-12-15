<?php
session_start();
// Cek dulu, kalau keranjang kosong, tendang balik ke menu
if (empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang masih kosong, Bro!'); window.location.href='index.php';</script>";
    exit;
}

// Hitung Total Belanja & Siapkan Data Item buat dikirim ke Midtrans/Backend
$total_harga = 0;
$item_details = []; // Array buat nampung detail item

foreach ($_SESSION['keranjang'] as $id_produk => $item) {
    $subtotal = $item['harga'] * $item['jumlah'];
    $total_harga += $subtotal;
    
    // Masukkan ke format item_details Midtrans
    $item_details[] = [
        'id'       => $id_produk,
        'price'    => intval($item['harga']),
        'quantity' => intval($item['jumlah']),
        'name'     => substr($item['nama'], 0, 50) // Midtrans limit nama item 50 char
    ];
}

// Order ID Unik
$order_id = "JAY-" . uniqid() . "-" . time(); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BUBUR AYAM BANG JAY</title>
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SB-Mid-client-p03qwXbZBJ7PooX6"></script> 
    <link rel="stylesheet" href="../../css/styles.css"> <style>
        /* Override dikit buat halaman checkout */
        body { background-color: #f7f0e6; }
        .checkout-container {
            max-width: 800px; margin: 50px auto; background: white; padding: 30px;
            border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .table-checkout { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-checkout th, .table-checkout td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .total-row { font-weight: bold; font-size: 1.2em; color: var(--warna-primer); }
        .btn-bayar {
            display: block; width: 100%; background-color: #00796b; color: white;
            padding: 15px; border: none; border-radius: 5px; font-size: 1.1em;
            cursor: pointer; text-align: center; font-weight: bold;
        }
        .btn-bayar:hover { background-color: #004d40; }
    </style>
</head>
<body>

    <div class="checkout-container">
        <h2 style="margin-bottom: 20px; color: #333;">Konfirmasi Pesanan</h2>
        
        <table class="table-checkout">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['keranjang'] as $itm): ?>
                <tr>
                    <td><?= $itm['nama'] ?></td>
                    <td>Rp <?= number_format($itm['harga'], 0, ',', '.') ?></td>
                    <td><?= $itm['jumlah'] ?></td>
                    <td>Rp <?= number_format($itm['harga'] * $itm['jumlah'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total Bayar:</td>
                    <td>Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <button id="pay-button" class="btn-bayar">BAYAR SEKARANG</button>
        <br>
        <a href="index.php" style="display:block; text-align:center; margin-top:10px; text-decoration:none; color: #666;">&larr; Kembali ke Menu</a>
    </div>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');

        payButton.onclick = function(){
            payButton.disabled = true;
            payButton.innerHTML = "Memproses...";

            // Kita kirim Data Lengkap ke Backend
            const transactionData = {
                order_id: '<?= $order_id ?>',
                gross_amount: <?= $total_harga ?>,
                // Kirim detail item biar muncul di email invoice Midtrans
                items: <?= json_encode($item_details) ?>, 
                // Data customer dummy (nanti bisa diambil dari session login)
                customer: {
                    first_name: "<?= $_SESSION['username'] ?? 'Pelanggan' ?>",
                    email: "user@example.com",
                    phone: "08123456789"
                }
            };

            fetch('proses_pembayaran.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(transactionData)
            })
            .then(response => response.json())
            .then(data => {
                if(data.token){
                    window.snap.pay(data.token, {
                        onSuccess: function(result){
                            alert("Pembayaran Berhasil!"); 
                            // Opsi: Redirect ke halaman 'clear_cart.php' untuk hapus session keranjang
                            window.location.href = "index.php"; 
                        },
                        onPending: function(result){
                            alert("Menunggu Pembayaran!"); 
                            console.log(result);
                        },
                        onError: function(result){
                            alert("Pembayaran Gagal!"); 
                            console.log(result);
                            payButton.disabled = false;
                            payButton.innerHTML = "BAYAR SEKARANG";
                        },
                        onClose: function(){
                            alert('Pop-up ditutup!');
                            payButton.disabled = false;
                            payButton.innerHTML = "BAYAR SEKARANG";
                        }
                    });
                } else {
                    console.error("Token error:", data);
                    alert('Gagal request token: ' + (data.error || 'Unknown Error'));
                    payButton.disabled = false;
                    payButton.innerHTML = "BAYAR SEKARANG";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
                payButton.disabled = false;
                payButton.innerHTML = "BAYAR SEKARANG";
            });
        };
    </script>

</body>
</html>