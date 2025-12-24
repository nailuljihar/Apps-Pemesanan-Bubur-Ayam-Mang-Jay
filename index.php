<?php
session_start();
require_once 'backend/config/koneksi.php';

// Jika sudah login, lempar ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: frontend/pages/admin/dashboard.php");
    } else {
        header("Location: frontend/pages/user/index.php");
    }
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Cek username
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Set Session
            $_SESSION['id_users'] = $row['id_users'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirect
            if ($row['role'] == 'admin') {
                header("Location: frontend/pages/admin/dashboard.php");
            } else {
                header("Location: frontend/pages/user/index.php");
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bubur Ayam Mang Jay</title>
    <link rel="stylesheet" href="frontend/css/styles.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <h2>Mang Jay</h2>
            <p>Silakan login untuk memesan bubur</p>
        </div>

        <?php if($error): ?>
            <div class="auth-alert auth-alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn-auth">MASUK SEKARANG</button>
        </form>

        <div class="auth-footer">
            <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
            <p style="margin-top:5px;"><a href="reset_pass.php" style="color:#636e72; font-size:0.85em;">Lupa Password?</a></p>
        </div>
    </div>

</body>
</html>