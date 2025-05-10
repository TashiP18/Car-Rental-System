<?php
include('../header.php');
include('../config.php');
if (!isset($_SESSION['user_id'])) header('Location: ../login.php');
$user_id = $_SESSION['user_id'];

$id = $_GET['id'];
$car = $conn->query("SELECT * FROM cars WHERE id='$id' AND user_id='$user_id'")->fetch_assoc();
if (!$car) exit("<script>showToast('Unauthorized.', 'danger'); window.location='dashboard.php';</script>");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Car - Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>
<body class="min-vh-100">

<main class="container mt-5 pt-5" style="max-width: 600px;">
  <h3 class="mb-4 text-center">Edit Car</h3>

  <form method="post" enctype="multipart/form-data">
    <input type="text" name="title" class="form-control mb-2" value="<?php echo $car['title']; ?>" required>
    <input type="text" name="brand" class="form-control mb-2" value="<?php echo $car['brand']; ?>" required>
    <input type="text" name="model" class="form-control mb-2" value="<?php echo $car['model']; ?>" required>
    <input type="number" name="year" class="form-control mb-2" value="<?php echo $car['year']; ?>" required>
    <input type="number" name="price" class="form-control mb-2" value="<?php echo $car['price_per_day']; ?>" required>

    <select name="category" class="form-select mb-2" required>
      <option value="">Select Category</option>
      <?php
      $cat = $conn->query("SELECT * FROM car_categories");
      while ($c = $cat->fetch_assoc()):
        $sel = $c['id'] == $car['category_id'] ? 'selected' : '';
        echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
      endwhile;
      ?>
    </select>

    <div class="mb-3">
      <label class="form-label">Current Image</label><br>
      <img src="../uploads/<?php echo $car['image_url']; ?>" width="120" class="img-thumbnail">
    </div>

    <input type="file" name="car_image" class="form-control mb-3">
    <textarea name="description" class="form-control mb-3" rows="4" required><?php echo $car['description']; ?></textarea>
    <button type="submit" name="update" class="btn btn-primary w-100">Update Car</button>
  </form>
</main>

<?php
if (isset($_POST['update'])) {
  $title = $_POST['title'];
  $brand = $_POST['brand'];
  $model = $_POST['model'];
  $year = $_POST['year'];
  $price = $_POST['price'];
  $cat = $_POST['category'];
  $desc = $_POST['description'];

  $image = $car['image_url'];
  if (!empty($_FILES['car_image']['name'])) {
    $image = uniqid().'_'.$_FILES['car_image']['name'];
    move_uploaded_file($_FILES['car_image']['tmp_name'], "../uploads/$image");
  }

  $conn->query("UPDATE cars SET title='$title', brand='$brand', model='$model', year='$year',
                price_per_day='$price', category_id='$cat', image_url='$image', description='$desc'
                WHERE id='$id' AND user_id='$user_id'");

echo "<script>
  localStorage.setItem('toastMessage', 'Car updated successfully!');
  window.location.href = 'dashboard.php';
</script>";
}
include('../footer.php');
?>
 </body>
</html>
