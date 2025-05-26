<?php
    session_start();

    // Cek apakah admin sudah login
    if (!isset($_SESSION['admin'])) {
        // Jika belum login, redirect ke halaman login
        header("Location: ../index.php");
        exit();
    }
    ?>

<?php
$content = '../layouts/admin/crud-kupon.php'; // file view
include('../layouts/admin/main-admin.php');   // file layout utama
?>

