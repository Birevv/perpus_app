<?php

function hapus_peminjaman_anggota($koneksi, $id_anggota)
{
    $id_anggota = mysqli_real_escape_string($koneksi, $id_anggota);
    $pinjaman_aktif = mysqli_query(
        $koneksi,
        "SELECT isbn, COUNT(*) AS total
         FROM peminjaman
         WHERE id_anggota='$id_anggota'
           AND (status IS NULL OR status = '' OR LOWER(status) = 'dipinjam')
         GROUP BY isbn"
    );

    if (!$pinjaman_aktif) {
        return false;
    }

    while ($row = mysqli_fetch_assoc($pinjaman_aktif)) {
        $isbn = mysqli_real_escape_string($koneksi, $row['isbn']);
        $total = (int) $row['total'];
        $stok_dikembalikan = mysqli_query($koneksi, "UPDATE buku SET stok = stok + $total WHERE isbn='$isbn'");

        if (!$stok_dikembalikan) {
            return false;
        }
    }

    return mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_anggota='$id_anggota'");
}

?>
