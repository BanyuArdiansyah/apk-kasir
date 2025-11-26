<?php
header('Content-Type: application/json');
require_once '../config/database.php';

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';

switch($action) {
    case 'stats':
        // Get today's sales
        $query = "SELECT COALESCE(SUM(total), 0) as today_sales 
                  FROM penjualan 
                  WHERE DATE(created_at) = CURDATE()";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $todaySales = $stmt->fetch(PDO::FETCH_ASSOC)['today_sales'];
        
        // Get total products
        $query = "SELECT COUNT(*) as total_products FROM produk WHERE is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];
        
        // Get low stock products (stock < 10)
        $query = "SELECT COUNT(*) as low_stock_products FROM produk WHERE stok < 10 AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $lowStockProducts = $stmt->fetch(PDO::FETCH_ASSOC)['low_stock_products'];
        
        echo json_encode([
            'success' => true,
            'stats' => [
                'today_sales' => $todaySales,
                'total_products' => $totalProducts,
                'low_stock_products' => $lowStockProducts
            ]
        ]);
        break;
        
    case 'recent_transactions':
        // Get recent transactions (last 10)
        $query = "SELECT p.*, pl.nama as nama_pelanggan, a.nama_lengkap as nama_kasir
                  FROM penjualan p
                  LEFT JOIN pelanggan pl ON p.pelanggan_id = pl.id
                  JOIN admin a ON p.admin_id = a.id
                  WHERE DATE(p.created_at) = CURDATE()
                  ORDER BY p.created_at DESC
                  LIMIT 10";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
