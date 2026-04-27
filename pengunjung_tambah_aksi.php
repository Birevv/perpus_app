<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    header("Location: " . (($_SESSION['level'] ?? '') === 'pegawai' ? 'dashboard_pegawai.php' : 'dashboard_pengunjung.php'));
    exit;
}

include 'koneksi.php';
$id_anggota = $_POST['id_anggota'];
$nama = $_POST['nama'];
$NIP_NIS = $_POST['NIP_NIS'];
$alamat = $_POST['alamat'];
$no_hp = $_POST['no_hp'];

mysqli_query($koneksi, "INSERT INTO anggota VALUES ('$id_anggota','$nama','$NIP_NIS','$alamat','$no_hp')");
header("location:pengunjung.php?pesan=input");
