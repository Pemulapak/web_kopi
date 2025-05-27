<?php
include('../config/koneksi.php');

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$message = '';
$error = '';

// Handle tambah kupon
if (isset($_POST['add_coupon'])) {
    $code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['code'])));
    $type = $_POST['type'];
    $value = (int)$_POST['value'];
    $cart_value = (int)$_POST['cart_value'];
    $expired_date = $_POST['expired_date'];
    $usage_limit = isset($_POST['usage_limit']) && $_POST['usage_limit'] !== '' ? (int)$_POST['usage_limit'] : NULL;

    // Validasi
    $errors = [];
    if (empty($code)) $errors[] = "Kode kupon wajib diisi";
    if (!in_array($type, ['fixed', 'percent'])) $errors[] = "Tipe kupon tidak valid";
    if ($value <= 0) $errors[] = "Nilai kupon harus lebih besar dari 0";
    if ($cart_value < 0) $errors[] = "Minimum pembelian tidak valid";
    if (empty($expired_date)) $errors[] = "Tanggal expired wajib diisi";

    // Cek duplikasi kode
    $check_code = mysqli_query($conn, "SELECT id FROM coupon WHERE code = '$code'");
    if (mysqli_num_rows($check_code) > 0) $errors[] = "Kode kupon sudah digunakan";

    if (empty($errors)) {
        $usage_limit_sql = is_null($usage_limit) ? 'NULL' : $usage_limit;
        $query = "INSERT INTO coupon (code, type, value, cart_value, expired_date, usage_limit, status, created_at) 
                  VALUES ('$code', '$type', $value, $cart_value, '$expired_date', $usage_limit_sql, 'active', NOW())";

        if (mysqli_query($conn, $query)) {
            $message = "Kupon berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan kupon: " . mysqli_error($conn);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Handle update status kupon
if (isset($_POST['toggle_status'])) {
    $coupon_id = (int)$_POST['coupon_id'];
    $new_status = $_POST['new_status'];

    $update_query = mysqli_query($conn, "UPDATE coupon SET status = '$new_status', updated_at = NOW() WHERE id = $coupon_id");
    if ($update_query) {
        $message = "Status kupon berhasil diubah!";
    } else {
        $error = "Gagal mengubah status kupon.";
    }
}

// Handle hapus kupon
if (isset($_POST['delete_coupon'])) {
    $coupon_id = (int)$_POST['coupon_id'];

    $check_usage = mysqli_query($conn, "SELECT COUNT(*) as count FROM user_coupons WHERE coupon_id = $coupon_id");
    $usage_count = mysqli_fetch_assoc($check_usage)['count'];

    if ($usage_count > 0) {
        $error = "Kupon tidak dapat dihapus karena sudah ada yang menggunakan.";
    } else {
        $delete_query = mysqli_query($conn, "DELETE FROM coupon WHERE id = $coupon_id");
        if ($delete_query) {
            $message = "Kupon berhasil dihapus!";
        } else {
            $error = "Gagal menghapus kupon.";
        }
    }
}

// Update kupon yang sudah expired menjadi inactive
mysqli_query($conn, "UPDATE coupon SET status = 'inactive' WHERE expired_date < CURDATE()");

// Ambil Semua Data kupon yang tidak katif
$inactive_coupons = mysqli_query($conn, "
    SELECT c.*, 
           COUNT(uc.id) as total_claimed,
           COUNT(CASE WHEN uc.is_used = 1 THEN 1 END) as total_used
    FROM coupon c 
    LEFT JOIN user_coupons uc ON c.id = uc.coupon_id 
    WHERE c.status = 'inactive'
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");

// Ambil semua kupon yang masih aktif
$coupons = mysqli_query($conn, "
    SELECT c.*, 
           COUNT(uc.id) as total_claimed,
           COUNT(CASE WHEN uc.is_used = 1 THEN 1 END) as total_used
    FROM coupon c 
    LEFT JOIN user_coupons uc ON c.id = uc.coupon_id 
    WHERE c.status = 'active'
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");

// Get statistics
$stats = mysqli_query($conn, "
    SELECT 
        COUNT(CASE WHEN status = 'active' THEN 1 END) as active_coupons,
        COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_coupons,
        COUNT(CASE WHEN expired_date < CURDATE() THEN 1 END) as expired_coupons
    FROM coupon
");
$stats_data = mysqli_fetch_assoc($stats);

// Handle edit kupon
if (isset($_POST['edit_coupon'])) {
    $coupon_id = (int)$_POST['coupon_id'];
    $code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['code'])));
    $type = $_POST['type'];
    $value = (int)$_POST['value'];
    $cart_value = (int)$_POST['cart_value'];
    $expired_date = $_POST['expired_date'];
    $usage_limit = isset($_POST['usage_limit']) && $_POST['usage_limit'] !== '' ? (int)$_POST['usage_limit'] : NULL;

    // Validasi
    $errors = [];
    if (empty($code)) $errors[] = "Kode kupon wajib diisi";
    if (!in_array($type, ['fixed', 'percent'])) $errors[] = "Tipe kupon tidak valid";
    if ($value <= 0) $errors[] = "Nilai kupon harus lebih besar dari 0";
    if ($cart_value < 0) $errors[] = "Minimum pembelian tidak valid";
    if (empty($expired_date)) $errors[] = "Tanggal expired wajib diisi";

    // Cek duplikasi kode (kecuali kupon yang sedang diedit)
    $check_code = mysqli_query($conn, "SELECT id FROM coupon WHERE code = '$code' AND id != $coupon_id");
    if (mysqli_num_rows($check_code) > 0) $errors[] = "Kode kupon sudah digunakan";

    // Cek apakah sudah digunakan
    $check_usage = mysqli_query($conn, "SELECT COUNT(*) as count FROM user_coupons WHERE coupon_id = $coupon_id AND is_used = 1");
    $usage_count = mysqli_fetch_assoc($check_usage)['count'];

    if ($usage_count > 0) {
        $original_coupon = mysqli_query($conn, "SELECT code, type, value, cart_value FROM coupon WHERE id = $coupon_id");
        $original_data = mysqli_fetch_assoc($original_coupon);

        if (
            $code != $original_data['code'] ||
            $type != $original_data['type'] ||
            $value != $original_data['value'] ||
            $cart_value != $original_data['cart_value']
        ) {
            $errors[] = "Kupon yang sudah digunakan hanya bisa mengubah tanggal expired dan limit penggunaan";
        }
    }

    if (empty($errors)) {
        $usage_limit_sql = is_null($usage_limit) ? 'NULL' : $usage_limit;
        $query = "UPDATE coupon SET 
                    code = '$code', 
                    type = '$type', 
                    value = $value, 
                    cart_value = $cart_value, 
                    expired_date = '$expired_date', 
                    usage_limit = $usage_limit_sql, 
                    updated_at = NOW() 
                  WHERE id = $coupon_id";

        if (mysqli_query($conn, $query)) {
            $message = "Kupon berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui kupon: " . mysqli_error($conn);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kupon</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4facfe, #00f2fe);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-number {
            font-size: 3em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1em;
            color: #666;
            font-weight: 500;
        }

        /* Sections */
        .section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.8em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section h2 i {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Form */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        .form-group input,
        .form-group select {
            padding: 12px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            background: white;
        }

        /* Buttons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85em;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        /* Table */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table th,
        .table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85em;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(79, 172, 254, 0.05);
            transform: scale(1.01);
        }

        .coupon-code {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #4facfe;
            background: rgba(79, 172, 254, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, #ffeaa7, #fab1a0);
            color: #2d3436;
        }

        .status-expired {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.5em;
            font-weight: 600;
        }

        .close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5em;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 4em;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #999;
        }

        /* Action buttons group */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table th,
            .table td {
                padding: 10px 8px;
                font-size: 0.9em;
            }

            .header h1 {
                font-size: 2em;
            }

            .content {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-sm {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-ticket-alt"></i> Manajemen Kupon</h1>
            <p>Kelola kupon diskon dan promosi dengan mudah</p>
        </div>

        <div class="content">
            <!-- Alert Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_data['active_coupons']; ?></div>
                    <div class="stat-label"><i class="fas fa-check-circle"></i> Kupon Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_data['inactive_coupons']; ?></div>
                    <div class="stat-label"><i class="fas fa-pause-circle"></i> Kupon Nonaktif</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_data['expired_coupons']; ?></div>
                    <div class="stat-label"><i class="fas fa-times-circle"></i> Kupon Expired</div>
                </div>
            </div>

            <!-- Form Tambah Kupon -->
            <div class="section">
                <h2><i class="fas fa-plus-circle"></i> Tambah Kupon Baru</h2>
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-code"></i> Kode Kupon:</label>
                            <input type="text" name="code" required placeholder="Contoh: DISKON20" style="text-transform: uppercase;">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-tags"></i> Tipe Diskon:</label>
                            <select name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="fixed">Nominal (Rp)</option>
                                <option value="percent">Persentase (%)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-percentage"></i> Nilai Diskon:</label>
                            <input type="number" name="value" required placeholder="Contoh: 20000 atau 20" min="1">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-shopping-cart"></i> Minimum Pembelian (Rp):</label>
                            <input type="number" name="cart_value" required placeholder="Contoh: 100000" min="0">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Tanggal Expired:</label>
                            <input type="date" name="expired_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-limit"></i> Limit Penggunaan (Opsional):</label>
                            <input type="number" name="usage_limit" placeholder="Kosongkan jika unlimited" min="1">
                        </div>
                    </div>
                    <button type="submit" name="add_coupon" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Kupon
                    </button>
                </form>
            </div>

            <!-- Daftar Kupon -->
            <div class="section">
                <h2><i class="fas fa-list"></i> Daftar Kupon</h2>

                <?php if (mysqli_num_rows($coupons) > 0): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-code"></i> Kode</th>
                                    <th><i class="fas fa-tags"></i> Tipe</th>
                                    <th><i class="fas fa-money-bill"></i> Nilai</th>
                                    <th><i class="fas fa-shopping-cart"></i> Min. Belanja</th>
                                    <th><i class="fas fa-calendar"></i> Expired</th>
                                    <th><i class="fas fa-info-circle"></i> Status</th>
                                    <th><i class="fas fa-chart-bar"></i> Limit</th>
                                    <th><i class="fas fa-hand-holding"></i> Diklaim</th>
                                    <th><i class="fas fa-check"></i> Digunakan</th>
                                    <th><i class="fas fa-cog"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($coupon = mysqli_fetch_assoc($coupons)): ?>
                                    <tr>
                                        <td class="coupon-code text-center"><?php echo htmlspecialchars($coupon['code']); ?></td>
                                        <td class="text-center"><?php echo $coupon['type'] == 'percent' ? 'Persentase' : 'Nominal'; ?></td>
                                        <td class="text-center">
                                            <?php
                                            if ($coupon['type'] == 'percent') {
                                                echo $coupon['value'] . "%";
                                            } else {
                                                echo "Rp " . number_format($coupon['value'], 0, ',', '.');
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">Rp <?php echo number_format($coupon['cart_value'], 0, ',', '.'); ?></td>
                                        <td class="text-center"><?php echo date('d M Y', strtotime($coupon['expired_date'])); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $current_date = date('Y-m-d');
                                            if ($coupon['expired_date'] < $current_date) {
                                                echo '<span class="status-badge status-expired">Expired</span>';
                                            } elseif ($coupon['status'] == 'active') {
                                                echo '<span class="status-badge status-active">Aktif</span>';
                                            } else {
                                                echo '<span class="status-badge status-inactive">Nonaktif</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            if ($coupon['usage_limit']) {
                                                echo $coupon['used_count'] . "/" . $coupon['usage_limit'];
                                            } else {
                                                echo "Unlimited";
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center"><?php echo $coupon['total_claimed']; ?></td>
                                        <td class="text-center"><?php echo $coupon['total_used']; ?></td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-sm btn-primary" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($coupon)); ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

                                                <!-- Toggle Status -->
                                                <?php if ($coupon['expired_date'] >= $current_date): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                                        <input type="hidden" name="new_status" value="<?php echo $coupon['status'] == 'active' ? 'inactive' : 'active'; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $coupon['status'] == 'active' ? 'btn-warning' : 'btn-success'; ?>">
                                                            <i class="fas fa-<?php echo $coupon['status'] == 'active' ? 'pause' : 'play'; ?>"></i>
                                                            <?php echo $coupon['status'] == 'active' ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- Delete Button -->
                                                <?php if ($coupon['total_claimed'] == 0): ?>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kupon ini?')">
                                                        <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                                        <button type="submit" name="delete_coupon" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-ticket-alt"></i>
                        <h3>Belum ada kupon yang dibuat</h3>
                        <p>Mulai buat kupon pertama Anda untuk menarik lebih banyak pelanggan!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Daftar Kupon yang dihapus atau delete -->
            <div class="section">
                <h2><i class="fas fa-list"></i> Daftar Kupon Hapus / In Active</h2>

                <?php if (mysqli_num_rows($inactive_coupons) > 0): ?>

                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-code"></i> Kode</th>
                                    <th><i class="fas fa-tags"></i> Tipe</th>
                                    <th><i class="fas fa-money-bill"></i> Nilai</th>
                                    <th><i class="fas fa-shopping-cart"></i> Min. Belanja</th>
                                    <th><i class="fas fa-calendar"></i> Expired</th>
                                    <th><i class="fas fa-info-circle"></i> Status</th>
                                    <th><i class="fas fa-chart-bar"></i> Limit</th>
                                    <th><i class="fas fa-hand-holding"></i> Diklaim</th>
                                    <th><i class="fas fa-check"></i> Digunakan</th>
                                    <th><i class="fas fa-cog"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($coupon = mysqli_fetch_assoc($inactive_coupons)): ?>
                                    <tr>
                                        <td class="coupon-code text-center"><?php echo htmlspecialchars($coupon['code']); ?></td>
                                        <td class="text-center"><?php echo $coupon['type'] == 'percent' ? 'Persentase' : 'Nominal'; ?></td>
                                        <td class="text-center">
                                            <?php
                                            if ($coupon['type'] == 'percent') {
                                                echo $coupon['value'] . "%";
                                            } else {
                                                echo "Rp " . number_format($coupon['value'], 0, ',', '.');
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">Rp <?php echo number_format($coupon['cart_value'], 0, ',', '.'); ?></td>
                                        <td class="text-center"><?php echo date('d M Y', strtotime($coupon['expired_date'])); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $current_date = date('Y-m-d');
                                            if ($coupon['expired_date'] < $current_date) {
                                                echo '<span class="status-badge status-expired">Expired</span>';
                                            } elseif ($coupon['status'] == 'active') {
                                                echo '<span class="status-badge status-active">Aktif</span>';
                                            } else {
                                                echo '<span class="status-badge status-inactive">Nonaktif</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            if ($coupon['usage_limit']) {
                                                echo $coupon['used_count'] . "/" . $coupon['usage_limit'];
                                            } else {
                                                echo "Unlimited";
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center"><?php echo $coupon['total_claimed']; ?></td>
                                        <td class="text-center"><?php echo $coupon['total_used']; ?></td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-sm btn-primary" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($coupon)); ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

                                                <!-- Toggle Status -->
                                                <?php if ($coupon['expired_date'] >= $current_date): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                                        <input type="hidden" name="new_status" value="<?php echo $coupon['status'] == 'active' ? 'inactive' : 'active'; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $coupon['status'] == 'active' ? 'btn-warning' : 'btn-success'; ?>">
                                                            <i class="fas fa-<?php echo $coupon['status'] == 'active' ? 'pause' : 'play'; ?>"></i>
                                                            <?php echo $coupon['status'] == 'active' ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- Delete Button -->
                                                <?php if ($coupon['total_claimed'] == 0): ?>
                                                    <!-- Tombol Aktifkan -->
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                                        <input type="hidden" name="new_status" value="active">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-success">
                                                            <i class="fas fa-play"></i> Aktifkan
                                                        </button>
                                                    </form>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-ticket-alt"></i>
                        <h3>Belum ada kupon yang dibuat</h3>
                        <p>Mulai buat kupon pertama Anda untuk menarik lebih banyak pelanggan!</p>
                    </div>
                <?php endif; ?>
            </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Kupon</h3>
                <button type="button" class="close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editForm">
                    <input type="hidden" name="coupon_id" id="edit_coupon_id">
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-code"></i> Kode Kupon:</label>
                            <input type="text" name="code" id="edit_code" required style="text-transform: uppercase;">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-tags"></i> Tipe Diskon:</label>
                            <select name="type" id="edit_type" required>
                                <option value="fixed">Nominal (Rp)</option>
                                <option value="percent">Persentase (%)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-percentage"></i> Nilai Diskon:</label>
                            <input type="number" name="value" id="edit_value" required min="1">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-shopping-cart"></i> Minimum Pembelian (Rp):</label>
                            <input type="number" name="cart_value" id="edit_cart_value" required min="0">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Tanggal Expired:</label>
                            <input type="date" name="expired_date" id="edit_expired_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-limit"></i> Limit Penggunaan:</label>
                            <input type="number" name="usage_limit" id="edit_usage_limit" placeholder="Kosongkan jika unlimited" min="1">
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 25px;">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" name="edit_coupon" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(coupon) {
            document.getElementById('edit_coupon_id').value = coupon.id;
            document.getElementById('edit_code').value = coupon.code;
            document.getElementById('edit_type').value = coupon.type;
            document.getElementById('edit_value').value = coupon.value;
            document.getElementById('edit_cart_value').value = coupon.cart_value;
            document.getElementById('edit_expired_date').value = coupon.expired_date;
            document.getElementById('edit_usage_limit').value = coupon.usage_limit || '';

            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300);
            });
        }, 5000);
    </script>
</body>

</html>
