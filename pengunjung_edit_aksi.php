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
include 'anggota_schema.php';

ensure_anggota_gender_column($koneksi);

$id_anggota = $_POST['id_anggota'];
$nama = $_POST['nama'];
$NIP_NIS = $_POST['NIP_NIS'];
$gender = $_POST['gender'];
$alamat = $_POST['alamat'];
$no_hp = $_POST['no_hp'];

if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
    die('Gender tidak valid.');
}

mysqli_query($koneksi, "UPDATE anggota SET nama='$nama', NIP_NIS='$NIP_NIS', gender='$gender', alamat='$alamat', no_hp='$no_hp' WHERE id_anggota='$id_anggota'");

header("location:pengunjung.php?pesan=update");
