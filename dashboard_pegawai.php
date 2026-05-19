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
    <link rel="stylesheet" href="styles.css?v=admin-dashboard-5">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>Dashboard Pegawai - Library</title>
</head>

<body class="admin-dashboard-body">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="admin-brand-icon icon-book"></span>
            <div>
                <h1>E Library</h1>
                <span>Staff Panel</span>
            </div>
        </div>

        <ul class="admin-menu">
            <li><a href="dashboard_pegawai.php" class="active"><span class="menu-icon icon-dashboard"></span>Dashboard</a></li>
            <li><a href="buku_pegawai.php"><span class="menu-icon icon-book"></span>Data Buku</a></li>
            <li><a href="peminjaman.php"><span class="menu-icon icon-transfer"></span>Peminjaman</a></li>
        </ul>

        <a href="logout.php" class="admin-logout"><span class="menu-icon icon-logout"></span>Logout</a>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div class="admin-top-actions">
                <button type="button" aria-label="Notifikasi" class="top-icon icon-bell"></button>
            </div>
        </header>

        <div class="admin-content">
            <section class="admin-title-row">
                <h2>Dashboard</h2>
                <p>Ringkasan data perpustakaan.</p>
            </section>

            <section class="admin-stat-grid">
                <a href="buku_pegawai.php" class="admin-stat-card">
                    <span class="admin-stat-label">Total Buku</span>
                    <strong><?= number_format((int) $total_buku, 0, ',', '.'); ?></strong>
                </a>

                <a href="buku_pegawai.php" class="admin-stat-card">
                    <span class="admin-stat-label">Stok Tersedia</span>
                    <strong><?= number_format((int) $stok_tersedia, 0, ',', '.'); ?></strong>
                </a>

                <a href="peminjaman.php" class="admin-stat-card">
                    <span class="admin-stat-label">Total Peminjaman</span>
                    <strong><?= number_format((int) $total_peminjaman, 0, ',', '.'); ?></strong>
                </a>

                <a href="peminjaman.php" class="admin-stat-card">
                    <span class="admin-stat-label">Peminjaman Aktif</span>
                    <strong><?= number_format((int) $peminjaman_aktif, 0, ',', '.'); ?></strong>
                </a>
            </section>

            <div class="staff-dashboard-actions">
                <a href="form_peminjaman.php" class="admin-add-btn">+ Tambah Peminjaman</a>
                <a href="buku_pegawai.php" class="visitor-action-btn secondary"><span class="menu-icon icon-book"></span>Lihat Buku</a>
                <a href="peminjaman.php" class="visitor-action-btn secondary"><span class="menu-icon icon-transfer"></span>Data Peminjaman</a>
            </div>

            <section class="admin-table-card">
                <div class="admin-section-header">
                    <h3>Peminjaman Terakhir</h3>
                    <a href="peminjaman.php">View All →</a>
                </div>

                <table class="admin-recent-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul Buku</th>
                            <th>Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($recent) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($recent)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_peminjaman']); ?></td>
                                <td><?= htmlspecialchars($row['judul'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['tgl_pinjam']); ?></td>
                                <td>
                                    <span class="admin-status status-<?= strtolower($row['status'] ?: 'dipinjam'); ?>">
                                        <?= htmlspecialchars($row['status'] ?: 'Dipinjam'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="dashboard-empty">Belum ada data peminjaman</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
</body>

</html>
