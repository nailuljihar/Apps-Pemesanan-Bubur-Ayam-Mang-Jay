<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="auth-header">
            <h2>Lupa Password?</h2>
            <p>Masukkan username lama & password baru kamu.</p>
        </div>

        <form action="" method="POST" class="auth-form">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Username akun kamu">
            </div>
            
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="new_password" required placeholder="Minimal 6 karakter">
            </div>

            <button type="submit" name="reset" class="btn-auth">SIMPAN PASSWORD BARU</button>
        </form>

        <div class="auth-footer">
            <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Login</a>
        </div>

        <?php
        if(isset($_POST['reset'])) {
            require_once 'backend/config/koneksi.php';
            
            $u = $_POST['username'];
            $p = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            
            // Cek user ada gak
            $cek = $koneksi->query("SELECT id_users FROM users WHERE username='$u'");
            if($cek->num_rows > 0){
                $koneksi->query("UPDATE users SET password='$p' WHERE username='$u'");
                echo "<script>alert('Password berhasil diubah! Silakan login.'); window.location='index.php';</script>";
            } else {
                echo "<script>alert('Username tidak ditemukan!');</script>";
            }
        }
        ?>
    </div>

</body>
</html>