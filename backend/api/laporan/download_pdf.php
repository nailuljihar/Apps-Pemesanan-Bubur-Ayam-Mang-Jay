<?php
require_once '../../../vendor/autoload.php'; // Load Composer Autoload
require_once '../../config/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Ambil Filter (Sama persis dengan reports.php)
$filter_periode = isset($_GET['periode']) ? $_GET['periode'] : 'harian';
$filter_tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'semua';
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// 2. Query Data (Copy logic dari reports.php)
$sql = "SELECT t.*, u.nama_lengkap 
        FROM transaksi t 
        LEFT JOIN users u ON t.id_users = u.id_users 
        WHERE 1=1";

// Filter Tipe
if($filter_tipe != 'semua'){
    $sql .= " AND t.jenis_transaksi = '$filter_tipe'";
}

// Filter Waktu
if($filter_periode == 'harian'){
    $sql .= " AND DATE(t.tanggal) = '$tgl_awal'";
    $judul_periode = "Harian: " . date('d-m-Y', strtotime($tgl_awal));
} elseif($filter_periode == 'mingguan'){
    $sql .= " AND DATE(t.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
    $judul_periode = "Periode: " . date('d-m-Y', strtotime($tgl_awal)) . " s/d " . date('d-m-Y', strtotime($tgl_akhir));
} elseif($filter_periode == 'bulanan'){
    $bulan = date('m', strtotime($tgl_awal));
    $tahun = date('Y', strtotime($tgl_awal));
    $sql .= " AND MONTH(t.tanggal) = '$bulan' AND YEAR(t.tanggal) = '$tahun'";
    $judul_periode = "Bulan: " . date('F Y', strtotime($tgl_awal));
} elseif($filter_periode == 'tahunan'){
    $tahun = date('Y', strtotime($tgl_awal));
    $sql .= " AND YEAR(t.tanggal) = '$tahun'";
    $judul_periode = "Tahun: " . $tahun;
}

$sql .= " AND t.status != 'Batal' ORDER BY t.tanggal DESC"; // Hanya yang valid
$result = $koneksi->query($sql);

// 3. Mulai Buffering HTML untuk PDF
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        
        .text-right { text-align: right; }
        .footer-total { background-color: #f4f4f4; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>BUBUR AYAM MANG JAY</h2>
        <p>Laporan Penjualan</p>
        <p><small><?= $judul_periode ?></small></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Pelanggan</th>
                <th>Pendapatan</th>
                <th>Ongkir</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $grand_total = 0;
            if($result->num_rows > 0):
                while($row = $result->fetch_assoc()): 
                    $total_trx = $row['total_pendapatan'] + $row['ongkir'];
                    $grand_total += $total_trx;
                    
                    // Nama Pelanggan
                    $nama = !empty($row['nama_lengkap']) ? $row['nama_lengkap'] : ($row['nama_penerima'] ?? 'Guest');
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                <td><?= ucfirst($row['jenis_transaksi']) ?></td>
                <td><?= htmlspecialchars($nama) ?></td>
                <td class="text-right">Rp <?= number_format($row['total_pendapatan'],0,',','.') ?></td>
                <td class="text-right">Rp <?= number_format($row['ongkir'],0,',','.') ?></td>
                <td class="text-right">Rp <?= number_format($total_trx,0,',','.') ?></td>
            </tr>
            <?php 
                endwhile; 
            else:
            ?>
            <tr>
                <td colspan="7" style="text-align:center;">Tidak ada data penjualan</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="6" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp <?= number_format($grand_total,0,',','.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Bangkalan, <?= date('d F Y') ?></p>
        <br><br><br>
        <p>( Admin Mang Jay )</p>
    </div>

</body>
</html>

<?php
$html = ob_get_clean(); // Ambil HTML dari buffer

// 4. Proses Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true); // Agar bisa load gambar jika ada link
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // Bisa ganti 'landscape' jika kolom banyak
$dompdf->render();

// Output file PDF ke Browser (Download)
$dompdf->stream("Laporan_Penjualan_" . date('Ymd_His') . ".pdf", array("Attachment" => false)); // Attachment: false = Preview, true = Download
?>