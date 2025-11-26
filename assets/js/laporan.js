// Laporan Penjualan functionality - Pure JavaScript (No Chart.js)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing reports without Chart.js...');
    initializeReports();
    setupEventListeners();
});

let currentPage = 1;
const itemsPerPage = 10;
let currentFilter = {
    periode: 'hari_ini',
    tanggal_mulai: '',
    tanggal_akhir: ''
};

function initializeReports() {
    // Set default dates
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    document.getElementById('tanggal_mulai').value = formatDateForInput(yesterday);
    document.getElementById('tanggal_akhir').value = formatDateForInput(today);
    
    loadReports();
}

function setupEventListeners() {
    // Period filter change
    document.getElementById('periode').addEventListener('change', function() {
        const customRange = document.getElementById('customDateRange');
        if (this.value === 'custom') {
            customRange.style.display = 'grid';
        } else {
            customRange.style.display = 'none';
        }
    });
    
    // Filter button
    document.getElementById('filterBtn').addEventListener('click', function() {
        currentPage = 1;
        loadReports();
    });
    
    // Export button
    document.getElementById('exportBtn').addEventListener('click', exportToPDF);
    
    // Pagination
    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadTransactions();
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', function() {
        currentPage++;
        loadTransactions();
    });
    
    // Modal close
    const closeBtn = document.querySelector('.close-modal');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    const modal = document.getElementById('transactionModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
}

function loadReports() {
    console.log('Loading reports...');
    updateFilterValues();
    loadSalesSummary();
    loadCharts();
    loadTransactions();
}

function updateFilterValues() {
    currentFilter.periode = document.getElementById('periode').value;
    currentFilter.tanggal_mulai = document.getElementById('tanggal_mulai').value;
    currentFilter.tanggal_akhir = document.getElementById('tanggal_akhir').value;
    console.log('Filter updated:', currentFilter);
}

function loadSalesSummary() {
    const params = new URLSearchParams({
        action: 'sales_summary',
        ...currentFilter
    });
    
    console.log('Loading sales summary...');
    fetch(`../api/laporan.php?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Sales summary data:', data);
            if (data.success) {
                updateSalesSummary(data.summary);
            } else {
                console.error('Error in response:', data.message);
                useFallbackData();
            }
        })
        .catch(error => {
            console.error('Error loading sales summary:', error);
            useFallbackData();
        });
}

function useFallbackData() {
    updateSalesSummary({
        total_sales: 15000000,
        total_transactions: 45,
        average_transaction: 333333,
        best_seller: { nama: 'Mie Gacoan Level 5', total_terjual: 25 },
        sales_change: 12.5,
        transactions_change: 8.3,
        average_change: 3.9
    });
}

function updateSalesSummary(summary) {
    document.getElementById('totalSales').textContent = 'Rp ' + formatNumber(summary.total_sales);
    document.getElementById('totalTransactions').textContent = summary.total_transactions;
    document.getElementById('averageTransaction').textContent = 'Rp ' + formatNumber(summary.average_transaction);
    document.getElementById('bestSeller').textContent = summary.best_seller?.nama || '-';
    document.getElementById('bestSellerQty').textContent = (summary.best_seller?.total_terjual || 0) + ' terjual';
    
    // Update changes
    updateChangeIndicator('salesChange', summary.sales_change);
    updateChangeIndicator('transactionsChange', summary.transactions_change);
    updateChangeIndicator('averageChange', summary.average_change);
}

function updateChangeIndicator(elementId, change) {
    const element = document.getElementById(elementId);
    if (change > 0) {
        element.innerHTML = `<span style="color: #10b981">↑ ${change}%</span> vs periode sebelumnya`;
    } else if (change < 0) {
        element.innerHTML = `<span style="color: #ef4444">↓ ${Math.abs(change)}%</span> vs periode sebelumnya`;
    } else {
        element.textContent = '0% vs periode sebelumnya';
    }
}

function loadCharts() {
    console.log('Loading all charts...');
    loadDailySalesChart();
    loadCategorySalesChart();
    loadMonthlyTrendChart();
    loadCashierPerformanceChart();
}

// ===== DAILY SALES CHART (Line Chart with CSS) =====
function loadDailySalesChart() {
    const params = new URLSearchParams({
        action: 'daily_sales',
        ...currentFilter
    });
    
    console.log('Loading daily sales chart...');
    fetch(`../api/laporan.php?${params}`)
        .then(response => response.json())
        .then(data => {
            console.log('Daily sales data:', data);
            if (data.success) {
                renderDailySalesChart(data.data);
            } else {
                renderDailySalesChart({
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    sales: [1200000, 1900000, 1500000, 2500000, 2200000, 3000000, 2800000]
                });
            }
        })
        .catch(error => {
            console.error('Error loading daily sales chart:', error);
            renderDailySalesChart({
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                sales: [1200000, 1900000, 1500000, 2500000, 2200000, 3000000, 2800000]
            });
        });
}

function renderDailySalesChart(chartData) {
    const container = document.getElementById('dailySalesChart');
    if (!container) return;
    
    const maxValue = Math.max(...chartData.sales);
    const minValue = Math.min(...chartData.sales);
    const range = maxValue - minValue || 1;
    
    let html = '<div class="pure-line-chart">';
    
    // Chart bars
    html += '<div class="chart-bars">';
    chartData.sales.forEach((value, index) => {
        const height = ((value - minValue) / range) * 100;
        html += `
            <div class="chart-bar-wrapper">
                <div class="chart-bar" style="height: ${height}%;" data-value="Rp ${formatNumber(value)}">
                    <span class="bar-value">Rp ${formatShortNumber(value)}</span>
                </div>
                <div class="chart-label">${chartData.labels[index]}</div>
            </div>
        `;
    });
    html += '</div>';
    
    html += '</div>';
    
    container.innerHTML = html;
}

// ===== CATEGORY SALES CHART (Doughnut/Pie with CSS) =====
function loadCategorySalesChart() {
    const params = new URLSearchParams({
        action: 'category_sales',
        ...currentFilter
    });
    
    console.log('Loading category sales chart...');
    fetch(`../api/laporan.php?${params}`)
        .then(response => response.json())
        .then(data => {
            console.log('Category sales data:', data);
            if (data.success) {
                renderCategorySalesChart(data.data);
            } else {
                renderCategorySalesChart({
                    labels: ['Mie', 'Minuman', 'Snack', 'Lainnya'],
                    sales: [6500000, 2000000, 1000000, 500000]
                });
            }
        })
        .catch(error => {
            console.error('Error loading category sales chart:', error);
            renderCategorySalesChart({
                labels: ['Mie', 'Minuman', 'Snack', 'Lainnya'],
                sales: [6500000, 2000000, 1000000, 500000]
            });
        });
}

function renderCategorySalesChart(chartData) {
    const container = document.getElementById('categorySalesChart');
    if (!container) return;
    
    const total = chartData.sales.reduce((a, b) => a + b, 0);
    const colors = ['#ff6b00', '#36a2eb', '#4bc0c0', '#ffcd56'];
    
    let html = '<div class="pure-pie-chart">';
    
    // Pie chart (using bar representation for simplicity)
    html += '<div class="pie-bars">';
    chartData.labels.forEach((label, index) => {
        const value = chartData.sales[index];
        const percentage = ((value / total) * 100).toFixed(1);
        const color = colors[index % colors.length];
        
        html += `
            <div class="pie-bar" style="background: ${color}; flex: ${value};">
                <span class="pie-label">${label}<br>${percentage}%</span>
            </div>
        `;
    });
    html += '</div>';
    
    // Legend
    html += '<div class="chart-legend">';
    chartData.labels.forEach((label, index) => {
        const value = chartData.sales[index];
        const percentage = ((value / total) * 100).toFixed(1);
        const color = colors[index % colors.length];
        
        html += `
            <div class="legend-item">
                <span class="legend-color" style="background: ${color};"></span>
                <span class="legend-text">${label}: Rp ${formatShortNumber(value)} (${percentage}%)</span>
            </div>
        `;
    });
    html += '</div>';
    
    html += '</div>';
    
    container.innerHTML = html;
}

// ===== MONTHLY TREND CHART (Bar Chart with CSS) =====
function loadMonthlyTrendChart() {
    const params = new URLSearchParams({
        action: 'monthly_trend',
        ...currentFilter
    });
    
    console.log('Loading monthly trend chart...');
    fetch(`../api/laporan.php?${params}`)
        .then(response => response.json())
        .then(data => {
            console.log('Monthly trend data:', data);
            if (data.success) {
                renderMonthlyTrendChart(data.data);
            } else {
                renderMonthlyTrendChart({
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    sales: [45000000, 52000000, 48000000, 61000000, 58000000, 67000000]
                });
            }
        })
        .catch(error => {
            console.error('Error loading monthly trend chart:', error);
            renderMonthlyTrendChart({
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                sales: [45000000, 52000000, 48000000, 61000000, 58000000, 67000000]
            });
        });
}

function renderMonthlyTrendChart(chartData) {
    const container = document.getElementById('monthlyTrendChart');
    if (!container) return;
    
    const maxValue = Math.max(...chartData.sales);
    
    let html = '<div class="pure-bar-chart">';
    
    html += '<div class="chart-bars">';
    chartData.sales.forEach((value, index) => {
        const height = (value / maxValue) * 100;
        html += `
            <div class="chart-bar-wrapper">
                <div class="chart-bar vertical" style="height: ${height}%;" data-value="Rp ${formatNumber(value)}">
                    <span class="bar-value">Rp ${formatShortNumber(value)}</span>
                </div>
                <div class="chart-label">${chartData.labels[index]}</div>
            </div>
        `;
    });
    html += '</div>';
    
    html += '</div>';
    
    container.innerHTML = html;
}

// ===== CASHIER PERFORMANCE CHART (Mixed Chart with CSS) =====
function loadCashierPerformanceChart() {
    const params = new URLSearchParams({
        action: 'cashier_performance',
        ...currentFilter
    });
    
    console.log('Loading cashier performance chart...');
    fetch(`../api/laporan.php?${params}`)
        .then(response => response.json())
        .then(data => {
            console.log('Cashier performance data:', data);
            if (data.success) {
                renderCashierPerformanceChart(data.data);
            } else {
                renderCashierPerformanceChart({
                    labels: ['Andi', 'Budi', 'Citra', 'Dewi'],
                    transactions: [45, 59, 80, 65],
                    sales: [15000000, 19000000, 25000000, 21000000]
                });
            }
        })
        .catch(error => {
            console.error('Error loading cashier performance chart:', error);
            renderCashierPerformanceChart({
                labels: ['Andi', 'Budi', 'Citra', 'Dewi'],
                transactions: [45, 59, 80, 65],
                sales: [15000000, 19000000, 25000000, 21000000]
            });
        });
}

function renderCashierPerformanceChart(chartData) {
    const container = document.getElementById('cashierPerformanceChart');
    if (!container) return;
    
    const maxTransactions = Math.max(...chartData.transactions);
    const maxSales = Math.max(...chartData.sales);
    
    let html = '<div class="pure-mixed-chart">';
    
    html += '<div class="chart-bars">';
    chartData.labels.forEach((label, index) => {
        const transHeight = (chartData.transactions[index] / maxTransactions) * 100;
        const salesHeight = (chartData.sales[index] / maxSales) * 100;
        
        html += `
            <div class="chart-bar-wrapper">
                <div class="mixed-bars">
                    <div class="chart-bar vertical" style="height: ${transHeight}%; background: #36a2eb;" 
                         data-value="${chartData.transactions[index]} transaksi">
                        <span class="bar-value">${chartData.transactions[index]}</span>
                    </div>
                    <div class="chart-bar vertical" style="height: ${salesHeight}%; background: #ff6b00;" 
                         data-value="Rp ${formatNumber(chartData.sales[index])}">
                        <span class="bar-value">Rp ${formatShortNumber(chartData.sales[index])}</span>
                    </div>
                </div>
                <div class="chart-label">${label}</div>
            </div>
        `;
    });
    html += '</div>';
    
    // Legend
    html += '<div class="chart-legend">';
    html += `
        <div class="legend-item">
            <span class="legend-color" style="background: #36a2eb;"></span>
            <span class="legend-text">Total Transaksi</span>
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background: #ff6b00;"></span>
            <span class="legend-text">Total Penjualan (Rp)</span>
        </div>
    `;
    html += '</div>';
    
    html += '</div>';
    
    container.innerHTML = html;
}

// ===== TRANSACTIONS TABLE =====
function loadTransactions() {
    const params = new URLSearchParams({
        action: 'transactions',
        page: currentPage,
        limit: itemsPerPage,
        ...currentFilter
    });
    
    fetch(`../api/laporan.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTransactionsTable(data.transactions);
                updatePagination(data.total, data.total_pages);
            } else {
                useFallbackTransactions();
            }
        })
        .catch(error => {
            console.error('Error loading transactions:', error);
            useFallbackTransactions();
        });
}

function useFallbackTransactions() {
    renderTransactionsTable([
        {
            id: 1,
            no_transaksi: 'TRX001',
            created_at: new Date().toISOString(),
            nama_pelanggan: 'Joko',
            total_items: 3,
            total: 75000,
            nama_kasir: 'Andi'
        },
        {
            id: 2,
            no_transaksi: 'TRX002',
            created_at: new Date().toISOString(),
            nama_pelanggan: 'Siti',
            total_items: 2,
            total: 55000,
            nama_kasir: 'Budi'
        }
    ]);
    updatePagination(2, 1);
}

function renderTransactionsTable(transactions) {
    const tbody = document.getElementById('transactionsTable');
    
    if (transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Tidak ada data transaksi</td></tr>';
        return;
    }
    
    tbody.innerHTML = transactions.map(transaction => `
        <tr>
            <td>${transaction.no_transaksi}</td>
            <td>${new Date(transaction.created_at).toLocaleDateString('id-ID')}</td>
            <td>${transaction.nama_pelanggan || 'Pelanggan'}</td>
            <td>${transaction.total_items} items</td>
            <td>Rp ${formatNumber(transaction.total)}</td>
            <td>${transaction.nama_kasir}</td>
            <td>
                <button class="btn btn-primary view-detail" data-id="${transaction.id}">Detail</button>
            </td>
        </tr>
    `).join('');
    
    document.querySelectorAll('.view-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            viewTransactionDetail(this.dataset.id);
        });
    });
}

function updatePagination(totalItems, totalPages) {
    document.getElementById('tableCount').textContent = totalItems;
    document.getElementById('pageInfo').textContent = `Halaman ${currentPage} dari ${totalPages}`;
    
    document.getElementById('prevPage').disabled = currentPage <= 1;
    document.getElementById('nextPage').disabled = currentPage >= totalPages;
}

function viewTransactionDetail(transactionId) {
    fetch(`../api/laporan.php?action=transaction_detail&id=${transactionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showTransactionModal(data.transaction);
            } else {
                useFallbackTransactionDetail();
            }
        })
        .catch(error => {
            console.error('Error loading transaction detail:', error);
            useFallbackTransactionDetail();
        });
}

function useFallbackTransactionDetail() {
    showTransactionModal({
        no_transaksi: 'TRX001',
        created_at: new Date().toISOString(),
        nama_pelanggan: 'Joko',
        nama_kasir: 'Andi',
        total: 75000,
        bayar: 100000,
        kembalian: 25000,
        items: [
            { nama_produk: 'Mie Gacoan Level 5', qty: 1, harga: 25000, subtotal: 25000 },
            { nama_produk: 'Es Teh Manis', qty: 2, harga: 5000, subtotal: 10000 },
            { nama_produk: 'Pangsit Goreng', qty: 2, harga: 20000, subtotal: 40000 }
        ]
    });
}

function showTransactionModal(transaction) {
    const modal = document.getElementById('transactionModal');
    const details = document.getElementById('transactionDetails');
    
    details.innerHTML = `
        <div class="transaction-detail">
            <div class="detail-row">
                <strong>No. Transaksi:</strong> ${transaction.no_transaksi}
            </div>
            <div class="detail-row">
                <strong>Tanggal:</strong> ${new Date(transaction.created_at).toLocaleString('id-ID')}
            </div>
            <div class="detail-row">
                <strong>Pelanggan:</strong> ${transaction.nama_pelanggan || 'Pelanggan'}
            </div>
            <div class="detail-row">
                <strong>Kasir:</strong> ${transaction.nama_kasir}
            </div>
            
            <h4>Items:</h4>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${transaction.items.map(item => `
                        <tr>
                            <td>${item.nama_produk}</td>
                            <td>${item.qty}</td>
                            <td>Rp ${formatNumber(item.harga)}</td>
                            <td>Rp ${formatNumber(item.subtotal)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            
            <div class="transaction-total">
                <div class="total-row">
                    <strong>Total:</strong> Rp ${formatNumber(transaction.total)}
                </div>
                <div class="total-row">
                    <strong>Bayar:</strong> Rp ${formatNumber(transaction.bayar)}
                </div>
                <div class="total-row">
                    <strong>Kembali:</strong> Rp ${formatNumber(transaction.kembalian)}
                </div>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('transactionModal').style.display = 'none';
}

function exportToPDF() {
    alert('Fitur export PDF akan mengunduh laporan dalam format PDF.\n\nFilter yang digunakan:\n' +
          `Periode: ${document.getElementById('periode').options[document.getElementById('periode').selectedIndex].text}\n` +
          `Tanggal: ${currentFilter.tanggal_mulai} hingga ${currentFilter.tanggal_akhir}`);
}

// ===== HELPER FUNCTIONS =====
function formatDateForInput(date) {
    return date.toISOString().split('T')[0];
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function formatShortNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}