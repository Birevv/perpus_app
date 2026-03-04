<?php
session_start();
include 'koneksi.php';

$id_peminjaman = $_POST['id_peminjaman'];
$id_anggota    = $_POST['id_anggota'];
$isbn          = $_POST['isbn'];
$tgl_pinjam    = $_POST['tgl_pinjam'];

if (empty($_POST['tgl_kembali'])) {
    $tgl_kembali = "NULL";       
    $status = 'Dipinjam';
} else {
    $tgl_kembali = "'" . $_POST['tgl_kembali'] . "'";
    $status = 'Dikembalikan';
}

$query = "INSERT INTO peminjaman 
(id_peminjaman, id_anggota, isbn, tgl_pinjam, tgl_kembali, status)
VALUES
('$id_peminjaman', '$id_anggota', '$isbn', '$tgl_pinjam', $tgl_kembali, '$status')";

if (mysqli_query($koneksi, $query)) {
    header("Location: peminjaman.php");
    exit;
} else {
    echo "Gagal Menyimpan Peminjaman: " . mysqli_error($koneksi);
}
?>
