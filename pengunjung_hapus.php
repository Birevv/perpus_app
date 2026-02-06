<?php
include 'koneksi.php';
$id_anggota = $_GET['id_anggota'];
mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota='$id_anggota'");
header("location:pengunjung.php?pesan=hapus");
