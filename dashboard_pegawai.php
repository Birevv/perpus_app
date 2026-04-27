<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'pegawai') {
    if (($_SESSION['level'] ?? '') === 'admin') {
        header("Location: dashboard.php");
    } elseif (($_SESSION['level'] ?? '') === 'user') {
        header("Location: dashboard_pengunjung.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

include 'koneksi.php';

$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku"))['total'];
$stok_tersedia = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(stok), 0) as total FROM buku"))['total'];
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman"))['total'];
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
    <title>Dashboard Pegawai - Library</title>
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard_pegawai.php" class="active">Dashboard</a></li>
            <li><a href="buku_pegawai.php">Buku</a></li>
            <li><a href="peminjaman.php">Peminjaman</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main>
        <div class="main">
            <h2>Dashboard Pegawai</h2>
            <p class="dashboard-intro">Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?>. Di sini Anda bisa melihat koleksi buku dan mengelola transaksi peminjaman.</p>

            <div class="tombol-tambah dashboard-actions">
                <a href="form_peminjaman.php" class="btn-tambah">Tambah Peminjaman</a>
                <a href="buku_pegawai.php" class="btn-tambah">Lihat Buku</a>
                <a href="peminjaman.php" class="btn-tambah">Data Peminjaman</a>
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
                        <span class="mini-stat-label">Total Peminjaman</span>
                        <strong class="mini-stat-value"><?= $total_peminjaman; ?></strong>
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
            </section>

            <div class="dashboard-grid">
                <section class="dashboard-section dashboard-section-wide">
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
                                    <td><?= htmlspecialchars($row['id_peminjaman']); ?></td>
                                    <td><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['judul'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['tgl_pinjam']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($row['status'] ?? 'unknown'); ?>">
                                            <?= htmlspecialchars($row['status'] ?? '-'); ?>
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

                <div class="dashboard-sidebar-stack">
                    <section class="dashboard-section">
                        <h3>Akses Cepat</h3>
                        <div class="quick-actions dashboard-quick-actions">
                            <a href="buku_pegawai.php" class="quick-action-btn">
                                <span class="qa-icon">BK</span>
                                <span>Lihat Buku</span>
                            </a>
                            <a href="form_peminjaman.php" class="quick-action-btn">
                                <span class="qa-icon">PJ</span>
                                <span>Tambah Peminjaman</span>
                            </a>
                            <a href="peminjaman.php" class="quick-action-btn">
                                <span class="qa-icon">DT</span>
                                <span>Data Peminjaman</span>
                            </a>
                            <a href="logout.php" class="quick-action-btn">
                                <span class="qa-icon">LO</span>
                                <span>Logout</span>
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <footer>
        &copy; 2025 Bima Revan Saputra XI RPL 2
    </footer>
</body>

</html>
