<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_peminjaman'])) {
    header("Location: peminjaman.php");
    exit;
}

$id_peminjaman = mysqli_real_escape_string($koneksi, $_POST['id_peminjaman']);
$tanggal_hari_ini = date('Y-m-d');

mysqli_begin_transaction($koneksi);

$cek_peminjaman = mysqli_query(
    $koneksi,
    "SELECT isbn, status FROM peminjaman WHERE id_peminjaman = '$id_peminjaman' LIMIT 1"
);

if (!$cek_peminjaman || mysqli_num_rows($cek_peminjaman) === 0) {
    mysqli_rollback($koneksi);
    echo "Data peminjaman tidak ditemukan.";
    exit;
}

$data_peminjaman = mysqli_fetch_assoc($cek_peminjaman);
$status_sekarang = strtolower($data_peminjaman['status'] ?? 'dipinjam');

if ($status_sekarang === 'dikembalikan') {
    mysqli_commit($koneksi);
    header("Location: peminjaman.php");
    exit;
}

$query = "UPDATE peminjaman SET
            status = 'Dikembalikan',
            tgl_kembali = '$tanggal_hari_ini'
          WHERE id_peminjaman = '$id_peminjaman'";

if (mysqli_query($koneksi, $query)) {
    $isbn = mysqli_real_escape_string($koneksi, $data_peminjaman['isbn']);
    $update_stok = mysqli_query(
        $koneksi,
        "UPDATE buku SET stok = stok + 1 WHERE isbn = '$isbn'"
    );

    if (!$update_stok) {
        mysqli_rollback($koneksi);
        echo "Gagal mengubah stok buku: " . mysqli_error($koneksi);
        exit;
    }

    mysqli_commit($koneksi);
    header("Location: peminjaman.php");
    exit;
}

mysqli_rollback($koneksi);
echo "Gagal mengubah status peminjaman: " . mysqli_error($koneksi);
?>
