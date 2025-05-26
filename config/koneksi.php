<?php
// koneksi database
define('SITEURL', 'http://localhost/tugasnadhif');
define('LOCALHOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'tugas_nadhif');

$conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$db_select = mysqli_select_db($conn, DB_NAME);
if (!$db_select) {
    die("Database tidak ditemukan: " . mysqli_error($conn));
}
?>