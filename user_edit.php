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
    <title>Edit User</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body">
    <main class="crud-container">
        <h1>Edit Data User</h1>

        <?php
        include 'koneksi.php';
        $id_user = mysqli_real_escape_string($koneksi, $_GET['id_user'] ?? '');
        $query = mysqli_query($koneksi, "SELECT * FROM `user` WHERE id_user='$id_user'");
        $data = mysqli_fetch_assoc($query);

        if (!$data) {
            die('Data user tidak ditemukan.');
        }
        ?>

        <form action="user_edit_aksi.php" method="post" class="crud-form">
            <div class="form-group">
                <label for="id_user">ID User</label>
                <input type="text" id="id_user" name="id_user" value="<?= htmlspecialchars($data['id_user']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($data['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" value="<?= htmlspecialchars($data['password']); ?>" required>
            </div>

            <div class="form-group">
                <label for="level">Level</label>
                <select id="level" name="level" required>
                    <option value="admin" <?= $data['level'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="pegawai" <?= $data['level'] === 'pegawai' ? 'selected' : ''; ?>>Pegawai</option>
                    <option value="user" <?= $data['level'] === 'user' ? 'selected' : ''; ?>>User</option>
                </select>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="user.php" class="btn-back">Kembali</a>
            </div>
        </form>
    </main>
</body>

</html>
