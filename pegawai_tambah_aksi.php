<?php
include 'koneksi.php';
$nip = $_POST['nip'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$gender = $_POST['gender'];

mysqli_query($koneksi, "INSERT INTO pegawai VALUES ('$nip','$nama','$alamat','$gender')");
header("location:pegawai.php?pesan=input");
