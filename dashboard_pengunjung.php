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

$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku"))['total'];
$stok_tersedia = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(stok), 0) as total FROM buku"))['total'];

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
         ORDER BY p.tgl_pinjam DESC
         LIMIT 8"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard_pengunjung.php" class="active">Dashboard</a></li>
            <li><a href="buku_pengunjung.php">Buku</a></li>
            <li><a href="riwayat_pengunjung.php">Riwayat</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main>
        <div class="main">
            <h2>Dashboard Pengunjung</h2>
            <p class="dashboard-intro">Selamat datang, <?= htmlspecialchars($user['nama']); ?>. Di sini Anda bisa melihat ringkasan akun dan akses cepat ke buku serta riwayat peminjaman.</p>

            <div class="tombol-tambah dashboard-actions">
                <a href="buku_pengunjung.php" class="btn-tambah">Lihat Buku</a>
                <a href="riwayat_pengunjung.php" class="btn-tambah">Riwayat Saya</a>
            </div>

            <section class="dashboard-section">
                <h3>Ringkasan</h3>
                <div class="dashboard-mini-stats">
                    <div class="mini-stat">
                        <span class="mini-stat-label">Total Koleksi</span>
                        <strong class="mini-stat-value"><?= $total_buku; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Stok Tersedia</span>
                        <strong class="mini-stat-value"><?= $stok_tersedia; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Riwayat Saya</span>
                        <strong class="mini-stat-value"><?= $riwayat_saya; ?></strong>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Pinjaman Aktif</span>
                        <strong class="mini-stat-value"><?= $pinjaman_aktif; ?></strong>
                    </div>
                </div>
            </section>

            <div class="dashboard-grid">
                <section class="dashboard-section dashboard-section-wide" id="riwayat-saya">
                    <div class="section-header">
                        <h3>Akses Cepat</h3>
                    </div>

                    <div class="quick-actions">
                        <a href="buku_pengunjung.php" class="quick-action-btn">
                            <span class="qa-icon">BK</span>
                            <span>Lihat Buku</span>
                        </a>
                        <a href="riwayat_pengunjung.php" class="quick-action-btn">
                            <span class="qa-icon">RW</span>
                            <span>Riwayat Saya</span>
                        </a>
                        <a href="logout.php" class="quick-action-btn">
                            <span class="qa-icon">LO</span>
                            <span>Logout</span>
                        </a>
                    </div>
                </section>

                <div class="dashboard-sidebar-stack">
                    <section class="dashboard-section">
                        <h3>Profil Pengguna</h3>

                        <div class="dashboard-info-list">
                            <div class="info-item">
                                <span>Nama</span>
                                <strong><?= htmlspecialchars($user['nama']); ?></strong>
                            </div>
                            <div class="info-item">
                                <span>Username</span>
                                <strong><?= htmlspecialchars($user['username']); ?></strong>
                            </div>
                            <div class="info-item">
                                <span>ID Anggota</span>
                                <strong><?= $id_anggota !== '' ? htmlspecialchars($id_anggota) : '-'; ?></strong>
                            </div>
                            <div class="info-item">
                                <span>No. HP</span>
                                <strong><?= $anggota ? htmlspecialchars($anggota['no_hp']) : '-'; ?></strong>
                            </div>
                        </div>
                    </section>

                    <section class="dashboard-section">
                        <h3>Status Akun</h3>

                        <div class="dashboard-info-list">
                            <div class="info-item">
                                <span>Riwayat Saya</span>
                                <strong><?= $riwayat_saya; ?></strong>
                            </div>
                            <div class="info-item">
                                <span>Pinjaman Aktif</span>
                                <strong><?= $pinjaman_aktif; ?></strong>
                            </div>
                            <div class="info-item">
                                <span>Stok Tersedia</span>
                                <strong><?= $stok_tersedia; ?></strong>
                            </div>
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
