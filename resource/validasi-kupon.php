<?php
session_start();
// Pastikan path ke koneksi.php sudah benar
include('../config/koneksi.php');

// Cek login kasir/admin
if (!isset($_SESSION['admin']) && !isset($_SESSION['kasir'])) {
    header('Location: ../login.php');
    exit();
}

$message = ''; // Pesan sukses
$error = '';   // Pesan error
$coupon_data = null; // Data kupon yang ditemukan (tidak digunakan langsung untuk validasi)

// --- Handle Validasi dan Penggunaan Kupon ---
if (isset($_POST['validate_coupon'])) {
    $coupon_code = isset($_POST['coupon_code']) ? mysqli_real_escape_string($conn, trim($_POST['coupon_code'])) : '';
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, trim($_POST['username'])) : '';

    if (empty($coupon_code) || empty($username)) {
        $error = "Kode kupon dan username harus diisi.";
    } else {
        // 1. Cek apakah user ada terlebih dahulu berdasarkan username
        $user_check = mysqli_query($conn, "SELECT id, username, nama_lengkap FROM akun_member WHERE username = '$username'");
        if (mysqli_num_rows($user_check) == 0) {
            $error = "Username '$username' tidak ditemukan dalam sistem.";
        } else {
            $user_data = mysqli_fetch_assoc($user_check);
            $user_id = $user_data['id'];
            
            // 2. Cek apakah kupon code ada di tabel 'coupon'
            $coupon_check = mysqli_query($conn, "SELECT id, code, type, value, cart_value, expired_date FROM coupon WHERE code = '$coupon_code'");
            if (mysqli_num_rows($coupon_check) == 0) {
                $error = "Kode kupon '$coupon_code' tidak ditemukan.";
            } else {
                $coupon_detail = mysqli_fetch_assoc($coupon_check);
                $coupon_id = $coupon_detail['id'];

                // Cek apakah kupon sudah expired
                if ($coupon_detail['expired_date'] < date('Y-m-d')) {
                    $error = "Kupon sudah expired pada tanggal " . date('d M Y', strtotime($coupon_detail['expired_date'])) . ".";
                } else {
                    // 3. Cek apakah user memiliki kupon ini di tabel 'user_coupons' dan statusnya
                    $user_coupon_status_check = mysqli_query($conn, "
                        SELECT id, is_used, used_at
                        FROM user_coupons
                        WHERE coupon_id = $coupon_id AND user_id = $user_id
                    ");

                    if (mysqli_num_rows($user_coupon_status_check) == 0) {
                        $error = "User '$username' tidak memiliki kupon '$coupon_code'.";
                    } else {
                        $user_coupon_info = mysqli_fetch_assoc($user_coupon_status_check);
                        
                        // Cek apakah kupon sudah digunakan
                        if ($user_coupon_info['is_used'] == 1) {
                            $used_date = date('d M Y H:i', strtotime($user_coupon_info['used_at']));
                            $error = "Kupon sudah pernah digunakan pada tanggal $used_date.";
                        } else {
                            // Kupon valid dan belum digunakan, lanjutkan untuk update
                            $user_coupon_id_to_update = $user_coupon_info['id'];
                            
                            // Mark kupon sebagai used dan set used_at
                            $use_query = mysqli_query($conn, "
                                UPDATE user_coupons 
                                SET is_used = 1, used_at = NOW() 
                                WHERE id = $user_coupon_id_to_update
                            ");

                            if ($use_query) {
                                $discount_text = ($coupon_detail['type'] == 'percent') ? 
                                                 $coupon_detail['value'] . "%" : 
                                                 "Rp " . number_format($coupon_detail['value'], 0, ',', '.');
                                
                                $message = "Kupon berhasil digunakan! Customer: " . $user_data['nama_lengkap'] . 
                                           " (" . $user_data['username'] . 
                                           ") | Diskon: " . $discount_text . 
                                           " | Min. Belanja: Rp " . number_format($coupon_detail['cart_value'], 0, ',', '.');
                            } else {
                                $error = "Gagal menggunakan kupon. Silakan coba lagi. Error: " . mysqli_error($conn);
                            }
                        }
                    }
                }
            }
        }
    }
}

// --- Handle Pencarian Kupon Customer ---
$search_results = [];
if (isset($_POST['search_customer'])) {
    $search_term = mysqli_real_escape_string($conn, trim($_POST['search_customer']));

    if (!empty($search_term)) {
        $search_query = mysqli_query($conn, "
            SELECT 
                uc.id as user_coupon_id, uc.is_used, uc.used_at, uc.claimed_at,
                c.code, c.type, c.value, c.cart_value, c.expired_date,
                am.nama_lengkap, am.username
            FROM user_coupons uc
            JOIN coupon c ON uc.coupon_id = c.id
            JOIN akun_member am ON uc.user_id = am.id
            WHERE am.username LIKE '%$search_term%' OR am.nama_lengkap LIKE '%$search_term%'
            ORDER BY uc.claimed_at DESC
        ");

        if (mysqli_num_rows($search_query) > 0) {
            while ($row = mysqli_fetch_assoc($search_query)) {
                $search_results[] = $row;
            }
        } else {
            $error = "Tidak ada kupon yang ditemukan untuk '$search_term'.";
        }
    } else {
        $error = "Masukkan username atau nama customer untuk mencari.";
    }
}

// --- Get Statistik dan Laporan Hari Ini ---
// Get list semua kupon yang sudah digunakan hari ini
$used_coupons_today = mysqli_query($conn, "
    SELECT uc.*, c.code, c.type, c.value, am.nama_lengkap, am.username
    FROM user_coupons uc 
    JOIN coupon c ON uc.coupon_id = c.id 
    JOIN akun_member am ON uc.user_id = am.id 
    WHERE uc.is_used = 1 
    AND DATE(uc.used_at) = CURDATE()
    ORDER BY uc.used_at DESC
");

// Get statistik hari ini
$stats_query = mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_used,
        SUM(CASE WHEN c.type = 'percent' THEN 1 ELSE 0 END) as percent_coupons,
        SUM(CASE WHEN c.type = 'fixed' THEN 1 ELSE 0 END) as fixed_coupons
    FROM user_coupons uc 
    JOIN coupon c ON uc.coupon_id = c.id 
    WHERE uc.is_used = 1 
    AND DATE(uc.used_at) = CURDATE()
");
$stats = mysqli_fetch_assoc($stats_query);

?>

<?php
$content = '../layouts/admin/validasi-kupon.php'; // file view
include('../layouts/admin/main-admin.php');   // file layout utama
?>