<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = intval($_GET['id']);

// Hapus user berdasarkan ID
$query = "DELETE FROM users WHERE id = $user_id";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['alert'] = [
        'message' => 'User berhasil dihapus.',
        'type' => 'success'
    ];
} else {
    $_SESSION['alert'] = [
        'message' => 'Gagal menghapus user.',
        'type' => 'error'
    ];
}

header("Location: dashboard_admin.php#crud-user");
exit;
?>