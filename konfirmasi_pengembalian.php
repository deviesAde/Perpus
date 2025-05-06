<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$peminjaman_id = intval($_GET['id']);


$cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id = $peminjaman_id");
$data = mysqli_fetch_assoc($cek);

if (!$data) {
    $_SESSION['alert'] = [
        'message' => 'Data peminjaman tidak ditemukan.',
        'type' => 'error'
    ];
    header("Location: dashboard_admin.php#konfirmasi-pengembalian");
    exit;
}

// Validasi status peminjaman
if ($data['status'] == 'Proses Pengembalian') {
  
    $update_peminjaman = mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Dikembalikan' WHERE id = $peminjaman_id");

    // Tambahkan stok buku
    $update_buku = mysqli_query($koneksi, "UPDATE books SET stok = stok + 1 WHERE id = " . $data['book_id']);

    if ($update_peminjaman && $update_buku) {
        $_SESSION['alert'] = [
            'message' => 'Pengembalian berhasil dikonfirmasi.',
            'type' => 'success'
        ];
    } else {
        $_SESSION['alert'] = [
            'message' => 'Gagal mengkonfirmasi pengembalian.',
            'type' => 'error'
        ];
    }
    header("Location: dashboard_admin.php#konfirmasi-pengembalian");
    exit;
} else {
    $_SESSION['alert'] = [
        'message' => 'Pengembalian tidak valid untuk dikonfirmasi.',
        'type' => 'error'
    ];
    header("Location: dashboard_admin.php#konfirmasi-pengembalian");
    exit;
}