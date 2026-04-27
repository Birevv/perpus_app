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
    <title>Tambah Buku</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body">
    <main class="crud-container">
        <h1>Tambah Data Pegawai</h1>

        <form action="pegawai_tambah_aksi.php" method="post" class="crud-form">

            <div class="form-group">
                <label for="nip">NIP</label>
                <input type="text" id="nip" name="nip" placeholder="NIP" required>
            </div>

            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" placeholder="Nama" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" placeholder="Alamat" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <input type="text" id="gender" name="gender" placeholder="Gender" required>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan</button>
                <a href="pegawai.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>
