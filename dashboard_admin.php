<?php
session_start(); // Tambahkan ini di bagian paling atas
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

$query_pengembalian = "SELECT peminjaman.*, books.judul, users.nama 
                       FROM peminjaman 
                       JOIN books ON peminjaman.book_id = books.id 
                       JOIN users ON peminjaman.user_id = users.id 
                       WHERE peminjaman.status = 'Proses Pengembalian'";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="#crud-user"><i class="fas fa-users"></i> Kelola User</a></li>
                    <li><a href="#crud-buku"><i class="fas fa-book"></i> Kelola Buku</a></li>
                    <li><a href="#konfirmasi-peminjaman"><i class="fas fa-check-circle"></i> Konfirmasi Peminjaman</a></li>
                    <li><a href="#konfirmasi-pengembalian"><i class="fas fa-undo"></i> Konfirmasi Pengembalian</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

    
        <div class="main-content">
            <div class="content">
                <h1>Dashboard Admin</h1>
                <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['user']['nama']) ?></strong>!</p>

               
                <section id="crud-user">
                    <h2><i class="fas fa-users"></i> Kelola User</h2>
                    <a href="tambah_user.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah User</a>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_user = "SELECT * FROM users";
                                $result_user = mysqli_query($koneksi, $query_user);
                                while ($user = mysqli_fetch_assoc($result_user)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['nama']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['role']) ?></td>
                                        <td>
                                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="hapus_user.php?id=<?= $user['id'] ?>" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- CRUD Buku -->
                <section id="crud-buku">
                    <h2><i class="fas fa-book"></i> Kelola Buku</h2>
                    <a href="tambah_buku.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Buku</a>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_buku = "SELECT * FROM books";
                                $result_buku = mysqli_query($koneksi, $query_buku);
                                while ($buku = mysqli_fetch_assoc($result_buku)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($buku['judul']) ?></td>
                                        <td><?= htmlspecialchars($buku['pengarang']) ?></td>
                                        <td><?= htmlspecialchars($buku['stok']) ?></td>
                                        <td>
                                            <a href="edit_buku.php?id=<?= $buku['id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="hapus_buku.php?id=<?= $buku['id'] ?>" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Konfirmasi Peminjaman -->
                <section id="konfirmasi-peminjaman">
                    <h2><i class="fas fa-check-circle"></i> Konfirmasi Peminjaman</h2>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama User</th>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_peminjaman = "SELECT peminjaman.*, books.judul, users.nama 
                                                    FROM peminjaman 
                                                    JOIN books ON peminjaman.book_id = books.id 
                                                    JOIN users ON peminjaman.user_id = users.id 
                                                    WHERE peminjaman.status = 'Pending'";
                                $result_peminjaman = mysqli_query($koneksi, $query_peminjaman);
                                while ($peminjaman = mysqli_fetch_assoc($result_peminjaman)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($peminjaman['nama']) ?></td>
                                        <td><?= htmlspecialchars($peminjaman['judul']) ?></td>
                                        <td><?= date('d M Y', strtotime($peminjaman['tgl_pinjam'])) ?></td>
                                        <td><span class="badge badge-pending">Pending</span></td>
                                        <td>
                                            <a href="konfirmasi_pinjaman.php?id=<?= $peminjaman['id'] ?>" class="btn btn-success"><i class="fas fa-check"></i> Konfirmasi</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

           <section id="konfirmasi-pengembalian">
    <h2><i class="fas fa-undo"></i> Konfirmasi Pengembalian</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama User</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result_pengembalian = mysqli_query($koneksi, $query_pengembalian);
                while ($pengembalian = mysqli_fetch_assoc($result_pengembalian)): ?>
                    <tr>
                        <td><?= htmlspecialchars($pengembalian['nama']) ?></td>
                        <td><?= htmlspecialchars($pengembalian['judul']) ?></td>
                        <td><?= date('d M Y', strtotime($pengembalian['tgl_kembali'])) ?></td>
                        <td><span class="badge badge-pending">Proses Pengembalian</span></td>
                        <td>
                            <a href="konfirmasi_pengembalian.php?id=<?= $pengembalian['id'] ?>" class="btn btn-success"><i class="fas fa-check"></i> Konfirmasi</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>