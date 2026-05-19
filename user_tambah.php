<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    header("Location: " . (($_SESSION['level'] ?? '') === 'pegawai' ? 'dashboard_pegawai.php' : 'dashboard_pengunjung.php'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body">
    <main class="crud-container">
        <h1>Tambah Data User</h1>

        <form action="user_tambah_aksi.php" method="post" class="crud-form">
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" placeholder="Nama" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <label for="level">Level</label>
                <select id="level" name="level" required>
                    <option value="" disabled selected>Pilih level</option>
                    <option value="admin">Admin</option>
                    <option value="pegawai">Pegawai</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan</button>
                <a href="user.php" class="btn-back">Kembali</a>
            </div>
        </form>
    </main>
</body>

</html>
