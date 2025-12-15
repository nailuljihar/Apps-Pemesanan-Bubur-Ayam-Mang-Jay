<?php
// Koneksi ke database (Naik 3 folder seperti login)
include 'backend/config/koneksi.php';

$message = "";

if (isset($_POST['register'])) {
    // 1. Ambil input dari form
    $nama     = $koneksi->real_escape_string($_POST['nama']);
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Password masih polos (plain text)
    $role = $koneksi->real_escape_string($_POST['role']);

    // 2. Cek apakah username sudah ada?
    $cek_user = $koneksi->query("SELECT * FROM users WHERE username = '$username'");
    
    if ($cek_user->num_rows > 0) {
        $message = "Username sudah terdaftar! Pilih username lain.";
    } else {
        // --- 3. BAGIAN HASHING (SANGAT PENTING) ---
        // Ini mengubah "rahasia123" menjadi kode acak "$2y$10$..."
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 4. Masukkan ke Database
        // Default role kita set 'user'
        $sql = "INSERT INTO users (nama_lengkap, username, password, role) 
                VALUES ('$nama', '$username', '$hashed_password', 'user')";

        if ($koneksi->query($sql) === TRUE) {
            // Jika berhasil, arahkan ke login
            echo "<script>
                    alert('Pendaftaran Berhasil! Silakan Login.');
                    window.location.href='index.php';
                  </script>";
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Bubur Ayam Bang Jay</title>
    <link rel="stylesheet" href="./frontend/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
     <style>
         body {
             background-color: #f7f0e6;
             display: flex;
             justify-content: center;
             align-items: center;
             height: 100vh;
         }
         .register-container {
             background: white;
             padding: 40px;
             border-radius: 10px;
             box-shadow: 0 4px 10px rgba(0,0,0,0.1);
             width: 100%;
             max-width: 400px;
         }
         .form-group { margin-bottom: 15px; }
         .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
         .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
         .btn-register {
             width: 100%;
             padding: 12px;
             background-color: var(--warna-primer); /* Hijau untuk Register */
             color: white;
             border: none;
             border-radius: 5px;
             cursor: pointer;
             font-size: 16px;
             margin-top: 10px;
         }
         .btn-register:hover { background-color: #2ecc71; }
         .link-login { margin-top: 15px; display: block; text-align: center; font-size: 0.9em; }
         .error-msg { color: red; margin-bottom: 15px; text-align: center; }
         .link-login { text-decoration: none; color: var(--warna-primer);}
         .link-login:hover { color: #2ecc71;}
        
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

    <div class="register-container">
        <h2 style="text-align: center; color: var(--warna-primer); margin-bottom: 20px;">DAFTAR AKUN BARU</h2>

        <?php if($message): ?>
            <div class="error-msg"><?= $message ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Contoh: Budi Santoso" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Buat username unik" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    <i class="fa-solid fa-eye toggle-password" id="eye-icon" onclick="togglePasswordVisibility()"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Daftar Sebagai:</label>
                <select name="role" id="roleSelect" class="form-control" onchange="checkRole()">
                    <option value="user">Pelanggan (User)</option>
                    <option value="admin">Pengelola (Admin)</option>
                </select>
            </div>

            <div class="form-group" id="adminTokenGroup" style="display:none;">
                <label>Kode Keamanan Toko:</label>
                <input type="password" name="admin_token" class="form-control" placeholder="Masukkan kode rahasia owner">
            </div>

            <button type="submit" name="register" class="btn-register">DAFTAR SEKARANG</button>
        </form>

        <a href="index.php" class="link-login">Sudah punya akun? Login disini</a>
    </div>

    <script>
    function checkRole() {
        var role = document.getElementById("roleSelect").value;
        var tokenInput = document.getElementById("adminTokenGroup");
    
        if(role === "admin") {
            tokenInput.style.display = "block";
        } else {
            tokenInput.style.display = "none";
        }
    }

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