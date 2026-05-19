<?php

function ensure_peminjaman_nama_petugas_column($koneksi)
{
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman LIKE 'nama_petugas'");

    if ($check && mysqli_num_rows($check) === 0) {
        return mysqli_query(
            $koneksi,
            "ALTER TABLE peminjaman ADD nama_petugas varchar(200) NOT NULL DEFAULT '' AFTER status"
        );
    }

    return true;
}

function ensure_peminjaman_id_auto_increment($koneksi)
{
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman LIKE 'id_peminjaman'");
    $column = $check ? mysqli_fetch_assoc($check) : null;

    if (!$column) {
        return false;
    }

    if (stripos($column['Extra'] ?? '', 'auto_increment') !== false) {
        return true;
    }

    $data = mysqli_query(
        $koneksi,
        "SELECT id_peminjaman FROM peminjaman ORDER BY CAST(id_peminjaman AS UNSIGNED) ASC, id_peminjaman ASC"
    );
    $next_id = 1;

    while ($data && ($row = mysqli_fetch_assoc($data))) {
        $id_lama = trim((string) $row['id_peminjaman']);
        $id_baru = (string) $next_id;

        if (!ctype_digit($id_lama) || (int) $id_lama === 0) {
            $safe_lama = mysqli_real_escape_string($koneksi, $id_lama);
            $updated = mysqli_query($koneksi, "UPDATE peminjaman SET id_peminjaman='$id_baru' WHERE id_peminjaman='$safe_lama'");

            if (!$updated) {
                return false;
            }
        }

        $next_id = max($next_id + 1, ((int) $id_lama) + 1);
    }

    $changed = mysqli_query(
        $koneksi,
        "ALTER TABLE peminjaman MODIFY id_peminjaman int NOT NULL AUTO_INCREMENT"
    );

    if (!$changed) {
        return false;
    }

    $max_query = mysqli_query($koneksi, "SELECT COALESCE(MAX(id_peminjaman), 0) + 1 AS next_id FROM peminjaman");
    $max_row = $max_query ? mysqli_fetch_assoc($max_query) : ['next_id' => 1];
    $auto_increment = max(1, (int) $max_row['next_id']);

    return mysqli_query($koneksi, "ALTER TABLE peminjaman AUTO_INCREMENT = $auto_increment");
}

function ensure_peminjaman_schema($koneksi)
{
    return ensure_peminjaman_id_auto_increment($koneksi)
        && ensure_peminjaman_nama_petugas_column($koneksi);
}

?>
