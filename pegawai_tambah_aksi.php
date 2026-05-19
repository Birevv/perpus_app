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
include 'pegawai_schema.php';

ensure_pegawai_id_column($koneksi);

$id_pegawai = mysqli_real_escape_string($koneksi, generate_next_id_pegawai($koneksi));
$nip = mysqli_real_escape_string($koneksi, $_POST['nip'] ?? '');
$nama = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat'] ?? '');
$gender = mysqli_real_escape_string($koneksi, $_POST['gender'] ?? '');

if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
    die('Gender tidak valid.');
}

mysqli_query(
    $koneksi,
    "INSERT INTO pegawai (id_pegawai, nip, nama, alamat, gender)
     VALUES ('$id_pegawai', '$nip', '$nama', '$alamat', '$gender')"
);
header("location:pegawai.php?pesan=input");
