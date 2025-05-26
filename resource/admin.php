<?php
session_start();
include('../config/koneksi.php');

// Handle POST requests untuk delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $id = intval($_POST['id']);
                $deleteQuery = "DELETE FROM akun_member WHERE id = ?";
                $stmt = mysqli_prepare($conn, $deleteQuery);
                mysqli_stmt_bind_param($stmt, "i", $id);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success'] = "Member berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Gagal menghapus member!";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
                break;
        }
    }
}

// Pagination setup
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = "WHERE nama_lengkap LIKE '%$search%' OR email LIKE '%$search%' OR no_hp LIKE '%$search%' OR alamat LIKE '%$search%'";
}

// Get total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM akun_member $searchCondition";
$countResult = mysqli_query($conn, $countQuery);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $limit);

// Get members data
$query = "SELECT id, nama_lengkap, email, no_hp, alamat, created_at 
          FROM akun_member 
          $searchCondition 
          ORDER BY created_at DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Get statistics
$statsQuery = "SELECT 
    COUNT(*) as total_members,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_members
    FROM akun_member";
$statsResult = mysqli_query($conn, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);

// Handle detail view
$detailMember = null;
if (isset($_GET['detail']) && !empty($_GET['detail'])) {
    $detailId = intval($_GET['detail']);
    $detailQuery = "SELECT * FROM akun_member WHERE id = ?";
    $detailStmt = mysqli_prepare($conn, $detailQuery);
    mysqli_stmt_bind_param($detailStmt, "i", $detailId);
    mysqli_stmt_execute($detailStmt);
    $detailResult = mysqli_stmt_get_result($detailStmt);
    $detailMember = mysqli_fetch_assoc($detailResult);
}
?>

<?php
    // Cek apakah admin sudah login
    if (!isset($_SESSION['admin'])) {
        // Jika belum login, redirect ke halaman login
        header("Location: ../index.php");
        exit();
    }
?>

<?php
$content = '../layouts/admin/crud-akun.php'; // file view
include('../layouts/admin/main-admin.php');   // file layout utama
?>
