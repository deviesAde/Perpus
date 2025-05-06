<?php
session_start();
include 'config/koneksi.php';

// Function to display styled alerts
function displayAlert($message, $type = 'error') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    displayAlert("Anda harus login terlebih dahulu.", "error");
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Cek apakah ID buku ada di URL
if (!isset($_GET['id'])) {
    displayAlert("ID buku tidak ditemukan.", "error");
    header("Location: dashboard_user.php");
    exit;
}

$book_id = intval($_GET['id']);

// Ambil data buku
$query = "SELECT * FROM books WHERE id = $book_id";
$result = mysqli_query($koneksi, $query);
$buku = mysqli_fetch_assoc($result);

// Cek apakah buku ditemukan
if (!$buku) {
    displayAlert("Buku tidak ditemukan.", "error");
    header("Location: dashboard_user.php");
    exit;
}

// Cek stok buku
if ($buku['stok'] <= 0) {
    displayAlert("Stok buku habis.", "error");
    header("Location: dashboard_user.php");
    exit;
}


$cek_pinjam = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE user_id = $user_id AND book_id = $book_id AND status IN ('Dipinjam', 'Proses Pengembalian')");
if (mysqli_num_rows($cek_pinjam) > 0) {
    displayAlert("Anda sudah meminjam buku ini dan belum mengembalikannya.", "error");
    header("Location: dashboard_user.php");
    exit;
}


$tgl_pinjam = date('Y-m-d');
$tgl_kembali = date('Y-m-d', strtotime('+7 days'));

$insert = "INSERT INTO peminjaman (user_id, book_id, tgl_pinjam, tgl_kembali, status, denda)
           VALUES ($user_id, $book_id, '$tgl_pinjam', '$tgl_kembali', 'Pending', 0)";

if (mysqli_query($koneksi, $insert)) {
    displayAlert("Permintaan peminjaman buku berhasil! Harap menunggu konfirmasi.", "success");
    header("Location: dashboard_user.php");
} else {
    displayAlert("Gagal memproses permintaan peminjaman. Silakan coba lagi.", "error");
    header("Location: dashboard_user.php");
}
exit;
?>