<?php
session_start();

if (isset($_SESSION['username'])) {
    if (($_SESSION['level'] ?? '') === 'admin') {
        header("Location: dashboard.php");
    } elseif (($_SESSION['level'] ?? '') === 'pegawai') {
        header("Location: dashboard_pegawai.php");
    } elseif (($_SESSION['level'] ?? '') === 'user') {
        header("Location: dashboard_pengunjung.php");
    } else {
        header("Location: logout.php");
    }
    exit();
}

$login_error = ($_GET['error'] ?? '') === 'login';
$register_success = ($_GET['success'] ?? '') === 'register';
$forgot_password_message = rawurlencode('Halo Admin, Saya Lupa Password Akun Perpus. Mohon Bantuannya');
$forgot_password_whatsapp = 'https://wa.me/6281947385200?text=' . $forgot_password_message;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=20260513-8">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Login</title>
</head>

<body class="login-body">
    <div class="container">
        <div class="form-box" id="login-form">
            <h1>Login</h1>
            <?php if ($register_success): ?>
                <div class="login-alert login-alert-success" role="alert">
                    Register berhasil! Silakan login.
                </div>
            <?php endif; ?>
            <?php if ($login_error): ?>
                <div class="login-alert" role="alert">
                    Username/Password yang anda masukan salah!
                </div>
            <?php endif; ?>
            <form action="login_aksi.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <div class="login-form-links">
                    <p class="login-switch">Belum punya akun? <a href="register.php">Register</a></p>
                    <p class="forgot-password">
                        <a class="forgot-password-link" href="<?php echo htmlspecialchars($forgot_password_whatsapp, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Lupa Password?</a>
                    </p>
                </div>
                <input type="submit" value="Login" class="btn">
            </form>
        </div>
    </div>
</body>
</html>
