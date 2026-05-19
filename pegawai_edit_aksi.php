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
$old_nip = trim($_POST['old_nip'] ?? '');
$nip = trim($_POST['nip'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$gender = trim($_POST['gender'] ?? '');

if ($old_nip === '' || $nip === '' || $nama === '' || $alamat === '' || $gender === '') {
    die('Data pegawai wajib diisi.');
}

if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
    die('Gender tidak valid.');
}

$old_nip = mysqli_real_escape_string($koneksi, $old_nip);
$nip = mysqli_real_escape_string($koneksi, $nip);
$nama = mysqli_real_escape_string($koneksi, $nama);
$alamat = mysqli_real_escape_string($koneksi, $alamat);
$gender = mysqli_real_escape_string($koneksi, $gender);

$cek_nip = mysqli_query($koneksi, "SELECT nip FROM pegawai WHERE nip='$nip' AND nip <> '$old_nip' LIMIT 1");
if ($cek_nip && mysqli_num_rows($cek_nip) > 0) {
    die('NIP sudah digunakan.');
}

mysqli_query($koneksi, "UPDATE pegawai SET nip='$nip', nama='$nama', alamat='$alamat', gender='$gender' WHERE nip='$old_nip'");

header("location:pegawai.php?pesan=update");
?>
