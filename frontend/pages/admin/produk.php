<?php
session_start();
// Pastikan path ke koneksi benar (sesuaikan naik 3 folder)
require_once '../../../backend/config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Modal Tambahan */
        .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 25px; border-radius: 8px; width: 90%; max-width: 500px; position: relative; animation: slideDown 0.3s ease; }
        .close { position: absolute; right: 20px; top: 15px; font-size: 24px; cursor: pointer; color: #666; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .preview-img { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; margin-top: 10px; border: 1px solid #ddd; }
        
        @keyframes slideDown { from {transform: translateY(-50px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
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
            <h1>Daftar Menu Makanan</h1>
            <button class="tombol-biru" onclick="bukaModalTambah()"><i class="fa fa-plus"></i> Tambah Produk</button>
        </div>

        <div id="alert-container"></div>

        <div class="table-container">
            <table id="tabelProduk">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="isiTabel">
                    </tbody>
            </table>
        </div>
    </main>

    <div id="modalForm" class="modal">
        <div class="modal-content">
            <span class="close" onclick="tutupModal()">&times;</span>
            <h3 id="judulModal">Tambah Menu Baru</h3>
            
            <form id="formProduk" enctype="multipart/form-data">
                <input type="hidden" name="id_produk" id="id_produk">
                
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" required placeholder="Contoh: Bubur Spesial">
                </div>
                
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" id="harga" required placeholder="Contoh: 15000">
                </div>

                <div class="form-group" id="groupStatus" style="display:none;">
                    <label>Status</label>
                    <select name="status_aktif" id="status_aktif">
                        <option value="1">Aktif (Tersedia)</option>
                        <option value="0">Non-Aktif (Habis)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="gambar" id="gambar" accept="image/*">
                    <div id="previewContainer" style="display:none;">
                        <p style="font-size:0.8em; margin-top:5px;">Gambar Saat Ini:</p>
                        <img id="edit_preview" src="" class="preview-img">
                    </div>
                </div>

                <button type="submit" class="tombol-biru" style="width:100%;">Simpan Data</button>
            </form>
        </div>
    </div>

    <script>
        const API_URL = "../../../backend/api/produk/";

        // FUNGSI NOTIFIKASI TOAST (PENGGANTI ALERT BIASA)
        function tampilkanNotifikasi(pesan, tipe = 'success') {
            const container = document.getElementById('alert-container');
            const div = document.createElement('div');
            div.className = `alert alert-${tipe}`;
            div.innerHTML = `
                <span>${pesan}</span>
                <span class="alert-close" onclick="this.parentElement.remove()">&times;</span>
            `;
            container.appendChild(div);

            // Hilang otomatis setelah 3 detik
            setTimeout(() => {
                div.style.animation = "fadeOut 0.5s ease-out forwards";
                setTimeout(() => div.remove(), 500);
            }, 3000);
        }

        // 1. Load Data Otomatis
        document.addEventListener('DOMContentLoaded', loadProduk);

        function loadProduk() {
            fetch(API_URL + 'read.php')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('isiTabel');
                tbody.innerHTML = '';
                
                if(data.status === 'success' && data.data.length > 0) {
                    let no = 1;
                    data.data.forEach(item => {
                        let statusBadge = item.status_aktif == 1 
                            ? '<span style="background:#d4edda; color:#155724; padding:3px 8px; border-radius:4px; font-size:0.8em; font-weight:bold;">Aktif</span>'
                            : '<span style="background:#f8d7da; color:#721c24; padding:3px 8px; border-radius:4px; font-size:0.8em; font-weight:bold;">Habis</span>';
                        
                        let row = `<tr>
                            <td>${no++}</td>
                            <td><img src="../../assets/images/${item.gambar}" width="50" height="50" style="object-fit:cover; border-radius:5px;"></td>
                            <td>${item.nama_produk}</td>
                            <td>Rp ${parseInt(item.harga).toLocaleString('id-ID')}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn-edit" onclick="bukaModalEdit(${item.id_produk}, '${item.nama_produk}', ${item.harga}, ${item.status_aktif}, '${item.gambar}')">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="hapusProduk(${item.id_produk}, '${item.nama_produk}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Belum ada data.</td></tr>';
                }
            })
            .catch(err => {
                console.error(err);
                tampilkanNotifikasi('Gagal memuat data produk', 'danger');
            });
        }

        // 2. Logic Modal
        const modal = document.getElementById('modalForm');
        const form = document.getElementById('formProduk');
        let mode = 'tambah'; 

        function bukaModalTambah() {
            mode = 'tambah';
            document.getElementById('judulModal').innerText = "Tambah Menu Baru";
            document.getElementById('groupStatus').style.display = 'none';
            document.getElementById('previewContainer').style.display = 'none';
            form.reset();
            document.getElementById('id_produk').value = '';
            modal.style.display = "block";
        }

        function bukaModalEdit(id, nama, harga, status, gambar) {
            mode = 'edit';
            document.getElementById('judulModal').innerText = "Edit Menu";
            document.getElementById('groupStatus').style.display = 'block';
            document.getElementById('previewContainer').style.display = 'block';
            
            document.getElementById('id_produk').value = id;
            document.getElementById('nama_produk').value = nama;
            document.getElementById('harga').value = harga;
            document.getElementById('status_aktif').value = status;
            document.getElementById('edit_preview').src = "../../assets/images/" + gambar;
            
            modal.style.display = "block";
        }

        function tutupModal() { modal.style.display = "none"; }

        // 3. Logic Submit (Simpan)
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            let targetUrl = API_URL + (mode === 'edit' ? 'update.php' : 'create.php');

            // Tambahkan ID manual untuk update karena input hidden kadang tertinggal
            if (mode === 'edit') {
                formData.append('id_produk', document.getElementById('id_produk').value);
                formData.append('status_aktif', document.getElementById('status_aktif').value);
            }

            fetch(targetUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    tampilkanNotifikasi(res.message, 'success'); // Gunakan Alert Baru
                    tutupModal();
                    loadProduk();
                } else {
                    tampilkanNotifikasi(res.message, 'danger');
                }
            })
            .catch(err => {
                console.error(err);
                tampilkanNotifikasi("Terjadi kesalahan sistem.", 'danger');
            });
        });

        // 4. Logic Hapus
        window.hapusProduk = function(id, nama) {
            if(confirm(`Yakin hapus menu "${nama}"?`)) {
                fetch(API_URL + 'delete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                })
                .then(res => res.json())
                .then(res => {
                    if(res.status === 'success') {
                        tampilkanNotifikasi("Produk berhasil dihapus", 'success');
                        loadProduk();
                    } else {
                        tampilkanNotifikasi(res.message, 'danger');
                    }
                })
                .catch(err => tampilkanNotifikasi("Gagal menghapus data", 'danger'));
            }
        }

        window.onclick = function(event) { if (event.target == modal) tutupModal(); }
    </script>

</body>
</html>