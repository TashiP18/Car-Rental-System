<?php
include('header.php');
include('config.php');

$success_msg = $error_msg = "";

if (isset($_POST['register'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $password = md5($_POST['password']);

  // Check if email is already registered
  $exists = $conn->query("SELECT * FROM users WHERE email='$email'");
  if ($exists->num_rows > 0) {
    $error_msg = "Email already registered.";
  } else {
    $conn->query("INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$password')");
    $success_msg = "Registered successfully! Redirecting to login...";
    echo "<script>
      setTimeout(() => {
        window.location.href = 'login.php?registered=1';
      }, 3000);
    </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Car Rental</title>
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
        <h3 class="text-center mb-4">Create an Account</h3>

        <form method="post" onsubmit="return checkPasswordMatch();">
          <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
          <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
          <input type="text" name="phone" class="form-control mb-3" placeholder="Phone" required>
          <input type="password" id="password" name="password" class="form-control mb-3" placeholder="Password" required>
          <input type="password" id="confirm_password" class="form-control mb-2" placeholder="Confirm Password" required>
          <div id="passwordMismatch" class="text-danger mb-2" style="display:none;">Passwords do not match.</div>
          <button type="submit" name="register" class="btn btn-primary w-100">Register</button>

          <!-- ✅ Alert messages below button -->
          <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger mt-3 mb-0"><?php echo $error_msg; ?></div>
          <?php elseif (!empty($success_msg)): ?>
            <div class="alert alert-success mt-3 mb-0"><?php echo $success_msg; ?></div>
          <?php endif; ?>
        </form>

        <div class="text-center mt-3">
          <a href="login.php">Already have an account? Login</a>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
function checkPasswordMatch() {
  const pw = document.getElementById("password").value;
  const cpw = document.getElementById("confirm_password").value;
  const warning = document.getElementById("passwordMismatch");
  if (pw !== cpw) {
    warning.style.display = "block";
    return false;
  }
  warning.style.display = "none";
  return true;
}
</script>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
