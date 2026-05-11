<?php

function get_genre_options($koneksi)
{
    $genres = [];
    $query = mysqli_query(
        $koneksi,
        "SELECT DISTINCT genre FROM buku WHERE genre IS NOT NULL AND genre <> '' ORDER BY genre ASC"
    );

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $genres[] = $row['genre'];
        }
    }

    return $genres;
}

function is_valid_genre($koneksi, $genre)
{
    return in_array($genre, get_genre_options($koneksi), true);
}

?>
