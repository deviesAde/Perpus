<?php
include 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'user'; 

    // Validate inputs
    if (empty($nama) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password_hash', '$role')";

        if (mysqli_query($koneksi, $query)) {
            $success = "Registrasi berhasil!";
        } else {
            $error = "Error: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="card-header">
                <h2>Create Your Account</h2>
                <p>Join our Library</p>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?> <a href='login.php'>Login now</a>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="register-form">
                <div class="form-group">
                    <label for="nama">Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="nama" name="nama" placeholder="Enter your full name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                       
                    </div>
                    <div class="password-strength">
                        <div id="strength-bar"></div>
                        <span id="strength-text"></span>
                    </div>
                </div>
                
                <button type="submit" class="btn-register">Sign Up</button>
            </form>
            
            <div class="card-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
    
    <script src="js/app.js"></script>
</body>
</html>