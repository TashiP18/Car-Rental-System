
<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

$active = basename($_SERVER['PHP_SELF']);
$is_home = ($active == 'index.php');
$default_img = 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
$profile_pic = $default_img;

if (isset($_SESSION['user_id'])) {
  include_once(__DIR__ . '/config.php');
  $uid = $_SESSION['user_id'];
  $user = $conn->query("SELECT profile_pic FROM users WHERE id = '$uid'")->fetch_assoc();
  if (!empty($user['profile_pic'])) {
    $stored_pic = $user['profile_pic'];
    $local_path = $_SERVER['DOCUMENT_ROOT'] . "/car_rental_system/uploads/" . $stored_pic;
    if (file_exists($local_path)) {
      $profile_pic = "/car_rental_system/uploads/" . $stored_pic;
    }
  }
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

<style>
  body {
    font-family: 'Poppins', sans-serif;
  }
  .navbar {
    transition: background-color 0.4s ease, box-shadow 0.4s ease;
  }
  .transparent-navbar {
    background-color: transparent;
  }
  .solid-navbar {
    background-color: #283e51 !important;
  }
  .navbar.scrolled {
    background-color: #283e51 !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
  }
  .navbar-brand,
  .navbar-nav .nav-link {
    color: #f1f1f1 !important;
    transition: color 0.3s ease;
  }
  .navbar-nav .nav-link:hover,
  .navbar-nav .nav-link.active {
    color: #ffc107 !important;
    background-color: rgba(255,255,255,0.1);
    border-radius: 30px;
  }
  .profile-img {
    width: 35px;
    height: 35px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #fff;
  }
  .animated-car {
  display: inline-block;
  font-size: 1.8rem;
  color: #ffc107;
  animation: drive 2s ease-in-out infinite;
}

@keyframes drive {
  0%   { transform: translateX(0); }
  50%  { transform: translateX(6px); }
  100% { transform: translateX(0); }
}

</style>

<nav class="navbar navbar-expand-lg fixed-top shadow-sm <?php echo $is_home ? 'transparent-navbar' : 'solid-navbar'; ?>">
  <div class="container">
  <a class="navbar-brand d-flex align-items-center gap-2" href="/car_rental_system/index.php">
  <span class="animated-car">
    <i class="bi bi-car-front-fill"></i>
  </span>
  <span>Car Rental</span>
</a>

  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">
  <?php if (!$is_admin): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo ($active == 'browse-cars.php') ? 'active' : ''; ?>" href="/car_rental_system/browse-cars.php">Browse Cars</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/car_rental_system/index.php#about-section">About</a>
    </li>
  <?php endif; ?>

  <?php if ($is_admin): ?>
    <li class="nav-item">
      <a class="nav-link text-warning fw-bold" href="/car_rental_system/admin/admin_dashboard.php">Admin Panel</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/car_rental_system/logout.php">Logout</a>
    </li>
  <?php elseif (isset($_SESSION['user_id'])): ?>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="<?php echo $profile_pic; ?>" alt="Profile" class="profile-img me-2">
        <span class="d-none d-md-inline">My Account</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="/car_rental_system/user/dashboard.php">My Cars</a></li>
        <li><a class="dropdown-item" href="/car_rental_system/user/manage_profile.php">Edit Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="/car_rental_system/logout.php">Logout</a></li>
      </ul>
    </li>
  <?php else: ?>
    <li class="nav-item">
      <a class="nav-link <?php echo ($active == 'login.php') ? 'active' : ''; ?>" href="/car_rental_system/login.php">Login</a>
    </li>
  <?php endif; ?>
</ul>

    </div>
  </div>
</nav>
<!-- Bootstrap Bundle JS (includes Popper) -->
 
 


<?php if ($is_home): ?>
<!-- Scroll-triggered style only on index.php -->
<script>
  window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
</script>
<?php endif; ?>
