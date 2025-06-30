<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Add room form handler
$roomError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_room'])) {
    $type = trim($_POST['room_type']);
    $price = trim($_POST['price']);
    $desc = trim($_POST['description']);

    if (!empty($type) && is_numeric($price)) {
        $stmt = $conn->prepare("INSERT INTO rooms (room_type, price, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $type, $price, $desc);
        $stmt->execute();
    } else {
        $roomError = "Room type and price are required.";
    }
}

// Get all rooms
$rooms = $conn->query("SELECT * FROM rooms");

// Get all bookings with user and room info
$bookings = $conn->query("
  SELECT b.*, u.name AS user_name, u.email AS user_email, r.room_type 
  FROM bookings b 
  JOIN users u ON b.user_id = u.id 
  JOIN rooms r ON b.room_id = r.id 
  ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['admin']) ?>!</h2>
  <p class="text-muted">This is your admin control panel.</p>

  <a href="logout.php" class="btn btn-danger mb-4">Logout</a>
<a href="/hotel-booking-system/index.php" class="btn btn-primary mb-4">Home</a>


  <!-- Add Room Form -->
  <h4 class="mt-4">➕ Add New Room</h4>
  <?php if ($roomError): ?><div class="alert alert-danger"><?= $roomError ?></div><?php endif; ?>
  <form method="POST" class="row g-2 mb-4">
    <div class="col-md-3">
      <input type="text" name="room_type" placeholder="Room Type" class="form-control" required>
    </div>
    <div class="col-md-2">
      <input type="number" step="0.01" name="price" placeholder="Price" class="form-control" required>
    </div>
    <div class="col-md-4">
      <input type="text" name="description" placeholder="Description" class="form-control">
    </div>
    <div class="col-md-2">
      <button type="submit" name="add_room" class="btn btn-success w-100">Add Room</button>
    </div>
  </form>

  <!-- List of Rooms -->
  <h4>🏨 All Rooms</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Room Type</th>
        <th>Price</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; while ($room = $rooms->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($room['room_type']) ?></td>
          <td>₹<?= $room['price'] ?></td>
          <td><?= htmlspecialchars($room['description']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Booking Details -->
  <h4 class="mt-5">📋 All Bookings</h4>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Email</th>
        <th>Room</th>
        <th>Check-In</th>
        <th>Check-Out</th>
        <th>ID Proof</th>
        <th>Booked At</th>
      </tr>
    </thead>
    <tbody>
      <?php $j = 1; while ($b = $bookings->fetch_assoc()): ?>
        <tr>
          <td><?= $j++ ?></td>
          <td><?= htmlspecialchars($b['user_name']) ?></td>
          <td><?= htmlspecialchars($b['user_email']) ?></td>
          <td><?= htmlspecialchars($b['room_type']) ?></td>
          <td><?= $b['check_in'] ?></td>
          <td><?= $b['checkout_date'] ?></td>
          <td><a href="../uploads/<?= $b['id_proof'] ?>" target="_blank">View</a></td>
          <td><?= $b['created_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>



</div>
</body>
</html>
