<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

include 'koneksi.php';
    $buku = mysqli_query($koneksi, "SELECT * FROM buku");
    $anggota = mysqli_query($koneksi, "SELECT * FROM anggota");
?>

<h3>Form Transaksi Peminjaman</h3>
<form action="simpan_peminjaman.php" method="post">
    <label for="id_peminjaman">ID Peminjaman</label>
    <input type="text" name="id_peminjaman" id="id_peminjaman" placeholder="ID Peminjaman" required>
    <!-- Buku -->

    <label>Judul Buku</label><br>
    <select name="isbn" required>
        <option value="">-- Pilih Buku --</option>
        <?php while ($cek_buku = mysqli_fetch_assoc($buku)) :?>
            <option value="<?= $cek_buku['isbn']; ?>">
                <?= $cek_buku['judul']; ?>
            </option>
            <?php endwhile; ?>
    </select>   
    <br><br>
    <!-- Anggota -->

    <label>Nama Peminjam</label><br>
    <select name="id_anggota">
        <option value="">-- Pilih Peminjam --</option>
        <?php while ($cek_anggota = mysqli_fetch_assoc($anggota)) :?>
            <option value="<?= $cek_anggota['id_anggota']; ?>">
                <?= $cek_anggota['nama']; ?>
            </option>
            <?php endwhile; ?>
    </select>
    <br><br>
    <!-- Tanggal -->
    <label>Tanggal Peminjaman</label><br>
    <input type="date" name="tgl_pinjam" required><br><br>
    <label>Tanggal Kembali</label><br>
    <input type="date" name="tgl_kembali">
    <br><br>
        <!-- Petugas (Hidden) -->
    <input type="hidden" name="id_user" value="<?= $_SESSION['id_user'];?>">
    <button type="submit">Simpan Peminjaman</button>
</form>
