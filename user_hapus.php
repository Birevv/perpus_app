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
include 'peminjaman_cleanup.php';

$id_user = mysqli_real_escape_string($koneksi, $_GET['id_user'] ?? '');

if ($id_user === ($_SESSION['id_user'] ?? '')) {
    die('User yang sedang login tidak boleh dihapus.');
}

$user_query = mysqli_query($koneksi, "SELECT * FROM `user` WHERE id_user='$id_user' LIMIT 1");
$user = $user_query ? mysqli_fetch_assoc($user_query) : null;

if (!$user) {
    header("location:user.php?pesan=hapus");
    exit;
}

$nama = mysqli_real_escape_string($koneksi, $user['nama']);
$username = mysqli_real_escape_string($koneksi, $user['username']);
$password = mysqli_real_escape_string($koneksi, $user['password']);
$level = $user['level'];

mysqli_begin_transaction($koneksi);

$hapus_relasi = true;
if (in_array($level, ['admin', 'pegawai'], true)) {
    $hapus_relasi = mysqli_query(
        $koneksi,
        "DELETE FROM pegawai
         WHERE nip='$username'
            OR nip='$password'
            OR nama='$nama'"
    );
} elseif ($level === 'user') {
    $anggota_query = mysqli_query(
        $koneksi,
        "SELECT id_anggota FROM anggota
         WHERE NIP_NIS='$username'
            OR NIP_NIS='$password'
            OR nama='$nama'"
    );

    if (!$anggota_query) {
        $hapus_relasi = false;
    } else {
        while ($anggota = mysqli_fetch_assoc($anggota_query)) {
            if (!hapus_peminjaman_anggota($koneksi, $anggota['id_anggota'])) {
                $hapus_relasi = false;
                break;
            }
        }
    }

    if ($hapus_relasi) {
        $hapus_relasi = mysqli_query(
            $koneksi,
            "DELETE FROM anggota
             WHERE NIP_NIS='$username'
                OR NIP_NIS='$password'
                OR nama='$nama'"
        );
    }
}

$hapus_user = mysqli_query($koneksi, "DELETE FROM `user` WHERE id_user='$id_user'");

if (!$hapus_relasi || !$hapus_user) {
    mysqli_rollback($koneksi);
    die('Gagal menghapus data user: ' . mysqli_error($koneksi));
}

mysqli_commit($koneksi);
header("location:user.php?pesan=hapus");
?>
