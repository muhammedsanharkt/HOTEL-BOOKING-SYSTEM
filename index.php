<?php
include('includes/db.php'); // adjust path if needed
$rooms = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hotel Room Booking System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="includes/style.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand text-white" href="#">🏨Hotel</a>
      <div class="d-flex gap-2">
        <a href="user/login.php" class="btn btn-outline-light">User Login</a>
        <a href="admin/login.php" class="btn btn-outline-warning">Admin Login</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <h1>Plan Your Stay with Ease</h1>
      <p class="mb-4">Fast. Reliable. Comfortable.</p>
      <div class="d-flex justify-content-center gap-3">
        <a href="user/register.php" class="btn btn-primary-custom">Register & Book</a>
        <a href="user/login.php" class="btn btn-outline-custom">User Login</a>
      </div>
    </div>
  </section>

  <!-- Info Section -->
  <section class="info-section text-center">
    <div class="container">
      <h2 class="mb-5 fw-bold">What We Offer</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="info-box shadow-sm">
            <h5>🛏 Comfortable Rooms</h5>
            <p>Enjoy luxurious and clean accommodations with all modern amenities.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box shadow-sm">
            <h5>📆 Easy Booking</h5>
            <p>Book your room online in just a few clicks — no hassle, no queues.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box shadow-sm">
            <h5>🔒 Secure Process</h5>
            <p>Upload ID proofs safely and access your booking history any time.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Available Rooms Section -->
  <section class="rooms-section">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold">Available Rooms</h2>
      <div class="row">
        <?php while($room = $rooms->fetch_assoc()): ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($room['room_type']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($room['description']) ?></p>
                <p><strong>Price:</strong> ₹<?= htmlspecialchars($room['price']) ?> / night</p>
                <a href="user/login.php" class="btn btn-primary">Book Now</a>

              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    &copy; <?php echo date('Y'); ?> Hotel Booking System. All rights reserved.
  </footer>

</body>
</html>
