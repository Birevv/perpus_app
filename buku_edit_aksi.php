<?php
include 'koneksi.php';
$isbn = $_POST['isbn'];
$judul = $_POST['judul'];
$pengarang = $_POST['pengarang'];
$penerbit = $_POST['penerbit'];
$tahun = $_POST['tahun'];
$genre = $_POST['genre'];

mysqli_query($koneksi, "UPDATE buku SET judul='$judul', pengarang='$pengarang', penerbit='$penerbit', tahun='$tahun', genre='$genre' WHERE isbn='$isbn'");

header("location:buku.php?pesan=update");
?>