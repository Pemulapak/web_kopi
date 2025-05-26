<?php
session_start();
include('../config/koneksi.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $query = $conn->prepare("SELECT id, nama_lengkap, username, email, password, no_hp, alamat, foto_profile, created_at, updated_at FROM akun_member WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        $_SESSION['login_success'] = true;
        header("Location: dashboard-user.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Email atau password salah!";
        header("Location: ../index.php");
        exit();
    }
}
?>
