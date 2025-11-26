<?php
session_start();

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 Login - Mie Gacoan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <nav>
        <div class="logo">🍜 MIE GACOAN</div>
        <ul class="nav-links">
            <li><a href="index.php">🏠 Beranda</a></li>
            <li><a href="produk.php">📦 Produk</a></li>
        </ul>
    </nav>
</header>

<main class="container">
    <div class="login-container">
        <form class="login-form" id="loginForm">
            <h2>🔐 Login Kasir</h2>

            <div class="form-group">
                <label for="username">👤 Username</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan username">
            </div>

            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">
            </div>

            <button type="submit" class="login-btn" style="width: 100%; margin-top: 1rem;">
                ✨ Login Sekarang
            </button>

            <div id="loginMessage" style="margin-top: 1.5rem; text-align: center; color: var(--danger-color); font-weight: 600;"></div>
        </form>
    </div>
</main>

<script>
document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = '⏳ Loading...';
    btn.disabled = true;

    let formData = new FormData(this);

    fetch("api/auth.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success || !data.user) {
            document.getElementById('loginMessage').textContent = '❌ ' + (data.message || "Login gagal");
            btn.textContent = originalText;
            btn.disabled = false;
            return;
        }

        // Success animation
        btn.textContent = '✅ Login Berhasil!';
        setTimeout(() => {
            if (data.user.role === "admin") {
                window.location.href = "admin/index.php";
            } else {
                window.location.href = "index.php";
            }
        }, 500);
    })
    .catch(err => {
        document.getElementById('loginMessage').textContent = '❌ Error: ' + err;
        btn.textContent = originalText;
        btn.disabled = false;
    });
});
</script>

</body>
</html>