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

$keyword = trim($_GET['q'] ?? '');
$keyword_escaped = mysqli_real_escape_string($koneksi, $keyword);
$where_user = '';

if ($keyword !== '') {
    $where_user = "WHERE id_user LIKE '%$keyword_escaped%'
        OR nama LIKE '%$keyword_escaped%'
        OR username LIKE '%$keyword_escaped%'
        OR level LIKE '%$keyword_escaped%'";
}

$per_page = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total_data = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM `user` $where_user"))['total'];
$total_pages = max(1, (int) ceil($total_data / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;
$query_string = $keyword !== '' ? '&q=' . urlencode($keyword) : '';

$query = mysqli_query($koneksi, "SELECT id_user, nama, username, password, level FROM `user` $where_user ORDER BY CAST(id_user AS UNSIGNED) DESC, id_user DESC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=admin-dashboard-4">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>User</title>
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
            <li><a href="buku.php"><span class="menu-icon icon-book"></span>Data Buku</a></li>
            <li><a href="pegawai.php"><span class="menu-icon icon-briefcase"></span>Data Pegawai</a></li>
            <li><a href="pengunjung.php"><span class="menu-icon icon-users"></span>Data Pengunjung</a></li>
            <li><a href="user.php" class="active"><span class="menu-icon icon-users"></span>Data User</a></li>
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
                <h2>Data User</h2>
                <a href="user_tambah.php" class="admin-add-btn">+ Tambah User</a>
            </div>

            <form class="admin-list-search" action="user.php" method="get">
                <span class="search-icon icon-search"></span>
                <input type="text" name="q" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari data user...">
            </form>

            <section class="admin-table-card">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>ID User</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Level</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_data > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['id_user']); ?></td>
                                    <td class="admin-table-title"><?= htmlspecialchars($data['nama']); ?></td>
                                    <td><?= htmlspecialchars($data['username']); ?></td>
                                    <td>******</td>
                                    <td><?= htmlspecialchars($data['level']); ?></td>
                                    <td>
                                        <div class="admin-table-actions">
                                            <a href="user_edit.php?id_user=<?= urlencode($data['id_user']); ?>" class="admin-icon-action edit">Edit</a>
                                            <a href="user_hapus.php?id_user=<?= urlencode($data['id_user']); ?>" class="admin-icon-action delete" onclick="return confirm('Hapus data user ini?')">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="dashboard-empty">Data user belum ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="admin-table-footer">
                        <span>Halaman <?= $page; ?> dari <?= $total_pages; ?></span>
                        <div class="admin-pagination">
                            <?php if ($page > 1): ?>
                                <a href="user.php?page=<?= $page - 1; ?><?= $query_string; ?>">Prev</a>
                            <?php else: ?>
                                <span class="disabled">Prev</span>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="user.php?page=<?= $i; ?><?= $query_string; ?>" class="<?= $i === $page ? 'active' : ''; ?>"><?= $i; ?></a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="user.php?page=<?= $page + 1; ?><?= $query_string; ?>">Next</a>
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
