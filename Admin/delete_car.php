<?php
session_start();
include('../config.php');

// Allow access for admin or car owner
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$car_id) {
  header('Location: dashboard.php');
  exit();
}

// Check ownership or admin
$car = $conn->query("SELECT user_id FROM cars WHERE id = $car_id")->fetch_assoc();

if (!$car) {
  header('Location: dashboard.php');
  exit();
}

$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $car['user_id'];
$is_admin = isset($_SESSION['is_admin']);

if ($is_owner || $is_admin) {
  $conn->query("DELETE FROM bookings WHERE car_id = $car_id");
  $conn->query("DELETE FROM cars WHERE id = $car_id");

  $redirect = $is_admin ? 'admin_dashboard.php' : 'dashboard.php';
  header("Location: $redirect");
  exit();
} else {
  echo "<script>alert('Unauthorized action.'); window.location='dashboard.php';</script>";
}
?>
<?php
session_start();
include('../config.php');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  header('Location: ../login.php');
  exit();
}

$id = $_GET['id'];
$conn->query("DELETE FROM cars WHERE id = '$id'");
header('Location: admin_dashboard.php?deleted=car');
exit();
?>
