<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'user') {
    header("Location: dashboard.php");
    exit;
}

include 'koneksi.php';

$id_user = mysqli_real_escape_string($koneksi, $_SESSION['id_user']);
$user_query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user' LIMIT 1");
$user = mysqli_fetch_assoc($user_query);

if (!$user) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$nama_user = mysqli_real_escape_string($koneksi, $user['nama']);
$anggota_query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nama = '$nama_user' LIMIT 1");
$anggota = mysqli_fetch_assoc($anggota_query);
$id_anggota = $anggota['id_anggota'] ?? '';

$riwayat_saya = 0;
$pinjaman_aktif = 0;
$total_pages = 1;
$page = 1;
$per_page = 5;

if ($id_anggota !== '') {
    $id_anggota_escaped = mysqli_real_escape_string($koneksi, $id_anggota);

    $riwayat_saya = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE id_anggota = '$id_anggota_escaped'")
    )['total'];

    $pinjaman_aktif = mysqli_fetch_assoc(
        mysqli_query(
            $koneksi,
            "SELECT COUNT(*) as total
             FROM peminjaman
             WHERE id_anggota = '$id_anggota_escaped'
             AND (status IS NULL OR status = '' OR LOWER(status) = 'dipinjam')"
        )
    )['total'];

    $page = max(1, (int) ($_GET['page'] ?? 1));
    $total_pages = max(1, (int) ceil($riwayat_saya / $per_page));
    $page = min($page, $total_pages);
    $offset = ($page - 1) * $per_page;

    $riwayat_query = mysqli_query(
        $koneksi,
        "SELECT p.id_peminjaman, p.tgl_pinjam, p.tgl_kembali, p.status, b.judul, b.isbn
         FROM peminjaman p
         LEFT JOIN buku b ON p.isbn = b.isbn
         WHERE p.id_anggota = '$id_anggota_escaped'
         ORDER BY p.tgl_pinjam DESC
         LIMIT $per_page OFFSET $offset"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengunjung</title>
    <link rel="stylesheet" href="styles.css?v=visitor-dashboard-2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
</head>

<body class="visitor-dashboard-body">
    <aside class="visitor-sidebar">
        <div class="visitor-brand">
            <span class="visitor-brand-icon icon-book"></span>
            <div>
                <h1>E Library</h1>
                <span>Portal Pengunjung</span>
            </div>
        </div>

        <ul class="visitor-menu">
            <li><a href="dashboard_pengunjung.php"><span class="menu-icon icon-dashboard"></span>Dashboard</a></li>
            <li><a href="buku_pengunjung.php"><span class="menu-icon icon-book"></span>Buku</a></li>
            <li><a href="riwayat_pengunjung.php" class="active"><span class="menu-icon icon-history"></span>Riwayat</a></li>
        </ul>

        <a href="logout.php" class="visitor-logout"><span class="menu-icon icon-logout"></span>Logout</a>
    </aside>

    <main class="visitor-main">
        <header class="visitor-topbar">
            <strong>Dashboard Pengunjung</strong>
            <div class="visitor-top-actions">
                <button type="button" aria-label="Notifikasi" class="top-icon icon-bell"></button>
            </div>
        </header>

        <div class="visitor-content">
            <div class="visitor-history-header">
                <section class="visitor-title">
                    <h2>Riwayat Peminjaman</h2>
                    <p>Kelola dan pantau semua aktivitas peminjaman buku Anda.</p>
                </section>
                <a href="buku_pengunjung.php" class="visitor-action-btn primary"><span class="menu-icon icon-book"></span>Lihat Buku</a>
            </div>

            <section class="visitor-history-stats">
                <div class="visitor-history-stat">
                    <span class="visitor-history-icon"><i class="icon-briefcase"></i></span>
                    <div>
                        <small>ID Anggota</small>
                        <strong><?= $id_anggota !== '' ? htmlspecialchars($id_anggota) : '-'; ?></strong>
                    </div>
                </div>
                <div class="visitor-history-stat">
                    <span class="visitor-history-icon blue"><i class="icon-book"></i></span>
                    <div>
                        <small>Total Riwayat</small>
                        <strong><?= number_format((int) $riwayat_saya, 0, ',', '.'); ?> <em>Buku</em></strong>
                    </div>
                </div>
                <div class="visitor-history-stat">
                    <span class="visitor-history-icon orange"><i class="icon-book"></i></span>
                    <div>
                        <small>Pinjaman Aktif</small>
                        <strong><?= number_format((int) $pinjaman_aktif, 0, ',', '.'); ?> <em>Buku</em></strong>
                    </div>
                </div>
            </section>

            <section class="admin-table-card visitor-history-table-card">
                <div class="admin-section-header visitor-history-table-header">
                    <h3>Daftar Transaksi</h3>
                    <span class="visitor-table-tools">•••</span>
                </div>

                <?php if ($id_anggota === ''): ?>
                    <p class="dashboard-empty visitor-empty">Data anggota yang terhubung ke akun ini belum ditemukan.</p>
                <?php elseif ($riwayat_query && mysqli_num_rows($riwayat_query) > 0): ?>
                    <table class="admin-data-table visitor-history-table">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>ISBN</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($riwayat_query)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_peminjaman']); ?></td>
                                    <td><?= htmlspecialchars($row['isbn'] ?? '-'); ?></td>
                                    <td class="admin-table-title"><?= htmlspecialchars($row['judul'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['tgl_pinjam']); ?></td>
                                    <td><?= htmlspecialchars($row['tgl_kembali'] ?: '-'); ?></td>
                                    <td>
                                        <span class="admin-status status-<?= strtolower($row['status'] ?: 'dipinjam'); ?>">
                                            <?= htmlspecialchars($row['status'] ?: 'Dipinjam'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <?php if ($total_pages > 1): ?>
                        <div class="admin-table-footer">
                            <span>Menampilkan halaman <?= $page; ?> dari <?= $total_pages; ?></span>
                            <div class="admin-pagination">
                                <?php if ($page > 1): ?>
                                    <a href="riwayat_pengunjung.php?page=<?= $page - 1; ?>">Prev</a>
                                <?php else: ?>
                                    <span class="disabled">Prev</span>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="riwayat_pengunjung.php?page=<?= $i; ?>" class="<?= $i === $page ? 'active' : ''; ?>"><?= $i; ?></a>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <a href="riwayat_pengunjung.php?page=<?= $page + 1; ?>">Next</a>
                                <?php else: ?>
                                    <span class="disabled">Next</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="dashboard-empty visitor-empty">Belum ada riwayat peminjaman untuk ID pengguna ini.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>

</html>
