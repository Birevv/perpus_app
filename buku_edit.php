<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    header("Location: " . (($_SESSION['level'] ?? '') === 'pegawai' ? 'buku_pegawai.php' : 'dashboard_pengunjung.php'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body">
    <main class="crud-container">
        <h1>Edit Data Buku</h1>

        <?php
        include 'koneksi.php';
        $isbn = $_GET['isbn'];
        $query = mysqli_query($koneksi, "SELECT * FROM buku WHERE isbn='$isbn'");
        $data = mysqli_fetch_assoc($query);
        ?>

        <form action="buku_edit_aksi.php" method="post" class="crud-form">

            <input type="hidden" name="isbn" value="<?= $data['isbn']; ?>">

            <div class="form-group">
                <label>Judul Buku</label>
                <input type="text" name="judul" value="<?= $data['judul']; ?>" required>
            </div>

            <div class="form-group">
                <label>Pengarang</label>
                <input type="text" name="pengarang" value="<?= $data['pengarang']; ?>" required>
            </div>

            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit" value="<?= $data['penerbit']; ?>" required>
            </div>

            <div class="form-group">
                <label>Tahun</label>
                <input type="text" name="tahun" value="<?= $data['tahun']; ?>" required>
            </div>

            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" value="<?= $data['genre']; ?>" required>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="buku.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>
