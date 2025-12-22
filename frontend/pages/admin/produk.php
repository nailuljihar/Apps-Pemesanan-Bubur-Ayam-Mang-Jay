<?php
session_start();
// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../user/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Menu - Admin Bang Jay</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Tambahan Khusus Halaman Produk */
        .btn-add { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-bottom: 15px; }
        .btn-edit { background: #f39c12; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; margin-right: 5px; }
        .btn-delete { background: #c0392b; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; }
        
        /* Modal Style */
        .modal { display: none; position: fixed; z-index: 99; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 8px; width: 400px; position: relative; }
        .close-modal { position: absolute; right: 15px; top: 10px; font-size: 20px; cursor: pointer; color: #aaa; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn-submit { width: 100%; padding: 10px; background: #2980b9; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>

    <nav class="admin-sidebar">
        <div class="sidebar-header"><h2>ADMIN PANEL</h2></div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a></li>
            <li><a href="data_pesanan.php"><i class="fa-solid fa-cart-shopping"></i> <span>Pesanan</span></a></li>
            <li><a href="produk.php" class="active"><i class="fa-solid fa-utensils"></i> <span>Menu Produk</span></a></li>
            <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span>Laporan</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../../../index.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Manajemen Menu Bubur</h1>
            <div class="user-info"><i class="fa-solid fa-user-tie"></i> Admin</div>
        </div>

        <div class="table-container">
            <div class="table-header" style="display:flex; justify-content:space-between; align-items:center;">
                <span>Daftar Menu Aktif</span>
                <button onclick="bukaModalTambah()" class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Menu</button>
            </div>
            
            <table id="tabelProduk">
    <thead>
        <tr>
            <th>ID</th>
            <th>Gambar</th>
            <th>Nama Menu</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Aksi</th>
                    </tr>
                    </thead>
                <tbody id="isiTabel"></tbody>
            </table>
        </div>
    </main>

    <div id="modalForm" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="tutupModal()">&times;</span>
            <h2 id="judulModal">Tambah Menu Baru</h2>
            <form id="formProduk">
                <input type="hidden" id="id_produk">
                
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" id="nama_produk" required placeholder="Contoh: Bubur Spesial">
                </div>
                
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" id="harga" required placeholder="Contoh: 15000">
                </div>

                <div class="form-group" id="groupStatus" style="display:none;">
                    <label>Status Aktif</label>
                    <select id="status_aktif">
                        <option value="1">Aktif (Tampil)</option>
                        <option value="0">Tidak Aktif (Sembunyi)</option>
                    </select>
                </div>

                <div class="form-group">
    <label>Nama File Gambar</label>
    <input type="text" id="gambar" placeholder="contoh: bubur-ayam1.jpg">
    <small style="color:#666;">*Pastikan file gambar sudah ada di folder assets/images</small>
</div>

                <button type="submit" class="btn-submit">Simpan Data</button>
            </form>
        </div>
    </div>

    <script>
        const API_URL = "../../../backend/api/produk/";

        // 1. Load Data Saat Halaman Dibuka
        document.addEventListener('DOMContentLoaded', loadProduk);

        function loadProduk() {
            fetch(API_URL + 'read.php')
                .then(response => response.json())
                .then(res => {
                    const tbody = document.getElementById('isiTabel');
                    tbody.innerHTML = '';

                    if(res.status === 'success' && res.data.length > 0) {
                        res.data.forEach(item => {
    // Logic Warna Status
    let badgeStatus = item.status_aktif == 1 
        ? '<span style="background:#d4edda; color:#155724; padding:3px 8px; border-radius:4px; font-weight:bold;">Aktif</span>' 
        : '<span style="background:#f8d7da; color:#721c24; padding:3px 8px; border-radius:4px; font-weight:bold;">Non-Aktif</span>';

    let row = `
        <tr>
            <td>#${item.id_produk}</td>
            <td>
                <img src="../../assets/images/${item.gambar}" width="50" height="50" style="object-fit:cover; border-radius:4px;" alt="img">
            </td>
            <td><strong>${item.nama_produk}</strong></td>
            <td>Rp ${parseInt(item.harga).toLocaleString('id-ID')}</td>
            <td>${badgeStatus}</td>
            <td>
                <button class="btn-edit" onclick="bukaModalEdit(${item.id_produk}, '${item.nama_produk}', ${item.harga}, ${item.status_aktif}, '${item.gambar}')">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button class="btn-delete" onclick="hapusProduk(${item.id_produk}, '${item.nama_produk}')">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.innerHTML += row;
});

                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Belum ada data menu.</td></tr>';
                    }
                })
                .catch(err => console.error("Gagal load data:", err));
        }

        // 2. Logic Modal Tambah/Edit
        const modal = document.getElementById('modalForm');
        const form = document.getElementById('formProduk');
        let mode = 'tambah'; // Bisa 'tambah' atau 'edit'

        function bukaModalTambah() {
            mode = 'tambah';
            document.getElementById('judulModal').innerText = "Tambah Menu Baru";
            document.getElementById('groupStatus').style.display = 'none'; // Status otomatis aktif kalau baru
            form.reset();
            document.getElementById('id_produk').value = '';
            modal.style.display = "block";
        }

        function bukaModalEdit(id, nama, harga) {
            mode = 'edit';
            document.getElementById('judulModal').innerText = "Edit Menu";
            document.getElementById('groupStatus').style.display = 'block'; // Bisa edit status
            
            document.getElementById('id_produk').value = id;
            document.getElementById('nama_produk').value = nama;
            document.getElementById('harga').value = harga;
            document.getElementById('status_aktif').value = 1; // Default
            
            modal.style.display = "block";
        }

        function tutupModal() {
            modal.style.display = "none";
        }

        // 3. Logic Submit Form (Create & Update)
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const dataKirim = {
                nama_produk: document.getElementById('nama_produk').value,
                harga: document.getElementById('harga').value
            };

            let targetUrl = API_URL + 'create.php';
            
            if (mode === 'edit') {
                targetUrl = API_URL + 'update.php';
                dataKirim.id_produk = document.getElementById('id_produk').value;
                dataKirim.status_aktif = document.getElementById('status_aktif').value;
            }

            fetch(targetUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataKirim)
            })
            .then(res => res.json())
            .then(response => {
                if(response.status === 'success') {
                    alert('Berhasil menyimpan data!');
                    tutupModal();
                    loadProduk(); // Refresh tabel otomatis
                } else {
                    alert('Gagal: ' + response.message);
                }
            })
            .catch(err => alert("Terjadi kesalahan sistem."));
        });

        // 4. Logic Hapus (Delete)
        window.hapusProduk = function(id, nama) {
            if(confirm(`Yakin ingin menghapus menu "${nama}"?`)) {
                fetch(API_URL + 'delete.php', {
                    method: 'POST', // Backend delete.php kamu support POST body json
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(res => res.json())
                .then(response => {
                    if(response.status === 'success') {
                        alert('Menu berhasil dihapus.');
                        loadProduk();
                    } else {
                        alert('Gagal menghapus: ' + response.message);
                    }
                })
                .catch(err => alert("Gagal koneksi ke server."));
            }
        }
        
        // Tutup modal kalau klik di luar kotak
        window.onclick = function(event) {
            if (event.target == modal) { tutupModal(); }
        }
    </script>

</body>
</html>