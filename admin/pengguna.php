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
                $username = $_POST['username'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $nama_lengkap = $_POST['nama_lengkap'];
                $role = $_POST['role'];
                
                $query = "INSERT INTO admin (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$username, $password, $nama_lengkap, $role]);
                break;
                
            case 'update':
                $id = $_POST['id'];
                $username = $_POST['username'];
                $nama_lengkap = $_POST['nama_lengkap'];
                $role = $_POST['role'];
                
                $query = "UPDATE admin SET username=?, nama_lengkap=?, role=? WHERE id=?";
                $stmt = $db->prepare($query);
                
                // If password is provided, update it too
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $query = "UPDATE admin SET username=?, password=?, nama_lengkap=?, role=? WHERE id=?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$username, $password, $nama_lengkap, $role, $id]);
                } else {
                    $stmt->execute([$username, $nama_lengkap, $role, $id]);
                }
                break;
                
            case 'delete':
                $id = $_POST['id'];
                // Don't allow deleting the current user
                if ($id != $_SESSION['user']['id']) {
                    $query = "DELETE FROM admin WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$id]);
                }
                break;
        }
    }
}

// Get all users
$query = "SELECT * FROM admin ORDER BY role, username";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h1>Kelola Pengguna</h1>
                <div class="user-info">
                    Selamat datang, <?php echo $_SESSION['user']['nama_lengkap']; ?>
                </div>
            </header>
            
            <!-- Add User Form -->
            <div class="admin-form">
                <h3>Tambah Pengguna Baru</h3>
                <form method="POST" id="userForm">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="userId">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Tambah Pengguna</button>
                        <button type="button" class="btn btn-danger" id="cancelBtn" style="display: none;">Batal</button>
                    </div>
                </form>
            </div>
            
            <!-- Users Table -->
            <div class="recent-transactions">
                <h2>Daftar Pengguna</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                <button class="btn btn-primary edit-btn" data-id="<?php echo $user['id']; ?>"
                                        data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                        data-nama_lengkap="<?php echo htmlspecialchars($user['nama_lengkap']); ?>"
                                        data-role="<?php echo $user['role']; ?>">
                                    Edit
                                </button>
                                <button class="btn btn-danger delete-btn" data-id="<?php echo $user['id']; ?>">Hapus</button>
                                <?php else: ?>
                                <span style="color: var(--text-secondary);">User Aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Edit user
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('formAction').value = 'update';
                document.getElementById('userId').value = this.dataset.id;
                document.getElementById('username').value = this.dataset.username;
                document.getElementById('nama_lengkap').value = this.dataset.nama_lengkap;
                document.getElementById('role').value = this.dataset.role;
                document.getElementById('password').required = false;
                document.getElementById('password').placeholder = 'Kosongkan jika tidak diubah';
                document.getElementById('submitBtn').textContent = 'Update Pengguna';
                document.getElementById('cancelBtn').style.display = 'inline-block';
            });
        });
        
        // Delete user
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
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
            document.getElementById('userForm').reset();
            document.getElementById('formAction').value = 'create';
            document.getElementById('userId').value = '';
            document.getElementById('password').required = true;
            document.getElementById('password').placeholder = '';
            document.getElementById('submitBtn').textContent = 'Tambah Pengguna';
            this.style.display = 'none';
        });
    </script>
</body>
</html>