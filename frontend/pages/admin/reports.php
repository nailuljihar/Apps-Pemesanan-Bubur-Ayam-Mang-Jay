<?php 
require_once '../../../backend/config/koneksi.php';

// --- LOGIKA FILTER ---
$filter_periode = isset($_GET['periode']) ? $_GET['periode'] : 'harian';
$filter_tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'semua';
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Base Query
$sql = "SELECT t.*, u.nama_lengkap 
        FROM transaksi t 
        LEFT JOIN users u ON t.id_users = u.id_users 
        WHERE 1=1";

// Filter Tipe (Online / Offline)
if($filter_tipe != 'semua'){
    $sql .= " AND t.jenis_transaksi = '$filter_tipe'";
}

// Filter Waktu
if($filter_periode == 'harian'){
    $sql .= " AND DATE(t.tanggal) = '$tgl_awal'";
} elseif($filter_periode == 'mingguan'){
    // Logika mingguan sederhana: range tanggal
    $sql .= " AND DATE(t.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
} elseif($filter_periode == 'bulanan'){
    $bulan = date('m', strtotime($tgl_awal));
    $tahun = date('Y', strtotime($tgl_awal));
    $sql .= " AND MONTH(t.tanggal) = '$bulan' AND YEAR(t.tanggal) = '$tahun'";
} elseif($filter_periode == 'tahunan'){
    $tahun = date('Y', strtotime($tgl_awal));
    $sql .= " AND YEAR(t.tanggal) = '$tahun'";
}

$sql .= " ORDER BY t.tanggal DESC";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Admin Lengkap</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="admin-sidebar">
        <div class="sidebar-header"><h2>ADMIN</h2></div>
        <ul class="sidebar-menu">
            <li><a href="reports.php" class="active">Laporan</a></li>
            <li><a href="data_pesanan.php">Data Pesanan (CRUD Offline)</a></li>
        </ul>
    </nav>

    <main class="admin-content">
        <div class="content-header">
            <h1>Laporan Penjualan</h1>
        </div>

        <div class="dashboard-wrapper">
    
    <div style="background:#fff; padding:20px; border-radius:8px; margin-bottom:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
        <form id="formFilter" action="" method="GET" style="display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end;">
            
            <div>
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Periode</label>
                <select name="periode" class="input-field" id="selectPeriode" onchange="cekPeriode()" style="padding:8px; border-radius:4px; border:1px solid #ddd;">
                    <option value="harian" <?= $filter_periode=='harian'?'selected':'' ?>>Harian</option>
                    <option value="mingguan" <?= $filter_periode=='mingguan'?'selected':'' ?>>Mingguan (Range)</option>
                    <option value="bulanan" <?= $filter_periode=='bulanan'?'selected':'' ?>>Bulanan</option>
                    <option value="tahunan" <?= $filter_periode=='tahunan'?'selected':'' ?>>Tahunan</option>
                </select>
            </div>

            <div>
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Tipe Pesanan</label>
                <select name="tipe" class="input-field" style="padding:8px; border-radius:4px; border:1px solid #ddd;">
                    <option value="semua" <?= $filter_tipe=='semua'?'selected':'' ?>>Semua Tipe</option>
                    <option value="online" <?= $filter_tipe=='online'?'selected':'' ?>>Online</option>
                    <option value="offline" <?= $filter_tipe=='offline'?'selected':'' ?>>Offline</option>
                </select>
            </div>

            <div>
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Tanggal Awal</label>
                <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="input-field" style="padding:8px; border-radius:4px; border:1px solid #ddd;">
            </div>

            <div id="boxAkhir" style="display: <?= $filter_periode == 'mingguan' ? 'block' : 'none' ?>;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="input-field" style="padding:8px; border-radius:4px; border:1px solid #ddd;">
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="tombol-biru" style="padding:8px 20px;">
                    <i class="fa fa-filter"></i> Tampilkan
                </button>

                <button type="button" onclick="downloadPDF()" class="tombol-biru" style="background:#e74c3c; padding:8px 20px;">
                    <i class="fa fa-file-pdf"></i> Download PDF
                </button>
            </div>
        </form>
    </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Pelanggan/Penerima</th>
                            <th>Status</th>
                            <th>Pendapatan</th>
                            <th>Ongkir</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_total_pendapatan = 0;
                        while($row = $result->fetch_assoc()): 
                            $total = $row['total_pendapatan'] + $row['ongkir'];
                            $grand_total_pendapatan += $total;
                        ?>
                        <tr>
                            <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <span style="padding:3px 8px; border-radius:5px; background: <?= $row['jenis_transaksi'] == 'online' ? '#d1ecf1' : '#fff3cd' ?>;">
                                    <?= strtoupper($row['jenis_transaksi']) ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    if($row['jenis_transaksi'] == 'online') {
                                        echo "<strong>" . $row['nama_penerima'] . "</strong><br><small>" . $row['alamat_pengiriman'] . "</small>";
                                    } else {
                                        echo "Offline Customer";
                                    }
                                ?>
                            </td>
                            <td><?= $row['status'] ?></td>
                            <td>Rp <?= number_format($row['total_pendapatan'],0,',','.') ?></td>
                            <td>Rp <?= number_format($row['ongkir'],0,',','.') ?></td>
                            <td style="font-weight:bold;">Rp <?= number_format($total,0,',','.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="text-align:right; font-weight:bold;">TOTAL PENDAPATAN:</td>
                            <td style="font-weight:bold; background:#e8f5e9;">Rp <?= number_format($grand_total_pendapatan,0,',','.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>

    <script>
        function cekPeriode() {
            var val = document.getElementById('selectPeriode').value;
            var box = document.getElementById('boxAkhir');
            if(val === 'mingguan') {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }

        function downloadPDF() {
            // Ambil semua nilai dari form
            var periode = document.querySelector('select[name="periode"]').value;
            var tipe = document.querySelector('select[name="tipe"]').value;
            var tgl_awal = document.querySelector('input[name="tgl_awal"]').value;
            var tgl_akhir = document.querySelector('input[name="tgl_akhir"]').value;

            // Redirect ke file PDF generator dengan membawa parameter
            var url = `../../../backend/api/laporan/download_pdf.php?periode=${periode}&tipe=${tipe}&tgl_awal=${tgl_awal}&tgl_akhir=${tgl_akhir}`;
            
            // Buka di tab baru
            window.open(url, '_blank');
        }
    </script>
</body>
</html>