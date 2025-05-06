<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: buku_saya.php");
    exit;
}

$peminjaman_id = intval($_GET['id']);

// Ambil data peminjaman
$query = "SELECT * FROM peminjaman WHERE id = $peminjaman_id";
$result = mysqli_query($koneksi, $query);
$peminjaman = mysqli_fetch_assoc($result);

if (!$peminjaman) {
    echo "Data peminjaman tidak ditemukan.";
    exit;
}

// Cek apakah sudah mencapai batas perpanjangan
if ($peminjaman['perpanjangan'] >= 2) {
    $_SESSION['alert'] = [
        'message' => 'Perpanjangan sudah mencapai batas maksimal (2 kali).',
        'type' => 'error'
    ];
    header("Location: buku_saya.php");
    exit;
}


$tgl_kembali_baru = date('Y-m-d', strtotime($peminjaman['tgl_kembali'] . ' +3 days'));


$perpanjangan_baru = $peminjaman['perpanjangan'] + 1;
$update = "UPDATE peminjaman SET tgl_kembali = '$tgl_kembali_baru', perpanjangan = $perpanjangan_baru WHERE id = $peminjaman_id";

if (mysqli_query($koneksi, $update)) {
    $_SESSION['alert'] = [
        'message' => 'Perpanjangan berhasil! Tanggal kembali diperbarui.',
        'type' => 'success'
    ];
} else {
    $_SESSION['alert'] = [
        'message' => 'Gagal memperpanjang peminjaman.',
        'type' => 'error'
    ];
}

header("Location: buku_saya.php");
exit;
?>