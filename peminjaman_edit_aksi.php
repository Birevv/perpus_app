<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (!in_array($_SESSION['level'] ?? '', ['admin', 'pegawai'], true)) {
    header("Location: dashboard_pengunjung.php");
    exit;
}

$id_peminjaman = mysqli_real_escape_string($koneksi, $_POST['id_peminjaman']);
$tgl_pinjam = mysqli_real_escape_string($koneksi, $_POST['tgl_pinjam']);

// LOGIKA TANGGAL KEMBALI
if (empty($_POST['tgl_kembali'])) {
    $tgl_kembali = "NULL";
    $status = 'Dipinjam';
} else {
    $tgl_kembali = "'" . mysqli_real_escape_string($koneksi, $_POST['tgl_kembali']) . "'";
    $status = 'Dikembalikan';
}

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

$data_lama = mysqli_fetch_assoc($cek_peminjaman);
$isbn = mysqli_real_escape_string($koneksi, $data_lama['isbn']);
$status_lama = strtolower($data_lama['status'] ?? 'dipinjam');
$status_baru = strtolower($status);

$query = "UPDATE peminjaman SET
            tgl_pinjam = '$tgl_pinjam',
            tgl_kembali = $tgl_kembali,
            status = '$status'
          WHERE id_peminjaman = '$id_peminjaman'";

if (mysqli_query($koneksi, $query)) {
    if ($status_lama !== 'dipinjam' && $status_baru === 'dipinjam') {
        $kurangi_stok = mysqli_query(
            $koneksi,
            "UPDATE buku SET stok = stok - 1 WHERE isbn = '$isbn' AND stok > 0"
        );

        if (!$kurangi_stok || mysqli_affected_rows($koneksi) === 0) {
            mysqli_rollback($koneksi);
            echo "Gagal update data: stok buku tidak tersedia.";
            exit;
        }
    }

    if ($status_lama === 'dipinjam' && $status_baru === 'dikembalikan') {
        $tambah_stok = mysqli_query(
            $koneksi,
            "UPDATE buku SET stok = stok + 1 WHERE isbn = '$isbn'"
        );

        if (!$tambah_stok) {
            mysqli_rollback($koneksi);
            echo "Gagal update stok buku: " . mysqli_error($koneksi);
            exit;
        }
    }

    mysqli_commit($koneksi);
    header("Location: peminjaman.php");
    exit;
} else {
    mysqli_rollback($koneksi);
    echo "Gagal Update Data: " . mysqli_error($koneksi);
}
