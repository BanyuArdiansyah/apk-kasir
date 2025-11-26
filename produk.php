<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📦 Produk - Mie Gacoan</title>
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
        <h1 class="page-title">📦 Daftar Produk</h1>
        <p>Semua menu terbaik kami dalam satu tempat! Pilih favorit Anda sekarang.</p>
        
        <div class="products-grid" id="productsGrid">
            <div class="loading" style="height: 400px; grid-column: 1/-1;"></div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>