<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$peminjaman_id = intval($_GET['id']);

// Cek apakah buku milik user
$cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id = $peminjaman_id AND user_id = $user_id");
$data = mysqli_fetch_assoc($cek);

if (!$data) {
    $_SESSION['alert'] = [
        'message' => 'Data tidak ditemukan atau bukan milik Anda.',
        'type' => 'error'
    ];
    header("Location: buku_saya.php");
    exit;
}

// Validasi status peminjaman
if ($data['status'] == 'Dipinjam' || $data['status'] == 'Terlambat' || $data['status'] == 'Lunas') {
    // Update status menjadi 'Proses Pengembalian'
    $update = mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Proses Pengembalian' WHERE id = $peminjaman_id");

    if ($update) {
        $_SESSION['alert'] = [
            'message' => 'Pengajuan pengembalian berhasil! Menunggu konfirmasi admin.',
            'type' => 'success'
        ];
    } else {
        $_SESSION['alert'] = [
            'message' => 'Gagal mengajukan pengembalian. Silakan coba lagi.',
            'type' => 'error'
        ];
    }
    header("Location: buku_saya.php");
    exit;
} else {
    $_SESSION['alert'] = [
        'message' => 'Buku tidak dapat diajukan untuk pengembalian.',
        'type' => 'error'
    ];
    header("Location: buku_saya.php");
    exit;
}