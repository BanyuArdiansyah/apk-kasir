<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// In a real application, you would get this data from the database
// based on the transaction ID passed via GET parameter
$transaksi_id = $_GET['id'] ?? 0;

// For demo purposes, we'll use session data or default values
$transaksi = $_SESSION['last_transaction'] ?? [
    'no_transaksi' => 'TRX-' . date('Ymd') . '-0001',
    'created_at' => date('Y-m-d H:i:s'),
    'nama_kasir' => $_SESSION['user']['nama_lengkap'] ?? 'Kasir',
    'total' => 0,
    'bayar' => 0,
    'kembalian' => 0,
    'items' => []
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - Mie Gacoan</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            margin: 0; 
            padding: 10px;
            background: white;
            color: black;
        }
        .struk { 
            max-width: 300px; 
            margin: 0 auto;
        }
        .struk-header { 
            text-align: center; 
            margin-bottom: 10px; 
            border-bottom: 1px dashed #000; 
            padding-bottom: 10px; 
        }
        .struk-item { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 5px; 
        }
        .struk-total { 
            border-top: 1px dashed #000; 
            padding-top: 10px; 
            margin-top: 10px; 
        }
        .print-controls { 
            text-align: center; 
            margin-top: 20px; 
        }
        @media print {
            .print-controls { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="struk">
        <div class="struk-header">
            <h3>MIE GACOAN</h3>
            <p>Jl. Contoh No. 123</p>
            <p>Telp: 08123456789</p>
        </div>
        <div class="struk-info">
            <p>No. Transaksi: <?php echo $transaksi['no_transaksi']; ?></p>
            <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($transaksi['created_at'])); ?></p>
            <p>Kasir: <?php echo $transaksi['nama_kasir']; ?></p>
        </div>
        <div class="struk-items">
            <?php foreach ($transaksi['items'] as $item): ?>
            <div class="struk-item">
                <span><?php echo $item['nama']; ?> x <?php echo $item['qty']; ?></span>
                <span>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="struk-total">
            <div class="struk-item">
                <span>Total:</span>
                <span>Rp <?php echo number_format($transaksi['total'], 0, ',', '.'); ?></span>
            </div>
            <div class="struk-item">
                <span>Bayar:</span>
                <span>Rp <?php echo number_format($transaksi['bayar'], 0, ',', '.'); ?></span>
            </div>
            <div class="struk-item">
                <span>Kembali:</span>
                <span>Rp <?php echo number_format($transaksi['kembalian'], 0, ',', '.'); ?></span>
            </div>
        </div>
        <div class="struk-footer">
            <p>Terima kasih atas kunjungan Anda</p>
        </div>
    </div>
    
    <div class="print-controls">
        <button onclick="window.print()">Print Ulang</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>