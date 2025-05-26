<script>
        // Data dummy untuk demo (dalam implementasi nyata, data diambil dari database via AJAX)
        const membersData = [
            {
                id: 1,
                nama_lengkap: "Ahmad Rizki Pratama",
                email: "ahmad.rizki@gmail.com",
                no_hp: "08123456789",
                alamat: "Jl. Sudirman No. 123, Jakarta Pusat",
                created_at: "2024-01-15",
                status: "Aktif"
            },
            {
                id: 2,
                nama_lengkap: "Siti Nurhaliza",
                email: "siti.nurhaliza@yahoo.com",
                no_hp: "08987654321",
                alamat: "Jl. Gatot Subroto No. 456, Jakarta Selatan",
                created_at: "2024-01-20",
                status: "Aktif"
            },
            {
                id: 3,
                nama_lengkap: "Budi Santoso",
                email: "budi.santoso@outlook.com",
                no_hp: "08555123456",
                alamat: "Jl. Thamrin No. 789, Jakarta Pusat",
                created_at: "2024-02-01",
                status: "Aktif"
            },
            {
                id: 4,
                nama_lengkap: "Maya Sari Dewi",
                email: "maya.sari@gmail.com",
                no_hp: "08777888999",
                alamat: "Jl. Kemang Raya No. 321, Jakarta Selatan",
                created_at: "2024-02-10",
                status: "Aktif"
            },
            {
                id: 5,
                nama_lengkap: "Dian Pratiwi",
                email: "dian.pratiwi@hotmail.com",
                no_hp: "08333444555",
                alamat: "Jl. Menteng No. 654, Jakarta Pusat",
                created_at: "2024-02-15",
                status: "Aktif"
            }
        ];

        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredData = [...membersData];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateStatistics();
            renderTable();
            setupSearch();
        });

        function updateStatistics() {
            document.getElementById('totalMembers').textContent = membersData.length;
            
            // Hitung member baru (30 hari terakhir)
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            const newMembers = membersData.filter(member => 
                new Date(member.created_at) >= thirtyDaysAgo
            ).length;
            document.getElementById('newMembers').textContent = newMembers;
            
            // Member aktif (semua member dalam demo ini aktif)
            const activeMembers = membersData.filter(member => 
                member.status === 'Aktif'
            ).length;
            document.getElementById('activeMembers').textContent = activeMembers;
        }

        function renderTable() {
            const tbody = document.getElementById('memberTableBody');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const currentData = filteredData.slice(startIndex, endIndex);

            if (currentData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="no-data">
                            <i class="fas fa-users-slash"></i>
                            <h3>Tidak ada data member</h3>
                            <p>Belum ada member yang terdaftar atau sesuai dengan pencarian Anda.</p>
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = currentData.map((member, index) => `
                    <tr>
                        <td>${startIndex + index + 1}</td>
                        <td><strong>${member.nama_lengkap}</strong></td>
                        <td>${member.email}</td>
                        <td>${member.no_hp}</td>
                        <td>${member.alamat}</td>
                        <td>${formatDate(member.created_at)}</td>
                        <td><span class="badge badge-active">${member.status}</span></td>
                        <td>
                            <button class="btn btn-view" onclick="viewMember(${member.id})">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <button class="btn btn-delete" onclick="deleteMember(${member.id})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const paginationContainer = document.getElementById('paginationContainer');

            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let paginationHTML = `
                <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                paginationHTML += `
                    <button onclick="changePage(${i})" ${i === currentPage ? 'class="active"' : ''}>
                        ${i}
                    </button>
                `;
            }

            paginationHTML += `
                <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            paginationContainer.innerHTML = paginationHTML;
        }

        function changePage(page) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                renderTable();
            }
        }

        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                if (searchTerm === '') {
                    filteredData = [...membersData];
                } else {
                    filteredData = membersData.filter(member => 
                        member.nama_lengkap.toLowerCase().includes(searchTerm) ||
                        member.email.toLowerCase().includes(searchTerm) ||
                        member.no_hp.includes(searchTerm) ||
                        member.alamat.toLowerCase().includes(searchTerm)
                    );
                }
                
                currentPage = 1;
                renderTable();
            });
        }

        function formatDate(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function viewMember(id) {
            const member = membersData.find(m => m.id === id);
            if (member) {
                Swal.fire({
                    title: '<strong>Detail Member</strong>',
                    html: `
                        <div style="text-align: left; padding: 20px;">
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #4a5568;">Nama Lengkap:</strong><br>
                                <span style="color: #2d3748; font-size: 1.1em;">${member.nama_lengkap}</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #4a5568;">Email:</strong><br>
                                <span style="color: #2d3748;">${member.email}</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #4a5568;">Nomor HP:</strong><br>
                                <span style="color: #2d3748;">${member.no_hp}</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #4a5568;">Alamat:</strong><br>
                                <span style="color: #2d3748;">${member.alamat}</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #4a5568;">Tanggal Daftar:</strong><br>
                                <span style="color: #2d3748;">${formatDate(member.created_at)}</span>
                            </div>
                            <div>
                                <strong style="color: #4a5568;">Status:</strong><br>
                                <span class="badge badge-active" style="display: inline-block; margin-top: 5px;">${member.status}</span>
                            </div>
                        </div>
                    `,
                    width: 600,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Tutup',
                    confirmButtonColor: '#667eea',
                    background: '#ffffff',
                    backdrop: `
                        rgba(0,0,0,0.4)
                        url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1' fill-rule='nonzero'%3E%3Ccircle cx='30' cy='30' r='30'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")
                        center/100px 100px
                    `
                });
            }
        }

        function deleteMember(id) {
            const member = membersData.find(m => m.id === id);
            if (member) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #f56565; margin-bottom: 20px;"></i>
                            <p style="font-size: 1.1em; color: #2d3748; margin-bottom: 10px;">
                                Apakah Anda yakin ingin menghapus member:
                            </p>
                            <strong style="color: #e53e3e; font-size: 1.2em;">${member.nama_lengkap}</strong>
                            <p style="color: #718096; margin-top: 15px; font-size: 0.9em;">
                                Tindakan ini tidak dapat dibatalkan!
                            </p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#e53e3e',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="fas fa-times"></i> Batal',
                    background: '#ffffff',
                    backdrop: `
                        rgba(0,0,0,0.4)
                        url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1' fill-rule='nonzero'%3E%3Ccircle cx='30' cy='30' r='30'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")
                        center/100px 100px
                    `
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Dalam implementasi nyata, kirim request AJAX ke server untuk menghapus data
                        // Untuk demo, hapus dari array lokal
                        const index = membersData.findIndex(m => m.id === id);
                        if (index > -1) {
                            membersData.splice(index, 1);
                            filteredData = [...membersData];
                            updateStatistics();
                            renderTable();
                            
                            Swal.fire({
                                title: 'Berhasil!',
                                html: `
                                    <div style="text-align: center; padding: 20px;">
                                        <i class="fas fa-check-circle" style="font-size: 4rem; color: #48bb78; margin-bottom: 20px;"></i>
                                        <p style="color: #2d3748; font-size: 1.1em;">
                                            Member <strong>${member.nama_lengkap}</strong> berhasil dihapus.
                                        </p>
                                    </div>
                                `,
                                confirmButtonColor: '#48bb78',
                                confirmButtonText: '<i class="fas fa-check"></i> OK',
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    }
                });
            }
        }

        // PHP Integration Functions (untuk implementasi nyata)
        /*
        function fetchMembers() {
            fetch('admin_api.php?action=get_members')
            .then(response => response.json())
            .then(data => {
                membersData = data;
                filteredData = [...data];
                updateStatistics();
                renderTable();
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal memuat data member', 'error');
            });
        }

        function deleteMemberFromDB(id) {
            fetch('admin_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_member&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh data
                    fetchMembers();
                    Swal.fire('Berhasil!', 'Member berhasil dihapus.', 'success');
                } else {
                    Swal.fire('Error!', data.message || 'Gagal menghapus member.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus member.', 'error');
            });
        }
        */
    </script>

    <script>
        // Close modal function
        function closeModal() {
            window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?><?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?><?php echo isset($_GET['page']) && empty($search) ? '?page=' . $_GET['page'] : (isset($_GET['page']) && !empty($search) ? '&page=' . $_GET['page'] : ''); ?>';
        }

        // Confirm delete function
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus member:<br><strong>${name}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteId').value = id;
                    document.getElementById('deleteForm').submit();
                }
            });
        }

        // Show success/error messages
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?php echo $_SESSION['success']; ?>',
                icon: 'success',
                confirmButtonColor: '#667eea'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                title: 'Error!',
                text: '<?php echo $_SESSION['error']; ?>',
                icon: 'error',
                confirmButtonColor: '#e74c3c'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>