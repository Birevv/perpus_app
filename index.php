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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Login</title>
</head>

<body class="login-body">
    <div class="container">
        <div class="form-box" id="login-form">
            <h1>Login</h1>
            <?php if ($login_error): ?>
                <div class="login-alert" role="alert">
                    Username/Password yang anda masukan salah!
                </div>
            <?php endif; ?>
            <form action="login_aksi.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Login" class="btn">
            </form>
        </div>
    </div>
</body>
</html>
