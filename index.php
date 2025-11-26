<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['user']['role'] === "admin") {
    header("Location: admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍜 Mie Gacoan - POS System</title>
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

            <?php if(isset($_SESSION['user'])): ?>
                <form action="api/logout.php" method="POST" style="margin:0;">
                    <button type="submit" class="login-btn">
                        🚪 Logout (<?php echo htmlspecialchars($_SESSION['user']['nama_lengkap']); ?>)
                    </button>
                </form>
            <?php else: ?>
                <button id="loginBtn" class="login-btn">🔐 Login</button>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <h1 class="page-title">Selamat Datang di Mie Gacoan! 🍜</h1>
        <p>Temukan berbagai pilihan mie dan minuman terbaik kami dengan sistem POS yang modern dan cepat.</p>
        
        <h2 style="margin-top: 3rem; font-size: 2rem; font-weight: 700; color: var(--text-dark);">
            ✨ Produk Terpopuler
        </h2>
        <div id="productsGrid" class="products-grid">
            <div class="loading" style="height: 400px; grid-column: 1/-1;"></div>
        </div>
    </main>

    <div id="modal" class="modal">
        <div class="modal-content">
            <!-- Dynamic content -->
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>