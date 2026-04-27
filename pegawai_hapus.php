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
$nip = $_GET['nip'];
mysqli_query($koneksi, "DELETE FROM pegawai WHERE nip='$nip'");
header("location:pegawai.php?pesan=hapus");
?>
