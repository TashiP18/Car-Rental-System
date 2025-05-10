<?php
include('../header.php');
include('../config.php');

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - My Cars</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
      font-family: 'Poppins', sans-serif;
    }
    main {
      flex: 1;
    }
    .card-img-top {
      height: 200px;
      object-fit: cover;
    }
    .modal-backdrop.show {
  opacity: 0.2 !important;
  backdrop-filter: blur(1px);
}

  </style>
</head>
<body class="min-vh-100">

<main class="container py-5 mt-5">

  <!-- ✅ Toast Success Message -->
  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
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
              Car posted successfully!
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

  <!-- ✅ Top Section with View Bookings Button -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Your Posted Cars</h3>
    <div>
      <a href="#bookings" class="btn btn-outline-info me-2">📋 View Bookings</a>
      <a href="add_car.php" class="btn btn-success">+ Add New Car</a>
    </div>
  </div>

  <!-- Posted Cars -->
  <div class="row">
    <?php
    $cars = $conn->query("SELECT cars.*, car_categories.name AS category_name 
                          FROM cars 
                          LEFT JOIN car_categories ON cars.category_id = car_categories.id
                          WHERE user_id = '$user_id' ORDER BY created_at DESC");

    if ($cars->num_rows > 0):
      while ($car = $cars->fetch_assoc()):
    ?>
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm">
        <img src="../uploads/<?php echo $car['image_url']; ?>" class="card-img-top">
        <div class="card-body">
          <h5 class="card-title"><?php echo $car['title']; ?></h5>
          <p class="card-text"><?php echo $car['brand'] . ' ' . $car['model']; ?> (<?php echo $car['category_name']; ?>)</p>
          <p><strong>Status:</strong>
            <?php if ($car['status'] === 'Booked'): ?>
              <span class="badge bg-secondary">Booked</span>
            <?php else: ?>
              <span class="badge bg-success">Available</span>
            <?php endif; ?>
          </p>
          <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
          <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $car['id']; ?>">Delete</button>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?php echo $car['id']; ?>" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to permanently remove <strong><?php echo htmlspecialchars($car['title']); ?></strong> from your listings?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <a href="delete_car.php?id=<?php echo $car['id']; ?>" class="btn btn-danger">Yes, Delete</a>
          </div>
        </div>
      </div>
    </div>

    <?php endwhile; else: ?>
      <div class="col text-center text-muted">You haven't posted any cars yet.</div>
    <?php endif; ?>
  </div>

  <!-- ✅ Bookings Section with Anchor ID -->
  <div id="bookings" class="mt-5 pt-4 border-top">
    <h4 class="mb-3">📋 Bookings Received</h4>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Car</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Booked At</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $bookings = $conn->query("SELECT bookings.*, cars.title 
                                    FROM bookings 
                                    INNER JOIN cars ON bookings.car_id = cars.id 
                                    WHERE cars.user_id = '$user_id'
                                    ORDER BY bookings.created_at DESC");

          if ($bookings->num_rows > 0):
            while ($b = $bookings->fetch_assoc()):
          ?>
          <tr>
            <td><?php echo htmlspecialchars($b['title']); ?></td>
            <td><?php echo htmlspecialchars($b['email']); ?></td>
            <td><?php echo htmlspecialchars($b['phone']); ?></td>
            <td><?php echo date('Y-m-d H:i', strtotime($b['created_at'])); ?></td>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="4" class="text-center text-muted">No bookings received yet.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include('../footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const msg = localStorage.getItem('toastMessage');
    if (msg) {
      const toast = document.createElement('div');
      toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3';
      toast.role = 'alert';
      toast.ariaLive = 'assertive';
      toast.ariaAtomic = 'true';
      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">${msg}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      `;
      document.body.appendChild(toast);
      new bootstrap.Toast(toast, { delay: 3000 }).show();
      localStorage.removeItem('toastMessage');
    }
  });
</script>

</body>
</html>
