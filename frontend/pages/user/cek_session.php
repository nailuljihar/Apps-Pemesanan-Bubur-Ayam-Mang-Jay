<?php
session_start();

echo "<h2>🕵️‍♂️ Detektif Session</h2>";
echo "<p>Mengecek data yang tersimpan di browser kamu...</p>";

echo "<pre style='background: #f4f4f4; padding: 15px; border: 1px solid #ddd;'>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Analisis:</h3>";

if (!isset($_SESSION['id_users'])) {
    echo "<p style='color: red; font-weight: bold;'>❌ ERROR FATAL: 'id_users' TIDAK DITEMUKAN!</p>";
    echo "<p>Penyebab: File login (index.php) belum menyimpan id_users, atau kamu belum Login ulang setelah edit kodingan.</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ AMAN: 'id_users' Ada. Nilainya: " . $_SESSION['id_users'] . "</p>";
    echo "<p>Kalau ini muncul tapi Profile masih error, berarti masalahnya ada di kodingan Profile.php (salah ketik variable).</p>";
}

echo "<br><a href='../../../index.php'>Logout & Login Ulang</a>";
?>