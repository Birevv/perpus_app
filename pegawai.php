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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Pegawai</title>
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="buku.php">Buku</a></li>
            <li><a href="pegawai.php" class="active">Pegawai</a></li>
            <li><a href="pengunjung.php">Pengunjung</a></li>
            <li><a href="peminjaman.php">Peminjaman</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>
    <main>
        <div class="main">
            <h2>Data Pegawai</h2>
            <div class="tombol-tambah">
                <a href="pegawai_tambah.php" class="btn-tambah">+ Tambah Data</a>
            </div>
            <table border="1" cellpadding="10" cellspacing="0" class="tabel">
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Gender</th>
                    <th>Aksi</th>
                </tr>
                <?php
                include 'koneksi.php';
                $query = mysqli_query($koneksi, "SELECT * FROM pegawai");
                while ($data = mysqli_fetch_array($query)) {
                ?>
                    <tr>
                        <td><?= $data['nip']; ?></td>
                        <td><?= $data['nama']; ?></td>
                        <td><?= $data['alamat']; ?></td>
                        <td><?= $data['gender']; ?></td>
                        <td>
                            <a href="pegawai_edit.php?nip=<?= $data['nip']; ?>" class="btn-action edit">Edit</a>
                            <a href="pegawai_hapus.php?nip=<?= $data['nip']; ?>" class="btn-action delete">Hapus</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Bima Revan Saputra XI RPL 2
    </footer>


</body>
