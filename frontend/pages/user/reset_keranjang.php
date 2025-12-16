<?php
session_start();
unset($_SESSION['keranjang']); // Hapus keranjang doang
echo "Keranjang berhasil di-reset! <a href='index.php'>Kembali Belanja</a>";
?>