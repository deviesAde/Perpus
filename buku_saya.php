<?php
session_start();
include 'config/koneksi.php';
include 'hitungdenda.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];


$query = "SELECT peminjaman.*, books.judul 
          FROM peminjaman 
          JOIN books ON peminjaman.book_id = books.id 
          WHERE peminjaman.user_id = $user_id 
          ORDER BY peminjaman.tgl_pinjam DESC";
$result = mysqli_query($koneksi, $query);



while ($row = mysqli_fetch_assoc($result)) {
    $denda = 0;

    if ($row['status'] == 'Dipinjam') {
        $tglKembali = new DateTime($row['tgl_kembali']);
        $today = new DateTime();

        if ($today > $tglKembali) {
            $denda = hitungDenda($today->format('Y-m-d'), $tglKembali->format('Y-m-d'));

            $update_query = "UPDATE peminjaman SET status = 'Terlambat', denda = $denda WHERE id = {$row['id']}";
            mysqli_query($koneksi, $update_query);
        }
    }
}


$result = mysqli_query($koneksi, $query);
?>

<?php if (isset($_SESSION['alert'])): ?>
    <div class="alert alert-<?= $_SESSION['alert']['type'] ?>">
        <?= $_SESSION['alert']['message'] ?>
    </div>
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Saya</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">

        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Perpustakaan</h2>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="dashboard_user.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="active">
                        <a href="buku_saya.php"><i class="fas fa-book"></i> Buku Saya</a>
                    </li>
                    <li>
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
            <div class="top-nav">
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="user-info">
                    <span>Halo, <?= htmlspecialchars($_SESSION['user']['nama']) ?></span>
                    <div class="user-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>

            <div class="content">
                <h2>Buku yang Dipinjam</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($koneksi, $query); 
                            while ($row = mysqli_fetch_assoc($result)): 
                                $statusClass = '';

                                if ($row['status'] == 'Dipinjam') {
                                    $statusClass = 'status-dipinjam';
                                } elseif ($row['status'] == 'Terlambat') {
                                    $statusClass = 'status-terlambat';
                                } elseif ($row['status'] == 'Dikembalikan') {
                                    $statusClass = 'status-dikembalikan';
                                } elseif ($row['status'] == 'Lunas') {
                                    $statusClass = 'status-lunas';
                                } elseif ($row['status'] == 'Pending') {
                                    $statusClass = 'status-pending';
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                                <td><?= date('d M Y', strtotime($row['tgl_kembali'])) ?></td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="<?= $row['denda'] > 0 ? 'denda-text' : '' ?>">
                                    Rp <?= number_format($row['denda'], 0, ',', '.') ?>
                                </td>
                               <td>
    <?php if ($row['status'] == 'Dipinjam'): ?>
        <?php if ($row['perpanjangan'] < 2): ?>
            <a class="btn btn-primary" href="perpanjang.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin memperpanjang peminjaman buku ini?')">
                <i class="fas fa-clock"></i> Perpanjang
            </a>
        <?php else: ?>
            <span class="text-muted">Maksimal Perpanjangan</span>
        <?php endif; ?>
    <?php elseif ($row['status'] == 'Terlambat'): ?>
        <span class="text-danger">Tidak dapat diperpanjang (Terlambat)</span>
    <?php elseif ($row['status'] == 'Lunas'): ?>
        <a class="btn btn-primary" href="ajukan_pengembalian.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin mengajukan pengembalian buku ini?')">
            <i class="fas fa-undo"></i> Ajukan Pengembalian
        </a>
    <?php endif; ?>

    <?php if ($row['denda'] > 0): ?>
        <a class="btn btn-warning" href="pembayaran.php?id=<?= $row['id'] ?>">
            <i class="fas fa-money-bill"></i> Bayar Denda
        </a>
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

    <script src="js/app.js"></script>
</body>
</html>