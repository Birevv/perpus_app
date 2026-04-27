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
$id_anggota = $_GET['id_anggota'];
mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota='$id_anggota'");
header("location:pengunjung.php?pesan=hapus");
