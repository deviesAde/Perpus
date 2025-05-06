<?php
session_start();
include 'config/koneksi.php';
include 'hitungdenda.php'; // Sertakan file hitungdenda.php

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$peminjaman_id = isset($_GET['id']) ? intval($_GET['id']) : null;


if ($peminjaman_id) {
    $query = "SELECT peminjaman.*, books.judul 
              FROM peminjaman 
              JOIN books ON peminjaman.book_id = books.id 
              WHERE peminjaman.id = $peminjaman_id AND peminjaman.user_id = $user_id";
    $result = mysqli_query($koneksi, $query);
    $peminjaman = mysqli_fetch_assoc($result);

    if (!$peminjaman) {
        echo "Data peminjaman tidak ditemukan.";
        exit;
    }


    $tgl_kembali = new DateTime($peminjaman['tgl_kembali']);
    $today = new DateTime();
    $denda = hitungDenda($today->format('Y-m-d'), $tgl_kembali->format('Y-m-d'));
} else {

    $query_all = "SELECT peminjaman.*, books.judul 
                  FROM peminjaman 
                  JOIN books ON peminjaman.book_id = books.id 
                  WHERE peminjaman.user_id = $user_id AND peminjaman.denda > 0";
    $result_all = mysqli_query($koneksi, $query_all);
    $total_denda = 0;
    $buku_dengan_denda = [];
    while ($row = mysqli_fetch_assoc($result_all)) {
        $total_denda += $row['denda'];
        $buku_dengan_denda[] = $row;
    }
}

$kembalian = 0; // Default kembalian
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah_bayar = intval($_POST['jumlah_bayar']);
    $bayar_semua = isset($_POST['bayar_semua']) && $_POST['bayar_semua'] === '1';

    if ($bayar_semua) {
        
        if ($jumlah_bayar >= $total_denda) {
            $kembalian = $jumlah_bayar - $total_denda;
            $update_all = "UPDATE peminjaman SET denda = 0, status = 'Dikembalikan' 
                           WHERE user_id = $user_id AND denda > 0";
            if (mysqli_query($koneksi, $update_all)) {
                $success_message = "Pembayaran berhasil untuk semua buku! Kembalian Anda: Rp " . number_format($kembalian, 0, ',', '.');
            } else {
                $error_message = "Gagal memproses pembayaran untuk semua buku.";
            }
        } else {
            $error_message = "Jumlah pembayaran kurang dari total denda.";
        }
    } else {
        
        if ($jumlah_bayar >= $denda) {
    $kembalian = $jumlah_bayar - $denda;

    
  $update = "UPDATE peminjaman SET denda = 0, status = 'Lunas' WHERE id = $peminjaman_id";
    if (mysqli_query($koneksi, $update)) {
        $success_message = "Pembayaran berhasil! Kembalian Anda: Rp " . number_format($kembalian, 0, ',', '.');
    } else {
        $error_message = "Gagal memproses pembayaran.";
    }
} else {
    $error_message = "Jumlah pembayaran kurang dari denda.";
}
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Denda - Perpustakaan Digital</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
        .payment-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        
        .payment-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .payment-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        
        .detail-value {
            font-weight: 700;
            color: #2c3e50;
        }
        
        .denda-amount {
            color: #e74c3c;
            font-size: 18px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn-payment {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-payment:hover {
            background: #2980b9;
        }
        
        .btn-payment i {
            font-size: 18px;
        }
        
        .btn-secondary {
            display: inline-block;
            background: #95a5a6;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
            margin-top: 15px;
            transition: background 0.3s;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert i {
            font-size: 20px;
        }
        
        .book-list {
            margin-top: 20px;
        }
        
        .book-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .book-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .book-denda {
            color: #e74c3c;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Perpustakaan</h2>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="dashboard_user.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="buku_saya.php"><i class="fas fa-book"></i> Buku Saya</a>
                    </li>
                    <li>
                        <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    </li>
                    <li class="active">
                        <a href="pembayaran.php"><i class="fas fa-money-bill"></i> Bayar Denda</a>
                    </li>
                    <li>
                        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    </li>
                    <li>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content">
                <div class="profile-container">
                    <div class="payment-card">
                        <div class="payment-header">
                            <h2><i class="fas fa-money-bill-wave"></i> Pembayaran Denda</h2>
                            <p>Selesaikan pembayaran denda untuk melanjutkan peminjaman</p>
                        </div>

                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?= $success_message ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($peminjaman_id): ?>
                            <div class="payment-details">
                                <div class="detail-row">
                                    <span class="detail-label">Judul Buku:</span>
                                    <span class="detail-value"><?= htmlspecialchars($peminjaman['judul']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Tanggal Kembali:</span>
                                    <span class="detail-value"><?= date('d M Y', strtotime($peminjaman['tgl_kembali'])) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Total Denda:</span>
                                    <span class="detail-value denda-amount">Rp <?= number_format($denda, 0, ',', '.') ?></span>
                                </div>
                            </div>

                            <form method="POST" class="form">
                                <div class="form-group">
                                    <label for="jumlah_bayar">Jumlah Pembayaran (Rp)</label>
                                    <input type="number" id="jumlah_bayar" name="jumlah_bayar" min="1" value="<?= isset($_POST['jumlah_bayar']) ? htmlspecialchars($_POST['jumlah_bayar']) : $denda ?>" required>
                                </div>
                                <button type="submit" class="btn-payment">
                                    <i class="fas fa-credit-card"></i> Bayar Denda
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="payment-details">
                                <div class="detail-row">
                                    <span class="detail-label">Total Buku dengan Denda:</span>
                                    <span class="detail-value"><?= count($buku_dengan_denda) ?> buku</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Total Denda:</span>
                                    <span class="detail-value denda-amount">Rp <?= number_format($total_denda, 0, ',', '.') ?></span>
                                </div>
                            </div>

                            <div class="book-list">
                                <h4>Daftar Buku dengan Denda:</h4>
                                <?php foreach ($buku_dengan_denda as $buku): ?>
                                    <div class="book-item">
                                        <div class="book-title"><?= htmlspecialchars($buku['judul']) ?></div>
                                        <div class="book-denda">Denda: Rp <?= number_format($buku['denda'], 0, ',', '.') ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <form method="POST" class="form">
                                <input type="hidden" name="bayar_semua" value="1">
                                <div class="form-group">
                                    <label for="jumlah_bayar">Jumlah Pembayaran (Rp)</label>
                                    <input type="number" id="jumlah_bayar" name="jumlah_bayar" min="1" value="<?= isset($_POST['jumlah_bayar']) ? htmlspecialchars($_POST['jumlah_bayar']) : $total_denda ?>" required>
                                </div>
                                <button type="submit" class="btn-payment">
                                    <i class="fas fa-credit-card"></i> Bayar Semua Denda
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <a href="buku_saya.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Buku Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>