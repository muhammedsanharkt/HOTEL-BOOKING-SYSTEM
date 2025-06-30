<?php
session_start();
include('../includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user'] ?? '';

// Fetch available rooms
$rooms = $conn->query("SELECT * FROM rooms");

// Fetch only this user's bookings
$history_query = $conn->prepare("
  SELECT b.id, r.room_type, r.price, b.check_in, b.checkout_date
  FROM bookings b
  JOIN rooms r ON b.room_id = r.id
  WHERE b.user_id = ?
  ORDER BY b.check_in DESC
");
$history_query->bind_param("i", $user_id);
$history_query->execute();
$history_result = $history_query->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
  <p class="text-muted">This is your dashboard. You can book rooms, view booking history, and more.</p>
  <a href="../logout.php" class="btn btn-danger mb-4">Logout</a>

  <!-- Available Rooms -->
  <h4 class="mt-4">🛏️ Available Rooms</h4>
  <div class="row">
    <?php if ($rooms->num_rows > 0): ?>
      <?php while($room = $rooms->fetch_assoc()): ?>
        <div class="col-md-4 mb-3">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($room['room_type']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($room['description']) ?></p>
              <p><strong>Price:</strong> ₹<?= htmlspecialchars($room['price']) ?> / night</p>
              <a href="book_room.php?room_id=<?= $room['id'] ?>" class="btn btn-primary">Book Now</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">No rooms available at the moment.</p>
    <?php endif; ?>
  </div>

  <!-- Booking History -->
  <h4 class="mt-5">📜 Your Booking History</h4>
  <?php if ($history_result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Booking ID</th>
            <th>Room Type</th>
            <th>Price (₹)</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $history_result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['room_type']) ?></td>
              <td><?= htmlspecialchars($row['price']) ?></td>
              <td><?= htmlspecialchars($row['check_in']) ?></td>
              <td><?= htmlspecialchars($row['checkout_date']) ?></td>
              <td>
                <a href="cancel_booking.php?booking_id=<?= $row['id'] ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure you want to cancel this booking?');">
                  Cancel
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="text-muted">You have not made any bookings yet.</p>
  <?php endif; ?>

  <a href="/hotel-booking-system/index.php" class="btn btn-primary mt-4">Home-page</a>
</div>
</body>
</html>
