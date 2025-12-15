<?php
// === KONFIGURASI DEBUGGING (TETAP DIPAKAI BUAT JAGA-JAGA) ===
$log_file = 'debug_log.txt'; 
function catatLog($pesan) {
    global $log_file;
    $isi = "[" . date('Y-m-d H:i:s') . "] " . $pesan . "\n";
    file_put_contents($log_file, $isi, FILE_APPEND);
}

// Tahan output browser
ob_start(); 
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // 1. LOAD LIBRARY
    require_once __DIR__ . '/../../../vendor/autoload.php';

    // 2. KONFIGURASI MIDTRANS
    \Midtrans\Config::$serverKey = 'SB-Mid-server-9nWvHSWmVVj4U90WuCfqJ-67'; 
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    // 3. BACA DATA (DENGAN MODE ARRAY)
    $json_mentah = file_get_contents('php://input');
    
    // PERUBAHAN PENTING DI SINI: tambahkan param 'true' agar jadi Array
    $data = json_decode($json_mentah, true); 

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON Invalid");
    }

    // Validasi Data
    if (empty($data['order_id']) || empty($data['gross_amount'])) {
        throw new Exception("Data tidak lengkap (Order ID/Gross Amount hilang)");
    }

    // 4. SUSUN PARAMETER (Explicit Casting biar Aman 100%)
    // Kita susun ulang items-nya biar formatnya Array murni dan tipe datanya pas
    $items_fixed = [];
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $item) {
            $items_fixed[] = [
                'id'       => (string) $item['id'],       // Midtrans minta ID string
                'price'    => (int) $item['price'],       // Midtrans minta Price integer
                'quantity' => (int) $item['quantity'],    // Midtrans minta Qty integer
                'name'     => (string) $item['name']
            ];
        }
    }

    $params = array(
        'transaction_details' => array(
            'order_id' => $data['order_id'],
            'gross_amount' => (int)$data['gross_amount'],
        ),
        'item_details' => $items_fixed, // Pakai array yang sudah kita perbaiki
        'customer_details' => array(
            'first_name' => $data['customer']['first_name'] ?? 'Pelanggan',
            'email' => $data['customer']['email'] ?? 'email@contoh.com',
            'phone' => $data['customer']['phone'] ?? '08123456789',
        ),
    );

    // 5. MINTA SNAP TOKEN
    catatLog("Mengirim request ke Midtrans... (Order: " . $data['order_id'] . ")");
    
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    
    catatLog("BERHASIL! Token: " . $snapToken);

    // 6. KIRIM HASIL
    ob_end_clean();
    echo json_encode(['token' => $snapToken]);

} catch (Exception $e) {
    catatLog("ERROR FATAL: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>