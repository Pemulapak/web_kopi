<?php
session_start();
include('config/koneksi.php');

$success = false;
$error_message = '';

// Fungsi untuk generate username random
function generateRandomUsername($length = 10)
{
  $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $username = '';
  for ($i = 0; $i < $length; $i++) {
    $username .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $username;
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
  // Ambil dan sanitize data POST
  $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['no_hp']);
  $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  // Validasi data tidak kosong
  if (empty($nama) || empty($email) || empty($phone) || empty($alamat) || empty($password) || empty($confirm_password)) {
    $error_message = "Semua field harus diisi";
  } elseif ($password !== $confirm_password) {
    $error_message = "Password dan konfirmasi password tidak cocok";
  } elseif (strlen($password) < 8) {
    $error_message = "Password minimal 8 karakter";
  } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/', $password)) {
    $error_message = "Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus";
  } else {
    // Cek apakah email sudah terdaftar
    $check_email = "SELECT email FROM akun_member WHERE email = '$email'";
    $result_check = mysqli_query($conn, $check_email);

    // Cek apakah nomor HP sudah terdaftar
    $check_phone = "SELECT no_hp FROM akun_member WHERE no_hp = '$phone'";
    $result_phone = mysqli_query($conn, $check_phone);

    if (mysqli_num_rows($result_check) > 0) {
      $error_message = "Email sudah pernah digunakan. Gunakan email lain.";
    } elseif (mysqli_num_rows($result_phone) > 0) {
      $error_message = "Nomor HP sudah pernah digunakan. Gunakan nomor HP lain.";
    } else {
      // Generate username dan hash password
      $username = generateRandomUsername();
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      // Insert data ke database
      $query = "INSERT INTO akun_member (nama_lengkap, username, email, no_hp, alamat, password, created_at) 
                      VALUES ('$nama', '$username', '$email', '$phone', '$alamat', '$hashed_password', NOW())";

      if (mysqli_query($conn, $query)) {
        $success = true;
        // Clear form data after successful registration
        $_POST = array();
      } else {
        $error_message = "Error database: " . mysqli_error($conn);
      }
    }
  }
}

?>

<!DOCTYPE html>
<html lang="id">
<?php include('layouts/user/partials/header.php') ?>

<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <a href="#home" class="logo">
        <i class="fas fa-coffee"></i> Titik Awal Kopitiam
      </a>
      <ul class="nav-menu">
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#menu">Menu</a></li>
        <li><a href="#benefits">Member</a></li>
        <li><a href="#register">Daftar</a></li>
        <a href="#" onclick="openModal('userModal')">Login</a>
      </ul>
      <div class="hamburger">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="home" class="hero">
    <div class="hero-content">
      <h1>Selamat Datang di <br />Titik Awal Kopitiam</h1>
      <p>
        Tempat berkumpul terbaik di Jatinangor untuk menikmati kopi
        berkualitas, suasana hangat, dan produktivitas maksimal
      </p>
      <a href="#about" class="cta-button">Jelajahi Lebih Lanjut</a>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="section about">
    <div class="container">
      <h2 class="section-title fade-in">Tentang Kami</h2>
      <p class="section-subtitle fade-in">
        Kopitiam yang menghadirkan pengalaman kopi autentik di jantung
        Jatinangor
      </p>
      <div class="about-content">
        <div class="about-text fade-in">
          <h3 style="color: #8b4513; margin-bottom: 1rem; font-size: 1.5rem">
            Cerita Kami
          </h3>
          <p>
            Titik Awal Kopitiam lahir dari kecintaan terhadap kopi berkualitas
            dan keinginan untuk menciptakan ruang komunitas yang nyaman di
            Jatinangor. Kami memahami kebutuhan mahasiswa, pekerja remote, dan
            warga lokal akan tempat yang tidak hanya menyajikan kopi lezat,
            tetapi juga suasana yang mendukung produktivitas dan interaksi
            sosial.
          </p>
          <br />
          <p>
            Dengan biji kopi pilihan terbaik, barista berpengalaman, dan
            interior yang dirancang khusus untuk kenyamanan, kami berkomitmen
            menjadi titik awal hari yang sempurna untuk setiap pelanggan.
          </p>
          <br />
          <div
            style="
                  background: #8b4513;
                  color: white;
                  padding: 1rem;
                  border-radius: 10px;
                ">
            <p>
              <i class="fas fa-map-marker-alt"></i>
              <strong>Lokasi:</strong> Jl. Raya Jatinangor No. 123,
              Jatinangor, Sumedang
            </p>
            <p>
              <i class="fas fa-clock"></i> <strong>Jam Buka:</strong> Senin -
              Minggu, 07:00 - 22:00 WIB
            </p>
          </div>
        </div>
        <div class="about-image fade-in">
          <img src="img/foto.jpg" alt="Interior Titik Awal Kopitiam" />
        </div>
      </div>
    </div>
  </section>

  <!-- Menu Section -->
  <section id="menu" class="section">
    <div class="container">
      <h2 class="section-title fade-in">Menu Favorit</h2>
      <p class="section-subtitle fade-in">
        Nikmati koleksi kopi dan makanan terbaik kami
      </p>
      <div class="menu-grid">
        <div class="menu-item fade-in">
          <img src="img/cofee.webp" alt="Kopi Titik Awal" />
          <h3>Kopi Titik Awal</h3>
          <p>
            Signature blend dengan rasa yang kaya dan aroma yang menggugah
            selera
          </p>
          <div class="price">Rp 18.000</div>
        </div>
        <div class="menu-item fade-in">
          <img src="img/cofee.webp" alt="Cappuccino Premium" />
          <h3>Cappuccino Premium</h3>
          <p>Espresso berkualitas dengan milk foam yang sempurna</p>
          <div class="price">Rp 22.000</div>
        </div>
        <div class="menu-item fade-in">
          <img src="img/cofee.webp" alt="Cold Brew Special" />
          <h3>Cold Brew Special</h3>
          <p>Kopi dingin dengan ekstraksi 12 jam untuk rasa yang smooth</p>
          <div class="price">Rp 20.000</div>
        </div>
        <div class="menu-item fade-in">
          <img src="img/cofee.webp" alt="Croissant Butter" />
          <h3>Croissant Butter</h3>
          <p>Pastry renyah dengan butter premium, cocok untuk teman ngopi</p>
          <div class="price">Rp 15.000</div>
        </div>
        <div class="menu-item fade-in">
          <img src="img/cofee.webp" alt="Sandwich Club" />
          <h3>Sandwich Club</h3>
          <p>Sandwich dengan isian lengkap, perfect untuk makan siang</p>
          <div class="price">Rp 25.000</div>
        </div>
        <div class="menu-item fade-in">
          <img src="img/cofee.webp" alt="Matcha Latte" />
          <h3>Matcha Latte</h3>
          <p>Minuman matcha premium dengan rasa yang autentik</p>
          <div class="price">Rp 24.000</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Benefits Section -->
  <section id="benefits" class="section benefits">
    <div class="container">
      <h2 class="section-title fade-in" style="color: white">
        Keuntungan Member
      </h2>
      <p class="section-subtitle fade-in" style="color: #ffe4b5">
        Bergabunglah dengan komunitas Titik Awal dan nikmati berbagai
        keuntungan eksklusif
      </p>
      <div class="benefits-grid">
        <div class="benefit-item fade-in">
          <i class="fas fa-percentage"></i>
          <h3>Diskon 15%</h3>
          <p>
            Dapatkan diskon 15% untuk setiap pembelian minuman dan makanan
          </p>
        </div>
        <div class="benefit-item fade-in">
          <i class="fas fa-gift"></i>
          <h3>Poin Reward</h3>
          <p>
            Kumpulkan poin setiap pembelian dan tukarkan dengan menu gratis
          </p>
        </div>
        <div class="benefit-item fade-in">
          <i class="fas fa-wifi"></i>
          <h3>WiFi Premium</h3>
          <p>Akses WiFi berkecepatan tinggi khusus untuk member</p>
        </div>
        <div class="benefit-item fade-in">
          <i class="fas fa-birthday-cake"></i>
          <h3>Birthday Treat</h3>
          <p>Dapatkan minuman gratis di bulan ulang tahun Anda</p>
        </div>
        <div class="benefit-item fade-in">
          <i class="fas fa-clock"></i>
          <h3>Happy Hour</h3>
          <p>Nikmati promo khusus member di jam-jam tertentu</p>
        </div>
        <div class="benefit-item fade-in">
          <i class="fas fa-users"></i>
          <h3>Event Eksklusif</h3>
          <p>Undangan khusus untuk acara dan workshop eksklusif</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Member Registration -->
  <section id="register" class="section member-form">
    <div class="form-container fade-in">
      <form method="POST" action="" id="memberForm">
        <div class="form-group">
          <label for="nama_lengkap">Nama Lengkap</label>
          <input
            type="text"
            id="nama_lengkap"
            name="nama_lengkap"
            placeholder="Masukkan nama lengkap Anda"
            value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>"
            required />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="contoh@email.com"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
            required />
        </div>
        <div class="form-group">
          <label for="no_hp">Nomor HP</label>
          <input
            type="text"
            id="no_hp"
            name="no_hp"
            placeholder="08xxxxxxxxxx"
            value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>"
            required />
        </div>
        <div class="form-group">
          <label for="alamat">Alamat</label>
          <input
            type="text"
            id="alamat"
            name="alamat"
            placeholder="Alamat lengkap Anda"
            value="<?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?>"
            required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
            placeholder="Masukkan password" required>
          <div class="password-strength" id="passwordStrength">
            <div class="strength-item" id="lengthCheck">
              <i class="fas fa-times"></i> Minimal 8 karakter
            </div>
            <div class="strength-item" id="uppercaseCheck">
              <i class="fas fa-times"></i> Huruf besar (A-Z)
            </div>
            <div class="strength-item" id="lowercaseCheck">
              <i class="fas fa-times"></i> Huruf kecil (a-z)
            </div>
            <div class="strength-item" id="numberCheck">
              <i class="fas fa-times"></i> Angka (0-9)
            </div>
            <div class="strength-item" id="specialCheck">
              <i class="fas fa-times"></i> Karakter khusus (@$!%*?&)
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="confirm_password">Konfirmasi Password</label>
          <input type="password" id="confirm_password" name="confirm_password"
            placeholder="Ulangi password" required>
          <div class="password-match" id="passwordMatch">
            <i class="fas fa-times"></i> Password tidak cocok
          </div>
        </div>

        <button type="submit" name="register" class="submit-btn">
          <i class="fas fa-user-plus"></i> Daftar Sekarang
        </button>
      </form>
    </div>
  </section>

  <!-- User Modal -->
  <div id="userModal" class="fixed inset-0 hidden items-center justify-center modal-bg z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-96 coffee-theme relative">
      <button onclick="closeModal('userModal')" class="absolute top-2 right-2 text-white">✕</button>
      <h2 class="text-2xl font-bold mb-4 text-center">☕ Welcome Back, Coffee Lover</h2>

      <!-- PERBAIKAN: Tambahkan name="login" pada button submit -->
      <form action="resource/login-user.php" method="POST">
        <input type="email" name="email" placeholder="Email" class="coffee-input p-2 rounded w-full mb-3" required />
        <input type="password" name="password" placeholder="Password" class="coffee-input p-2 rounded w-full mb-3" required />

        <!-- PERBAIKAN: Tambahkan name="login" -->
        <button type="submit" name="login" class="bg-[#4e342e] text-white py-2 px-4 rounded w-full hover:bg-[#3e2723]">Login</button>
      </form>

      <p class="text-sm mt-4 text-center">
        Are you an admin? <a href="#" class="underline" onclick="switchModal('userModal', 'adminModal')">Login here</a>
      </p>
    </div>
  </div>

  <!-- Admin Modal -->
  <div id="adminModal" class="fixed inset-0 hidden items-center justify-center modal-bg z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-96 coffee-theme relative">
      <button onclick="closeModal('adminModal')" class="absolute top-2 right-2 text-white">✕</button>
      <h2 class="text-2xl font-bold mb-4 text-center">🔐 Admin Login</h2>
      <form method="POST" action="resource/login-admin.php">
        <input type="email" name="email" placeholder="Admin Email" class="coffee-input p-2 rounded w-full mb-3" required />
        <input type="password" name="password" placeholder="Password" class="coffee-input p-2 rounded w-full mb-3" required />
        <button type="submit" class="bg-[#4e342e] text-white py-2 px-4 rounded w-full hover:bg-[#3e2723]">Login as Admin</button>
      </form>
      <p class="text-sm mt-4 text-center">
        Not an admin? <a href="#" class="underline" onclick="switchModal('adminModal', 'userModal')">Go back</a>
      </p>
    </div>
  </div>

  <?php include('layouts/user/partials/footer.php') ?>

  <!-- Script -->
  <?php include('layouts/user/partials/script.php') ?>

  <!-- Sweet Alert -->
  <script>
    <?php if ($success): ?>
      Swal.fire({
        icon: 'success',
        title: 'Pendaftaran Berhasil!',
        text: 'Selamat bergabung dengan keluarga Titik Awal Kopitiam!',
        confirmButtonText: 'OK'
      });
    <?php elseif (!empty($error_message)): ?>
      Swal.fire({
        icon: 'error',
        title: 'Pendaftaran Gagal',
        text: <?php echo json_encode($error_message); ?>,
        confirmButtonText: 'Coba Lagi'
      });
    <?php endif; ?>
  </script>

  <!-- Login Error User-->
  <?php if (isset($_SESSION['login_error'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal Login',
        text: '<?php echo $_SESSION['login_error']; ?>',
        confirmButtonColor: '#d33'
      });
    </script>
  <?php unset($_SESSION['login_error']);
  endif; ?>

  <!-- Login Error Admin -->
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


  <!-- Modal Script -->
  <script>
    function openModal(id) {
      document.getElementById(id).classList.remove('hidden');
      document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('flex');
      document.getElementById(id).classList.add('hidden');
    }

    function switchModal(from, to) {
      closeModal(from);
      openModal(to);
    }
  </script>