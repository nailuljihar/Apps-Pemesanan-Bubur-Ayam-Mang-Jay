<?php
include '../../config/koneksi.php';

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$role_input = $_POST['role'];
$admin_token = $_POST['admin_token'];

// LOGIKA PENGAMANAN
$final_role = 'user'; // Default aman

if ($role_input === 'admin') {
    // Cek Token Rahasia (Bisa ditaruh di config atau hardcode dlu)
    $SECRET_CODE = "HORAS"; 
    
    if ($admin_token === $SECRET_CODE) {
        $final_role = 'admin';
    } else {
        // Jika token salah, stop proses atau paksa jadi user biasa
        die("Eits! Kode rahasia Admin salah. Anda tidak berhak mendaftar sebagai admin.");
    }
}

// Simpan ke Database
$query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$final_role')";
if (mysqli_query($conn, $query)) {
    echo "Registrasi Berhasil!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>