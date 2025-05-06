<?php
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>