<?php
// File: frontend/pages/user/cek_path.php
echo "<h2>Diagnosa Path Server</h2>";
echo "Posisi file ini: " . __DIR__ . "<br><br>";

// Coba path standar (naik 3 folder)
$path_standar = __DIR__ . '/../../../vendor/autoload.php';
$real_path = realpath($path_standar);

echo "Mencoba mencari autoload di (Logic): <b>" . $path_standar . "</b><br>";

if (file_exists($path_standar)) {
    echo "Status File: <b style='color:green'>DITEMUKAN!</b><br>";
    echo "Path Asli Sistem: " . $real_path . "<br><br>";
    
    // Coba Load
    require_once $path_standar;
    echo "Load Autoload: <b style='color:green'>SUKSES</b><br>";
    
    // Cek Midtrans
    if (class_exists('Midtrans\Config')) {
        echo "Library Midtrans: <b style='color:green'>AMAN (Terinstall)</b>";
    } else {
        echo "Library Midtrans: <b style='color:red'>GAGAL (Class tidak ditemukan). Coba composer update.</b>";
    }

} else {
    echo "Status File: <b style='color:red'>TIDAK DITEMUKAN (Gagal Total)</b><br>";
    echo "Saran: Cek apakah folder 'vendor' sejajar dengan folder 'backend' dan 'frontend'?";
}
?>