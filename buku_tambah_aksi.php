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

include 'koneksi.php';
$isbn = mysqli_real_escape_string($koneksi, $_POST['isbn']);
$judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
$pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
$penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
$tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
$genre = mysqli_real_escape_string($koneksi, $_POST['genre']);
$stok = (int) $_POST['stok'];

mysqli_query(
    $koneksi,
    "INSERT INTO buku (isbn, judul, pengarang, penerbit, tahun, genre, stok)
     VALUES ('$isbn', '$judul', '$pengarang', '$penerbit', '$tahun', '$genre', '$stok')"
);
header("location:buku.php?pesan=input");

?>
