<?php
header('Content-Type: application/json');
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db->beginTransaction();
        
        // Generate transaction number
        $no_transaksi = 'TRX-' . date('YmdHis') . '-' . rand(100, 999);
        
        // Get form data
        $nama_pelanggan = $_POST['nama_pelanggan'] ?? 'Pelanggan';
        $total = floatval($_POST['total']);
        $bayar = floatval($_POST['bayar']);
        $kembalian = floatval($_POST['kembalian']);
        $cart = json_decode($_POST['cart'], true);
        
        // Debug: Log received data
        error_log("Checkout Data - Total: $total, Bayar: $bayar, Kembalian: $kembalian");
        error_log("Cart: " . print_r($cart, true));
        
        // Validasi data
        if (empty($cart)) {
            throw new Exception('Keranjang kosong');
        }
        
        if ($total <= 0) {
            throw new Exception('Total tidak valid: ' . $total);
        }
        
        if ($bayar < $total) {
            throw new Exception('Pembayaran kurang dari total');
        }
        
        // Insert customer if name is provided
        $pelanggan_id = null;
        if ($nama_pelanggan && $nama_pelanggan !== 'Pelanggan') {
            $query = "INSERT INTO pelanggan (nama) VALUES (?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$nama_pelanggan]);
            $pelanggan_id = $db->lastInsertId();
        }
        
        // Insert sale - Bulatkan nilai untuk uang
        $query = "INSERT INTO penjualan (no_transaksi, pelanggan_id, admin_id, total, bayar, kembalian) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        $total_rounded = round($total);
        $bayar_rounded = round($bayar);
        $kembalian_rounded = round($kembalian);
        
        $stmt->execute([
            $no_transaksi,
            $pelanggan_id,
            $_SESSION['user']['id'],
            $total_rounded,
            $bayar_rounded,
            $kembalian_rounded
        ]);
        
        $penjualan_id = $db->lastInsertId();
        
        // Insert sale details and update stock
        foreach ($cart as $item) {
            // Validasi stok
            $query = "SELECT stok, nama FROM produk WHERE id = ? AND is_active = 1";
            $stmt = $db->prepare($query);
            $stmt->execute([$item['id']]);
            $produk = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$produk) {
                throw new Exception("Produk tidak ditemukan: " . $item['nama']);
            }
            
            if ($produk['stok'] < $item['quantity']) {
                throw new Exception("Stok tidak cukup untuk: " . $item['nama'] . ". Stok tersedia: " . $produk['stok']);
            }
            
            // Insert detail
            $query = "INSERT INTO detail_penjualan (penjualan_id, produk_id, qty, harga, subtotal) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            $harga_rounded = round($item['harga']);
            $subtotal_rounded = round($item['subtotal']);
            
            $stmt->execute([
                $penjualan_id,
                $item['id'],
                $item['quantity'],
                $harga_rounded,
                $subtotal_rounded
            ]);
            
            // Update stock
            $query = "UPDATE produk SET stok = stok - ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$item['quantity'], $item['id']]);
        }
        
        $db->commit();
        
        // Get transaction data for receipt
        $query = "SELECT p.*, a.nama_lengkap as nama_kasir 
                  FROM penjualan p 
                  JOIN admin a ON p.admin_id = a.id 
                  WHERE p.id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$penjualan_id]);
        $transaksi = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$transaksi) {
            throw new Exception('Gagal mengambil data transaksi');
        }
        
        // Get transaction items
        $query = "SELECT dp.*, pr.nama 
                  FROM detail_penjualan dp 
                  JOIN produk pr ON dp.produk_id = pr.id 
                  WHERE dp.penjualan_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$penjualan_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $transaksi['items'] = $items;
        
        echo json_encode([
            'success' => true,
            'message' => 'Transaksi berhasil',
            'transaksi' => $transaksi
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    // Rollback transaction jika ada error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Checkout Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>