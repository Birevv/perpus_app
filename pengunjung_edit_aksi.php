<?php
include 'koneksi.php';
$id_anggota = $_POST['id_anggota'];
$nama = $_POST['nama'];
$NIP_NIS = $_POST['NIP_NIS'];
$alamat = $_POST['alamat'];
$no_hp = $_POST['no_hp'];
mysqli_query($koneksi, "UPDATE anggota SET nama='$nama', NIP_NIS='$NIP_NIS', alamat='$alamat', no_hp='$no_hp' WHERE id_anggota='$id_anggota'");

header("location:pengunjung.php?pesan=update");
