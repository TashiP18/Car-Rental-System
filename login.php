<?php
include('header.php');
include('config.php');

$login_error = "";
$registered = isset($_GET['registered']);
if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = md5($_POST['password']);
  $check = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");
  if ($_POST['email'] === 'admin@rental.com' && $_POST['password'] === 'admin123') {
    $_SESSION['is_admin'] = true;
    header('Location: admin/admin_dashboard.php');
    exit();
  }
  
  if ($check->num_rows > 0) {
    $_SESSION['user_id'] = $check->fetch_assoc()['id'];
    header('Location: user/dashboard.php');
    exit();
  } else {
    $login_error = "Invalid email or password.";
  }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Car Rental</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      margin: 0;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
    }
    .center-form-wrapper {
      min-height: calc(100vh - 100px);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    footer {
      background: linear-gradient(to right, #141e30, #243b55);
      color: white;
    }
  </style>
</head>
<body class="min-vh-100">

<main>
  <div class="center-form-wrapper">
    <div class="container" style="max-width: 480px;">
      <div class="bg-white p-4 shadow rounded">
        <h3 class="text-center mb-4">Login</h3>
        <form method="post">
          <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
          <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
          <button type="submit" name="login" class="btn btn-primary w-100">Login</button>

          <!-- ✅ Show inline alert below login button -->
          <?php if (!empty($login_error)): ?>
            <div class="alert alert-danger mt-3 mb-0"><?php echo $login_error; ?></div>
          <?php elseif ($registered): ?>
            <div class="alert alert-success mt-3 mb-0">Registered successfully! Please login.</div>
          <?php endif; ?>
        </form>

        <div class="text-center mt-3">
          <a href="register.php">Don't have an account? Register</a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
