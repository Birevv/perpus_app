
<?php   
include 'koneksi.php';
$isbn = $_GET['isbn'];
mysqli_query($koneksi, "DELETE FROM buku WHERE isbn='$isbn'");
header("location:buku.php?pesan=hapus");
?>