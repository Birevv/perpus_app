<?php
include 'koneksi.php';
$isbn = $_POST['isbn'];
$judul = $_POST['judul'];
$pengarang = $_POST['pengarang'];
$penerbit = $_POST['penerbit'];
$tahun = $_POST['tahun'];
$genre = $_POST['genre'];

mysqli_query($koneksi, "INSERT INTO buku VALUES ('$isbn','$judul','$pengarang','$penerbit','$tahun','$genre')");
header("location:buku.php?pesan=input");

?>
