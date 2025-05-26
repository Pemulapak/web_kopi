<?php include('../layouts/admin/partials-section/head.php') ?>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header animate__animated animate__fadeInDown">
            <h1>
                <i class="fas fa-users-cog"></i>
                Dashboard Admin
            </h1>
            <p class="subtitle">Kelola akun member dengan mudah dan efisien</p>
        </div>

        <!-- Statistics -->
        <div class="stats-container">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-number"><?php echo number_format($stats['total_members']); ?></div>
                    <div class="stat-label">Total Member</div>
                </div>
            </div>
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <div class="stat-number"><?php echo number_format($stats['new_members']); ?></div>
                    <div class="stat-label">Member Baru (30 hari)</div>
                </div>
            </div>
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="stat-number"><?php echo number_format($stats['total_members']); ?></div>
                    <div class="stat-label">Member Aktif</div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input"
                    placeholder="Cari member berdasarkan nama, email, atau nomor HP..."
                    value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="table-container animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Daftar Member</h2>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Lengkap</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">No. HP</th>
                            <th class="text-center">Alamat</th>
                            <th class="text-center">Tanggal Daftar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = $offset + 1;
                        while ($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['alamat'], 0, 50)) . (strlen($row['alamat']) > 50 ? '...' : ''); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td><span class="status-badge status-active">Aktif</span></td>
                                <td>
                                    <div class="action-buttons d-flex gap-2">
                                        <a href="?detail=<?php echo $row['id']; ?>" class="btn btn-info d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-eye"></i> <span>Detail</span>
                                        </a>
                                        <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_lengkap']); ?>')"
                                            class="btn btn-danger d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-trash"></i> <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="?page=<?php echo ($page - 1); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);

                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <?php if ($i == $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo ($page + 1); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="?page=<?php echo $totalPages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-users" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                    <p><?php echo !empty($search) ? 'Tidak ada data yang sesuai dengan pencarian.' : 'Belum ada data member.'; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detail Modal -->
    <?php if ($detailMember): ?>
        <div id="detailModal" class="modal" style="display: block;">
            <div class="modal-content animate__animated animate__zoomIn">
                <div class="modal-header">
                    <h2><i class="fas fa-user"></i> Detail Member</h2>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>

                <div class="detail-grid">
                    <div class="detail-label">ID:</div>
                    <div class="detail-value"><?php echo $detailMember['id']; ?></div>

                    <div class="detail-label">Nama Lengkap:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($detailMember['nama_lengkap']); ?></div>

                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($detailMember['email']); ?></div>

                    <div class="detail-label">No. HP:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($detailMember['no_hp']); ?></div>

                    <div class="detail-label">Alamat:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($detailMember['alamat']); ?></div>

                    <div class="detail-label">Tanggal Daftar:</div>
                    <div class="detail-value"><?php echo date('d F Y, H:i:s', strtotime($detailMember['created_at'])); ?></div>

                    <div class="detail-label">Status:</div>
                    <div class="detail-value"><span class="status-badge status-active">Aktif</span></div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <button onclick="closeModal()" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Hidden Form for Delete -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <?php include('../layouts/admin/partials-section/script.php') ?>


</body>

</html>