<?php

function ensure_anggota_gender_column($koneksi)
{
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM anggota LIKE 'gender'");

    if ($check && mysqli_num_rows($check) === 0) {
        return mysqli_query(
            $koneksi,
            "ALTER TABLE anggota ADD gender varchar(20) NOT NULL DEFAULT '' AFTER NIP_NIS"
        );
    }

    return true;
}

?>
