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

$error = $_GET['error'] ?? '';
$error_messages = [
    'required' => 'Semua data register wajib diisi.',
    'username' => 'Username sudah digunakan.',
    'nip_nis' => 'NIP/NIS sudah terdaftar.',
    'gender' => 'Gender tidak valid.',
    'save' => 'Register gagal disimpan. Silakan coba lagi.',
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>Register</title>
</head>

<body class="login-body">
    <div class="container">
        <div class="form-box register-box">
            <h1>Register</h1>

            <?php if (isset($error_messages[$error])): ?>
                <div class="login-alert" role="alert">
                    <?= htmlspecialchars($error_messages[$error]); ?>
                </div>
            <?php endif; ?>

            <form action="register_aksi.php" method="post">
                <input type="text" name="nama" placeholder="Nama" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="NIP_NIS" placeholder="NIP/NIS" required>
                <input type="text" name="no_hp" placeholder="No HP" required>
                <input type="text" name="alamat" placeholder="Alamat" required>
                <select name="gender" class="register-select" required>
                    <option value="" disabled selected>Pilih gender</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <input type="submit" value="Register" class="btn">
            </form>

            <p class="login-switch">Sudah punya akun? <a href="index.php">Login</a></p>
        </div>
    </div>
</body>

</html>
