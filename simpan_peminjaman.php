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

$redirect_url = (($_SESSION['level'] ?? '') === 'pegawai') ? 'peminjaman.php' : 'peminjaman.php';

$id_peminjaman = mysqli_real_escape_string($koneksi, $_POST['id_peminjaman']);
$id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
$isbn = mysqli_real_escape_string($koneksi, $_POST['isbn']);
$tgl_pinjam = mysqli_real_escape_string($koneksi, $_POST['tgl_pinjam']);

if (empty($_POST['tgl_kembali'])) {
    $tgl_kembali = "NULL";
    $status = 'Dipinjam';
} else {
    $tgl_kembali = "'" . mysqli_real_escape_string($koneksi, $_POST['tgl_kembali']) . "'";
    $status = 'Dikembalikan';
}

mysqli_begin_transaction($koneksi);

$sedangDipinjam = strtolower($status) === 'dipinjam';

if ($sedangDipinjam) {
    $update_stok = mysqli_query(
        $koneksi,
        "UPDATE buku SET stok = stok - 1 WHERE isbn = '$isbn' AND stok > 0"
    );

    if (!$update_stok || mysqli_affected_rows($koneksi) === 0) {
        mysqli_rollback($koneksi);
        echo "Gagal menyimpan peminjaman: stok buku tidak tersedia.";
        exit;
    }
}

$query = "INSERT INTO peminjaman 
(id_peminjaman, id_anggota, isbn, tgl_pinjam, tgl_kembali, status)
VALUES
('$id_peminjaman', '$id_anggota', '$isbn', '$tgl_pinjam', $tgl_kembali, '$status')";

if (mysqli_query($koneksi, $query)) {
    mysqli_commit($koneksi);
    header("Location: $redirect_url");
    exit;
} else {
    mysqli_rollback($koneksi);
    echo "Gagal Menyimpan Peminjaman: " . mysqli_error($koneksi);
}
?>
