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

include 'koneksi.php';

$keyword = trim($_GET['q'] ?? '');
$keyword_escaped = mysqli_real_escape_string($koneksi, $keyword);
$where_peminjaman = '';

if ($keyword !== '') {
    $where_peminjaman = "WHERE p.id_peminjaman LIKE '%$keyword_escaped%'
        OR p.id_anggota LIKE '%$keyword_escaped%'
        OR p.isbn LIKE '%$keyword_escaped%'
        OR a.nama LIKE '%$keyword_escaped%'
        OR b.judul LIKE '%$keyword_escaped%'
        OR p.status LIKE '%$keyword_escaped%'";
}

$per_page = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$count_query = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) as total
     FROM peminjaman p
     LEFT JOIN anggota a ON p.id_anggota = a.id_anggota
     LEFT JOIN buku b ON p.isbn = b.isbn
     $where_peminjaman"
);
$total_data = (int) mysqli_fetch_assoc($count_query)['total'];
$total_pages = max(1, (int) ceil($total_data / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;
$query_string = $keyword !== '' ? '&q=' . urlencode($keyword) : '';

$query = mysqli_query(
    $koneksi,
    "SELECT p.*, a.nama, b.judul
     FROM peminjaman p
     LEFT JOIN anggota a ON p.id_anggota = a.id_anggota
     LEFT JOIN buku b ON p.isbn = b.isbn
     $where_peminjaman
     ORDER BY p.tgl_pinjam DESC
     LIMIT $per_page OFFSET $offset"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=admin-dashboard-4">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Peminjaman</title>
</head>

<body class="admin-dashboard-body">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="admin-brand-icon icon-book"></span>
            <div>
                <h1>E Library</h1>
                <span><?= $is_pegawai ? 'Staff Panel' : 'Admin Panel'; ?></span>
            </div>
        </div>

        <ul class="admin-menu">
            <li><a href="<?= $is_pegawai ? 'dashboard_pegawai.php' : 'dashboard.php'; ?>"><span class="menu-icon icon-dashboard"></span>Dashboard</a></li>
            <li><a href="<?= $is_pegawai ? 'buku_pegawai.php' : 'buku.php'; ?>"><span class="menu-icon icon-book"></span>Data Buku</a></li>
            <?php if (!$is_pegawai): ?>
                <li><a href="pegawai.php"><span class="menu-icon icon-briefcase"></span>Data Pegawai</a></li>
                <li><a href="pengunjung.php"><span class="menu-icon icon-users"></span>Data Pengunjung</a></li>
                <li><a href="user.php"><span class="menu-icon icon-users"></span>Data User</a></li>
            <?php endif; ?>
            <li><a href="peminjaman.php" class="active"><span class="menu-icon icon-transfer"></span>Peminjaman</a></li>
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
            <div class="admin-page-header">
                <h2>Data Peminjaman</h2>
                <a href="form_peminjaman.php" class="admin-add-btn">+ Tambah Peminjaman</a>
            </div>

            <form class="admin-list-search" action="peminjaman.php" method="get">
                <span class="search-icon icon-search"></span>
                <input type="text" name="q" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari data peminjaman...">
            </form>

            <section class="admin-table-card">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul Buku</th>
                            <th>Peminjam</th>
                            <th>ISBN</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_data > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['id_peminjaman']); ?></td>
                                    <td class="admin-table-title"><?= htmlspecialchars($data['judul'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($data['nama'] ?? $data['id_anggota']); ?></td>
                                    <td><?= htmlspecialchars($data['isbn']); ?></td>
                                    <td><?= htmlspecialchars($data['tgl_pinjam']); ?></td>
                                    <td><?= htmlspecialchars($data['tgl_kembali'] ?: '-'); ?></td>
                                    <td>
                                        <span class="admin-status status-<?= strtolower($data['status'] ?: 'dipinjam'); ?>">
                                            <?= htmlspecialchars($data['status'] ?: 'Dipinjam'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-table-actions">
                                            <a href="peminjaman_edit.php?id_peminjaman=<?= urlencode($data['id_peminjaman']); ?>" class="admin-icon-action edit">Edit</a>
                                            <a href="peminjaman_hapus.php?id_peminjaman=<?= urlencode($data['id_peminjaman']); ?>" class="admin-icon-action delete" onclick="return confirm('Hapus data peminjaman ini?')">Hapus</a>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (empty($data['status']) || strtolower($data['status']) === 'dipinjam'): ?>
                                            <form action="peminjaman_kembalikan.php" method="post" class="inline-action-form" onsubmit="return confirm('Konfirmasi pengembalian buku?')">
                                                <input type="hidden" name="id_peminjaman" value="<?= htmlspecialchars($data['id_peminjaman']); ?>">
                                                <button type="submit" class="admin-return-btn">Kembalikan</button>
                                            </form>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="dashboard-empty">Data peminjaman belum ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="admin-table-footer">
                        <span>Halaman <?= $page; ?> dari <?= $total_pages; ?></span>
                        <div class="admin-pagination">
                            <?php if ($page > 1): ?>
                                <a href="peminjaman.php?page=<?= $page - 1; ?><?= $query_string; ?>">Prev</a>
                            <?php else: ?>
                                <span class="disabled">Prev</span>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="peminjaman.php?page=<?= $i; ?><?= $query_string; ?>" class="<?= $i === $page ? 'active' : ''; ?>"><?= $i; ?></a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="peminjaman.php?page=<?= $page + 1; ?><?= $query_string; ?>">Next</a>
                            <?php else: ?>
                                <span class="disabled">Next</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
