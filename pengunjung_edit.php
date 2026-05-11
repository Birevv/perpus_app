<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    header("Location: " . (($_SESSION['level'] ?? '') === 'pegawai' ? 'dashboard_pegawai.php' : 'dashboard_pengunjung.php'));
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

<body class="crud-body">
    <main class="crud-container">
        <h1>Edit Data Pengunjung</h1>

        <?php
        include 'koneksi.php';
        include 'anggota_schema.php';

        ensure_anggota_gender_column($koneksi);

        $id_anggota = $_GET['id_anggota'];
        $query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota='$id_anggota'");
        $data = mysqli_fetch_assoc($query);
        $current_gender = $data['gender'] ?? '';
        ?>

        <form action="pengunjung_edit_aksi.php" method="post" class="crud-form">

            <input type="hidden" name="id_anggota" value="<?= $data['id_anggota']; ?>">

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" value="<?= $data['nama']; ?>" required>
            </div>

            <div class="form-group">
                <label>NIP/NIS</label>
                <input type="text" name="NIP_NIS" value="<?= $data['NIP_NIS']; ?>" required>
            </div>

            <div class="form-group">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="" disabled <?= in_array($current_gender, ['Laki-laki', 'Perempuan'], true) ? '' : 'selected'; ?>>Pilih gender</option>
                    <option value="Laki-laki" <?= $current_gender === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $current_gender === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat" value="<?= $data['alamat']; ?>" required>
            </div>

             <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" value="<?= $data['no_hp']; ?>" required>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="pengunjung.php" class="btn-back">Kembali</a>
            </div>

        </form>
    </main>
</body>

</html>
