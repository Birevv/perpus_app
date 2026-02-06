<?php
include 'koneksi.php';
$nip = $_POST['nip'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$gender = $_POST['gender'];

mysqli_query($koneksi, "UPDATE pegawai SET nama='$nama', alamat='$alamat', gender='$gender' WHERE nip='$nip'");

header("location:pegawai.php?pesan=update");
?>