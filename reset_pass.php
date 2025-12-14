<?php
// Pastikan path koneksi benar
include 'backend/config/koneksi.php';

$message = "";
$message_type = ""; // 'success' atau 'error'

if (isset($_POST['reset_password'])) {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password_baru = $_POST['new_password'];

    // 1. Cek dulu apakah usernya ada?
    $cek_user = $koneksi->query("SELECT * FROM users WHERE username = '$username'");

    if ($cek_user->num_rows > 0) {
        // 2. Hash Password Baru
        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

        // 3. Update Database
        $update = $koneksi->query("UPDATE users SET password = '$password_hash' WHERE username = '$username'");

        if ($update) {
            $message = "Berhasil! Password untuk user <b>$username</b> telah diganti.";
            $message_type = "success";
        } else {
            $message = "Gagal mengupdate database: " . $koneksi->error;
            $message_type = "error";
        }
    } else {
        $message = "Username <b>$username</b> tidak ditemukan di database.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Tool - Bang Jay</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Override sedikit style body biar fokus di tengah */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--warna-latar-halaman);
        }
        
        .reset-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            border-top: 5px solid var(--warna-primer);
        }

        .tool-badge {
            background-color: #f39c12; /* Warna Oranye buat penanda 'Tools' */
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.7em;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 10px;
        }

        .alert-box {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .password-container { position: relative; }
        .password-container input { padding-right: 40px; }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="reset-card">
        <div style="text-align: center; margin-bottom: 30px;">
            <span class="tool-badge">Developer Tool</span>
            <h2 style="color: var(--warna-primer);">Reset Password</h2>
            <p style="color: var(--warna-sekunder); font-size: 0.9em;">Atur ulang password user tanpa login lama.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert-box <?= $message_type == 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Target Username</label>
                <input type="text" name="username" placeholder="Contoh: admin" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Password Baru</label>
                <div class="password-container">
                    <input type="password" name="new_password" id="new_password" placeholder="Masukkan password baru..." required 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePass()"></i>
                </div>
            </div>

            <button type="submit" name="reset_password" class="tombol-form-cta">
                <i class="fa-solid fa-key"></i> UPDATE PASSWORD
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.9em;">
            <a href="index.php" style="text-decoration: none; color: var(--warna-primer); font-weight: bold;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
            </a>
        </div>
    </div>

    <script>
        function togglePass() {
            var x = document.getElementById("new_password");
            var icon = document.querySelector(".toggle-password");
            if (x.type === "password") {
                x.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                x.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>