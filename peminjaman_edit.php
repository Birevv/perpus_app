<?php
session_start();
include 'koneksi.php';

$id = $_GET['id_peminjaman'];

$query = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data tidak ditemukan";
    exit;
}
?>

<h3>Edit Peminjaman Buku</h3>

<form action="peminjaman_edit_aksi.php" method="post">
    <input type="hidden" name="id_peminjaman" value="<?= $data['id_peminjaman']; ?>">

    <label>Anggota</label><br>
    <input type="text" name="id_anggota" value="<?= $data['id_anggota']; ?>" readonly><br><br>

    <label>ISBN Buku</label><br>
    <input type="text" name="isbn" value="<?= $data['isbn']; ?>" readonly><br><br>

    <label>Tanggal Pinjam</label><br>
    <input type="date" name="tgl_pinjam" value="<?= $data['tgl_pinjam']; ?>" required><br><br>

    <label>Tanggal Kembali</label><br>
    <input type="date" name="tgl_kembali" value="<?= $data['tgl_kembali']; ?>"><br><br>

    <button type="submit">Update</button>
    <a href="peminjaman.php">Batal</a>
</form>