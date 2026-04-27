<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (!in_array($_SESSION['level'] ?? '', ['admin', 'pegawai'], true)) {
    header("Location: dashboard_pengunjung.php");
    exit;
}

include 'koneksi.php';
$buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul ASC");
$anggota = mysqli_query($koneksi, "SELECT * FROM anggota");
$back_url = (($_SESSION['level'] ?? '') === 'pegawai') ? 'dashboard_pegawai.php' : 'peminjaman.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="crud-body">
    <main class="crud-container">
        <h1>Form Transaksi Peminjaman</h1>

        <form action="simpan_peminjaman.php" method="post" class="crud-form">

            <div class="form-group">
                <label for="id_peminjaman">ID Peminjaman</label>
                <input type="text" name="id_peminjaman" id="id_peminjaman" placeholder="ID Peminjaman" required>
            </div>

            <div class="form-group">
                <label>Judul Buku</label>
                <select name="isbn" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php while ($cek_buku = mysqli_fetch_assoc($buku)) :?>
                        <option value="<?= $cek_buku['isbn']; ?>">
                            <?= $cek_buku['judul']; ?> (Stok: <?= $cek_buku['stok']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nama Peminjam</label>
                <select name="id_anggota" required>
                    <option value="">-- Pilih Peminjam --</option>
                    <?php while ($cek_anggota = mysqli_fetch_assoc($anggota)) :?>
                        <option value="<?= $cek_anggota['id_anggota']; ?>">
                            <?= $cek_anggota['nama']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tanggal Peminjaman</label>
                <input type="date" name="tgl_pinjam" required>
            </div>

            <div class="form-group">
                <label>Tanggal Kembali</label>
                <input type="date" name="tgl_kembali">
            </div>

            <!-- Petugas (Hidden) -->
            <input type="hidden" name="id_user" value="<?= $_SESSION['id_user'];?>">

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan Peminjaman</button>
                <a href="<?= $back_url; ?>" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>
