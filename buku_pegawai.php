<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION['level'] ?? '') !== 'pegawai') {
    if (($_SESSION['level'] ?? '') === 'admin') {
        header("Location: buku.php");
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

$buku = mysqli_query(
    $koneksi,
    "SELECT * FROM buku
     $where_buku
     ORDER BY judul ASC
     LIMIT $per_page OFFSET $offset"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Pegawai</title>
    <link rel="stylesheet" href="styles.css?v=admin-dashboard-4">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
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
            <li><a href="dashboard_pegawai.php"><span class="menu-icon icon-dashboard"></span>Dashboard</a></li>
            <li><a href="buku_pegawai.php" class="active"><span class="menu-icon icon-book"></span>Data Buku</a></li>
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
            </div>

            <form class="admin-list-search" action="buku_pegawai.php" method="get">
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($buku) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($buku)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['isbn']); ?></td>
                                    <td class="admin-table-title"><?= htmlspecialchars($row['judul']); ?></td>
                                    <td><?= htmlspecialchars($row['pengarang']); ?></td>
                                    <td><?= htmlspecialchars($row['penerbit']); ?></td>
                                    <td><?= htmlspecialchars($row['tahun']); ?></td>
                                    <td><?= htmlspecialchars($row['genre']); ?></td>
                                    <td>
                                        <span class="admin-stock-pill <?= (int) $row['stok'] > 0 ? 'available' : 'empty'; ?>">
                                            <?= (int) $row['stok']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="dashboard-empty">Buku yang dicari belum ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="admin-table-footer">
                        <span>Halaman <?= $page; ?> dari <?= $total_pages; ?></span>
                        <div class="admin-pagination">
                            <?php if ($page > 1): ?>
                                <a href="buku_pegawai.php?page=<?= $page - 1; ?><?= $query_string; ?>">Prev</a>
                            <?php else: ?>
                                <span class="disabled">Prev</span>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="buku_pegawai.php?page=<?= $i; ?><?= $query_string; ?>" class="<?= $i === $page ? 'active' : ''; ?>"><?= $i; ?></a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="buku_pegawai.php?page=<?= $page + 1; ?><?= $query_string; ?>">Next</a>
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
