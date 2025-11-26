<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒 Keranjang - Mie Gacoan</title>
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
            <button id="loginBtn" class="login-btn">
                <?php echo isset($_SESSION['user']) ? '👤 ' . htmlspecialchars($_SESSION['user']['nama_lengkap']) : '🔐 Login'; ?>
            </button>
        </nav>
    </header>

    <main class="container">
        <h1 class="page-title">🛒 Keranjang Belanja</h1>
        
        <div class="cart-container">
            <div id="cartItems">
                <div class="loading" style="height: 200px;"></div>
            </div>
            
            <div id="cartSummary" class="cart-summary" style="display: none;">
                <div class="total-amount">
                    💰 Total: <span id="cartTotal">Rp 0</span>
                </div>
                <button id="checkoutBtn" class="checkout-btn">
                    ✨ Checkout Sekarang
                </button>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>