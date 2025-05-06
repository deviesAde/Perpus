<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Query untuk mengambil data riwayat peminjaman
$query_riwayat = "
    SELECT peminjaman.*, books.judul, books.pengarang 
    FROM peminjaman 
    JOIN books ON peminjaman.book_id = books.id 
    WHERE peminjaman.user_id = $user_id
    ORDER BY peminjaman.tgl_pinjam DESC
";
$result_riwayat = mysqli_query($koneksi, $query_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
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
                    <li class="active">
                        <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    </li>
                    <li>
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
                <h2>Riwayat Peminjaman</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Pengarang</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result_riwayat)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['judul']) ?></td>
                                        <td><?= htmlspecialchars($row['pengarang']) ?></td>
                                        <td><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                                        <td>
                                            <?= $row['tgl_kembali'] ? date('d M Y', strtotime($row['tgl_kembali'])) : '<span class="text-warning">Belum Dikembalikan</span>' ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $row['status'] == 'Dikembalikan' ? 'badge-confirmed' : 'badge-pending' ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Tidak ada riwayat peminjaman.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>