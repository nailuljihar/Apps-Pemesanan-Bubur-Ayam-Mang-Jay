<?php
session_start();
$id_user_login = $_SESSION['id_users'] ?? NULL;
require_once '../../../backend/config/koneksi.php';
require_once '../../../vendor/autoload.php'; // Load Library Midtrans

// 1. Cek Login & Keranjang
if (!isset($_SESSION['id_users'])) {
    header("Location: ../../../index.php"); // Tendang kalau belum login
    exit;
}

if (empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang kosong!'); window.location='index.php';</script>";
    exit;
}

// 2. Setup Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-9nWvHSWmVVj4U90WuCfqJ-67'; // PAKE SERVER KEY LO
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// 3. Siapkan Data Transaksi
$id_user = $_SESSION['id_users'];
$nama_user = $_SESSION['nama_lengkap']; // Pastikan session ini ada pas login
$email_user = $_SESSION['email'] ?? ''; // Default kalau kosong
$no_hp = $_SESSION['no_hp'] ?? '';

// Bikin Order ID Unik (ORD-WAKTU-IDUSER) biar gak duplikat
$order_id = "ORD-" . time() . "-" . $id_user; 

// Hitung Total & Siapkan Item Details buat Midtrans
$total_bayar = 0;
$item_details = [];

foreach ($_SESSION['keranjang'] as $id_produk => $item) {
    $subtotal = $item['harga'] * $item['jumlah'];
    $total_bayar += $subtotal;

    $item_details[] = [
        'id' => $id_produk,
        'price' => $item['harga'],
        'quantity' => $item['jumlah'],
        'name' => substr($item['nama_produk'], 0, 50) // Midtrans batesin panjang nama
    ];
}

// Parameter Transaksi buat dikirim ke Midtrans
$transaction_details = [
    'order_id' => $order_id,
    'gross_amount' => $total_bayar,
];

$customer_details = [
    'first_name' => $nama_user,
    'email' => $email_user,
    'phone' => $no_hp,
];

// Tentukan alamat website kamu
// PENTING: Ganti 'http://localhost/folder-kamu' sesuai alamat asli di browser kamu!
$base_url = "http://localhost/Apps-Pemesanan-Bubur-Ayam-Mang-Jay/frontend/pages/user/";

$midtrans_params = [
    'transaction_details' => $transaction_details,
    'item_details'      => $item_details,
    'customer_details'  => $customer_details,
    // TAMBAHKAN INI:
    'callbacks' => [
        'finish' => $base_url . '/riwayat.php?status=sukses'
    ]
];

try {
    // 4. Minta Snap Token ke Midtrans
    $snapToken = \Midtrans\Snap::getSnapToken($midtrans_params);

    // 5. Simpan ke Database (Tabel Transaksi)
    // Status awal 'Pending', metode 'qris' (bisa diubah user nanti pas bayar), jenis 'online'
    $query_transaksi = "INSERT INTO transaksi (id_user, order_id, tanggal, jenis_transaksi, total_pendapatan, metode_pembayaran, status, snap_token, catatan) 
                        VALUES (?, ?, NOW(), 'online', ?, 'qris', 'Pending', ?, 'Pemesanan Web')";
    
    // UPDATE QUERY INSERT
    $stmt = $koneksi->prepare("INSERT INTO transaksi 
        (id_transaksi, id_users, tanggal, jenis_transaksi, total_pendapatan, ongkir, nama_penerima, alamat_pengiriman, no_hp_penerima, status, metode_pembayaran, catatan) 
        VALUES (?, ?, NOW(), 'online', ?, ?, ?, ?, ?, 'Pending', 'qris', ?)");
    $stmt->bind_param("siissssss", 
        $data['order_id'], 
        $id_user_login,      // Masukkan ID User di sini
        $data['gross_amount'], 
        $data['ongkir'], 
        $data['nama_penerima'], 
        $data['alamat'], 
        $data['no_hp'], 
        $data['catatan']
    );

    $stmt->execute();
    
    if ($stmt->execute()) {
        $id_transaksi_baru = $koneksi->insert_id; // Ambil ID Auto Increment

        // 6. Simpan Detail Transaksi (Looping keranjang)
        $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal, tanggal) VALUES (?, ?, ?, ?, NOW())";
        $stmt_detail = $koneksi->prepare($query_detail);

        foreach ($_SESSION['keranjang'] as $id_produk => $item) {
            $subtotal = $item['harga'] * $item['jumlah'];
            $stmt_detail->bind_param("iiii", $id_transaksi_baru, $id_produk, $item['jumlah'], $subtotal);
            $stmt_detail->execute();
        }

        // 7. Kosongkan Keranjang & Redirect
        unset($_SESSION['keranjang']);
        
        // Lempar ke halaman Riwayat biar user bisa langsung bayar
        echo "<script>
            alert('Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
            window.location = 'riwayat.php'; 
        </script>";

    } else {
        throw new Exception("Gagal menyimpan transaksi ke database.");
    }

} catch (Exception $e) {
    echo "Terjadi Kesalahan: " . $e->getMessage();
    // Bisa tambah tombol kembali disini
}
?>