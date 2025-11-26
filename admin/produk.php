<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle form actions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $nama = $_POST['nama'];
                $kategori = $_POST['kategori'];
                $harga = $_POST['harga'];
                $stok = $_POST['stok'];
                $deskripsi = $_POST['deskripsi'];
                
                $query = "INSERT INTO produk (nama, kategori, harga, stok, deskripsi) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$nama, $kategori, $harga, $stok, $deskripsi]);
                break;
                
            case 'update':
                $id = $_POST['id'];
                $nama = $_POST['nama'];
                $kategori = $_POST['kategori'];
                $harga = $_POST['harga'];
                $stok = $_POST['stok'];
                $deskripsi = $_POST['deskripsi'];
                
                $query = "UPDATE produk SET nama=?, kategori=?, harga=?, stok=?, deskripsi=? WHERE id=?";
                $stmt = $db->prepare($query);
                $stmt->execute([$nama, $kategori, $harga, $stok, $deskripsi, $id]);
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $query = "UPDATE produk SET is_active = 0 WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$id]);
                break;
        }
    }
}

// Get all products
$query = "SELECT * FROM produk WHERE is_active = 1 ORDER BY nama";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h1>Kelola Produk</h1>
                <div class="user-info">
                    Selamat datang, <?php echo $_SESSION['user']['nama_lengkap']; ?>
                </div>
            </header>
            
            <!-- Add Product Form -->
            <div class="admin-form">
                <h3>Tambah Produk Baru</h3>
                <form method="POST" id="productForm">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="productId">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama">Nama Produk</label>
                            <input type="text" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <select id="kategori" name="kategori" required>
                                <option value="mie">Mie</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="harga">Harga (Rp)</label>
                            <input type="number" id="harga" name="harga" required min="0">
                        </div>
                        <div class="form-group">
                            <label for="stok">Stok</label>
                            <input type="number" id="stok" name="stok" required min="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Tambah Produk</button>
                        <button type="button" class="btn btn-danger" id="cancelBtn" style="display: none;">Batal</button>
                    </div>
                </form>
            </div>
            
            <!-- Products Table -->
            <div class="recent-transactions">
                <h2>Daftar Produk</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['nama']); ?></td>
                            <td><?php echo ucfirst($product['kategori']); ?></td>
                            <td>Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $product['stok']; ?></td>
                            <td>
                                <button class="btn btn-primary edit-btn" data-id="<?php echo $product['id']; ?>"
                                        data-nama="<?php echo htmlspecialchars($product['nama']); ?>"
                                        data-kategori="<?php echo $product['kategori']; ?>"
                                        data-harga="<?php echo $product['harga']; ?>"
                                        data-stok="<?php echo $product['stok']; ?>"
                                        data-deskripsi="<?php echo htmlspecialchars($product['deskripsi']); ?>">
                                    Edit
                                </button>
                                <button class="btn btn-danger delete-btn" data-id="<?php echo $product['id']; ?>">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Edit product
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('formAction').value = 'update';
                document.getElementById('productId').value = this.dataset.id;
                document.getElementById('nama').value = this.dataset.nama;
                document.getElementById('kategori').value = this.dataset.kategori;
                document.getElementById('harga').value = this.dataset.harga;
                document.getElementById('stok').value = this.dataset.stok;
                document.getElementById('deskripsi').value = this.dataset.deskripsi;
                document.getElementById('submitBtn').textContent = 'Update Produk';
                document.getElementById('cancelBtn').style.display = 'inline-block';
            });
        });
        
        // Delete product
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${this.dataset.id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
        
        // Cancel edit
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('productForm').reset();
            document.getElementById('formAction').value = 'create';
            document.getElementById('productId').value = '';
            document.getElementById('submitBtn').textContent = 'Tambah Produk';
            this.style.display = 'none';
        });
    </script>
</body>
</html>