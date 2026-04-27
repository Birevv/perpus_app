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
    <title>Tambah Buku</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body book-form-page">
    <main class="crud-container book-form-container">
        <h1>Tambah Data Buku</h1>
        <p class="form-subtitle">Lengkapi informasi buku baru sebelum disimpan ke katalog perpustakaan.</p>

        <form action="buku_tambah_aksi.php" method="post" class="crud-form book-form">
            <div class="book-form-grid">
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" placeholder="Contoh: 9786020324789" inputmode="numeric" required>
                    <small class="field-hint">Gunakan kode ISBN yang unik untuk setiap buku.</small>
                </div>

                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" placeholder="0" min="0" required>
                    <small class="field-hint">Jumlah buku yang tersedia untuk dipinjam.</small>
                </div>

                <div class="form-group full-width">
                    <label for="judul">Judul Buku</label>
                    <input type="text" id="judul" name="judul" placeholder="Masukkan judul buku" required>
                </div>

                <div class="form-group">
                    <label for="pengarang">Pengarang</label>
                    <input type="text" id="pengarang" name="pengarang" placeholder="Nama pengarang" required>
                </div>

                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" name="genre" placeholder="Contoh: Novel, Sejarah, Teknologi" required>
                </div>

                <div class="form-group full-width">
                    <label for="penerbit">Penerbit</label>
                    <input type="text" id="penerbit" name="penerbit" placeholder="Nama penerbit" required>
                </div>

                <div class="form-group">
                    <label for="tahun">Tahun Terbit</label>
                    <input type="number" id="tahun" name="tahun" placeholder="2025" min="1900" max="<?= date('Y'); ?>" required>
                </div>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan</button>
                <a href="buku.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>
