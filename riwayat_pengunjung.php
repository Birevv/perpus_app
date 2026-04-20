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
$riwayat_query = false;

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

    $riwayat_query = mysqli_query(
        $koneksi,
        "SELECT p.id_peminjaman, p.tgl_pinjam, p.tgl_kembali, p.status, b.judul, b.isbn
         FROM peminjaman p
         LEFT JOIN buku b ON p.isbn = b.isbn
         WHERE p.id_anggota = '$id_anggota_escaped'
         ORDER BY p.tgl_pinjam DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengunjung</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard_pengunjung.php">Dashboard</a></li>
            <li><a href="buku_pengunjung.php">Buku</a></li>
            <li><a href="riwayat_pengunjung.php" class="active">Riwayat</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main>
        <div class="main">
            <h2>Riwayat Peminjaman</h2>
            <p class="dashboard-intro">Halaman ini menampilkan seluruh riwayat peminjaman berdasarkan ID pengguna Anda.</p>

            <div class="tombol-tambah dashboard-actions">
                <a href="buku_pengunjung.php" class="btn-tambah">Lihat Buku</a>
            </div>

            <section class="dashboard-section">
                <h3>Ringkasan Riwayat</h3>
                <div class="dashboard-mini-stats">
                    <div class="mini-stat">
                        <span class="mini-stat-label">ID Anggota</span>
                        <strong class="mini-stat-value"><?= $id_anggota !== '' ? htmlspecialchars($id_anggota) : '-'; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Total Riwayat</span>
                        <strong class="mini-stat-value"><?= $riwayat_saya; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Pinjaman Aktif</span>
                        <strong class="mini-stat-value"><?= $pinjaman_aktif; ?></strong>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <h3>Daftar Riwayat</h3>

                <?php if ($id_anggota === ''): ?>
                    <p class="dashboard-empty dashboard-empty-left">Data anggota yang terhubung ke akun ini belum ditemukan.</p>
                <?php elseif ($riwayat_query && mysqli_num_rows($riwayat_query) > 0): ?>
                    <table border="1" cellpadding="10" cellspacing="0" class="tabel dashboard-simple-table">
                        <tr>
                            <th>ID</th>
                            <th>ISBN</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                        </tr>
                        <?php while ($row = mysqli_fetch_assoc($riwayat_query)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_peminjaman']); ?></td>
                                <td><?= htmlspecialchars($row['isbn'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['judul'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['tgl_pinjam']); ?></td>
                                <td><?= htmlspecialchars($row['tgl_kembali'] ?: '-'); ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($row['status'] ?? 'unknown'); ?>">
                                        <?= htmlspecialchars($row['status'] ?? '-'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p class="dashboard-empty dashboard-empty-left">Belum ada riwayat peminjaman untuk ID pengguna ini.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer>
        &copy; 2025 Bima Revan Saputra XI RPL 2
    </footer>
</body>

</html>
