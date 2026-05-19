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

$id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota'] ?? '');
$nama = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
$NIP_NIS = mysqli_real_escape_string($koneksi, $_POST['NIP_NIS'] ?? '');
$gender = mysqli_real_escape_string($koneksi, $_POST['gender'] ?? '');
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat'] ?? '');
$no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp'] ?? '');

if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
    die('Gender tidak valid.');
}

mysqli_query(
    $koneksi,
    "INSERT INTO anggota (id_anggota, nama, NIP_NIS, gender, alamat, no_hp)
     VALUES ('$id_anggota','$nama','$NIP_NIS','$gender','$alamat','$no_hp')"
);
header("location:pengunjung.php?pesan=input");
