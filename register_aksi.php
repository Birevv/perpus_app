<?php
session_start();
include 'koneksi.php';
include 'anggota_schema.php';
include 'id_generator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

ensure_anggota_gender_column($koneksi);

$username = trim($_POST['username'] ?? '');
$NIP_NIS = trim($_POST['NIP_NIS'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$gender = trim($_POST['gender'] ?? '');

if ($nama === '' || $username === '' || $NIP_NIS === '' || $no_hp === '' || $alamat === '' || $gender === '') {
    header("Location: register.php?error=required");
    exit;
}

if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
    header("Location: register.php?error=gender");
    exit;
}

$username_safe = mysqli_real_escape_string($koneksi, $username);
$NIP_NIS_safe = mysqli_real_escape_string($koneksi, $NIP_NIS);
$nama_safe = mysqli_real_escape_string($koneksi, $nama);
$no_hp_safe = mysqli_real_escape_string($koneksi, $no_hp);
$alamat_safe = mysqli_real_escape_string($koneksi, $alamat);
$gender_safe = mysqli_real_escape_string($koneksi, $gender);

$cek_username = mysqli_query($koneksi, "SELECT id_user FROM `user` WHERE username='$username_safe' LIMIT 1");
if ($cek_username && mysqli_num_rows($cek_username) > 0) {
    header("Location: register.php?error=username");
    exit;
}

$cek_nip_nis = mysqli_query($koneksi, "SELECT id_anggota FROM anggota WHERE NIP_NIS='$NIP_NIS_safe' LIMIT 1");
if ($cek_nip_nis && mysqli_num_rows($cek_nip_nis) > 0) {
    header("Location: register.php?error=nip_nis");
    exit;
}

$id_user = mysqli_real_escape_string($koneksi, generate_next_code($koneksi, 'user', 'id_user', 3));
$id_anggota = mysqli_real_escape_string($koneksi, generate_next_code($koneksi, 'anggota', 'id_anggota', 4));

mysqli_begin_transaction($koneksi);

$user_saved = mysqli_query(
    $koneksi,
    "INSERT INTO `user` (id_user, nama, username, password, level)
     VALUES ('$id_user', '$nama_safe', '$username_safe', '$NIP_NIS_safe', 'user')"
);

$anggota_saved = $user_saved && mysqli_query(
    $koneksi,
    "INSERT INTO anggota (id_anggota, nama, NIP_NIS, gender, alamat, no_hp)
     VALUES ('$id_anggota', '$nama_safe', '$NIP_NIS_safe', '$gender_safe', '$alamat_safe', '$no_hp_safe')"
);

if (!$user_saved || !$anggota_saved) {
    mysqli_rollback($koneksi);
    header("Location: register.php?error=save");
    exit;
}

mysqli_commit($koneksi);
header("Location: index.php?success=register");
exit;
?>
