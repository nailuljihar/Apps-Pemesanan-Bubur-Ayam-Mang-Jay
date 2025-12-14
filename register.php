<?php
// Koneksi ke database (Naik 3 folder seperti login)
include 'backend/config/koneksi.php';

$message = "Anda Berhasil Mendaftar";

if (isset($_POST['register'])) {
    // 1. Ambil input dari form
    $nama     = $conn->real_escape_string($_POST['nama']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Password masih polos (plain text)

    // 2. Cek apakah username sudah ada?
    $cek_user = $conn->query("SELECT * FROM users WHERE username = '$username'");
    
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

        if ($conn->query($sql) === TRUE) {
            // Jika berhasil, arahkan ke login
            echo "<script>
                    alert('Pendaftaran Berhasil! Silakan Login.');
                    window.location.href='login.php';
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
    
    // <style>
    //     body {
    //         background-color: #f7f0e6;
    //         display: flex;
    //         justify-content: center;
    //         align-items: center;
    //         height: 100vh;
    //     }
    //     .register-container {
    //         background: white;
    //         padding: 40px;
    //         border-radius: 10px;
    //         box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    //         width: 100%;
    //         max-width: 400px;
    //     }
    //     .form-group { margin-bottom: 15px; }
    //     .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    //     .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
    //     .btn-register {
    //         width: 100%;
    //         padding: 12px;
    //         background-color: var(--warna-primer); /* Hijau untuk Register */
    //         color: white;
    //         border: none;
    //         border-radius: 5px;
    //         cursor: pointer;
    //         font-size: 16px;
    //         margin-top: 10px;
    //     }
    //     .btn-register:hover { background-color: #2ecc71; }
    //     .link-login { margin-top: 15px; display: block; text-align: center; font-size: 0.9em; }
    //     .error-msg { color: red; margin-bottom: 15px; text-align: center; }
    //     .link-login { text-decoration: none; color: var(--warna-primer);}
    //     .link-login:hover { color: #2ecc71;}
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
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>

            <button type="submit" name="register" class="btn-register">DAFTAR SEKARANG</button>
        </form>

        <a href="index.php" class="link-login">Sudah punya akun? Login disini</a>
    </div>

</body>
</html>