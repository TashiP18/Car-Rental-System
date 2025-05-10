<?php
include('../config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$error_msg = '';

// Handle form submission
if (isset($_POST['add'])) {
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  $title = $conn->real_escape_string($_POST['title']);
  $brand = $conn->real_escape_string($_POST['brand']);
  $model = $conn->real_escape_string($_POST['model']);
  $year = (int)$_POST['year'];
  $price = (float)$_POST['price'];
  $cat = (int)$_POST['category'];
  $desc = $conn->real_escape_string($_POST['description']);

  $img = $_FILES['car_image']['name'];
  $tmp = $_FILES['car_image']['tmp_name'];
  $ext = pathinfo($img, PATHINFO_EXTENSION);
  $new_img = uniqid('car_') . '.' . $ext;
  $path = "../uploads/$new_img";

  if (move_uploaded_file($tmp, $path)) {
    $query = "INSERT INTO cars (user_id, category_id, title, brand, model, year, price_per_day, image_url, description)
              VALUES ('$user_id', '$cat', '$title', '$brand', '$model', '$year', '$price', '$new_img', '$desc')";
    if ($conn->query($query)) {
      header('Location: dashboard.php?success=1');
      exit();
    } else {
      $error_msg = "Database error: cannot save car.";
    }
  } else {
    $error_msg = "File upload failed.";
  }
}
?>

<?php include('../header.php'); ?>

<div class="container mt-5 pt-5" style="max-width: 600px;">
  <h3 class="mb-3">Add Car</h3>

  <?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="text" name="title" class="form-control mb-2" placeholder="Car Title" required>
    <input type="text" name="brand" class="form-control mb-2" placeholder="Brand" required>
    <input type="text" name="model" class="form-control mb-2" placeholder="Model" required>
    <input type="number" name="year" class="form-control mb-2" placeholder="Year" required>
    <input type="number" name="price" class="form-control mb-2" placeholder="Price Per Day" required>

    <select name="category" class="form-select mb-2" required>
      <option value="">Select Car Type</option>
      <?php
      $cat = $conn->query("SELECT * FROM car_categories");
      while ($c = $cat->fetch_assoc()):
        echo "<option value='{$c['id']}'>{$c['name']}</option>";
      endwhile;
      ?>
    </select>

    <input type="file" name="car_image" class="form-control mb-2" required>
    <textarea name="description" class="form-control mb-3" placeholder="Description" rows="4" required></textarea>
    <button type="submit" name="add" class="btn btn-success w-100">Post Car</button>
  </form>
</div>

<?php include('../footer.php'); ?>
