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
        <h1>Tambah Data Buku</h1>

        <form action="buku_tambah_aksi.php" method="post" class="crud-form">

            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn" placeholder="ISBN" required>
            </div>

            <div class="form-group">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul" placeholder="Judul Buku" required>
            </div>

            <div class="form-group">
                <label for="pengarang">Pengarang</label>
                <input type="text" id="pengarang" name="pengarang" placeholder="Pengarang" required>
            </div>

            <div class="form-group">
                <label for="penerbit">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit" placeholder="Penerbit" required>
            </div>

            <div class="form-group">
                <label for="tahun">Tahun</label>
                <input type="text" id="tahun" name="tahun" placeholder="Tahun" required>
            </div>

            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" placeholder="Genre" required>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan</button>
                <a href="buku.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>