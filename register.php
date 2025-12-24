<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun Baru</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

    <div class="auth-card" style="max-width: 500px;"> <div class="auth-header">
            <h2>Buat Akun</h2>
            <p>Gabung jadi pelanggan Mang Jay</p>
        </div>

        <form id="registerForm" class="auth-form">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Contoh: Budi Santoso">
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" id="username" name="username" required placeholder="Buat username unik">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email" name="email" required placeholder="email@contoh.com">
            </div>

            <div class="form-group">
                <label>Nomor HP (WhatsApp)</label>
                <input type="text" id="no_hp" name="no_hp" required placeholder="0812xxxx">
            </div>

            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="2" placeholder="Jalan, Nomor rumah, Patokan..."></textarea>
            </div>

            <div style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Ulangi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <button type="submit" class="btn-auth">DAFTAR AKUN</button>
        </form>

        <div class="auth-footer">
            <p>Sudah punya akun? <a href="index.php">Login disini</a></p>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;

            if (pass !== confirm) {
                alert("Konfirmasi password tidak cocok!");
                return;
            }

            const formData = new FormData(this);

            fetch('backend/api/auth/register_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Registrasi Berhasil! Silakan Login.');
                    window.location.href = 'index.php';
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem.');
            });
        });
    </script>

</body>
</html>