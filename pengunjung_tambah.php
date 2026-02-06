<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
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
        <h1>Tambah Data Pengunjung</h1>

        <form action="pengunjung_tambah_aksi.php" method="post" class="crud-form">

            <div class="form-group">
                <label for="id_anggota">ID Anggota</label>
                <input type="text" id="id_anggota" name="id_anggota" placeholder="ID Anggota" required>
            </div>

            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" placeholder="Nama" required>
            </div>

            <div class="form-group">
                <label for="NIP_NIS">NIP/NIS</label>
                <input type="text" id="NIP_NIS" name="NIP_NIS" placeholder="NIP/NIS" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" placeholder="Alamat" required>
            </div>

            <div class="form-group">
                <label for="no_hp">No HP</label>
                <input type="text" id="no_hp" name="no_hp" placeholder="No HP" required>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan</button>
                <a href="pengunjung.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>