<?php
session_start();
include('../includes/db.php');

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Fetch all bookings
$stmt = $conn->prepare("
    SELECT b.*, 
           u.name AS user_name, 
           u.email AS user_email, 
           r.room_type, 
           r.price_per_night 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    JOIN rooms r ON b.room_id = r.id 
    ORDER BY b.created_at DESC
");
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>All Bookings - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>All Bookings</h2>

  <?php if ($bookings->num_rows > 0): ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Email</th>
          <th>Room</th>
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
          <td><?= htmlspecialchars($row['user_name']) ?></td>
          <td><?= htmlspecialchars($row['user_email']) ?></td>
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
