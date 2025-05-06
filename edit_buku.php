<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$book_id = intval($_GET['id']);

// Ambil data buku berdasarkan ID
$query = "SELECT * FROM books WHERE id = $book_id";
$result = mysqli_query($koneksi, $query);
$buku = mysqli_fetch_assoc($result);

if (!$buku) {
    $_SESSION['alert'] = [
        'message' => 'Buku tidak ditemukan.',
        'type' => 'error'
    ];
    header("Location: dashboard_admin.php#crud-buku");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $stok = intval($_POST['stok']);

    $update = "UPDATE books SET judul = '$judul', pengarang = '$pengarang', stok = $stok WHERE id = $book_id";
    if (mysqli_query($koneksi, $update)) {
        $_SESSION['alert'] = [
            'message' => 'Buku berhasil diperbarui.',
            'type' => 'success'
        ];
    } else {
        $_SESSION['alert'] = [
            'message' => 'Gagal memperbarui buku.',
            'type' => 'error'
        ];
    }
    header("Location: dashboard_admin.php#crud-buku");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f5f9ff;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 100, 0.1);
        }
        h1 {
            color: #2c5fa8;
            margin-top: 0;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e9ff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c5fa8;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #d0d9e8;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #2c5fa8;
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 95, 168, 0.1);
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        button, .back-button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        button {
            background-color: #2c5fa8;
            color: white;
        }
        button:hover {
            background-color: #1e4a8a;
            transform: translateY(-2px);
        }
        .back-button {
            background-color: white;
            color: #2c5fa8;
            border: 1px solid #2c5fa8;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .back-button:hover {
            background-color: #f0f5ff;
            transform: translateY(-2px);
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            .button-group {
                flex-direction: column;
            }
            button, .back-button {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Buku</h1>
        <form method="POST">
            <div class="form-group">
                <label for="judul">Judul Buku:</label>
                <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($buku['judul']) ?>" required>
            </div>

            <div class="form-group">
                <label for="pengarang">Pengarang:</label>
                <input type="text" id="pengarang" name="pengarang" value="<?= htmlspecialchars($buku['pengarang']) ?>" required>
            </div>

            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($buku['stok']) ?>" required>
            </div>

            <div class="button-group">
                <a href="dashboard_admin.php#crud-buku" class="back-button">Kembali</a>
                <button type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>
</html>