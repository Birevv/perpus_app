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

$id_user = trim($_POST['id_user'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$level = trim($_POST['level'] ?? '');

if ($id_user === '' || $nama === '' || $username === '' || $password === '' || $level === '') {
    die('Data user wajib diisi.');
}

if (!in_array($level, ['admin', 'pegawai', 'user'], true)) {
    die('Level tidak valid.');
}

$id_user = mysqli_real_escape_string($koneksi, $id_user);
$nama = mysqli_real_escape_string($koneksi, $nama);
$username = mysqli_real_escape_string($koneksi, $username);
$password = mysqli_real_escape_string($koneksi, $password);
$level = mysqli_real_escape_string($koneksi, $level);

$cek_username = mysqli_query(
    $koneksi,
    "SELECT id_user FROM `user` WHERE username='$username' AND id_user <> '$id_user' LIMIT 1"
);
if ($cek_username && mysqli_num_rows($cek_username) > 0) {
    die('Username sudah digunakan.');
}

mysqli_query(
    $koneksi,
    "UPDATE `user`
     SET nama='$nama', username='$username', password='$password', level='$level'
     WHERE id_user='$id_user'"
);

header("location:user.php?pesan=update");
?>
