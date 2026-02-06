<?php   
include 'koneksi.php';
$nip = $_GET['nip'];
mysqli_query($koneksi, "DELETE FROM pegawai WHERE nip='$nip'");
header("location:pegawai.php?pesan=hapus");
?>