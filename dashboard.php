<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    if (($_SESSION['level'] ?? '') === 'pegawai') {
        header("Location: dashboard_pegawai.php");
    } elseif (($_SESSION['level'] ?? '') === 'user') {
        header("Location: dashboard_pengunjung.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

include 'koneksi.php';

$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota"))['total'];
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman"))['total'];
$total_pegawai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pegawai"))['total'];
$stok_tersedia = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(stok), 0) as total FROM buku"))['total'];
$peminjaman_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status IS NULL OR status = '' OR LOWER(status) = 'dipinjam'"))['total'];
$peminjaman_selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE LOWER(status) = 'dikembalikan'"))['total'];

$recent = mysqli_query(
    $koneksi,
    "SELECT p.id_peminjaman, a.nama, b.judul, p.tgl_pinjam, p.status
     FROM peminjaman p
     LEFT JOIN anggota a ON p.id_anggota = a.id_anggota
     LEFT JOIN buku b ON p.isbn = b.isbn
     ORDER BY p.tgl_pinjam DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Dashboard - Library</title>
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="buku.php">Buku</a></li>
            <li><a href="pegawai.php">Pegawai</a></li>
            <li><a href="pengunjung.php">Pengunjung</a></li>
            <li><a href="peminjaman.php">Peminjaman</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main>
        <div class="main">
            <h2>Dashboard Admin</h2>
            <p class="dashboard-intro">Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?>. Di sini Anda bisa melihat ringkasan perpustakaan dan akses cepat ke menu utama.</p>

            <div class="tombol-tambah dashboard-actions">
                <a href="form_peminjaman.php" class="btn-tambah">Tambah Peminjaman</a>
                <a href="buku_tambah.php" class="btn-tambah">Tambah Buku</a>
                <a href="pegawai_tambah.php" class="btn-tambah">Tambah Pegawai</a>
                <a href="pengunjung_tambah.php" class="btn-tambah">Tambah Pengunjung</a>
            </div>

            <section class="dashboard-section">
                <h3>Ringkasan</h3>
                <div class="dashboard-mini-stats">
                    <div class="mini-stat">
                        <span class="mini-stat-label">Total Buku</span>
                        <strong class="mini-stat-value"><?= $total_buku; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Stok Tersedia</span>
                        <strong class="mini-stat-value"><?= $stok_tersedia; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Total Anggota</span>
                        <strong class="mini-stat-value"><?= $total_anggota; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Total Pegawai</span>
                        <strong class="mini-stat-value"><?= $total_pegawai; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Peminjaman Aktif</span>
                        <strong class="mini-stat-value"><?= $peminjaman_aktif; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Sudah Kembali</span>
                        <strong class="mini-stat-value"><?= $peminjaman_selesai; ?></strong>
                    </div>
                </div>
                <p class="dashboard-intro">Stok buku yang tersedia saat ini berjumlah <strong><?= $stok_tersedia; ?></strong> dan ada <strong><?= $peminjaman_aktif; ?></strong> peminjaman yang masih berjalan.</p>
            </section>

            <section class="dashboard-section">
                <div class="section-header">
                    <h3>Peminjaman Terakhir</h3>
                    <a href="peminjaman.php" class="btn-view-all">Lihat Semua</a>
                </div>

                <table border="1" cellpadding="10" cellspacing="0" class="tabel dashboard-simple-table">
                    <tr>
                        <th>ID</th>
                        <th>Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                    </tr>
                    <?php if (mysqli_num_rows($recent) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($recent)): ?>
                            <tr>
                                <td><?= $row['id_peminjaman']; ?></td>
                                <td><?= $row['nama'] ?? '-'; ?></td>
                                <td><?= $row['judul'] ?? '-'; ?></td>
                                <td><?= $row['tgl_pinjam']; ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($row['status'] ?? 'unknown'); ?>">
                                        <?= $row['status'] ?? '-'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="dashboard-empty">Belum ada data peminjaman</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </section>
        </div>
    </main>

    <footer>
        &copy; 2025 Bima Revan Saputra XI RPL 2
    </footer>
</body>

</html>
