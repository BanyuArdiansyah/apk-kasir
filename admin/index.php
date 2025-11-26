<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - Mie Gacoan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">🍜 MIE GACOAN</div>
<nav class="admin-nav">
    <ul>
        <li><a href="index.php">📊 Dashboard</a></li>
        <li><a href="laporan.php">📈 Laporan Penjualan</a></li>
        <li><a href="produk.php" class="active">📦 Kelola Produk</a></li>
        <li><a href="pengguna.php">👥 Kelola Pengguna</a></li>
        <li><a href="../index.php">🏪 Kembali ke Toko</a></li>
        <li><a href="../api/logout.php">🚪 Logout</a></li>
    </ul>
</nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>📊 Dashboard Admin</h1>
                <div class="user-info">
                    👋 Selamat datang, <?php echo htmlspecialchars($_SESSION['user']['nama_lengkap']); ?>
                </div>
            </header>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>💰 Penjualan Hari Ini</h3>
                    <div class="stat-value" id="todaySales">
                        <div class="loading" style="height: 50px;"></div>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>📦 Total Produk</h3>
                    <div class="stat-value" id="totalProducts">
                        <div class="loading" style="height: 50px;"></div>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>⚠️ Stok Rendah</h3>
                    <div class="stat-value" id="lowStockProducts">
                        <div class="loading" style="height: 50px;"></div>
                    </div>
                </div>
            </div>
            
            <div class="recent-transactions">
                <h2>📋 Transaksi Terbaru Hari Ini</h2>
                <div id="recentTransactions">
                    <div class="loading" style="height: 200px;"></div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
