<?php
session_start();
include 'koneksi.php';

$id_peminjaman = $_POST['id_peminjaman'];
$tgl_pinjam    = $_POST['tgl_pinjam'];

// LOGIKA TANGGAL KEMBALI
if (empty($_POST['tgl_kembali'])) {
    $tgl_kembali = "NULL";
    $status = 'Dipinjam';
} else {
    $tgl_kembali = "'" . $_POST['tgl_kembali'] . "'";
    $status = 'Dikembalikan';
}

$query = "UPDATE peminjaman SET
            tgl_pinjam = '$tgl_pinjam',
            tgl_kembali = $tgl_kembali,
            status = '$status'
          WHERE id_peminjaman = '$id_peminjaman'";

if (mysqli_query($koneksi, $query)) {
    header("Location: peminjaman.php");
    exit;
} else {
    echo "Gagal Update Data: " . mysqli_error($koneksi);
}
