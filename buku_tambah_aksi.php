<?php
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
