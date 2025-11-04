<?php
// Pastikan lo udah install pake composer
// Path ini relatif dari 'FINTECH/frontend/pages/user/' ke 'FINTECH/vendor/autoload.php'
require_once dirname(__FILE__) . '/../../../vendor/autoload.php'; 

// 1. INI ADALAH VERSI SANDBOX (buat testing)
// Set Server Key Sandbox lo
\Midtrans\Config::$serverKey = 'SB-Mid-server-9nWvHSWmVVj4U90WuCfqJ-67'; // GANTI xxxxxxxxx DENGAN SERVER KEY SANDBOX LO
// Set environment ke Sandbox
\Midtrans\Config::$isProduction = false;
// Aktifin 3DS (WAJIB kalo pake kartu kredit)
\Midtrans\Config::$is3ds = false;

// Ambil data JSON yang dikirim dari frontend (checkout.php)
$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

// Kalo datanya ga ada, matiin
if (!$data || !isset($data->harga) || !isset($data->order_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Data pesanan tidak lengkap.']);
    exit;
}

// Siapin data buat Midtrans
$params = array(
    'transaction_details' => array(
        'order_id' => $data->order_id,          // Order ID unik lo
        'gross_amount' => $data->harga,         // Total harga
    ),
    'item_details' => array(
        array(
            'id' => 'BUBUR-01',                 // ID produk
            'price' => $data->harga,
            'quantity' => 1,
            'name' => $data->nama_produk
        )
    ),
    'customer_details' => array(
        // (Opsional) Lo bisa tambahin data customer dari form
        'first_name' => 'Pelanggan',
        'last_name' => 'Bang Jay',
        'email' => 'pelanggan@example.com',
        'phone' => '081234567890',
    ),
);

try {
    // Dapetin Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    
    // Kirim token balik ke frontend
    header('Content-Type: application/json');
    echo json_encode(['token' => $snapToken]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>