<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$nama_user = $_SESSION['user']['nama'];


$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$query = "SELECT * FROM books WHERE judul LIKE '%$cari%' OR pengarang LIKE '%$cari%'";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
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
                    <li class="active">
                        <a href="dashboard_user.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="buku_saya.php"><i class="fas fa-book"></i> Buku Saya</a>
                    </li>
                    <li>
                        <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    </li>
                    <li >
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
            <div class="top-nav">
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="search-box">
                   <form method="GET" action="dashboard_user.php">
    <input type="text" name="cari" placeholder="Cari buku..." value="<?= htmlspecialchars($cari) ?>">
    <button type="submit"><i class="fas fa-search"></i></button>
</form>
                </div>
                <div class="user-info">
                    <span>Halo, <?= htmlspecialchars($nama_user) ?></span>
                    <div class="user-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>

            <div class="content">
                <h2>Daftar Buku</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['pengarang']) ?></td>
                                <td><?= $row['tahun'] ?></td>
                                <td><?= $row['stok'] ?></td>
                                <td>
                                    <a class="btn btn-detail" href="detail_buku.php?id=<?= $row['id'] ?>"><i class="fas fa-info-circle"></i> Detail</a>
                                    <?php if ($row['stok'] > 0): ?>
                                        <a class="btn btn-pinjam" href="pinjam_buku.php?id=<?= $row['id'] ?>"><i class="fas fa-book-open"></i> Pinjam</a>
                                    <?php else: ?>
                                        <span class="habis"><i class="fas fa-times-circle"></i> Habis</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
     <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-<?= $_SESSION['alert']['type'] ?> php-alert">
            <?= $_SESSION['alert']['message'] ?>
            <span class="close-btn">&times;</span>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>                                    
    <script src="js/app.js"></script>
</body>
</html>