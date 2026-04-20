<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

include 'koneksi.php';

$id = $_GET['id_peminjaman'];

$query = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data tidak ditemukan";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body">
    <main class="crud-container">
        <h1>Edit Peminjaman Buku</h1>

        <form action="peminjaman_edit_aksi.php" method="post" class="crud-form">
            <input type="hidden" name="id_peminjaman" value="<?= $data['id_peminjaman']; ?>">

            <div class="form-group">
                <label>Anggota</label>
                <input type="text" name="id_anggota" value="<?= $data['id_anggota']; ?>" readonly>
            </div>

            <div class="form-group">
                <label>ISBN Buku</label>
                <input type="text" name="isbn" value="<?= $data['isbn']; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="<?= $data['tgl_pinjam']; ?>" required>
            </div>

            <div class="form-group">
                <label>Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" value="<?= $data['tgl_kembali']; ?>">
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Update</button>
                <a href="peminjaman.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>