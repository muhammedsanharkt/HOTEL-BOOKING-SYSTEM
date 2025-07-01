<?php
session_start();
include('../includes/db.php');

// ✅ Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user'] ?? '';

// ✅ Get active page
$page = $_GET['page'] ?? 'rooms';

// ✅ Fetch available rooms
$rooms = $conn->query("SELECT * FROM rooms");

// ✅ Fetch user's bookings
$history_query = $conn->prepare("
  SELECT b.id, r.room_type, r.price, b.check_in, b.checkout_date, b.status
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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background: #f8fafc; }
    .sidebar {
      height: 100vh;
      background: #465071;
      color: #fff;
      padding: 20px;
    }
    .sidebar a {
      color: #fff;
      display: block;
      margin: 10px 0;
      text-decoration: none;
    }
    .sidebar a.active {
      font-weight: bold;
      text-decoration: underline;
    }
    .content {
      padding: 30px;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
      <h4>User Dashboard</h4>
      <p>Hello, <?= htmlspecialchars($username) ?></p>
      <a href="dashboard.php?page=rooms" class="<?= $page == 'rooms' ? 'active' : '' ?>">🛏️ Available Rooms</a>
      <a href="dashboard.php?page=bookings" class="<?= $page == 'bookings' ? 'active' : '' ?>">📜 My Bookings</a>
      <hr>
      <a href="../user/logout.php" class="btn btn-danger btn-sm mb-2">Logout</a>
      <a href="/hotel-booking-system/index.php" class="btn btn-success btn-sm">Home</a>
    </div>

    <!-- Content -->
    <div class="col-md-9 content">
      <?php if ($page == 'rooms'): ?>
        <h4>🛏️ Available Rooms</h4>
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

      <?php elseif ($page == 'bookings'): ?>
        <h4>📜 Your Booking History</h4>
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
                    <td class="d-flex gap-2">
  <?php if ($row['status'] === 'cancelled'): ?>
    <span class="badge bg-secondary">Cancelled</span>
  <?php else: ?>
    <a href="cancel_booking.php?booking_id=<?= $row['id'] ?>" 
       class="btn btn-danger btn-sm"
       onclick="return confirm('Are you sure you want to cancel this booking?');">
      Cancel
    </a>
  <?php endif; ?>

  <a href="generate_invoice.php?booking_id=<?= $row['id'] ?>" 
     class="btn btn-success btn-sm">
    Download Invoice
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
      <?php else: ?>
        <p>Welcome! Please select an option from the left.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
