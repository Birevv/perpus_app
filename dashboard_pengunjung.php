<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <main>
        <div class="pengunjung-wrapper">
            <h1>Selamat Datang di Dashboard Pengunjung</h1>
            <p>Ini adalah halaman khusus untuk pengunjung.</p>

            <table class="pengunjung-table">
                <tr>
                    <th>ISBN</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Genre</th>
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
                    </tr>
                <?php } ?>
            </table>

            <a href="logout.php" class="pengunjung-logout">Logout</a>
        </div>
    </main>


</body>

</html>