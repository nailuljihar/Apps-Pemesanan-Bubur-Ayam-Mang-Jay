<?php
session_start();
require_once '../../../backend/config/koneksi.php';

// Cek Login
if (!isset($_SESSION['id_users'])) {
    header("Location: ../../../index.php");
    exit;
}

$id_user = $_SESSION['id_users'];
$msg = "";

// 1. Logic Update Data
if (isset($_POST['update_profile'])) {
    $nama = $koneksi->real_escape_string($_POST['nama_lengkap']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $hp = $koneksi->real_escape_string($_POST['no_hp']);
    $alamat = $koneksi->real_escape_string($_POST['alamat']);
    $password_baru = $_POST['password'];

    // Update query dasar
    $sql = "UPDATE users SET nama_lengkap='$nama', email='$email', no_hp='$hp', alamat='$alamat' WHERE id_users='$id_user'";

    // Cek kalau user mau ganti password juga
    if (!empty($password_baru)) {
        $hash_pass = password_hash($password_baru, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama_lengkap='$nama', email='$email', no_hp='$hp', alamat='$alamat', password='$hash_pass' WHERE id_users='$id_user'";
    }

    if ($koneksi->query($sql)) {
        // Update Session biar realtime
        $_SESSION['nama_lengkap'] = $nama;
        $_SESSION['email'] = $email;
        $_SESSION['no_hp'] = $hp;
        $msg = "<div style='color: green; margin-bottom: 10px;'>Profil berhasil diupdate!</div>";
    } else {
        $msg = "<div style='color: red; margin-bottom: 10px;'>Gagal update: " . $koneksi->error . "</div>";
    }
}

// 2. Ambil Data Terbaru User
$query = "SELECT * FROM users WHERE id_users = '$id_user'";
$result = $koneksi->query($query);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Profile - Bang Jay</title>
    <link rel="stylesheet" href="../../css/styles.css"> <style>
        .profile-container { max-width: 600px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-save { background: var(--warna-primer, #009688); color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; width: 100%; }
        .btn-back { display: block; text-align: center; margin-top: 10px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="profile-container">
    <h2 style="text-align: center;">Edit Profil Saya</h2>
    <?= $msg ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?= $data['nama_lengkap'] ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $data['email'] ?>">
        </div>
        <div class="form-group">
            <label>No. HP (WhatsApp)</label>
            <input type="text" name="no_hp" value="<?= $data['no_hp'] ?>">
        </div>
        <div class="form-group">
            <label>Alamat Lengkap</label>
            <textarea name="alamat" rows="3"><?= $data['alamat'] ?></textarea>
        </div>
        <div class="form-group">
            <label>Password Baru <small style="color: red;">(Kosongkan jika tidak ingin mengganti)</small></label>
            <input type="password" name="password" placeholder="Masukan password baru...">
        </div>
        
        <button type="submit" name="update_profile" class="btn-save">Simpan Perubahan</button>
        <a href="index.php" class="btn-back">Kembali ke Menu</a>
    </form>
</div>

</body>
</html>