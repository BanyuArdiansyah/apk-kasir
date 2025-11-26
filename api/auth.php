<?php
header('Content-Type: application/json');
require_once '../config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role' => $user['role']
        ];

        // Return success with user data
        echo json_encode([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => $_SESSION['user']
        ]);
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => 'Username atau password salah'
    ]);
    exit;

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>