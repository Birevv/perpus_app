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

$buku = mysqli_query(
    $koneksi,
    "SELECT * FROM buku
     $where_buku
     ORDER BY judul ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Pengunjung</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard_pengunjung.php">Dashboard</a></li>
            <li><a href="buku_pengunjung.php" class="active">Buku</a></li>
            <li><a href="riwayat_pengunjung.php">Riwayat</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main>
        <div class="main katalog-page">
            <h2>Data Buku</h2>
            <p class="catalog-subtitle">Gunakan pencarian untuk menemukan judul, pengarang, genre, penerbit, atau ISBN buku yang ingin dilihat.</p>

            <div class="catalog-toolbar">
                <form action="buku_pengunjung.php" method="get" class="dashboard-search-form catalog-search-form">
                    <input
                        type="text"
                        name="q"
                        value="<?= htmlspecialchars($keyword); ?>"
                        class="dashboard-search-input catalog-search-input"
                        placeholder="Cari judul buku, ISBN, pengarang, penerbit, tahun, atau genre">
                    <button type="submit" class="btn-submit dashboard-search-button">Cari Buku</button>
                    <?php if ($keyword !== ''): ?>
                        <a href="buku_pengunjung.php" class="btn-back dashboard-search-reset">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <table border="1" cellpadding="10" cellspacing="0" class="tabel catalog-table">
                <tr>
                    <th>ISBN</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Genre</th>
                    <th>Stok</th>
                </tr>
                <?php if (mysqli_num_rows($buku) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($buku)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['isbn']); ?></td>
                            <td><?= htmlspecialchars($row['judul']); ?></td>
                            <td><?= htmlspecialchars($row['pengarang']); ?></td>
                            <td><?= htmlspecialchars($row['penerbit']); ?></td>
                            <td><?= htmlspecialchars($row['tahun']); ?></td>
                            <td><?= htmlspecialchars($row['genre']); ?></td>
                            <td>
                                <span class="stock-badge <?= (int) $row['stok'] > 0 ? 'available' : 'empty'; ?>">
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
            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Bima Revan Saputra XI RPL 2
    </footer>
</body>

</html>
