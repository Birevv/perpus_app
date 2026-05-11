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
$id_anggota = mysqli_real_escape_string($koneksi, $_GET['id_anggota'] ?? '');
$anggota_query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota='$id_anggota' LIMIT 1");
$anggota = $anggota_query ? mysqli_fetch_assoc($anggota_query) : null;

if (!$anggota) {
    header("location:pengunjung.php?pesan=hapus");
    exit;
}

$nip_nis = mysqli_real_escape_string($koneksi, $anggota['NIP_NIS']);
$nama = mysqli_real_escape_string($koneksi, $anggota['nama']);

mysqli_begin_transaction($koneksi);

$hapus_anggota = mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota='$id_anggota'");
$hapus_user = mysqli_query(
    $koneksi,
    "DELETE FROM `user`
     WHERE level='user'
        AND (username='$nip_nis' OR password='$nip_nis' OR nama='$nama')"
);

if (!$hapus_anggota || !$hapus_user) {
    mysqli_rollback($koneksi);
    die('Gagal menghapus data pengunjung: ' . mysqli_error($koneksi));
}

mysqli_commit($koneksi);
header("location:pengunjung.php?pesan=hapus");
