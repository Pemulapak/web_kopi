<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Validasi Kupon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a6fd8;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --background-dark: #0f1419;
            --surface-dark: #1a202c;
            --surface-light: #2d3748;
            --text-primary: #e2e8f0;
            --text-secondary: #a0aec0;
            --border-color: #4a5568;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--background-dark) 0%, var(--surface-dark) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .section {
            background: var(--surface-dark);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            width: 100%;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--surface-light);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #218838 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #d4edda;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #f8d7da;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: var(--border-radius);
            background: var(--surface-light);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .table th {
            background: var(--surface-dark);
            font-weight: 600;
            color: var(--primary-color);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-striped tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .bg-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #218838 100%);
            color: white;
        }

        .bg-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--surface-light) 0%, var(--surface-dark) 100%);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: var(--surface-dark);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            max-width: 500px;
            width: 90%;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-header h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .modal-actions .btn {
            flex: 1;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section {
                padding: 1.5rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }

            .modal-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.75rem;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .table-responsive {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-ticket-alt"></i> Sistem Validasi Kupon</h1>
            <p>Kelola dan validasi kupon pelanggan dengan mudah dan cepat</p>
        </div>

        <div id="alertContainer">
            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Section 1: Validasi Kupon (Paling Atas) -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-ticket-alt"></i>
                Validasi & Gunakan Kupon
            </div>

            <form id="validateCouponForm" method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-barcode"></i> Kode Kupon:</label>
                        <input type="text" name="coupon_code" class="form-control" required
                            placeholder="Masukkan kode kupon" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username Customer:</label>
                        <input type="text" name="username" class="form-control" required
                            placeholder="Masukkan username customer" autocomplete="off">
                    </div>
                </div>
                <button type="submit" name="validate_coupon" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-check-circle"></i> Validasi & Gunakan Kupon
                </button>
            </form>
        </div>

        <!-- Section 2: Cari Customer (Tengah) -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-search"></i>
                Cari Kupon Customer
            </div>

            <form id="searchCustomerForm" method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user-search"></i> Cari Customer:</label>
                    <input type="text" name="search_customer" class="form-control"
                        placeholder="Username atau nama customer" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-search"></i> Cari Customer
                </button>
            </form>

            <div id="searchResults" class="table-responsive mt-3">
                <?php if (!empty($search_results)): ?>
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem; padding: 1rem;">Hasil Pencarian untuk "<?= htmlspecialchars($_POST['search_customer']) ?>"</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">Kode Kupon</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Min. Belanja</th>
                                <th class="text-center">Expired</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Digunakan Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $res): ?>
                                <tr>
                                    <td class="text-center text-black"><?= htmlspecialchars($res['code']) ?></td>
                                    <td class="text-center text-black"><?= htmlspecialchars($res['nama_lengkap']) ?> (<?= htmlspecialchars($res['username']) ?>)</td>
                                    <td class="text-center text-black"><?= ucfirst($res['type']) ?></td>
                                    <td class="text-center text-black">
                                        <?php
                                        if ($res['type'] == 'percent') {
                                            echo htmlspecialchars($res['value']) . "%";
                                        } else {
                                            echo "Rp " . number_format($res['value'], 0, ',', '.');
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center text-black">Rp <?= number_format($res['cart_value'], 0, ',', '.') ?></td>
                                    <td class="text-center text-black"><?= date('d M Y', strtotime($res['expired_date'])) ?></td>
                                    <td class="text-center text-black">
                                        <?php if ($res['is_used'] == 1): ?>
                                            <span class="badge bg-danger">Sudah Digunakan</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Belum Digunakan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-black">
                                        <?= ($res['used_at'] != null) ? date('d M Y H:i', strtotime($res['used_at'])) . ' WIB' : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 3: Laporan (Paling Bawah) -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-chart-line"></i>
                Laporan Kupon Hari Ini
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalUsed"><?= $stats['total_used'] ?? 0 ?></div>
                    <div class="stat-label">Total Kupon Digunakan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="percentCoupons"><?= $stats['percent_coupons'] ?? 0 ?></div>
                    <div class="stat-label">Kupon Persentase</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="fixedCoupons"><?= $stats['fixed_coupons'] ?? 0 ?></div>
                    <div class="stat-label">Kupon Nominal</div>
                </div>
            </div>

            <div id="usedCouponsTable" class="table-responsive mt-3">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem; padding: 1rem;">Daftar Kupon Digunakan Hari Ini</h4>
                <?php if (mysqli_num_rows($used_coupons_today) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center text-black">Waktu</th>
                                <th class="text-center text-black">Kode Kupon</th>
                                <th class="text-center text-black">Customer</th>
                                <th class="text-center text-black">Tipe</th>
                                <th class="text-center text-black">Nilai Diskon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($coupon = mysqli_fetch_assoc($used_coupons_today)): ?>
                                <tr>
                                    <td class="text-center text-black"><?= date('H:i:s', strtotime($coupon['used_at'])) ?> WIB</td>
                                    <td class="text-center text-black"><?= htmlspecialchars($coupon['code']) ?></td>
                                    <td class="text-center text-black"><?= htmlspecialchars($coupon['nama_lengkap']) ?> (<?= htmlspecialchars($coupon['username']) ?>)</td>
                                    <td class="text-center text-black"><?= ucfirst($coupon['type']) ?></td>
                                    <td class="text-center text-black">
                                        <?php
                                        if ($coupon['type'] == 'percent') {
                                            echo htmlspecialchars($coupon['value']) . "%";
                                        } else {
                                            echo "Rp " . number_format($coupon['value'], 0, ',', '.');
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="padding: 2rem; text-align: center; color: var(--text-secondary);">Belum ada kupon yang digunakan hari ini.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal Konfirmasi -->
        <div id="confirmModal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-question-circle"></i> Konfirmasi Penggunaan Kupon</h3>
                    <p>Apakah Anda yakin ingin menggunakan kupon ini?</p>
                </div>

                <div id="couponPreview" style="background: rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 1rem; margin: 1rem 0;"></div>

                <div class="modal-actions">
                    <button id="confirmUse" class="btn btn-success">
                        <i class="fas fa-check"></i> Ya, Gunakan Kupon
                    </button>
                    <button id="cancelUse" class="btn btn-danger">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript untuk menyembunyikan alert setelah beberapa detik
        document.addEventListener('DOMContentLoaded', function() {
            const alertContainer = document.getElementById('alertContainer');
            if (alertContainer && alertContainer.children.length > 0) {
                setTimeout(() => {
                    const alerts = alertContainer.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        alert.style.transform = 'translateX(100px)';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    });
                }, 5000);
            }

            // Add smooth focus effects to form inputs
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Add hover effects to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });

        // Catatan: Untuk validasi dan penggunaan kupon, form disubmit secara tradisional (full page reload).
        // Jika Anda ingin pengalaman yang lebih dinamis tanpa reload, Anda dapat mengimplementasikan
        // AJAX (Fetch API atau XMLHttpRequest) untuk mengirim data form dan memperbarui UI berdasarkan
        // respons JSON dari server. Namun, ini akan memerlukan endpoint API terpisah di sisi server.
    </script>
</body>
</html>