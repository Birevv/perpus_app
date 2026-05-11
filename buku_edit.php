<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    header("Location: " . (($_SESSION['level'] ?? '') === 'pegawai' ? 'buku_pegawai.php' : 'dashboard_pengunjung.php'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body book-form-page">
    <main class="crud-container book-form-container">
        <h1>Edit Data Buku</h1>
        <p class="form-subtitle">Perbarui informasi buku agar data katalog tetap akurat.</p>

        <?php
        include 'koneksi.php';
        include 'genre_options.php';

        $isbn = mysqli_real_escape_string($koneksi, $_GET['isbn']);
        $query = mysqli_query($koneksi, "SELECT * FROM buku WHERE isbn='$isbn'");
        $data = mysqli_fetch_assoc($query);
        $genre_options = get_genre_options($koneksi);
        ?>

        <form action="buku_edit_aksi.php" method="post" class="crud-form book-form">
            <div class="book-form-grid">
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="<?= $data['isbn']; ?>" readonly>
                    <small class="field-hint">ISBN dipakai sebagai kode utama buku.</small>
                </div>

                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" value="<?= (int) $data['stok']; ?>" min="0" required>
                    <small class="field-hint">Jumlah buku yang tersedia untuk dipinjam.</small>
                </div>

                <div class="form-group full-width">
                    <label for="judul">Judul Buku</label>
                    <input type="text" id="judul" name="judul" value="<?= $data['judul']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="pengarang">Pengarang</label>
                    <input type="text" id="pengarang" name="pengarang" value="<?= $data['pengarang']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre" required>
                        <option value="" disabled>Pilih genre buku</option>
                        <?php foreach ($genre_options as $genre): ?>
                            <option value="<?= htmlspecialchars($genre); ?>" <?= $data['genre'] === $genre ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($genre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="penerbit">Penerbit</label>
                    <input type="text" id="penerbit" name="penerbit" value="<?= $data['penerbit']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="tahun">Tahun Terbit</label>
                    <input type="number" id="tahun" name="tahun" value="<?= $data['tahun']; ?>" min="1900" max="<?= date('Y'); ?>" required>
                </div>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="buku.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>
