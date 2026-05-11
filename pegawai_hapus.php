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
$nip = mysqli_real_escape_string($koneksi, $_GET['nip'] ?? '');
$pegawai_query = mysqli_query($koneksi, "SELECT * FROM pegawai WHERE nip='$nip' LIMIT 1");
$pegawai = $pegawai_query ? mysqli_fetch_assoc($pegawai_query) : null;

if (!$pegawai) {
    header("location:pegawai.php?pesan=hapus");
    exit;
}

$nama = mysqli_real_escape_string($koneksi, $pegawai['nama']);

mysqli_begin_transaction($koneksi);

$hapus_pegawai = mysqli_query($koneksi, "DELETE FROM pegawai WHERE nip='$nip'");
$hapus_user = mysqli_query(
    $koneksi,
    "DELETE FROM `user`
     WHERE level IN ('admin', 'pegawai')
        AND (username='$nip' OR password='$nip' OR nama='$nama')"
);

if (!$hapus_pegawai || !$hapus_user) {
    mysqli_rollback($koneksi);
    die('Gagal menghapus data pegawai: ' . mysqli_error($koneksi));
}

mysqli_commit($koneksi);
header("location:pegawai.php?pesan=hapus");
?>
