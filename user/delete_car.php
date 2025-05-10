<?php include('../config.php'); session_start();
if (!isset($_SESSION['user_id'])) header('Location: ../login.php');
$user_id = $_SESSION['user_id'];

$id = $_GET['id'];
$car = $conn->query("SELECT image_url FROM cars WHERE id='$id' AND user_id='$user_id'")->fetch_assoc();
if ($car) {
  $conn->query("DELETE FROM cars WHERE id='$id' AND user_id='$user_id'");
  unlink("../uploads/".$car['image_url']);
}
header("Location: dashboard.php");
?>
