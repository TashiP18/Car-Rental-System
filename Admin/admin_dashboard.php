<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  header('Location: ../login.php');
  exit();
}
include('../header.php');
include('../config.php');

$user_search = $_GET['user_search'] ?? '';
$car_search = $_GET['car_search'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Car Rental</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
  .scroll-offset {
    scroll-margin-top: 80px; /* adjust based on navbar height */
  }
</style>
</head>
<body class="min-vh-100 d-flex flex-column">

<main class="flex-fill container mt-5 pt-5">
  

 <!-- ✅ Quick Access Buttons (Right Aligned) -->
<div class="d-flex justify-content-end gap-3 mb-4">
  <a href="#users" class="btn btn-outline-primary">
    🧑‍💼 Manage Users
  </a>
  <a href="#cars" class="btn btn-outline-success">
    🚗 Manage Cars
  </a>
</div>


  <!-- ✅ User Management -->
  <section id="users" class="mb-5">
    <h4 class="mb-3 scroll-offset" id="users>🧑‍💼 Manage Users</h4>
    <form class="mb-3" method="GET">
      <div class="input-group">
        <input type="text" name="user_search" class="form-control" placeholder="Search user by name..." value="<?php echo htmlspecialchars($user_search); ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
             <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $user_sql = "SELECT * FROM users";
        if (!empty($user_search)) {
          $user_sql .= " WHERE name LIKE '%$user_search%'";
        }
        $user_sql .= " ORDER BY id DESC";
        $users = $conn->query($user_sql);

        if ($users->num_rows > 0):
          while ($u = $users->fetch_assoc()):
        ?>
          <tr>
             <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['phone']); ?></td>
            <td>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?php echo $u['id']; ?>">Delete</button>
            </td>
          </tr>
          <!-- User Delete Modal -->
<div class="modal fade" id="deleteUserModal<?php echo $u['id']; ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to permanently delete <strong><?php echo htmlspecialchars($u['name']); ?></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="delete_user.php?id=<?php echo $u['id']; ?>" class="btn btn-danger">Yes, Delete</a>
      </div>
    </div>
  </div>
</div>

        <?php endwhile; else: ?>
          <tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- ✅ Car/Item Management -->
  <section id="cars">
    <h4 class="mb-3 scroll-offset" id="cars">🚗 Manage Cars</h4>
    <form class="mb-3" method="GET">
      <div class="input-group">
        <input type="text" name="car_search" class="form-control" placeholder="Search car by title..." value="<?php echo htmlspecialchars($car_search); ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
             <th>Title</th>
            <th>Owner ID</th>
            <th>Status</th>
            <th>Posted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $car_sql = "SELECT * FROM cars";
        if (!empty($car_search)) {
          $car_sql .= " WHERE title LIKE '%$car_search%'";
        }
        $car_sql .= " ORDER BY id DESC";
        $cars = $conn->query($car_sql);

        if ($cars->num_rows > 0):
          while ($c = $cars->fetch_assoc()):
        ?>
          <tr>
             <td><?php echo htmlspecialchars($c['title']); ?></td>
            <td><?php echo $c['user_id']; ?></td>
            <td>
              <span class="badge bg-<?php echo $c['status'] == 'Booked' ? 'secondary' : 'success'; ?>">
                <?php echo $c['status']; ?>
              </span>
            </td>
            <td><?php echo date('Y-m-d', strtotime($c['created_at'])); ?></td>
            <td>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCarModal<?php echo $c['id']; ?>">Delete</button>
            </td>
          </tr>
          <!-- Car Delete Modal -->
<div class="modal fade" id="deleteCarModal<?php echo $c['id']; ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the car <strong><?php echo htmlspecialchars($c['title']); ?></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="delete_car.php?id=<?php echo $c['id']; ?>" class="btn btn-danger">Yes, Delete</a>
      </div>
    </div>
  </div>
</div>

        <?php endwhile; else: ?>
          <tr><td colspan="6" class="text-center text-muted">No cars found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php include('../footer.php'); ?>
</body>
</html>
