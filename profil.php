<?php
session_start();
include 'config/koneksi.php';
include 'hitungdenda.php'; 


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$nama_user = $_SESSION['user']['nama'];
$email_user = $_SESSION['user']['email'] ?? '';


$query_buku = "SELECT COUNT(*) AS jumlah_buku FROM peminjaman WHERE user_id = $user_id AND status = 'Dipinjam'";
$result_buku = mysqli_query($koneksi, $query_buku);
$data_buku = mysqli_fetch_assoc($result_buku);
$jumlah_buku = $data_buku['jumlah_buku'] ?? 0;

// Hitung total denda
$query_denda = "SELECT SUM(denda) AS total_denda FROM peminjaman WHERE user_id = $user_id AND (status = 'Terlambat' OR status = 'Dipinjam')";
$result_denda = mysqli_query($koneksi, $query_denda);
$data_denda = mysqli_fetch_assoc($result_denda);
$total_denda = $data_denda['total_denda'] ?? 0;

// Proses ganti password
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password_lama'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Ambil password dari database
    $query_password = "SELECT password FROM users WHERE id = $user_id";
    $result_password = mysqli_query($koneksi, $query_password);
    $data_password = mysqli_fetch_assoc($result_password);

    // Verifikasi password lama
    if (password_verify($password_lama, $data_password['password'])) {
        if ($password_baru === $konfirmasi_password) {
            // Validasi panjang password
            if (strlen($password_baru) < 8) {
                $error_message = "Password harus minimal 8 karakter.";
            } else {
                // Hash password baru
                $password_baru_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

                // Update password di database
                $update_password = "UPDATE users SET password = '$password_baru_hashed' WHERE id = $user_id";
                if (mysqli_query($koneksi, $update_password)) {
                    $success_message = "Password berhasil diubah.";
                } else {
                    $error_message = "Gagal mengubah password. Silakan coba lagi.";
                }
            }
        } else {
            $error_message = "Konfirmasi password tidak cocok.";
        }
    } else {
        $error_message = "Password lama salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Perpustakaan</h2>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="dashboard_user.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="buku_saya.php"><i class="fas fa-book"></i> Buku Saya</a>
                    </li>
                    <li>
                        <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                    </li>
                    <li >
                        <a href="pembayaran.php"><i class="fas fa-money-bill"></i> Bayar Denda</a>
                    </li>
                    <li class="active">
                        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    </li>
                    <li>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content">
                <h2>Profil Saya</h2>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $success_message ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                    </div>
                <?php endif; ?>
                
               <div class="profile-container">
    <div class="profile-card profile-main">
        <div class="profile-header">
            <div class="profile-avatar" style="background-color: <?= generatePastelColor($nama_user) ?>">
                <?= strtoupper(substr($nama_user, 0, 1)) ?>
            </div>
            <div class="profile-info">
                <h3><?= htmlspecialchars($nama_user) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($email_user) ?></p>
                <div class="member-since">
                    <i class="fas fa-calendar-alt"></i>
                    Anggota sejak <?= date('d F Y', strtotime($_SESSION['user']['created_at'] ?? 'now')) ?>
                </div>
            </div>
            <div class="status-badge active">
                <i class="fas fa-check-circle"></i> Aktif
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-item books-borrowed">
                <div class="stat-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $jumlah_buku ?></div>
                    <div class="stat-label">Buku Dipinjam</div>
                </div>
            </div>
            <div class="stat-item fines">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">Rp <?= number_format($total_denda, 0, ',', '.') ?></div>
                    <div class="stat-label">
                        Total Denda 
                        <?php if ($total_denda > 0): ?>
                        <span class="denda-badge">
                            <i class="fas fa-times"></i> BELUM LUNAS
                        </span>
                        <?php else: ?>
                        <span class="paid-badge">
                            <i class="fas fa-check"></i> LUNAS
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="profile-actions">
            <a href="buku_saya.php" class="btn-action">
                <i class="fas fa-book"></i> Buku Saya
            </a>
          
        </div>
    </div>
    
    <!-- Change Password Card -->
    <div class="profile-card password-card">
        <h3 class="section-title">
            <i class="fas fa-key"></i> Ganti Password
        </h3>
        <form method="POST" class="form">
            <div class="form-group password-toggle">
                <label for="password_lama">
                    <i class="fas fa-lock"></i> Password Lama
                </label>
                <div class="input-with-icon">
                    <input type="password" id="password_lama" name="password_lama" required placeholder="Masukkan password lama">
                    <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('password_lama')"></i>
                </div>
            </div>
            
            <div class="form-group password-toggle">
                <label for="password_baru">
                    <i class="fas fa-lock"></i> Password Baru
                </label>
                <div class="input-with-icon">
                    <input type="password" id="password_baru" name="password_baru" required placeholder="Masukkan password baru (min. 8 karakter)">
                    <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('password_baru')"></i>
                </div>
                <div class="password-strength-meter">
                    <div class="strength-bar"></div>
                    <small class="text-muted">Kekuatan password</small>
                </div>
            </div>
            
            <div class="form-group password-toggle">
                <label for="konfirmasi_password">
                    <i class="fas fa-lock"></i> Konfirmasi Password Baru
                </label>
                <div class="input-with-icon">
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required placeholder="Konfirmasi password baru">
                    <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('konfirmasi_password')"></i>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<style>
/* Base Styles */
.profile-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}

@media (min-width: 768px) {
    .profile-container {
        grid-template-columns: 1fr 1fr;
    }
}

.profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

/* Profile Header */
.profile-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    position: relative;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
    margin-right: 1.25rem;
    flex-shrink: 0;
}

.profile-info h3 {
    margin: 0;
    font-size: 1.5rem;
    color: #2c3e50;
}

.profile-info p.text-muted {
    margin: 0.25rem 0;
    color: #7f8c8d;
    font-size: 0.9rem;
}

.member-since {
    font-size: 0.85rem;
    color: #95a5a6;
    margin-top: 0.5rem;
}

.member-since i {
    margin-right: 0.3rem;
}

.status-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #e8f8f5;
    color: #27ae60;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge i {
    margin-right: 0.3rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin: 1.5rem 0;
}

.stat-item {
    padding: 1rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
}

.stat-item.books-borrowed {
    background: #f0f7ff;
    border-left: 4px solid #3498db;
}

.stat-item.fines {
    background: #fff5f5;
    border-left: 4px solid #e74c3c;
}

.stat-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.books-borrowed .stat-icon {
    background: #d4e6ff;
    color: #2980b9;
}

.fines .stat-icon {
    background: #ffdfdf;
    color: #c0392b;
}

.stat-value {
    font-size: 1.4rem;
    font-weight: bold;
    color: #2c3e50;
}

.stat-label {
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-top: 0.2rem;
}

.denda-badge {
    background: #ffebee;
    color: #e74c3c;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-left: 0.5rem;
    font-weight: 500;
}

.denda-badge i {
    margin-right: 0.2rem;
}

.paid-badge {
    background: #e8f8f5;
    color: #27ae60;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-left: 0.5rem;
    font-weight: 500;
}

/* Profile Actions */
.profile-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.btn-action {
    flex: 1;
    padding: 0.7rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    color: #3498db;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-action:hover {
    background: #f8fafc;
    border-color: #3498db;
}

/* Password Card */
.password-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.section-title {
    color: #2c3e50;
    margin-top: 0;
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #34495e;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.input-with-icon {
    position: relative;
}

.input-with-icon input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: border-color 0.3s ease;
    padding-right: 2.5rem;
}

.input-with-icon input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.password-toggle-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #95a5a6;
    cursor: pointer;
}

.password-strength-meter {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 4px;
    background: #ecf0f1;
    border-radius: 2px;
    margin-bottom: 0.25rem;
    overflow: hidden;
}

.strength-bar::after {
    content: '';
    display: block;
    height: 100%;
    width: 0;
    background: #e74c3c;
    transition: width 0.3s ease, background 0.3s ease;
}

.btn-primary {
    background: #3498db;
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    background: #2980b9;
}

<?php 
function generatePastelColor($str) {
    $hash = md5($str);
    $r = hexdec(substr($hash, 0, 2)) % 128 + 128;
    $g = hexdec(substr($hash, 2, 2)) % 128 + 128;
    $b = hexdec(substr($hash, 4, 2)) % 128 + 128;
    return "rgb($r, $g, $b)";
}
?>
</style>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling;
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}


document.getElementById('password_baru').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.querySelector('.strength-bar');
    let strength = 0;
    
    if (password.length > 0) strength += 20;
    if (password.length >= 8) strength += 20;
    if (/[A-Z]/.test(password)) strength += 20;
    if (/\d/.test(password)) strength += 20;
    if (/[^A-Za-z0-9]/.test(password)) strength += 20;
    
    strengthBar.style.width = strength + '%';
    
    if (strength < 40) {
        strengthBar.style.backgroundColor = '#e74c3c';
    } else if (strength < 70) {
        strengthBar.style.backgroundColor = '#f39c12';
    } else {
        strengthBar.style.backgroundColor = '#2ecc71';
    }
});
</script>