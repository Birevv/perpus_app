<?php
session_start();
include 'koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

$query = "SELECT * FROM user
          WHERE username = '$username'
          AND password = '$password'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 1) {

    $user = mysqli_fetch_assoc($result);

    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['level'] = $user['level'];

    if ($user['level'] == 'admin') {
        header("location: dashboard.php");
    } elseif ($user['level'] == 'pegawai') {
        header("location: dashboard_pegawai.php");
    } else {
        header("location: dashboard_pengunjung.php");
    }
    exit;

} else {
    echo "Username Atau Password Salah";
}



