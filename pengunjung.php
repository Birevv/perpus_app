<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Pengunjung</title>
</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="buku.php">Buku</a></li>
            <li><a href="pegawai.php">Pegawai</a></li>
            <li><a href="pengunjung.php" class="active">Pengunjung</a></li>
            <li><a href="peminjaman.php">Peminjaman</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>
    <main>
        <div class="main">
            <h2>Data Pengunjung</h2>
            <div class="tombol-tambah">
                <a href="pengunjung_tambah.php" class="btn-tambah">+ Tambah Data</a>
            </div>
            <table border="1" cellpadding="10" cellspacing="0" class="tabel">
                <tr>
                    <th>ID Anggota</th>
                    <th>Nama</th>
                    <th>NIP/NIS</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
                <?php
                include 'koneksi.php';
                $query = mysqli_query($koneksi, "SELECT * FROM anggota");
                while ($data = mysqli_fetch_array($query)) {
                ?>
                    <tr>
                        <td><?= $data['id_anggota']; ?></td>
                        <td><?= $data['nama']; ?></td>
                        <td><?= $data['NIP_NIS']; ?></td>
                        <td><?= $data['alamat']; ?></td>
                        <td><?= $data['no_hp']; ?></td>
                        <td>
                            <a href="pengunjung_edit.php?id_anggota=<?= $data['id_anggota']; ?>" class="btn-action edit">Edit</a>
                            <a href="pengunjung_hapus.php?id_anggota=<?= $data['id_anggota']; ?>" class="btn-action delete">Hapus</a>
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
