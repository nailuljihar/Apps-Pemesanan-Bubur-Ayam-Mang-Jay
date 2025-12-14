<?php
session_start();
// Pastikan path ini benar sesuai struktur folder
include 'backend/config/koneksi.php';

$error_msg = "";

if (isset($_POST['login'])) {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Cek user di database
    $sql = "SELECT * FROM users WHERE username = '$username'"; 
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verifikasi Password Hash
        if (password_verify($password, $row['password'])) { 
            // Set Session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; 
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];

            // Redirect berdasarkan role
            if ($row['role'] == 'admin') {
                header("Location: frontend/pages/admin/dashboard.php");
            } else {
                header("Location: frontend/pages/user/index.php");
            }
            exit();
        } else {
            $error_msg = "Password yang Anda masukkan salah.";
        }
    } else {
        $error_msg = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bubur Ayam Bang Jay</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px; /* Kasih jarak di kanan biar teks gak nabrak ikon */
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%; /* Posisi tengah vertikal */
            transform: translateY(-50%); /* Koreksi posisi tengah */
            cursor: pointer;
            color: var(--warna-teks-nav); /* Warna abu-abu */
            font-size: 1.1em;
            z-index: 10;
        }
        .toggle-password:hover {
            color: var(--warna-primer); /* Berubah warna saat di-hover */
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <header class="header-toko">
            <div class="container-header">
                <div class="logo-text">
                    <h3>BUBUR AYAM BANDUNG</h3>
                    <h1>BANG JAY</h1>
                </div>
                <div class="welcome-section">
                    <a href="index.php" style="text-decoration: none; color: inherit;">KEMBALI KE HOME</a>
                </div>
            </div>
        </header>

        <main class="main-content-form">
            <div class="breadcrumb">
                <a href="index.php">Home</a> / <span>Login</span>
            </div>

            <div class="form-container">
                <div class="form-illustration">
                    <img src="../../assets/images/login-illustration.jpg" alt="Login Illustration" onerror="this.style.display='none';">
                </div>

                <div class="form-content">
                    <h2 class="form-title">LOGIN</h2>
                    <p class="form-subtitle">Masuk untuk menikmati Bubur Ayam terbaik.</p>
                    
                    <?php if($error_msg): ?>
                        <div style="color: var(--warna-harga-diskon); margin-bottom: 15px; font-size: 0.9em;">
                            <?= $error_msg ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan username Anda" required>

                        <label for="password">Password</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                            <i class="fa-solid fa-eye toggle-password" id="eye-icon" onclick="togglePasswordVisibility()"></i>
                        </div>
                        
                        <a href="reset_pass.php" class="forgot-password">Lupa Password?</a>

                        <button type="submit" name="login" class="tombol-form-cta">MASUK SEKARANG</button>
                    </form>

                    <div class="switch-page">
                        Belum punya akun? <a href="register.php">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                // Ubah jadi text (kelihatan)
                passwordInput.type = 'text';
                // Ganti ikon mata jadi "mata dicoret" (slash)
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                // Balikin jadi password (titik-titik)
                passwordInput.type = 'password';
                // Balikin ikon mata jadi normal
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>