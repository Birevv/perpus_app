<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    header("Location: " . (($_SESSION['level'] ?? '') === 'pegawai' ? 'buku_pegawai.php' : 'dashboard_pengunjung.php'));
    exit;
}

include 'koneksi.php';
$isbn = $_GET['isbn'];
mysqli_query($koneksi, "DELETE FROM buku WHERE isbn='$isbn'");
header("location:buku.php?pesan=hapus");
?>
