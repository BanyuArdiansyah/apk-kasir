<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💳 Checkout - Mie Gacoan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🍜 MIE GACOAN</div>
            <ul class="nav-links">
                <li><a href="index.php">🏠 Beranda</a></li>
                <li><a href="produk.php">📦 Produk</a></li>
                <li><a href="cart.php">🛒 Keranjang <span id="cartCount">0</span></a></li>
            </ul>
            <button class="login-btn">
                👤 <?php echo htmlspecialchars($_SESSION['user']['nama_lengkap']); ?>
            </button>
        </nav>
    </header>

    <main class="container">
        <h1 class="page-title">💳 Checkout</h1>
        
        <div class="checkout-form">
            <form id="checkoutForm">
                <input type="hidden" id="totalAmount" name="total" value="0">
                
                <div class="form-group">
                    <label for="nama_pelanggan">👤 Nama Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" required value="Pelanggan" placeholder="Masukkan nama pelanggan">
                </div>
                
                <div class="payment-summary">
                    <h3>📋 Ringkasan Pembayaran</h3>
                    <div id="checkoutItems">
                        <div class="loading" style="height: 100px;"></div>
                    </div>
                    <div class="summary-total">
                        💰 Total: <span id="checkoutTotal">Rp 0</span>
                    </div>
                    
                    <div class="form-group" style="margin-top: 2rem;">
                        <label for="bayar">💵 Bayar (Rp)</label>
                        <input type="number" id="bayar" name="bayar" required min="0" step="1000" placeholder="Masukkan jumlah pembayaran">
                    </div>
                    
                    <div class="form-group">
                        <label for="kembalian">💰 Kembalian (Rp)</label>
                        <input type="text" id="kembalian" name="kembalian" readonly class="kembalian-field" placeholder="0">
                    </div>
                </div>
                
                <button type="button" id="payBtn" class="pay-btn" disabled>
                    ✨ Bayar Sekarang
                </button>
            </form>
        </div>
    </main>

    <div id="modal" class="modal">
        <div class="modal-content">
            <!-- Receipt will be loaded here -->
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/checkout.js"></script>
</body>
</html>