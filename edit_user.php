<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = intval($_GET['id']);

// Ambil data user berdasarkan ID
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $_SESSION['alert'] = [
        'message' => 'User tidak ditemukan.',
        'type' => 'error'
    ];
    header("Location: dashboard_admin.php#crud-user");
    exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    $update = "UPDATE users SET nama = '$nama', email = '$email', role = '$role' WHERE id = $user_id";
    if (mysqli_query($koneksi, $update)) {
        $_SESSION['alert'] = [
            'message' => 'User berhasil diperbarui.',
            'type' => 'success'
        ];
    } else {
        $_SESSION['alert'] = [
            'message' => 'Gagal memperbarui user.',
            'type' => 'error'
        ];
    }
    header("Location: dashboard_admin.php#crud-user");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
        input[type="email"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 16px;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(30, 136, 229, 0.2);
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
        <h1>Edit User</h1>
        <form method="POST" id="userForm">
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>

            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>

    <div id="notification" class="notification"></div>

    <script>
       
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type} show`;

            setTimeout(() => {
                notification.classList.remove('show');
            }, 1000);
        }

        
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