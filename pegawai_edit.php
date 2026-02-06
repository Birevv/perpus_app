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
    <title>Edit Buku</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body">
    <main class="crud-container">
        <h1>Edit Data Pegawai</h1>

        <?php
        include 'koneksi.php';
        $nip = $_GET['nip'];
        $query = mysqli_query($koneksi, "SELECT * FROM pegawai WHERE nip='$nip'");
        $data = mysqli_fetch_assoc($query);
        ?>

        <form action="pegawai_edit_aksi.php" method="post" class="crud-form">

            <input type="hidden" name="nip" value="<?= $data['nip']; ?>">

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" value="<?= $data['nama']; ?>" required>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat" value="<?= $data['alamat']; ?>" required>
            </div>

            <div class="form-group">
                <label>Gender</label>
                <input type="text" name="gender" value="<?= $data['gender']; ?>" required>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="pegawai.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>