<?php
session_start();
include 'config/koneksi.php';

// Cek apakah user sudah login
// if (isset($_SESSION['user'])) {
//     if ($_SESSION['user']['role'] == 'admin') {
//         header("Location: dashboard_admin.php");
//     } else {
//         header("Location: dashboard_user.php");
//     }
//     exit;
// }

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $email = mysqli_real_escape_string($koneksi, $email);

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($koneksi, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user'] = $user;

        if ($user['role'] == 'admin') {
            header("Location:dashboard_admin.php");
        } else {
            header("Location: dashboard_user.php");
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="card-header">
                <h2>Welcome Back</h2>
                <p>Login to access your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="register-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-register">Login</button>
            </form>
            
            <div class="card-footer">
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
            </div>
        </div>
    </div>
    
    <script src="js/app.js"></script>
</body>
</html>