<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$peminjaman_id = intval($_GET['id']);


$is_admin = $_SESSION['user']['role'] === 'admin';

$cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id = $peminjaman_id");
$data = mysqli_fetch_assoc($cek);

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

// Hanya admin yang dapat mengubah status menjadi "Dikembalikan"
if ($is_admin) {
    if ($data['status'] == 'Lunas') {
        mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Dikembalikan' WHERE id = $peminjaman_id");
        mysqli_query($koneksi, "UPDATE books SET stok = stok + 1 WHERE id = " . $data['book_id']);
        header("Location: buku_saya.php?pesan=dikembalikan");
        exit;
    } else {
        echo "Status buku tidak valid untuk dikembalikan.";
        exit;
    }
} else {
    echo "Hanya admin yang dapat mengembalikan buku.";
    exit;
}