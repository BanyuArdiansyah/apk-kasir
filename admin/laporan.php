<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Mie Gacoan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/chart.css">
    <!-- REMOVED Chart.js - Using Pure CSS Charts -->
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">🍜 MIE GACOAN</div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="index.php">📊 Dashboard</a></li>
                    <li><a href="laporan.php" class="active">📈 Laporan Penjualan</a></li>
                    <li><a href="produk.php">📦 Kelola Produk</a></li>
                    <li><a href="pengguna.php">👥 Kelola Pengguna</a></li>
                    <li><a href="../index.php">🏪 Kembali ke Toko</a></li>
                    <li><a href="../api/logout.php">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <h1>📈 Laporan Penjualan</h1>
                <div class="user-info">
                    👋 <?php echo htmlspecialchars($_SESSION['user']['nama_lengkap']); ?>
                </div>
            </header>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="periode">📅 Periode</label>
                            <select id="periode" name="periode">
                                <option value="hari_ini">Hari Ini</option>
                                <option value="kemarin">Kemarin</option>
                                <option value="7_hari">7 Hari Terakhir</option>
                                <option value="30_hari">30 Hari Terakhir</option>
                                <option value="bulan_ini">Bulan Ini</option>
                                <option value="bulan_lalu">Bulan Lalu</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="form-group" id="customDateRange" style="display: none;">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" id="tanggal_akhir" name="tanggal_akhir">
                        </div>
                        <div class="form-group">
                            <button type="button" id="filterBtn" class="btn btn-primary">
                                🔍 Terapkan Filter
                            </button>
                            <button type="button" id="exportBtn" class="btn btn-success">
                                📄 Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Summary Cards -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>💰 Total Penjualan</h3>
                    <div class="stat-value" id="totalSales">Rp 0</div>
                    <div class="stat-change" id="salesChange">
                        <span>0%</span> vs periode sebelumnya
                    </div>
                </div>
                <div class="stat-card">
                    <h3>🛒 Total Transaksi</h3>
                    <div class="stat-value" id="totalTransactions">0</div>
                    <div class="stat-change" id="transactionsChange">
                        <span>0%</span> vs periode sebelumnya
                    </div>
                </div>
                <div class="stat-card">
                    <h3>📊 Rata-rata / Transaksi</h3>
                    <div class="stat-value" id="averageTransaction">Rp 0</div>
                    <div class="stat-change" id="averageChange">
                        <span>0%</span> vs periode sebelumnya
                    </div>
                </div>
                <div class="stat-card">
                    <h3>🏆 Produk Terlaris</h3>
                    <div class="stat-value" id="bestSeller">-</div>
                    <div class="stat-change" id="bestSellerQty">0 terjual</div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-container">
                <div class="chart-row">
                    <div class="chart-card">
                        <h3>📈 Penjualan Harian</h3>
                        <div style="position: relative; height: 300px;">
                            <div id="dailySalesChart"></div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>🎯 Penjualan per Kategori</h3>
                        <div style="position: relative; height: 300px;">
                            <div id="categorySalesChart"></div>
                        </div>
                    </div>
                </div>
                <div class="chart-row">
                    <div class="chart-card">
                        <h3>📊 Trend Penjualan Bulanan</h3>
                        <div style="position: relative; height: 300px;">
                            <div id="monthlyTrendChart"></div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>👨‍💼 Performa Kasir</h3>
                        <div style="position: relative; height: 300px;">
                            <div id="cashierPerformanceChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Sales Table -->
            <div class="recent-transactions">
                <h2>📋 Detail Transaksi</h2>
                <div class="table-controls">
                    <div class="table-info">
                        📦 Menampilkan <strong><span id="tableCount">0</span></strong> transaksi
                    </div>
                    <div class="table-pagination">
                        <button id="prevPage" class="btn btn-secondary">← Sebelumnya</button>
                        <span id="pageInfo">Halaman 1</span>
                        <button id="nextPage" class="btn btn-secondary">Selanjutnya →</button>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>🔖 No. Transaksi</th>
                            <th>📅 Tanggal</th>
                            <th>👤 Pelanggan</th>
                            <th>📦 Items</th>
                            <th>💰 Total</th>
                            <th>👨‍💼 Kasir</th>
                            <th>⚡ Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTable">
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem;">
                                <div class="loading" style="height: 60px; border-radius: 12px;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Transaction Detail Modal -->
    <div id="transactionModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🧾 Detail Transaksi</h3>
                <button class="close-modal">✕</button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <!-- Transaction details will be loaded here -->
            </div>
        </div>
    </div>

    <script src="../assets/js/laporan.js"></script>
</body>
</html>