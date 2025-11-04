<?php
// Di sini lo bisa tambahin logika buat ambil data cart dari session, dll.
// Untuk contoh ini, kita hardcode aja ya.
$nama_produk = "Bubur Ayam Jumbo";
$harga = 10000;
$order_id = "BUBUR-" . time(); // Bikin Order ID unik
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BUBUR AYAM BANG JAY</title>
    
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SB-Mid-client-p03qwXbZBJ7PooX6"></script> 
      <style>
        /* Bikin style-nya mirip sama web lo */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .order-details {
            margin-bottom: 25px;
        }
        .order-details p {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }
        .order-details span {
            float: right;
            font-weight: bold;
            color: #222;
        }
        /* Styling tombol biar sama kayak di web lo */
        .btn-bayar {
            display: inline-block;
            background-color: #1abc9c; /* Warna hijau/teal dari tombol lo */
            color: #ffffff;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-bayar:hover {
            background-color: #16a085;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Detail Pesanan</h2>
        <div class="order-details">
            <p>Produk: <span><?php echo $nama_produk; ?></span></p>
            <p>Total Harga: <span>Rp. <?php echo number_format($harga, 0, ',', '.'); ?></span></p>
        </div>

        <button id="pay-button" class="btn-bayar">BAYAR SEKARANG</button>
    </div>

    <script type="text/javascript">
        // Ambil tombol bayar
        var payButton = document.getElementById('pay-button');

        // Kasih event click
        payButton.onclick = function(){
            // Tampilkan loading atau disable tombol biar ga di-klik berkali-kali
            payButton.disabled = true;
            payButton.innerHTML = "Loading...";

            // Panggil backend (proses_pembayaran.php) untuk dapetin Snap Token
            fetch('proses_pembayaran.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                // Kirim data pesanan ke backend
                body: JSON.stringify({
                    order_id: '<?php echo $order_id; ?>',
                    harga: <?php echo $harga; ?>,
                    nama_produk: '<?php echo $nama_produk; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.token){
                    // Kalo dapet token, buka pop-up Midtrans
                    window.snap.pay(data.token, {
                        onSuccess: function(result){
                            /* Kasih notif sukses */
                            alert("Pembayaran sukses!"); 
                            console.log(result);
                            // Arahin ke halaman status pesanan, dll.
                            // window.location.href = "/status_sukses.php"; 
                        },
                        onPending: function(result){
                            /* Kasih notif pending */
                            alert("Menunggu pembayaran!"); 
                            console.log(result);
                        },
                        onError: function(result){
                            /* Kasih notif error */
                            alert("Pembayaran gagal!"); 
                            console.log(result);
                        },
                        onClose: function(){
                            /* Kalo pop-up ditutup sebelum bayar */
                            alert('Anda menutup pop-up tanpa menyelesaikan pembayaran');
                            // Aktifin lagi tombolnya
                            payButton.disabled = false;
                            payButton.innerHTML = "BAYAR SEKARANG";
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token pembayaran.');
                    payButton.disabled = false;
                    payButton.innerHTML = "BAYAR SEKARANG";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Cek konsol.');
                payButton.disabled = false;
                payButton.innerHTML = "BAYAR SEKARANG";
            });
        };
    </script>

</body>
</html>