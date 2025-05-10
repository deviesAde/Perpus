<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $stok = intval($_POST['stok']);
    $tahun = intval($_POST['tahun']);

    $insert = "INSERT INTO books (judul, pengarang, deskripsi, stok, tahun) 
               VALUES ('$judul', '$pengarang', '$deskripsi', $stok, $tahun)";
    if (mysqli_query($koneksi, $insert)) {
        $_SESSION['alert'] = [
            'message' => 'Buku berhasil ditambahkan.',
            'type' => 'success'
        ];
    } else {
        $_SESSION['alert'] = [
            'message' => 'Gagal menambahkan buku.',
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
    <title>Tambah Buku</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e88e5;
            --primary-dark: #1565c0;
            --light-color: #f5f9ff;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: var(--white);
            padding: 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
        
        h1 {
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 16px;
            transition: border 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(30, 136, 229, 0.2);
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 100%;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }
        
        .notification.show {
            opacity: 1;
        }
        
        .notification.success {
            background-color: #4caf50;
        }
        
        .notification.error {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Buku Baru</h1>
        <form method="POST" id="bookForm">
            <div class="form-group">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul" required>
            </div>
            
            <div class="form-group">
                <label for="pengarang">Pengarang</label>
                <input type="text" id="pengarang" name="pengarang" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="tahun">Tahun Terbit</label>
                <input type="number" id="tahun" name="tahun" min="1900" max="<?php echo date('Y'); ?>" required>
            </div>
            
            <button type="submit" class="btn">Tambah Buku</button>
        </form>
    </div>

    <div id="notification" class="notification"></div>

    <script>
       
        document.getElementById('bookForm').addEventListener('submit', function(e) {
            const tahunInput = document.getElementById('tahun');
            const currentYear = new Date().getFullYear();
            
            if (tahunInput.value < 1900 || tahunInput.value > currentYear) {
                e.preventDefault();
                showNotification('Tahun terbit harus antara 1900 dan ' + currentYear, 'error');
                tahunInput.focus();
            }
            
            const stokInput = document.getElementById('stok');
            if (stokInput.value < 0) {
                e.preventDefault();
                showNotification('Stok tidak boleh negatif', 'error');
                stokInput.focus();
            }
        });
        
       
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type} show`;
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Check for session alert and show notification
        <?php if (isset($_SESSION['alert'])): ?>
            window.onload = function() {
                showNotification(
                    '<?php echo $_SESSION['alert']['message']; ?>', 
                    '<?php echo $_SESSION['alert']['type']; ?>'
                );
                <?php unset($_SESSION['alert']); ?>
            };
        <?php endif; ?>
    </script>
</body>
</html>