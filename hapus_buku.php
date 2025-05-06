<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$book_id = intval($_GET['id']);

// Hapus buku berdasarkan ID
$query = "DELETE FROM books WHERE id = $book_id";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['alert'] = [
        'message' => 'Buku berhasil dihapus.',
        'type' => 'success'
    ];
} else {
    $_SESSION['alert'] = [
        'message' => 'Gagal menghapus buku.',
        'type' => 'error'
    ];
}

header("Location: dashboard_admin.php#crud-buku");
exit;
?>