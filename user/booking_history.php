<?php
session_start();
include('../includes/db.php');

// Check user session
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's bookings
$stmt = $conn->prepare("
    SELECT b.*, r.room_type, r.price_per_night 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Bookings</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>My Booking History</h2>

  <?php if ($bookings->num_rows > 0): ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Room Type</th>
          <th>Price/Night</th>
          <th>Check-In</th>
          <th>Check-Out</th>
          <th>ID Proof</th>
          <th>Booked At</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while($row = $bookings->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['room_type']) ?></td>
          <td>₹<?= $row['price_per_night'] ?></td>
          <td><?= $row['check_in'] ?></td>
          <td><?= $row['check_out'] ?></td>
          <td>
            <a href="../uploads/<?= $row['id_proof'] ?>" target="_blank">View</a>
          </td>
          <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="text-muted">No bookings found.</p>
  <?php endif; ?>

  <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>
</body>
</html>
