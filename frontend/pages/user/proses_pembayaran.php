<?php
session_start();
require_once '../../../backend/config/koneksi.php';
require_once '../../../vendor/autoload.php';

// 1. Ambil Data JSON dari Checkout.php
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['error' => 'Data tidak valid']);
    exit;
}

// 2. Setup Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-9nWvHSWmVVj4U90WuCfqJ-67'; // Sesuaikan Key Anda
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// 3. Persiapan Data
$id_user_login = $_SESSION['id_users'] ?? 0;
// Order ID harus disimpan di kolom khusus (varchar), bukan id_transaksi (int)
// Format: TRX-Jam-IDUser
$custom_order_id = "TRX-" . time() . "-" . $id_user_login; 

$params = [
    'transaction_details' => [
        'order_id' => $custom_order_id,
        'gross_amount' => (int) $data['grand_total'],
    ],
    'customer_details' => [
        'first_name' => $data['nama_penerima'],
        'phone' => $data['no_hp'],
        'billing_address' => ['address' => $data['alamat']],
        'shipping_address' => ['address' => $data['alamat']]
    ]
];

try {
    // 4. Get Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    // 5. Simpan ke Database
    // Pastikan Anda sudah menjalankan SQL ALTER TABLE untuk menambah kolom 'order_id' (VARCHAR)
    $stmt = $koneksi->prepare("INSERT INTO transaksi 
        (id_users, order_id, tanggal, jenis_transaksi, total_pendapatan, ongkir, nama_penerima, alamat_pengiriman, no_hp_penerima, status, metode_pembayaran, catatan, snap_token) 
        VALUES (?, ?, NOW(), 'online', ?, ?, ?, ?, ?, 'Pending', 'qris', ?, ?)");

    // Urutan tipe data: i (int), s (string), i, i, s, s, s, s, s
    $stmt->bind_param("isiisssss", 
        $id_user_login,
        $custom_order_id,
        $data['grand_total'], // Total + Ongkir
        $data['ongkir'],
        $data['nama_penerima'],
        $data['alamat'],
        $data['no_hp'],
        $data['catatan'],
        $snapToken
    );

    if ($stmt->execute()) {
        $id_transaksi_db = $koneksi->insert_id; // ID Auto Increment asli

        // 6. Simpan Detail Item
        $stmt_detail = $koneksi->prepare("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal, tanggal) VALUES (?, ?, ?, ?, NOW())");
        
        foreach ($_SESSION['keranjang'] as $id_produk => $item) {
            $subtotal = $item['harga'] * $item['jumlah'];
            $stmt_detail->bind_param("iiii", $id_transaksi_db, $id_produk, $item['jumlah'], $subtotal);
            $stmt_detail->execute();
        }

        // Hapus Keranjang
        unset($_SESSION['keranjang']);

        // Kirim Token ke Frontend (Checkout.php)
        echo json_encode(['token' => $snapToken]);
    } else {
        echo json_encode(['error' => 'Gagal simpan database: ' . $stmt->error]);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>