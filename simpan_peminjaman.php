<?php
session_start();
include 'koneksi.php';
include 'peminjaman_schema.php';

date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (!in_array($_SESSION['level'] ?? '', ['admin', 'pegawai'], true)) {
    header("Location: dashboard_pengunjung.php");
    exit;
}

ensure_peminjaman_schema($koneksi);

$redirect_url = (($_SESSION['level'] ?? '') === 'pegawai') ? 'peminjaman.php' : 'peminjaman.php';

function get_nama_petugas_peminjaman($koneksi)
{
    if (($_SESSION['level'] ?? '') === 'admin') {
        return 'Adminstrator';
    }

    $id_user = mysqli_real_escape_string($koneksi, $_SESSION['id_user'] ?? '');
    $username_session = trim($_SESSION['username'] ?? '');
    $user = null;

    if ($id_user !== '') {
        $user_query = mysqli_query(
            $koneksi,
            "SELECT nama, username, password FROM `user` WHERE id_user='$id_user' LIMIT 1"
        );
        $user = $user_query ? mysqli_fetch_assoc($user_query) : null;
    }

    $nama_user = trim($user['nama'] ?? '');
    $username = trim($user['username'] ?? $username_session);
    $password = trim($user['password'] ?? '');
    $pegawai_checks = [];

    foreach ([$username, $password] as $nip) {
        if ($nip !== '') {
            $pegawai_checks[] = "nip='" . mysqli_real_escape_string($koneksi, $nip) . "'";
        }
    }

    if ($nama_user !== '') {
        $pegawai_checks[] = "nama='" . mysqli_real_escape_string($koneksi, $nama_user) . "'";
    }

    if ($pegawai_checks !== []) {
        $pegawai_query = mysqli_query(
            $koneksi,
            "SELECT nama FROM pegawai WHERE " . implode(' OR ', $pegawai_checks) . " LIMIT 1"
        );
        $pegawai = $pegawai_query ? mysqli_fetch_assoc($pegawai_query) : null;

        if ($pegawai && trim($pegawai['nama']) !== '') {
            return $pegawai['nama'];
        }
    }

    if ($nama_user !== '') {
        return $nama_user;
    }

    return $username_session !== '' ? $username_session : 'Pegawai';
}

$id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
$isbn = mysqli_real_escape_string($koneksi, $_POST['isbn']);
$tgl_pinjam = date('Y-m-d');
$nama_petugas = mysqli_real_escape_string($koneksi, get_nama_petugas_peminjaman($koneksi));

$tgl_kembali = "NULL";
$status = 'Dipinjam';

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
(id_anggota, isbn, tgl_pinjam, tgl_kembali, status, nama_petugas)
VALUES
('$id_anggota', '$isbn', '$tgl_pinjam', $tgl_kembali, '$status', '$nama_petugas')";

if (mysqli_query($koneksi, $query)) {
    mysqli_commit($koneksi);
    header("Location: $redirect_url");
    exit;
} else {
    mysqli_rollback($koneksi);
    echo "Gagal Menyimpan Peminjaman: " . mysqli_error($koneksi);
}
?>
