<?php

function ensure_pegawai_id_column($koneksi)
{
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM pegawai LIKE 'id_pegawai'");

    if ($check && mysqli_num_rows($check) === 0) {
        $added = mysqli_query(
            $koneksi,
            "ALTER TABLE pegawai
             ADD id_pegawai varchar(10) NULL FIRST"
        );

        if (!$added) {
            return false;
        }
    } else {
        $column = mysqli_fetch_assoc($check);

        if (stripos($column['Extra'] ?? '', 'auto_increment') !== false || stripos($column['Type'] ?? '', 'varchar') === false) {
            $modified = mysqli_query(
                $koneksi,
                "ALTER TABLE pegawai MODIFY id_pegawai varchar(10) NOT NULL"
            );

            if (!$modified) {
                return false;
            }
        }
    }

    $result = mysqli_query($koneksi, "SELECT nip, id_pegawai FROM pegawai ORDER BY CAST(id_pegawai AS UNSIGNED) ASC, nip ASC");
    $used_ids = [];

    while ($result && ($row = mysqli_fetch_assoc($result))) {
        $current_id = trim((string) ($row['id_pegawai'] ?? ''));
        $new_id = $current_id;

        if ($current_id !== '' && ctype_digit($current_id)) {
            $new_id = str_pad((string) ((int) $current_id), 3, '0', STR_PAD_LEFT);
        }

        if ($new_id === '' || in_array($new_id, $used_ids, true)) {
            $next_number = 1;

            do {
                $new_id = str_pad((string) $next_number, 3, '0', STR_PAD_LEFT);
                $next_number++;
            } while (in_array($new_id, $used_ids, true));
        }

        if ($current_id !== $new_id) {
            $nip = mysqli_real_escape_string($koneksi, $row['nip']);
            $safe_id = mysqli_real_escape_string($koneksi, $new_id);
            $updated = mysqli_query($koneksi, "UPDATE pegawai SET id_pegawai='$safe_id' WHERE nip='$nip'");

            if (!$updated) {
                return false;
            }
        }

        $used_ids[] = $new_id;
    }

    $check_required = mysqli_query($koneksi, "SHOW COLUMNS FROM pegawai LIKE 'id_pegawai'");
    $column = $check_required ? mysqli_fetch_assoc($check_required) : null;

    if ($column && strtoupper($column['Null'] ?? '') !== 'NO') {
        $required = mysqli_query($koneksi, "ALTER TABLE pegawai MODIFY id_pegawai varchar(10) NOT NULL");

        if (!$required) {
            return false;
        }
    }

    $unique_check = mysqli_query($koneksi, "SHOW INDEX FROM pegawai WHERE Key_name='pegawai_id_pegawai_unique'");

    if ($unique_check && mysqli_num_rows($unique_check) === 0) {
        return mysqli_query(
            $koneksi,
            "ALTER TABLE pegawai ADD UNIQUE KEY pegawai_id_pegawai_unique (id_pegawai)"
        );
    }

    return true;
}

function generate_next_id_pegawai($koneksi)
{
    ensure_pegawai_id_column($koneksi);

    $query = mysqli_query(
        $koneksi,
        "SELECT id_pegawai FROM pegawai ORDER BY CAST(id_pegawai AS UNSIGNED) DESC, id_pegawai DESC LIMIT 1"
    );
    $next_number = 1;

    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $next_number = ((int) $row['id_pegawai']) + 1;
    }

    do {
        $id_pegawai = str_pad((string) $next_number, 3, '0', STR_PAD_LEFT);
        $safe_id = mysqli_real_escape_string($koneksi, $id_pegawai);
        $exists = mysqli_query($koneksi, "SELECT id_pegawai FROM pegawai WHERE id_pegawai='$safe_id' LIMIT 1");
        $next_number++;
    } while ($exists && mysqli_num_rows($exists) > 0);

    return $id_pegawai;
}

?>
