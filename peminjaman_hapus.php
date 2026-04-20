<?php
include 'koneksi.php';

$id_peminjaman = mysqli_real_escape_string($koneksi, $_GET['id_peminjaman']);

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
$isbn = mysqli_real_escape_string($koneksi, $data_peminjaman['isbn']);
$status = strtolower($data_peminjaman['status'] ?? 'dipinjam');

$hapus = mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_peminjaman='$id_peminjaman'");

if (!$hapus) {
    mysqli_rollback($koneksi);
    echo "Gagal menghapus data peminjaman: " . mysqli_error($koneksi);
    exit;
}

if ($status === 'dipinjam') {
    $update_stok = mysqli_query(
        $koneksi,
        "UPDATE buku SET stok = stok + 1 WHERE isbn = '$isbn'"
    );

    if (!$update_stok) {
        mysqli_rollback($koneksi);
        echo "Gagal mengembalikan stok buku: " . mysqli_error($koneksi);
        exit;
    }
}

mysqli_commit($koneksi);
header("location:peminjaman.php?pesan=hapus");
?>
