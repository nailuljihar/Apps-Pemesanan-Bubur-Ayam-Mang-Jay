<?php
// Paksa munculin error di layar (Mode Debugging Keras)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>TESTING MIDTRANS CONNECTION</h1>";
echo "<hr>";

// 1. CEK PATH AUTOLOAD
echo "<h3>1. Cek File Vendor</h3>";
$path_vendor = __DIR__ . '/../../../vendor/autoload.php';
echo "Mencari autoload di: " . $path_vendor . "<br>";

if (file_exists($path_vendor)) {
    echo "<span style='color:green; font-weight:bold;'>[OK] File Autoload Ditemukan.</span><br>";
    require_once $path_vendor;
} else {
    echo "<span style='color:red; font-weight:bold;'>[FATAL] File Autoload TIDAK ADA! Cek folder vendor.</span>";
    die();
}

// 2. CEK CLASS MIDTRANS
echo "<h3>2. Cek Library Midtrans</h3>";
if (class_exists('Midtrans\Config')) {
    echo "<span style='color:green; font-weight:bold;'>[OK] Class Midtrans Terbaca.</span><br>";
} else {
    echo "<span style='color:red; font-weight:bold;'>[FATAL] Class Midtrans tidak terbaca meski autoload ada. Coba 'composer dump-autoload'.</span>";
    die();
}

// 3. TES REQUEST TOKEN (DUMMY DATA)
echo "<h3>3. Tes Request Token ke Midtrans</h3>";
try {
    // Set Konfigurasi
    \Midtrans\Config::$serverKey = 'SB-Mid-server-9nWvHSWmVVj4U90WuCfqJ-67'; // Key Sandbox Lo
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    echo "Config berhasil di-set.<br>";

    // Buat data dummy seolah-olah ada orang beli
    $params = array(
        'transaction_details' => array(
            'order_id' => 'TEST-DEBUG-' . time(),
            'gross_amount' => 10000,
        ),
        'customer_details' => array(
            'first_name' => 'Tester',
            'email' => 'test@example.com',
            'phone' => '08123456789',
        ),
    );

    echo "Mengirim request ke server Midtrans...<br>";
    
    // Minta Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    
    echo "<br><div style='padding:20px; background:#e0ffd4; border:2px solid green;'>";
    echo "<h2 style='color:green; margin:0;'>SUKSES! \o/</h2>";
    echo "<p>Berhasil dapat Snap Token: <b>" . $snapToken . "</b></p>";
    echo "<p>Artinya: Backend lo SEHAT 100%. Masalahnya ada di JavaScript/Data Frontend.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<br><div style='padding:20px; background:#ffd4d4; border:2px solid red;'>";
    echo "<h2 style='color:red; margin:0;'>GAGAL! :(</h2>";
    echo "<p>Pesan Error: <b>" . $e->getMessage() . "</b></p>";
    echo "</div>";
    
    // Tampilkan detail error lengkap
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}
?>