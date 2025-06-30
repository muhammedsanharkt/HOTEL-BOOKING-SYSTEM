<?php
include('../includes/db.php');
$rooms = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Available Rooms</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Available Rooms</h2>
  <div class="row">
    <?php while($room = $rooms->fetch_assoc()): ?>
      <div class="col-md-4 mb-3">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($room['room_type']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($room['description']) ?></p>
            <p><strong>Price:</strong> ₹<?= $room['price_per_night'] ?> / night</p>
            <a href="book_room.php" class="btn btn-primary">Book Now</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
