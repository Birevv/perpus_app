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

}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung</title>
    <link rel="stylesheet" href="styles.css?v=visitor-dashboard-1">
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
            <li><a href="dashboard_pengunjung.php" class="active"><span class="menu-icon icon-dashboard"></span>Dashboard</a></li>
            <li><a href="buku_pengunjung.php"><span class="menu-icon icon-book"></span>Buku</a></li>
            <li><a href="riwayat_pengunjung.php"><span class="menu-icon icon-history"></span>Riwayat</a></li>
        </ul>

        <a href="logout.php" class="visitor-logout"><span class="menu-icon icon-logout"></span>Logout</a>
    </aside>

    <main class="visitor-main">
        <header class="visitor-topbar">
            <strong>Dashboard Pengunjung</strong>
            <div class="visitor-top-actions">
                <button type="button" aria-label="Notifikasi" class="top-icon icon-bell"></button>
                <div class="visitor-avatar"><?= strtoupper(substr($user['nama'], 0, 1)); ?></div>
            </div>
        </header>

        <div class="visitor-content">
            <div class="visitor-hero-row">
                <section class="visitor-title">
                    <h2>Dashboard Pengunjung</h2>
                    <p>Selamat datang, <?= htmlspecialchars($user['nama']); ?>! Siap menjelajahi dunia pengetahuan hari ini?</p>
                </section>

                <div class="visitor-header-actions">
                    <a href="riwayat_pengunjung.php" class="visitor-action-btn secondary"><span class="menu-icon icon-history"></span>Riwayat Saya</a>
                    <a href="buku_pengunjung.php" class="visitor-action-btn primary"><span class="menu-icon icon-book"></span>Lihat Buku</a>
                </div>
            </div>

            <section class="visitor-stat-grid">
                <div class="visitor-stat-card blue">
                    <span class="visitor-stat-icon"><i class="icon-book"></i></span>
                    <strong><?= number_format((int) $total_buku, 0, ',', '.'); ?></strong>
                    <span>Total Koleksi</span>
                </div>
                <div class="visitor-stat-card indigo">
                    <span class="visitor-stat-icon"><i class="icon-stock"></i></span>
                    <strong><?= number_format((int) $stok_tersedia, 0, ',', '.'); ?></strong>
                    <span>Stok Tersedia</span>
                </div>
                <div class="visitor-stat-card peach">
                    <span class="visitor-stat-icon"><i class="icon-history"></i></span>
                    <strong><?= number_format((int) $riwayat_saya, 0, ',', '.'); ?></strong>
                    <span>Riwayat Saya</span>
                </div>
                <div class="visitor-stat-card red">
                    <span class="visitor-stat-icon"><i class="icon-bookmark"></i></span>
                    <strong><?= number_format((int) $pinjaman_aktif, 0, ',', '.'); ?></strong>
                    <span>Pinjaman Aktif</span>
                </div>
            </section>

            <section class="visitor-quick-section">
                <h3>Akses Cepat</h3>
                <div class="visitor-quick-grid">
                    <a href="buku_pengunjung.php" class="visitor-quick-card book">
                        <strong>Lihat Buku</strong>
                        <span>Jelajahi katalog lengkap perpustakaan kami.</span>
                        <em>Mulai →</em>
                    </a>
                    <a href="riwayat_pengunjung.php" class="visitor-quick-card history">
                        <strong>Riwayat Saya</strong>
                        <span>Lihat buku yang sedang dan pernah dipinjam.</span>
                        <em>Lihat →</em>
                    </a>
                    <a href="logout.php" class="visitor-quick-card logout">
                        <strong>Logout</strong>
                        <span>Keluar dari sesi dashboard pengunjung Anda.</span>
                        <em>Keluar →</em>
                    </a>
                </div>
            </section>
        </div>
    </main>
</body>

</html>
