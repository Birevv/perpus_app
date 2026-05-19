<?php

if (!function_exists('generate_next_code')) {
    function generate_next_code($koneksi, $table, $field, $length)
    {
        $table = str_replace('`', '``', $table);
        $field = str_replace('`', '``', $field);

        $query = mysqli_query(
            $koneksi,
            "SELECT `$field` AS kode FROM `$table` ORDER BY CAST(`$field` AS UNSIGNED) DESC, `$field` DESC LIMIT 1"
        );
        $next_number = 1;

        if ($query && mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            $next_number = ((int) $row['kode']) + 1;
        }

        do {
            $code = str_pad((string) $next_number, $length, '0', STR_PAD_LEFT);
            $safe_code = mysqli_real_escape_string($koneksi, $code);
            $exists = mysqli_query($koneksi, "SELECT `$field` FROM `$table` WHERE `$field`='$safe_code' LIMIT 1");
            $next_number++;
        } while ($exists && mysqli_num_rows($exists) > 0);

        return $code;
    }
}

?>
