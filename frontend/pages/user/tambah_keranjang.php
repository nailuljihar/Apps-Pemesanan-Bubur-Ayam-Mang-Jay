<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_produk'];
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];

    // Struktur Keranjang: Array of Arrays
    // Format: [id_produk => [nama, harga, jumlah]]
    
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Cek apakah produk sudah ada di keranjang?
    if (isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id]['jumlah'] += 1; // Kalau ada, tambah jumlahnya
    } else {
        $_SESSION['keranjang'][$id] = [
            'nama_produk' => $nama,
            'harga' => $harga,
            'jumlah' => 1
        ]; // Kalau belum, buat baru
    }

    // Redirect balik ke index dengan pesan sukses (opsional bisa pakai alert JS)
    echo "<script>alert('Menu berhasil ditambahkan ke keranjang!'); window.location.href='index.php';</script>";
}
?>