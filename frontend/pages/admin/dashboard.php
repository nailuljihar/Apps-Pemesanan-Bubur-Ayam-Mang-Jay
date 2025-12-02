<?php
// Dashboard Admin - Bubur Ayam Bang Jay

// Koneksi database
include '../../../backend/config/koneksi.php';

// Logika Sidebar Aktif: Cek nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// ==========================
// 1. PROSES ADD / EDIT MENU
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi      = $_POST['aksi'] ?? '';
    $nama_menu = trim($_POST['nama_menu'] ?? '');
    $harga     = isset($_POST['harga']) ? (int) $_POST['harga'] : 0;
    $id_produk = isset($_POST['id_produk']) ? (int) $_POST['id_produk'] : 0;

    // ==========================
    // HANDLE UPLOAD FILE GAMBAR
    // ==========================
    $nama_gambar_baru = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {

        $folder = __DIR__ . '/../../assets/images/menu/'; // lokasi penyimpanan fisik

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if ($ext === '') {
            $ext = 'jpg';
        }

        $nama_gambar_baru = 'menu_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $nama_gambar_baru);
    }

    // ========== AKSI TAMBAH ==========
    if ($aksi === 'tambah' && $nama_menu !== '' && $harga > 0) {

        $stmt = $koneksi->prepare("
            INSERT INTO produk (nama_produk, harga, gambar, status_aktif) 
            VALUES (?, ?, ?, 1)
        ");
        $stmt->bind_param("sis", $nama_menu, $harga, $nama_gambar_baru);
        $stmt->execute();
        $stmt->close();

    // ========== AKSI EDIT ==========
    } elseif ($aksi === 'edit' && $id_produk > 0 && $nama_menu !== '' && $harga > 0) {

        // Jika tidak upload gambar baru → pakai gambar lama
        if ($nama_gambar_baru === null) {
            $getOld = $koneksi->prepare("SELECT gambar FROM produk WHERE id_produk = ?");
            $getOld->bind_param("i", $id_produk);
            $getOld->execute();
            $resultOld = $getOld->get_result()->fetch_assoc();
            $nama_gambar_baru = $resultOld['gambar'] ?? null;
            $getOld->close();
        }

        $stmt = $koneksi->prepare("
            UPDATE produk 
            SET nama_produk = ?, harga = ?, gambar = ?
            WHERE id_produk = ?
        ");
        $stmt->bind_param("sisi", $nama_menu, $harga, $nama_gambar_baru, $id_produk);
        $stmt->execute();
        $stmt->close();
    }

    // Setelah proses, reload halaman supaya data terbaru muncul
    header("Location: dashboard.php");
    exit;
}

// ==========================
// 2. PROSES HAPUS MENU
// ==========================
if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];

    if ($id_hapus > 0) {
        // Soft delete: set status_aktif = 0
        $stmt = $koneksi->prepare("UPDATE produk SET status_aktif = 0 WHERE id_produk = ?");
        $stmt->bind_param("i", $id_hapus);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: dashboard.php");
    exit;
}

// ==========================
// 3. AMBIL DATA MENU
// ==========================
$produk = [];
$sql    = "SELECT id_produk, nama_produk, harga, gambar 
           FROM produk 
           WHERE status_aktif = 1 
           ORDER BY id_produk ASC";
$result = $koneksi->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $produk[] = $row;
    }
    $result->free();
}

// Koneksi boleh ditutup di sini
$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Menu</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/admin.css">

    <!-- Tambahan styling ringan khusus untuk tampilan daftar & popup -->
    <style>
        .content-area {
            flex: 1;
            padding: 25px 30px;
            background-color: var(--warna-latar-konten, #ffffff);
        }

        .menu-list-section {
            width: 100%;
        }

        .menu-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .content-header-menu {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .btn-tambah-menu {
            padding: 8px 18px;
            border-radius: 20px;
            border: none;
            background-color: #009688;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-tambah-menu:hover {
            opacity: 0.9;
        }

        .separator {
            height: 3px;
            border: 0;
            background-color: #009688;
            margin: 0 0 18px 0;
        }

        .menu-item-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 18px;
            font-size: 14px;
        }

        .menu-item-card:nth-child(odd) {
            background-color: #fafafa;
        }

        .item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            background-color: #e0e0e0;
        }

        .item-name {
            flex: 1;
        }

        .item-price {
            margin-right: 20px;
            font-weight: 500;
        }

        .item-actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit,
        .btn-hapus {
            padding: 6px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-edit {
            background-color: #0056b3;
            color: #fff;
        }

        .btn-hapus {
            background-color: #c82333;
            color: #fff;
            text-decoration: none;
        }

        .empty-text {
            font-size: 14px;
            color: #777;
            margin-top: 10px;
        }

        /* ======== Popup Tambah/Edit Menu ======== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: none;              /* ditampilkan via JS */
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .modal-header h3 {
            margin: 0;
            color: #009688;
            font-size: 20px;
        }

        .modal-close {
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            line-height: 1;
        }

        .menu-form label {
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .menu-form input {
            width: 100%;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 13px;
        }

        .form-actions-bottom {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-batal {
            background: #b0b0b0;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-tambah {
            background: #009688;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- HEADER -->
        <header class="header-dashboard">
            <div class="logo-admin">BUBUR AYAM BANDUNG BANG JAY</div>
            <div class="status-admin">
                <span class="notification">WELCOME</span>
                <span class="admin-name">ADMIN</span>
            </div>
        </header>

        <div class="main-admin-content">
            <!-- SIDEBAR -->
            <aside class="sidebar-nav">
                <h4 class="sidebar-title">DASHBOARD</h4>
                <ul>
                    <li><a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">Makanan dan Minuman</a></li>
                    <li><a href="payment.php" class="<?= $current_page === 'payment.php' ? 'active' : '' ?>">Riwayat Pembayaran</a></li>
                    <li><a href="reports.php" class="<?= $current_page === 'reports.php' ? 'active' : '' ?>">Data Pesanan</a></li>
                    <li><a href="#">Kritik dan Saran</a></li>
                </ul>
            </aside>

            <!-- KONTEN UTAMA -->
            <section class="content-area">
                <div class="menu-list-section">
                    <div class="menu-header-row">
                        <h4 class="content-header-menu">Daftar Menu</h4>
                        <button type="button" id="btnTambahMenu" class="btn-tambah-menu">TAMBAH MENU</button>
                    </div>

                    <hr class="separator">

                    <?php if (count($produk) > 0): ?>
                        <?php foreach ($produk as $item): ?>
                            <div class="menu-item-card">
                                <div class="item-left">
                                    <?php if (!empty($item['gambar'])): ?>
                                        <img src="../../assets/images/menu/<?= htmlspecialchars($item['gambar'], ENT_QUOTES, 'UTF-8'); ?>"
                                             alt="menu"
                                             class="menu-thumb">
                                    <?php endif; ?>

                                    <span class="item-name">
                                        <?= htmlspecialchars($item['nama_produk'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>

                                <span class="item-price">
                                    Rp <?= number_format((int) $item['harga'], 0, ',', '.'); ?>
                                </span>

                                <div class="item-actions">
                                    <button
                                        type="button"
                                        class="btn-edit"
                                        data-id="<?= (int) $item['id_produk']; ?>"
                                        data-nama="<?= htmlspecialchars($item['nama_produk'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-harga="<?= (int) $item['harga']; ?>"
                                    >
                                        EDIT
                                    </button>
                                    <a
                                        href="dashboard.php?hapus=<?= (int) $item['id_produk']; ?>"
                                        class="btn-hapus"
                                        onclick="return confirm('Yakin ingin menghapus menu ini?');"
                                    >
                                        HAPUS
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-text">Belum ada menu yang aktif.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <!-- POPUP TAMBAH / EDIT MENU -->
    <div id="modalMenu" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Menu</h3>
                <button type="button" id="modalClose" class="modal-close">&times;</button>
            </div>
            <form method="POST" class="menu-form" id="formMenu" enctype="multipart/form-data">
                <input type="hidden" name="aksi" id="aksi" value="tambah">
                <input type="hidden" name="id_produk" id="id_produk">

                <label for="nama_menu">Nama Menu</label>
                <input type="text" name="nama_menu" id="nama_menu" required>

                <label for="deskripsi_menu">Detail Menu</label>
                <input type="text" name="deskripsi_menu" id="deskripsi_menu">

                <label for="kategori">Kategori</label>
                <input type="text" name="kategori" id="kategori">

                <label for="harga">Harga</label>
                <input type="number" name="harga" id="harga" min="0" required>

                <label for="gambar">Gambar Menu</label>
                <input type="file" name="gambar" id="gambar" accept="image/*">

                <div class="form-actions-bottom">
                    <button type="button" class="btn-batal">BATAL</button>
                    <button type="submit" class="btn-tambah">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT UNTUK POPUP & EDIT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('modalMenu');
            const btnTambah = document.getElementById('btnTambahMenu');
            const btnClose = document.getElementById('modalClose');
            const btnBatal = document.querySelector('.btn-batal');
            const aksiInput = document.getElementById('aksi');
            const idInput = document.getElementById('id_produk');
            const namaInput = document.getElementById('nama_menu');
            const hargaInput = document.getElementById('harga');
            const modalTitle = document.getElementById('modalTitle');
            const formMenu = document.getElementById('formMenu');

            function openModal() {
                modal.style.display = 'flex';
            }

            function closeModal() {
                modal.style.display = 'none';
                formMenu.reset();
            }

            // Mode TAMBAH MENU
            btnTambah.addEventListener('click', function () {
                modalTitle.textContent = 'Tambah Menu';
                aksiInput.value = 'tambah';
                idInput.value = '';
                formMenu.reset();
                openModal();
            });

            // Mode EDIT MENU
            document.querySelectorAll('.btn-edit').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const nama = this.dataset.nama;
                    const harga = this.dataset.harga;

                    modalTitle.textContent = 'Edit Menu';
                    aksiInput.value = 'edit';
                    idInput.value = id;
                    namaInput.value = nama;
                    hargaInput.value = harga;

                    // input file tidak bisa di-set via JS, biarkan kosong
                    openModal();
                });
            });

            // Tombol close & batal
            btnClose.addEventListener('click', function () {
                closeModal();
            });
            btnBatal.addEventListener('click', function () {
                closeModal();
            });

            // Klik di luar modal untuk menutup
            window.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>
