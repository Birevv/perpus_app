<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
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
    <title>Buku</title>

</head>

<body>
    <aside>
        <h1>Library</h1>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="buku.php">Buku</a></li>
            <li><a href="pegawai.php">Pegawai</a></li>
            <li><a href="pengunjung.php">Pengunjung</a></li>
            <li><a href="peminjaman.php">Peminjaman</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>
    <main>

        <div class="main">
            <h2>Data Buku</h2>
            <div class="tombol-tambah">
                <a href="buku_tambah.php" class="btn-tambah">+ Tambah Data</a>
            </div>
            <table border="1" cellpadding="10" cellspacing="0" class="tabel" text>
                <tr>
                    <th>ISBN</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Genre</th>
                    <th>Aksi</th>
                </tr>

                <?php
                include 'koneksi.php';

                $query = mysqli_query($koneksi, "SELECT * FROM buku");
                while ($data = mysqli_fetch_array($query)) {
                ?>
                    <tr>
                        <td><?= $data['isbn']; ?></td>
                        <td><?= $data['judul']; ?></td>
                        <td><?= $data['pengarang']; ?></td>
                        <td><?= $data['penerbit']; ?></td>
                        <td><?= $data['tahun']; ?></td>
                        <td><?= $data['genre']; ?></td>
                        <td class="action-cell">
                            <a href="buku_edit.php?isbn=<?= $data['isbn']; ?>" class="btn-action edit">Edit</a>
                            <a href="buku_hapus.php?isbn=<?= $data['isbn']; ?>" class="btn-action delete">Hapus</a>
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

</html>