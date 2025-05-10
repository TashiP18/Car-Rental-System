<?php
session_start();
include('../config.php');

// Ensure only admin can perform this
if (!isset($_SESSION['is_admin'])) {
  header('Location: ../login.php');
  exit();
}

if (isset($_GET['id'])) {
  $user_id = (int)$_GET['id'];

  // Optionally delete user's cars too
  $conn->query("DELETE FROM cars WHERE user_id = $user_id");

  // Delete user
  $conn->query("DELETE FROM users WHERE id = $user_id");

  header('Location: admin_dashboard.php');
  exit();
}
?>
