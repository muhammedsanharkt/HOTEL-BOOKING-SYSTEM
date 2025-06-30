<?php
session_start();
include('../includes/db.php');
if (!isset($_SESSION['user_id'])) header("Location: ../login.php");

$user_id = $_SESSION['user_id'];

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>My Bookings</h2>

  <div class="row">
    <?php while($b = $bookings->fetch_assoc()): ?>
    <div class="col-md-4 mb-3">
      <div class="card border-success">
        <div class="card-body">
          <h5 class="card-title"><?= $b['room_type'] ?></h5>
          <p><strong>Check-in:</strong> <?= $b['check_in'] ?></p>
          <p><strong>Check-out:</strong> <?= $b['check_out'] ?></p>
          <p><strong>Price:</strong> ₹<?= $b['price_per_night'] ?></p>
          <a href="../uploads/<?= $b['id_proof'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">View ID</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
