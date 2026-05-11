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
$stok_tersedia = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(stok), 0) as total FROM buku"))['total'];
$peminjaman_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status IS NULL OR status = '' OR LOWER(status) = 'dipinjam'"))['total'];

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
    <link rel="stylesheet" href="styles.css?v=admin-dashboard-4">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Dashboard - Library</title>
</head>

<body class="admin-dashboard-body">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="admin-brand-icon icon-book"></span>
            <div>
                <h1>E Library</h1>
                <span>Admin Panel</span>
            </div>
        </div>

        <ul class="admin-menu">
            <li><a href="dashboard.php" class="active"><span class="menu-icon icon-dashboard"></span>Dashboard</a></li>
            <li><a href="buku.php"><span class="menu-icon icon-book"></span>Data Buku</a></li>
            <li><a href="pegawai.php"><span class="menu-icon icon-briefcase"></span>Data Pegawai</a></li>
            <li><a href="pengunjung.php"><span class="menu-icon icon-users"></span>Data Pengunjung</a></li>
            <li><a href="user.php"><span class="menu-icon icon-users"></span>Data User</a></li>
            <li><a href="peminjaman.php"><span class="menu-icon icon-transfer"></span>Peminjaman</a></li>
        </ul>

        <a href="logout.php" class="admin-logout"><span class="menu-icon icon-logout"></span>Logout</a>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div class="admin-top-actions">
                <button type="button" aria-label="Notifikasi" class="top-icon icon-bell"></button>
                <button type="button" aria-label="Bantuan">?</button>
                <div class="admin-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
            </div>
        </header>

        <div class="admin-content">
            <section class="admin-title-row">
                <h2>Dashboard Admin</h2>
                <p>Welcome back, here is what's happening today.</p>
            </section>

            <section class="admin-stat-grid">
                <a href="buku.php" class="admin-stat-card">
                    <div class="admin-stat-top">
                        <span class="admin-stat-icon icon-book"></span>
                        <span class="admin-stat-pill positive">+4% this week</span>
                    </div>
                    <span class="admin-stat-label">Total Buku</span>
                    <strong><?= number_format((int) $total_buku, 0, ',', '.'); ?></strong>
                </a>

                <a href="pengunjung.php" class="admin-stat-card">
                    <div class="admin-stat-top">
                        <span class="admin-stat-icon icon-users"></span>
                        <span class="admin-stat-pill positive">+12 new</span>
                    </div>
                    <span class="admin-stat-label">Total Anggota</span>
                    <strong><?= number_format((int) $total_anggota, 0, ',', '.'); ?></strong>
                </a>

                <a href="buku.php" class="admin-stat-card">
                    <div class="admin-stat-top">
                        <span class="admin-stat-icon icon-stock"></span>
                        <span class="admin-stat-pill warning">-2% today</span>
                    </div>
                    <span class="admin-stat-label">Stok Tersedia</span>
                    <strong><?= number_format((int) $stok_tersedia, 0, ',', '.'); ?></strong>
                </a>

                <a href="peminjaman.php" class="admin-stat-card active">
                    <div class="admin-stat-top">
                        <span class="admin-stat-icon icon-transfer"></span>
                        <span class="admin-stat-pill light">Active Now</span>
                    </div>
                    <span class="admin-stat-label">Peminjaman Aktif</span>
                    <strong><?= number_format((int) $peminjaman_aktif, 0, ',', '.'); ?></strong>
                </a>
            </section>

            <section class="admin-table-card">
                <div class="admin-section-header">
                    <h3>Peminjaman Terbaru</h3>
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
