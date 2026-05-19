<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'admin') {
    if (($_SESSION['level'] ?? '') === 'pegawai') {
        header("Location: buku_pegawai.php");
    } elseif (($_SESSION['level'] ?? '') === 'user') {
        header("Location: buku_pengunjung.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

include 'koneksi.php';

$keyword = trim($_GET['q'] ?? '');
$keyword_escaped = mysqli_real_escape_string($koneksi, $keyword);
$where_buku = '';

if ($keyword !== '') {
    $where_buku = "WHERE isbn LIKE '%$keyword_escaped%'
        OR judul LIKE '%$keyword_escaped%'
        OR pengarang LIKE '%$keyword_escaped%'
        OR penerbit LIKE '%$keyword_escaped%'
        OR genre LIKE '%$keyword_escaped%'
        OR tahun LIKE '%$keyword_escaped%'";
}

$per_page = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total_data = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku $where_buku"))['total'];
$total_pages = max(1, (int) ceil($total_data / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;
$query_string = $keyword !== '' ? '&q=' . urlencode($keyword) : '';

$query = mysqli_query($koneksi, "SELECT * FROM buku $where_buku ORDER BY judul ASC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=admin-dashboard-4">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Buku</title>

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
            <li><a href="dashboard.php"><span class="menu-icon icon-dashboard"></span>Dashboard</a></li>
            <li><a href="buku.php" class="active"><span class="menu-icon icon-book"></span>Data Buku</a></li>
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
            </div>
        </header>

        <div class="admin-content">
            <div class="admin-page-header">
                <h2>Data Buku</h2>
                <a href="buku_tambah.php" class="admin-add-btn">+ Tambah Buku</a>
            </div>

            <form class="admin-list-search" action="buku.php" method="get">
                <span class="search-icon icon-search"></span>
                <input type="text" name="q" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari data buku...">
            </form>

            <section class="admin-table-card">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th>Tahun Terbit</th>
                            <th>Genre</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_data > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['isbn']); ?></td>
                                    <td class="admin-table-title"><?= htmlspecialchars($data['judul']); ?></td>
                                    <td><?= htmlspecialchars($data['pengarang']); ?></td>
                                    <td><?= htmlspecialchars($data['penerbit']); ?></td>
                                    <td><?= htmlspecialchars($data['tahun']); ?></td>
                                    <td><?= htmlspecialchars($data['genre']); ?></td>
                                    <td>
                                        <span class="admin-stock-pill <?= (int) $data['stok'] > 0 ? 'available' : 'empty'; ?>">
                                            <?= (int) $data['stok']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-table-actions">
                                            <a href="buku_edit.php?isbn=<?= urlencode($data['isbn']); ?>" class="admin-icon-action edit">Edit</a>
                                            <a href="buku_hapus.php?isbn=<?= urlencode($data['isbn']); ?>" class="admin-icon-action delete" onclick="return confirm('Hapus data buku ini?')">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="dashboard-empty">Data buku belum ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="admin-table-footer">
                        <span>Halaman <?= $page; ?> dari <?= $total_pages; ?></span>
                        <div class="admin-pagination">
                            <?php if ($page > 1): ?>
                                <a href="buku.php?page=<?= $page - 1; ?><?= $query_string; ?>">Prev</a>
                            <?php else: ?>
                                <span class="disabled">Prev</span>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="buku.php?page=<?= $i; ?><?= $query_string; ?>" class="<?= $i === $page ? 'active' : ''; ?>"><?= $i; ?></a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="buku.php?page=<?= $page + 1; ?><?= $query_string; ?>">Next</a>
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

</html>
