// Admin dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadRecentTransactions();
});

// Load dashboard statistics
function loadDashboardStats() {
    fetch('../api/admin.php?action=stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('todaySales').textContent = 'Rp ' + formatNumber(data.stats.today_sales);
                document.getElementById('totalProducts').textContent = data.stats.total_products;
                document.getElementById('lowStockProducts').textContent = data.stats.low_stock_products;
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}

// Load recent transactions
function loadRecentTransactions() {
    fetch('../api/admin.php?action=recent_transactions')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRecentTransactions(data.transactions);
            }
        })
        .catch(error => {
            console.error('Error loading recent transactions:', error);
        });
}

// Display recent transactions
function displayRecentTransactions(transactions) {
    const container = document.getElementById('recentTransactions');
    
    if (transactions.length === 0) {
        container.innerHTML = '<p>Belum ada transaksi hari ini</p>';
        return;
    }
    
    let html = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Kasir</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    transactions.forEach(transaction => {
        html += `
            <tr>
                <td>${transaction.no_transaksi}</td>
                <td>${transaction.nama_pelanggan || 'Pelanggan'}</td>
                <td>Rp ${formatNumber(transaction.total)}</td>
                <td>${transaction.nama_kasir}</td>
                <td>${new Date(transaction.created_at).toLocaleString()}</td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    container.innerHTML = html;
}

// Format number with thousand separators
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}
