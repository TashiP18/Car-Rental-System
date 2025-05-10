<?php
session_start();
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
  header('Location: admin/admin_dashboard.php');
  exit();
}
?>
<?php include('header.php'); include('config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Car Rental</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
  <style>
    html {
      scroll-behavior: smooth;
    }
    body {
      font-family: 'Poppins', sans-serif;
    }
    .hero-section {
      background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1950&q=80') no-repeat center center;
      background-size: cover;
      height: 90vh;
      position: relative;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .hero-overlay {
      background: rgba(0, 0, 0, 0.5);
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }
    .hero-content {
      z-index: 2;
      padding: 2rem;
      width: 100%;
    }
    .hero-content h1, .hero-content p {
      color: #fff;
    }
    .search-form input {
      border-radius: 50px;
      padding: 0.75rem 1.5rem;
    }
    .search-form button {
      border-radius: 50px;
      padding: 0.75rem 1.5rem;
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
    .about-section {
      background: linear-gradient(to right, #141e30, #243b55);
      color: white;
      padding: 60px 20px;
      text-align: center;
    }
    .about-section h2 {
      font-weight: bold;
      margin-bottom: 40px;
      color: #fff;
    }
    .about-features {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
    }
    .about-box {
      flex: 1 1 250px;
      max-width: 320px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 30px;
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .about-box:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .about-box i {
      font-size: 3rem;
      color: #ffc107;
      margin-bottom: 20px;
      display: inline-block;
      animation: bounce 2s infinite;
    }
    .about-box:nth-child(2) i { animation-delay: 0.2s; }
    .about-box:nth-child(3) i { animation-delay: 0.4s; }
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
    .about-box h5 {
      font-weight: 600;
      margin-bottom: 10px;
      color: #fff;
    }
    .about-box p {
      font-size: 0.95rem;
      line-height: 1.6;
      color: #ddd;
    }
    .developer-section h2 {
  color:rgb(3, 3, 3);
  margin-top: 20px
}
.developer-section img {
  border: 5px solid #fff;
  transition: transform 0.3s ease;
}
.developer-section img:hover {
  transform: scale(1.05);
}
[id] {
  scroll-margin-top: 100px; /* adjust based on your navbar height */
}
  </style>
</head>


<body class="d-flex flex-column min-vh-100">

<main class="flex-fill">

  <!-- Hero Section -->
  <div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
      <h1 class="display-4 fw-bold">Explore the Roads in Style</h1>
      <p class="lead">Book your perfect ride — from the mountains to the city.</p>
      <form action="browse-cars.php" method="GET" class="d-flex justify-content-center mt-4 search-form">
        <input type="text" name="search" class="form-control w-50 me-2" placeholder="Search by brand, model or title" required>
        <button type="submit" class="btn btn-light text-dark">Search</button>
      </form>
      <a href="#recent-cars" class="btn btn-outline-light btn-lg rounded-pill mt-4">Browse Cars</a>
    </div>
  </div>

  <!-- Recently Added Cars -->
  <div class="container my-5" id="recent-cars">
    <h2 class="text-center mb-4">Recently Added Cars</h2>
    <div class="row">
      <?php
      $cars = $conn->query("SELECT cars.*, car_categories.name AS category_name FROM cars 
                            LEFT JOIN car_categories ON cars.category_id = car_categories.id 
                            ORDER BY created_at DESC LIMIT 6");
      if ($cars->num_rows > 0):
        while ($car = $cars->fetch_assoc()):
      ?>
      <div class="col-md-4 mb-4">
        <div class="card car-card h-100 position-relative shadow-sm">
          <span class="badge bg-<?php echo $car['status'] === 'Booked' ? 'secondary' : 'success'; ?> position-absolute top-0 end-0 m-2">
            <?php echo $car['status'] === 'Booked' ? 'Booked' : '$' . $car['price_per_day'] . '/day'; ?>
          </span>
          <img src="uploads/<?php echo $car['image_url']; ?>" class="card-img-top" style="height:200px;object-fit:cover;">
          <div class="card-body text-center">
            <h5 class="card-title"><?php echo $car['title']; ?></h5>
            <p class="text-muted"><?php echo $car['brand'].' '.$car['model']; ?></p>
            <p class="small text-secondary"><?php echo $car['category_name']; ?></p>
            <div class="d-flex justify-content-center gap-2">
              <button class="btn btn-outline-info btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $car['id']; ?>">View</button>
              <?php if ($car['status'] === 'Available'): ?>
                <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $car['id']; ?>">Book Now</button>
              <?php else: ?>
                <button class="btn btn-outline-secondary btn-sm rounded-pill" disabled>Booked</button>
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

      <!-- Book Modal -->
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
        <div class="col text-center text-muted">No cars posted yet.</div>
      <?php endif; ?>
    </div>
    <div class="text-center mt-4">
      <a href="browse-cars.php" class="btn btn-outline-primary rounded-pill">View All Cars</a>
    </div>
  </div>

  <!-- Stylish About Us Section -->
  <section id="about-section" class="about-section">
    <div class="container">
      <h2>Why Choose Us</h2>
      <div class="about-features">
        <div class="about-box">
          <i class="bi bi-car-front-fill"></i>
          <h5>Variety of Vehicles</h5>
          <p>From compact city rides to luxury SUVs — find the perfect car for any destination or occasion.</p>
        </div>
        <div class="about-box">
          <i class="bi bi-speedometer2"></i>
          <h5>Fast, Seamless Booking</h5>
          <p>Book in seconds. Our platform is designed for speed, simplicity, and reliability — every time.</p>
        </div>
        <div class="about-box">
          <i class="bi bi-shield-lock-fill"></i>
          <h5>Reliable & Secure</h5>
          <p>Your trust matters. Every listing is verified. Every transaction is encrypted. Every ride is protected.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Meet the Developers Section -->
<section class="developer-section text-center">
    <div class="container">
      <h2 class="mb-5 fw-bold">Meet the Developers</h2>
      <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
          <img src="uploads/t.jpg" class="rounded-circle mb-3" alt="Tshering" style="width:150px;height:150px;object-fit:cover;">
          <h5 class="fw-semibold mb-0">Tshering </h5>
          <p class="text-muted small mb-1">Full Stack Developer</p>
          <p class="text-secondary small px-3">HTML, CSS, and JS enthusiast with a knack for UI/UX and deployment automation.</p>
        </div>
        <div class="col-md-4 mb-4">
          <img src="uploads/tt.jpeg" class="rounded-circle mb-3" alt="Tshering Thinley" style="width:150px;height:150px;object-fit:cover;">
          <h5 class="fw-semibold mb-0">Tshering Thinley</h5>
          <p class="text-muted small mb-1">Frontend Engineer</p>
          <p class="text-secondary small px-3">CSS wizard and accessibility advocate specializing in responsive web design.</p>
        </div>
        <div class="col-md-4 mb-4">
          <img src="uploads/cham.jpg" class="rounded-circle mb-3" alt="Tashi Phuntsho" style="width:150px;height:150px;object-fit:cover;">
          <h5 class="fw-semibold mb-0">Tashi Phuntsho</h5>
          <p class="text-muted small mb-1">Backend & DB Admin</p>
          <p class="text-secondary small px-3">PHP and MySQL designer. Loves building clean and securing server-side logic.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include('footer.php'); ?>
</body>
</html>
