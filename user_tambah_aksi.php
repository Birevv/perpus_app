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
include 'anggota_schema.php';
include 'pegawai_schema.php';
include 'id_generator.php';

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$level = trim($_POST['level'] ?? '');

if ($nama === '' || $username === '' || $password === '' || $level === '') {
    die('Data user wajib diisi.');
}

if (!in_array($level, ['admin', 'pegawai', 'user'], true)) {
    die('Level tidak valid.');
}

$id_user = mysqli_real_escape_string($koneksi, generate_next_code($koneksi, 'user', 'id_user', 3));
$nama = mysqli_real_escape_string($koneksi, $nama);
$username = mysqli_real_escape_string($koneksi, $username);
$password = mysqli_real_escape_string($koneksi, $password);
$level = mysqli_real_escape_string($koneksi, $level);

$cek_username = mysqli_query($koneksi, "SELECT id_user FROM `user` WHERE username='$username' LIMIT 1");
if ($cek_username && mysqli_num_rows($cek_username) > 0) {
    die('Username sudah digunakan.');
}

if ($level === 'user') {
    ensure_anggota_gender_column($koneksi);

    $cek_nip_nis = mysqli_query($koneksi, "SELECT id_anggota FROM anggota WHERE NIP_NIS='$password' LIMIT 1");
    if ($cek_nip_nis && mysqli_num_rows($cek_nip_nis) > 0) {
        die('NIP/NIS sudah terdaftar.');
    }
} elseif ($level === 'pegawai') {
    ensure_pegawai_id_column($koneksi);

    $cek_nip = mysqli_query($koneksi, "SELECT nip FROM pegawai WHERE nip='$password' LIMIT 1");
    if ($cek_nip && mysqli_num_rows($cek_nip) > 0) {
        die('NIP sudah terdaftar.');
    }
}

mysqli_begin_transaction($koneksi);

$user_saved = mysqli_query(
    $koneksi,
    "INSERT INTO `user` (id_user, nama, username, password, level)
     VALUES ('$id_user', '$nama', '$username', '$password', '$level')"
);

$profile_saved = true;

if ($user_saved && $level === 'user') {
    $id_anggota = mysqli_real_escape_string($koneksi, generate_next_code($koneksi, 'anggota', 'id_anggota', 4));
    $profile_saved = mysqli_query(
        $koneksi,
        "INSERT INTO anggota (id_anggota, nama, NIP_NIS, gender, alamat, no_hp)
         VALUES ('$id_anggota', '$nama', '$password', '', '', '')"
    );
} elseif ($user_saved && $level === 'pegawai') {
    $id_pegawai = mysqli_real_escape_string($koneksi, generate_next_id_pegawai($koneksi));
    $profile_saved = mysqli_query(
        $koneksi,
        "INSERT INTO pegawai (id_pegawai, nip, nama, alamat, gender)
         VALUES ('$id_pegawai', '$password', '$nama', '', '')"
    );
}

if (!$user_saved || !$profile_saved) {
    mysqli_rollback($koneksi);
    die('Gagal menyimpan data user: ' . mysqli_error($koneksi));
}

mysqli_commit($koneksi);
header("location:user.php?pesan=input");
?>
