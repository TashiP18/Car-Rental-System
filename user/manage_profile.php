<?php
include('../header.php');
include('../config.php');

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$error_msg = "";

// Fetch user data
$user = $conn->query("SELECT * FROM users WHERE id = '$user_id'")->fetch_assoc();
$profile_pic = $user['profile_pic'];

// Handle update
if (isset($_POST['update'])) {
  $name = $conn->real_escape_string($_POST['name']);
  $email = $conn->real_escape_string($_POST['email']);
  $phone = $conn->real_escape_string($_POST['phone']);
  $password = $_POST['new_password'];

  if (!empty($_FILES['profile_pic']['name'])) {
    $img = $_FILES['profile_pic']['name'];
    $tmp = $_FILES['profile_pic']['tmp_name'];
    $new_name = 'profile_' . uniqid() . '.' . pathinfo($img, PATHINFO_EXTENSION);
    if (move_uploaded_file($tmp, "../uploads/$new_name")) {
      $profile_pic = $new_name;
    } else {
      $error_msg = "Failed to upload profile image.";
    }
  }

  if (empty($error_msg)) {
    $query = !empty($password)
      ? "UPDATE users SET name='$name', email='$email', phone='$phone', profile_pic='$profile_pic', password='" . md5($password) . "' WHERE id='$user_id'"
      : "UPDATE users SET name='$name', email='$email', phone='$phone', profile_pic='$profile_pic' WHERE id='$user_id'";

    if ($conn->query($query)) {
      header("Location: manage_profile.php?updated=1");
      exit();
    } else {
      $error_msg = "Database error. Please try again.";
    }
  }
}

// Image path resolution
$default_img = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
$profile_src = $default_img;
if (!empty($user['profile_pic'])) {
  $stored_pic = $user['profile_pic'];
  $local_path = $_SERVER['DOCUMENT_ROOT'] . "/car_rental_system/uploads/" . $stored_pic;
  if (file_exists($local_path)) {
    $profile_src = "../uploads/" . $stored_pic;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="d-flex flex-column min-vh-100">

<main class="flex-fill pt-5">
  <!-- ✅ Toast message -->
  <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3';
        toast.role = 'alert';
        toast.ariaLive = 'assertive';
        toast.ariaAtomic = 'true';
        toast.innerHTML = `
          <div class="d-flex">
            <div class="toast-body">
              ✅ Profile updated successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
      });
    </script>
  <?php endif; ?>

  <div class="container" style="max-width: 600px; margin-top: 30px;">
    <h3 class="mb-4 text-center">Manage Profile</h3>
    <form method="post" enctype="multipart/form-data">
      <div class="text-center mb-3">
        <img src="<?php echo $profile_src; ?>" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
      </div>

      <input type="text" name="name" class="form-control mb-2" value="<?php echo $user['name']; ?>" required>
      <input type="email" name="email" class="form-control mb-2" value="<?php echo $user['email']; ?>" required>
      <input type="text" name="phone" class="form-control mb-2" value="<?php echo $user['phone']; ?>" required>

      <label class="form-label mt-2">Update Profile Image</label>
      <input type="file" name="profile_pic" class="form-control mb-3" accept="image/*">

      <input type="password" name="new_password" class="form-control mb-2" placeholder="New Password (leave blank to keep current)">
      <button type="submit" name="update" class="btn btn-primary w-100">Update Profile</button>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error_msg; ?></div>
      <?php endif; ?>
    </form>
  </div>
</main>

<?php include('../footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
