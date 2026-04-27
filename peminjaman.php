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

$is_pegawai = ($_SESSION['level'] ?? '') === 'pegawai';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Peminjaman</title>
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="<?= $is_pegawai ? 'dashboard_pegawai.php' : 'dashboard.php'; ?>">Dashboard</a></li>
            <li><a href="<?= $is_pegawai ? 'buku_pegawai.php' : 'buku.php'; ?>">Buku</a></li>
            <?php if (!$is_pegawai): ?>
                <li><a href="pegawai.php">Pegawai</a></li>
                <li><a href="pengunjung.php">Pengunjung</a></li>
            <?php endif; ?>
            <li><a href="peminjaman.php" class="active">Peminjaman</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>
    <main>
        <div class="main">
            <h2>Data Peminjaman</h2>
            <div class="tombol-tambah">
                <a href="form_peminjaman.php" class="btn-tambah">+ Tambah Data</a>
            </div>
            <table border="1" cellpadding="10" cellspacing="0" class="tabel">
                <tr>
                    <th>Id Peminjaman</th>
                    <th>Id Anggota</th>
                    <th>ISBN</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                    <th>Opsi</th>
                </tr>
                <?php
                include 'koneksi.php';
                $query = mysqli_query($koneksi, "SELECT * FROM peminjaman");
                while ($data = mysqli_fetch_array($query)) {
                ?>
                    <tr>
                        <td><?= $data['id_peminjaman']; ?></td>
                        <td><?= $data['id_anggota']; ?></td>
                        <td><?= $data['isbn']; ?></td>
                        <td><?= $data['tgl_pinjam']; ?></td>
                        <td><?= $data['tgl_kembali'] ?: '-'; ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($data['status'] ?: 'dipinjam'); ?>">
                                <?= $data['status'] ?: 'Dipinjam'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-cell">
                                <a href="peminjaman_edit.php?id_peminjaman=<?= $data['id_peminjaman']; ?>" class="btn-action edit">Edit</a>
                                <a href="peminjaman_hapus.php?id_peminjaman=<?= $data['id_peminjaman']; ?>" class="btn-action delete">Hapus</a>
                            </div>
                        </td>
                        <td class="option-cell">
                            <?php if (empty($data['status']) || strtolower($data['status']) === 'dipinjam'): ?>
                                <form action="peminjaman_kembalikan.php" method="post" class="inline-action-form" onsubmit="return confirm('Konfirmasi pengembalian buku?')">
                                    <input type="hidden" name="id_peminjaman" value="<?= $data['id_peminjaman']; ?>">
                                    <button type="submit" class="btn-action return">Kembalikan</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Bima Revan Saputra XI RPL 2
    </footer>


</body>
