<?php include('header.php'); include('config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Browse Cars - Car Rental</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
    }
    footer {
      background: linear-gradient(to right, #141e30, #243b55);
      color: white;
    }
    .car-card {
      transition: all 0.3s ease;
      border-radius: 0.5rem;
      overflow: hidden;
    }
    .car-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .car-image {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 0.5rem;
      border-top-right-radius: 0.5rem;
    }
  </style>
</head>
<body class="min-vh-100">

<main class="container my-5">
  <h2 class="text-center mb-4">Browse Cars</h2>

  <!-- Search and Filter -->
  <form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Search by title, brand, or model" value="<?php echo $_GET['search'] ?? ''; ?>">
    </div>
    <div class="col-md-4">
      <select name="category" class="form-select">
        <option value="">Filter by Car Type</option>
        <?php
        $cat_result = $conn->query("SELECT * FROM car_categories");
        while($cat = $cat_result->fetch_assoc()):
          $selected = ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '';
          echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
        endwhile;
        ?>
      </select>
    </div>
    <div class="col-md-4">
      <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
  </form>

  <!-- Toast Messages -->
  <?php
  if (isset($_GET['booked'])) {
    if ($_GET['booked'] == '1') echo "<script>showToast('Car booked successfully!');</script>";
    else echo "<script>showToast('This car has already been booked.', 'danger');</script>";
  }
  ?>

  <!-- Car Cards -->
  <div class="row">
    <?php
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $sql = "SELECT cars.*, car_categories.name AS category_name 
            FROM cars 
            LEFT JOIN car_categories ON cars.category_id = car_categories.id 
            WHERE 1=1";

    if (!empty($category)) {
      $sql .= " AND cars.category_id = '$category'";
    }

    if (!empty($search)) {
      $sql .= " AND (cars.title LIKE '%$search%' OR cars.brand LIKE '%$search%' OR cars.model LIKE '%$search%')";
    }

    $sql .= " ORDER BY cars.created_at DESC";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0):
      while ($car = $result->fetch_assoc()):
    ?>
    <div class="col-md-4 mb-4">
      <div class="card car-card h-100 position-relative shadow-sm">
        <span class="badge bg-<?php echo $car['status'] === 'Booked' ? 'secondary' : 'success'; ?> position-absolute top-0 end-0 m-2">
          <?php echo $car['status'] === 'Booked' ? 'Booked' : '$' . $car['price_per_day'] . '/day'; ?>
        </span>
        <img src="uploads/<?php echo $car['image_url']; ?>" class="car-image w-100" alt="Car Image">
        <div class="card-body text-center">
          <h5 class="card-title"><?php echo $car['title']; ?></h5>
          <p class="text-muted mb-2"><?php echo $car['brand'].' '.$car['model']; ?></p>
          <p class="small text-secondary">Type: <?php echo $car['category_name']; ?></p>
          <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-outline-info btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $car['id']; ?>">View</button>

            <?php if ($car['status'] === 'Available'): ?>
              <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $car['id']; ?>">Book Now</button>
            <?php else: ?>
              <button class="btn btn-outline-secondary btn-sm rounded-pill" disabled>Already Booked</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal<?php echo $car['id']; ?>" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?php echo $car['title']; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body row">
            <div class="col-md-6">
              <img src="uploads/<?php echo $car['image_url']; ?>" class="img-fluid rounded">
            </div>
            <div class="col-md-6">
              <p><strong>Brand:</strong> <?php echo $car['brand']; ?></p>
              <p><strong>Model:</strong> <?php echo $car['model']; ?></p>
              <p><strong>Year:</strong> <?php echo $car['year']; ?></p>
              <p><strong>Category:</strong> <?php echo $car['category_name']; ?></p>
              <p><strong>Price:</strong> $<?php echo $car['price_per_day']; ?>/day</p>
              <hr>
              <p><strong>Description:</strong></p>
              <p><?php echo nl2br($car['description']); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookModal<?php echo $car['id']; ?>" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="book_car.php" method="post">
            <div class="modal-header">
              <h5 class="modal-title">Book This Car</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
              <input type="email" name="email" class="form-control mb-2" placeholder="Your Email" required>
              <input type="text" name="phone" class="form-control mb-2" placeholder="Phone Number" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Confirm Booking</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endwhile; else: ?>
      <div class="col text-center text-muted">No cars found for your search.</div>
    <?php endif; ?>
  </div>
</main>

<?php include('footer.php'); ?>
</body>
</html>
