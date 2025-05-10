<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $car_id = (int)$_POST['car_id'];
  $email = $conn->real_escape_string($_POST['email']);
  $phone = $conn->real_escape_string($_POST['phone']);

  $car = $conn->query("SELECT * FROM cars WHERE id='$car_id' AND status='Available'")->fetch_assoc();

  if ($car) {
    $conn->query("INSERT INTO bookings (car_id, email, phone) VALUES ('$car_id', '$email', '$phone')");
    $conn->query("UPDATE cars SET status='Booked' WHERE id='$car_id'");
    header("Location: browse-cars.php?booked=1");
    exit();
  } else {
    header("Location: browse-cars.php?booked=0");
    exit();
  }
}
?>
