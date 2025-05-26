<?php
session_start();
include('../config/koneksi.php'); // koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $query = $conn->prepare("SELECT * FROM akun_admin WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin;
        $_SESSION['login_success_admin'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $_SESSION['login_error_admin'] = "Login gagal: Email atau password salah!";
        header("Location: ../index.php");
        exit();
    }
}
?>
