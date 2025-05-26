<?php
session_start();
include('../config/koneksi.php');

// Cek login
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int)$_SESSION['user']['id'];
$message = '';
$error = '';

// Handle claim kupon
if (isset($_POST['claim_coupon'])) {
    $coupon_id = (int)$_POST['coupon_id'];

    // Cek apakah kupon masih valid dan ada
    $coupon_check = mysqli_query($conn, "SELECT * FROM coupon WHERE id = $coupon_id AND expired_date >= CURDATE()");

    if (mysqli_num_rows($coupon_check) > 0) {
        $coupon_data = mysqli_fetch_assoc($coupon_check);

        // Cek apakah user sudah punya kupon ini
        $user_coupon_check = mysqli_query($conn, "SELECT id FROM user_coupons WHERE user_id = $userId AND coupon_id = $coupon_id");

        if (mysqli_num_rows($user_coupon_check) == 0) {
            // Claim kupon - insert ke user_coupons dengan timestamp claimed_at
            $claim_query = mysqli_query($conn, "INSERT INTO user_coupons (user_id, coupon_id, is_used, claimed_at) VALUES ($userId, $coupon_id, 0, NOW())");

            if ($claim_query) {
                $message = "Kupon berhasil di-claim!";
            } else {
                $error = "Gagal claim kupon: " . mysqli_error($conn);
            }
        } else {
            $error = "Anda sudah memiliki kupon ini.";
        }
    } else {
        $error = "Kupon tidak valid atau sudah expired.";
    }
}

// Query kupon tersedia (belum di-claim user dan masih valid)
$available_coupons = mysqli_query($conn, "
    SELECT c.* FROM coupon c 
    WHERE c.expired_date >= CURDATE() 
    AND c.id NOT IN (
        SELECT coupon_id FROM user_coupons WHERE user_id = $userId
    )
    ORDER BY c.created_at DESC
");

// Query kupon yang dimiliki user
$user_coupons = mysqli_query($conn, "
    SELECT c.*, uc.is_used, uc.used_at, uc.claimed_at 
    FROM coupon c 
    JOIN user_coupons uc ON c.id = uc.coupon_id 
    WHERE uc.user_id = $userId 
    ORDER BY uc.claimed_at DESC
");

// Ambil data user untuk profile
$user_query = mysqli_query($conn, "SELECT * FROM akun_member WHERE id = $userId");
$user_data = mysqli_fetch_assoc($user_query);

// Update session foto jika kosong
if (empty($_SESSION['user']['foto_profile']) && !empty($user_data['foto_profile'])) {
    $_SESSION['user']['foto_profile'] = $user_data['foto_profile'];
}

// Proses update profile (kode yang sudah ada)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['claim_coupon'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];

    // Validasi
    if (!$nama_lengkap) $errors[] = "Nama lengkap wajib diisi";
    if (!$username) $errors[] = "Username wajib diisi";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";
    if (!$no_hp) $errors[] = "Nomor HP wajib diisi";
    if (!$alamat) $errors[] = "Alamat wajib diisi";

    // Cek unik username & email
    $cekUsername = mysqli_query($conn, "SELECT id FROM akun_member WHERE username = '$username' AND id != $userId");
    if (mysqli_num_rows($cekUsername) > 0) $errors[] = "Username sudah digunakan";

    $cekEmail = mysqli_query($conn, "SELECT id FROM akun_member WHERE email = '$email' AND id != $userId");
    if (mysqli_num_rows($cekEmail) > 0) $errors[] = "Email sudah digunakan";

    // Cek password
    if (!empty($password)) {
        if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter";
        if ($password !== $confirm_password) $errors[] = "Konfirmasi password tidak cocok";
    }

    // Update data
    if (empty($errors)) {
        $query = "UPDATE akun_member SET 
                    nama_lengkap = '$nama_lengkap',
                    username = '$username',
                    email = '$email',
                    no_hp = '$no_hp',
                    alamat = '$alamat',
                    updated_at = NOW()";

        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", password = '$hashed'";
        }

        $query .= " WHERE id = $userId";

        if (mysqli_query($conn, $query)) {
            $message = "Profil berhasil diperbarui.";
        } else {
            $error = "Gagal update data.";
        }
    } else {
        $error = implode('<br>', $errors);
    }

    // Upload foto
    if (!empty($_FILES['foto_profile']['name']) && $_FILES['foto_profile']['error'] == 0) {
        $upload_dir = '../uploads/profile_photos/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $ext = strtolower(pathinfo($_FILES['foto_profile']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $mime = mime_content_type($_FILES['foto_profile']['tmp_name']);
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($ext, $allowed) && in_array($mime, $allowed_mimes)) {
            $new_name = 'profile_' . $userId . '_' . time() . '.' . $ext;
            $target = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['foto_profile']['tmp_name'], $target)) {
                $old_photo = $user_data['foto_profile'];
                mysqli_query($conn, "UPDATE akun_member SET foto_profile = '$new_name', updated_at = NOW() WHERE id = $userId");

                if ($old_photo && file_exists($upload_dir . $old_photo)) {
                    unlink($upload_dir . $old_photo);
                }

                $_SESSION['user']['foto_profile'] = $new_name;
                $user_data['foto_profile'] = $new_name;

                $message .= "<br>Foto profil berhasil diupload.";
            } else {
                $error .= "<br>Gagal upload foto.";
            }
        } else {
            $error .= "<br>Format gambar tidak didukung.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>

    <!-- Login berhasil -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($_SESSION['login_success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Login!',
                text: 'Selamat datang kembali, <?php echo $_SESSION['user']['nama_lengkap']; ?>!',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    <?php unset($_SESSION['login_success']);
    endif; ?>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .tab {
            padding: 15px 30px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab:hover {
            color: #667eea;
            background: #f8f9ff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 40px;
            margin-bottom: 30px;
        }

        .profile-photo {
            text-align: center;
        }

        .photo-container {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 5px solid #667eea;
            position: relative;
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .file-input {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            background: #f8f9ff;
            border: 2px dashed #667eea;
            color: #667eea;
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background: #667eea;
            color: white;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .coupon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .tab-content {
            padding: 20px;
        }

        .coupon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .coupon-card {
            position: relative;
            background: linear-gradient(to bottom right, #ffffff, #f9f9f9);
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .coupon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .used-coupon {
            opacity: 0.6;
            background-color: #f7f7f7;
            position: relative;
        }

        .used-stamp,
        .available-stamp {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #ff6666;
            color: white;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .available-stamp {
            background-color: #4caf50;
        }

        .coupon-code {
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .coupon-value {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #ff9800;
        }

        .coupon-details p {
            font-size: 0.9rem;
            margin: 3px 0;
            color: #555;
        }

        .claim-btn {
            background: linear-gradient(to right, #ff9800, #ffc107);
            border: none;
            color: #fff;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
            font-size: 0.95rem;
        }

        .claim-btn:hover {
            background: linear-gradient(to right, #ffa726, #ffb300);
        }

        @keyframes shimmer {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .coupon-code {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .coupon-value {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .coupon-details {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .validation-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .validation-message.show {
            display: block;
        }

        .form-group.error input {
            border-color: #e74c3c;
        }

        .claim-btn {
            background: linear-gradient(135deg, #ff6a00, #ee0979);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: relative;
            z-index: 3;
        }

        .claim-btn:hover {
            background: linear-gradient(135deg, #ee0979, #ff6a00);
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .claim-btn:active {
            transform: scale(0.98);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .profile-section {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .tabs {
                flex-wrap: wrap;
            }

            .tab {
                flex: 1;
                min-width: 120px;
            }

            .coupon-card {
                padding: 15px;
            }

            .coupon-code,
            .coupon-value {
                font-size: 16px;
            }

            .claim-btn {
                width: 100%;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard User</h1>
            <p>Selamat datang, <?php echo htmlspecialchars($user_data['nama_lengkap']); ?>!</p>
            <div style="margin-top: 15px;">
                <a href="../logout.php" style="color: rgba(255,255,255,0.9); text-decoration: none; padding: 8px 20px; border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; transition: all 0.3s ease;"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                    onmouseout="this.style.background='transparent'">
                    🚪 Logout
                </a>
            </div>
        </div>

        <div class="content">
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab active" onclick="showTab('profile')">Profile Saya</button>
                <button class="tab" onclick="showTab('my-coupons')">Kupon Saya</button>

                <button class="tab" onclick="showTab('available-coupons')">Kupon Tersedia</button>
            </div>

            <!-- Profile Tab -->
            <div id="profile" class="tab-content active">
                <form method="POST" enctype="multipart/form-data" id="profileForm">
                    <div class="profile-section">
                        <div class="profile-photo">
                            <div class="photo-container">
                                <?php if (!empty($user_data['foto_profile'])): ?>
                                    <img
                                        src="../uploads/profile_photos/<?php echo htmlspecialchars($user_data['foto_profile'] ?? 'default.png'); ?>"
                                        alt="Foto Profil"
                                        style="border-radius: 50%; object-fit: cover;"><br><br>
                                <?php else: ?>
                                    <div class="photo-placeholder" id="photoPlaceholder">
                                        <?php echo strtoupper(substr($user_data['nama_lengkap'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="file-input">
                                <input type="file" id="foto_profile" name="foto_profile" accept="image/*" onchange="previewPhoto(this)">
                                <label for="foto_profile" class="file-input-label">
                                    📷 Ganti Foto
                                </label>
                            </div>

                            <button type="submit" name="upload_photo" class="btn" style="margin-top: 10px; display: none;" id="uploadBtn">
                                Upload Foto
                            </button>
                        </div>

                        <div class="profile-form">
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap *</label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap"
                                    value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>"
                                    required onblur="validateField(this)">
                                <div class="validation-message" id="nama_lengkap_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username"
                                    value="<?php echo htmlspecialchars($user_data['username']); ?>"
                                    required onblur="validateUsername(this)">
                                <div class="validation-message" id="username_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user_data['email']); ?>"
                                    required onblur="validateEmail(this)">
                                <div class="validation-message" id="email_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="no_hp">No HP *</label>
                                <input type="text" id="no_hp" name="no_hp"
                                    value="<?php echo htmlspecialchars($user_data['no_hp']); ?>"
                                    required onblur="validatePhone(this)">
                                <div class="validation-message" id="no_hp_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat *</label>
                                <textarea id="alamat" name="alamat" required onblur="validateField(this)"><?php echo htmlspecialchars($user_data['alamat']); ?></textarea>
                                <div class="validation-message" id="alamat_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" id="password" name="password" onblur="validatePassword(this)">
                                <div class="validation-message" id="password_error"></div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Konfirmasi Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" onblur="validateConfirmPassword(this)">
                                <div class="validation-message" id="confirm_password_error"></div>
                            </div>

                            <button type="submit" name="update_profile" class="btn">Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Kupon Available -->
            <div id="available-coupons" class="tab-content">
                <h2 style="margin-bottom: 30px; color: #333;">🎁 Kupon Tersedia</h2>

                <div class="coupon-grid">
                    <?php if (mysqli_num_rows($available_coupons) > 0): ?>
                        <?php while ($coupon = mysqli_fetch_assoc($available_coupons)): ?>
                            <div class="coupon-card">
                                <div class="available-stamp">🟢 TERSEDIA</div>
                                <div class="coupon-code">Kode: <?php echo htmlspecialchars($coupon['code']); ?></div>
                                <div class="coupon-value">
                                    <?php
                                    echo $coupon['type'] == 'percent'
                                        ? "Diskon " . $coupon['value'] . "%"
                                        : "Diskon Rp " . number_format($coupon['value'], 0, ',', '.');
                                    ?>
                                </div>
                                <div class="coupon-details">
                                    <p>Min. pembelian: Rp <?php echo number_format($coupon['cart_value'], 0, ',', '.'); ?></p>
                                    <p>Berlaku sampai: <?php echo date('d M Y', strtotime($coupon['expired_date'])); ?></p>
                                    <p>Dibuat: <?php echo !empty($coupon['created_at']) ? date('d M Y', strtotime($coupon['created_at'])) : ''; ?></p>
                                </div>

                                <form method="POST" action="" style="margin-top: 10px;">
                                    <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                    <button type="submit" name="claim_coupon" class="claim-btn">🎉 Klaim Kupon</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; grid-column: 1 / -1;">🤷‍♀️ Tidak ada kupon tersedia saat ini</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Coupons Tab -->
            <div id="my-coupons" class="tab-content">
                <h2 style="margin-bottom: 30px; color: #333;">🎟️ Kupon Saya</h2>

                <div class="coupon-grid">
                    <?php if (mysqli_num_rows($user_coupons) > 0): ?>
                        <?php while ($coupon = mysqli_fetch_assoc($user_coupons)): ?>
                            <div class="coupon-card <?php echo $coupon['is_used'] ? 'used-coupon' : ''; ?>">
                                <?php if ($coupon['is_used']): ?>
                                    <div class="used-stamp">✅ SUDAH DIGUNAKAN</div>
                                <?php else: ?>
                                    <div class="available-stamp">🟢 SIAP PAKAI</div>
                                <?php endif; ?>

                                <div class="coupon-code">Kode: <?php echo htmlspecialchars($coupon['code']); ?></div>
                                <div class="coupon-value">
                                    <?php
                                    echo $coupon['type'] == 'percent'
                                        ? "Diskon " . $coupon['value'] . "%"
                                        : "Diskon Rp " . number_format($coupon['value'], 0, ',', '.');
                                    ?>
                                </div>
                                <div class="coupon-details">
                                    <p>Min. pembelian: Rp <?php echo number_format($coupon['cart_value'], 0, ',', '.'); ?></p>
                                    <p>Berlaku sampai: <?php echo date('d M Y', strtotime($coupon['expired_date'])); ?></p>
                                    <p>Di-claim: <?php echo date('d M Y H:i', strtotime($coupon['claimed_at'])); ?></p>
                                    <?php if ($coupon['is_used'] && $coupon['used_at']): ?>
                                        <p>Digunakan: <?php echo date('d M Y H:i', strtotime($coupon['used_at'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; grid-column: 1 / -1;">🤷‍♂️ Anda belum memiliki kupon</p>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const photoContainer = document.querySelector('.photo-container');
                    photoContainer.innerHTML = `<img src="${e.target.result}" alt="Profile Photo" id="photoPreview">`;

                    // Show upload button
                    document.getElementById('uploadBtn').style.display = 'inline-block';
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Real-time validation functions
        function validateField(field) {
            const value = field.value.trim();
            const fieldName = field.name;
            const errorElement = document.getElementById(fieldName + '_error');
            const formGroup = field.parentElement;

            if (value === '') {
                showError(field, errorElement, formGroup, 'Field ini harus diisi');
                return false;
            } else {
                clearError(field, errorElement, formGroup);
                return true;
            }
        }

        function validateUsername(field) {
            const value = field.value.trim();
            const errorElement = document.getElementById('username_error');
            const formGroup = field.parentElement;

            if (value === '') {
                showError(field, errorElement, formGroup, 'Username harus diisi');
                return false;
            } else if (value.length < 3) {
                showError(field, errorElement, formGroup, 'Username minimal 3 karakter');
                return false;
            } else {
                // Check username availability via AJAX
                checkUsernameAvailability(value, field, errorElement, formGroup);
                return true;
            }
        }

        function validateEmail(field) {
            const value = field.value.trim();
            const errorElement = document.getElementById('email_error');
            const formGroup = field.parentElement;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (value === '') {
                showError(field, errorElement, formGroup, 'Email harus diisi');
                return false;
            } else if (!emailRegex.test(value)) {
                showError(field, errorElement, formGroup, 'Format email tidak valid');
                return false;
            } else {
                // Check email availability via AJAX
                checkEmailAvailability(value, field, errorElement, formGroup);
                return true;
            }
        }

        function validatePhone(field) {
            const value = field.value.trim();
            const errorElement = document.getElementById('no_hp_error');
            const formGroup = field.parentElement;
            const phoneRegex = /^[0-9+\-\s]{8,}$/;

            if (value === '') {
                showError(field, errorElement, formGroup, 'No HP harus diisi');
                return false;
            } else if (!phoneRegex.test(value)) {
                showError(field, errorElement, formGroup, 'Format No HP tidak valid');
                return false;
            } else {
                clearError(field, errorElement, formGroup);
                return true;
            }
        }

        function validatePassword(field) {
            const value = field.value;
            const errorElement = document.getElementById('password_error');
            const formGroup = field.parentElement;

            if (value !== '' && value.length < 6) {
                showError(field, errorElement, formGroup, 'Password minimal 6 karakter');
                return false;
            } else {
                clearError(field, errorElement, formGroup);
                return true;
            }
        }

        function validateConfirmPassword(field) {
            const value = field.value;
            const password = document.getElementById('password').value;
            const errorElement = document.getElementById('confirm_password_error');
            const formGroup = field.parentElement;

            if (password !== '' && value !== password) {
                showError(field, errorElement, formGroup, 'Konfirmasi password tidak sama');
                return false;
            } else {
                clearError(field, errorElement, formGroup);
                return true;
            }
        }

        function showError(field, errorElement, formGroup, message) {
            field.classList.add('error');
            formGroup.classList.add('error');
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }

        function clearError(field, errorElement, formGroup) {
            field.classList.remove('error');
            formGroup.classList.remove('error');
            errorElement.classList.remove('show');
        }

        function checkUsernameAvailability(username, field, errorElement, formGroup) {
            fetch('check_availability.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `type=username&value=${encodeURIComponent(username)}&user=<?php echo $user; ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.available) {
                        showError(field, errorElement, formGroup, 'Username sudah digunakan');
                    } else {
                        clearError(field, errorElement, formGroup);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function checkEmailAvailability(email, field, errorElement, formGroup) {
            fetch('check_availability.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `type=email&value=${encodeURIComponent(email)}&user=<?php echo $user; ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.available) {
                        showError(field, errorElement, formGroup, 'Email sudah digunakan');
                    } else {
                        clearError(field, errorElement, formGroup);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Form submission validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const fields = [
                document.getElementById('nama_lengkap'),
                document.getElementById('username'),
                document.getElementById('email'),
                document.getElementById('no_hp'),
                document.getElementById('alamat'),
                document.getElementById('password'),
                document.getElementById('confirm_password')
            ];

            let isValid = true;

            fields.forEach(field => {
                if (field.name === 'password' || field.name === 'confirm_password') {
                    if (!validatePassword(field) || !validateConfirmPassword(field)) {
                        isValid = false;
                    }
                } else if (field.name === 'username') {
                    if (!validateUsername(field)) isValid = false;
                } else if (field.name === 'email') {
                    if (!validateEmail(field)) isValid = false;
                } else if (field.name === 'no_hp') {
                    if (!validatePhone(field)) isValid = false;
                } else {
                    if (!validateField(field)) isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const photoContainer = document.querySelector('.photo-container');
                    const placeholder = document.getElementById('photoPlaceholder');

                    if (placeholder) {
                        placeholder.remove();
                    }

                    let img = photoContainer.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        img.alt = 'Foto Profil';
                        photoContainer.appendChild(img);
                    }

                    img.src = e.target.result;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Auto-refresh halaman setelah claim kupon untuk memperbarui daftar
        <?php if (isset($_POST['claim_coupon']) && !empty($message)): ?>
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        <?php endif; ?>
    </script>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            var tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(function(content) {
                content.classList.remove('active');
            });

            // Remove active class from all tabs
            var tabs = document.querySelectorAll('.tab');
            tabs.forEach(function(tab) {
                tab.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>

    <?php if (isset($_SESSION['login_success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil',
                text: 'Selamat datang kembali, <?php echo htmlspecialchars($_SESSION["user"]["nama_lengkap"]); ?>!',
                confirmButtonColor: '#3085d6'
            });
        </script>
    <?php unset($_SESSION['login_success']);
    endif; ?>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($_SESSION['login_error_admin'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Admin Gagal',
            text: '<?php echo $_SESSION["login_error_admin"]; ?>',
            confirmButtonColor: '#d33'
        });
    </script>
    <?php unset($_SESSION['login_error_admin']); endif; ?>

</body>




</html>