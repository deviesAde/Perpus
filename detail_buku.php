<?php
session_start();
include 'config/koneksi.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Pastikan parameter id tersedia
if (!isset($_GET['id'])) {
    echo "ID buku tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Ambil data buku dari database
$query = "SELECT * FROM books WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$buku = mysqli_fetch_assoc($result);

if (!$buku) {
    echo "Buku tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <div class="page-header">
            <h2><i class="fas fa-book-open"></i> Detail Buku</h2>
            <div class="breadcrumb">
                <a href="dashboard_user.php"><i class="fas fa-home"></i> Dashboard</a> 
                <span><i class="fas fa-chevron-right"></i></span> 
                <span>Detail Buku</span>
            </div>
        </div>
        
        <div class="card book-detail">
            <div class="card-header">
                <h3 class="book-title"><?= htmlspecialchars($buku['judul']) ?></h3>
                <div class="book-meta">
                    <span class="badge badge-year"><i class="fas fa-calendar-alt"></i> <?= $buku['tahun'] ?></span>
                    <span class="badge <?= $buku['stok'] > 0 ? 'badge-available' : 'badge-unavailable' ?>">
                        <i class="fas <?= $buku['stok'] > 0 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i> 
                        <?= $buku['stok'] > 0 ? 'Tersedia' : 'Tidak Tersedia' ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="book-info-grid">
                    <div class="book-info-item">
                        <h4><i class="fas fa-user-edit"></i> Pengarang</h4>
                        <p><?= htmlspecialchars($buku['pengarang']) ?></p>
                    </div>
                    <div class="book-info-item">
                        <h4><i class="fas fa-boxes"></i> Stok</h4>
                        <p><?= $buku['stok'] ?> buku</p>
                    </div>
                </div>
                
                <div class="book-description">
                    <h4><i class="fas fa-align-left"></i> Deskripsi</h4>
                    <p><?= nl2br(htmlspecialchars($buku['deskripsi'])) ?></p>
                </div>
                
                <div class="book-actions">
                    <a href="dashboard_user.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <?php if ($buku['stok'] > 0): ?>
                        <a href="pinjam_buku.php?id=<?= $buku['id'] ?>" class="btn btn-borrow">
                            <i class="fas fa-book"></i> Pinjam Buku Ini
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="js/app.js"></script>
</body>
</html>