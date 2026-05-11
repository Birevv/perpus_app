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
$nip = $_POST['nip'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$gender = $_POST['gender'];

if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
    die('Gender tidak valid.');
}

mysqli_query($koneksi, "UPDATE pegawai SET nama='$nama', alamat='$alamat', gender='$gender' WHERE nip='$nip'");

header("location:pegawai.php?pesan=update");
?>
