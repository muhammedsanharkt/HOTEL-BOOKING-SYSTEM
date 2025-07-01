<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Get page
$page = $_GET['page'] ?? 'rooms';

// Add Room
$roomError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_room'])) {
    $type = trim($_POST['room_type']);
    $price = trim($_POST['price']);
    $desc = trim($_POST['description']);

    if (!empty($type) && is_numeric($price)) {
        $stmt = $conn->prepare("INSERT INTO rooms (room_type, price, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $type, $price, $desc);
        $stmt->execute();
        header("Location: dashboard.php?page=rooms&added=1");
        exit;
    } else {
        $roomError = "Room type and price are required.";
    }
}

// Delete Room
if (isset($_GET['delete_room'])) {
    $room_id = intval($_GET['delete_room']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    header("Location: dashboard.php?page=rooms&deleted=1");
    exit;
}

// ✅ Cancel Booking → UPDATE status instead of DELETE
if (isset($_GET['cancel_booking'])) {
    $booking_id = intval($_GET['cancel_booking']);
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    header("Location: dashboard.php?page=bookings&cancelled=1");
    exit;
}

// Data
$rooms = $conn->query("SELECT * FROM rooms");
$bookings = $conn->query("
  SELECT b.*, u.name AS user_name, u.email AS user_email, r.room_type 
  FROM bookings b 
  JOIN users u ON b.user_id = u.id 
  JOIN rooms r ON b.room_id = r.id 
  ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" />
  <style>
    body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }
    .sidebar {
      height: 100vh;
      background: #1e293b;
      color: #fff;
      padding: 30px 20px;
    }
    .sidebar h4 { color: #f8fafc; margin-bottom: 30px; }
    .nav-link {
      color: #cbd5e1;
      display: flex;
      align-items: center;
      padding: 10px 15px;
      border-radius: 5px;
      text-decoration: none;
      transition: background 0.2s ease;
    }
    .nav-link:hover {
      background: #334155;
      color: #f8fafc;
    }
    .nav-link.active {
      background: #475569;
      color: #fff;
      font-weight: 500;
    }
    .nav-link i {
      margin-right: 10px;
    }
    .content {
      padding: 40px;
    }
    .header {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
      <h4>⚙️ Admin Dashboard</h4>
      <a href="dashboard.php?page=rooms" class="nav-link <?= $page == 'rooms' ? 'active' : '' ?>"><i class="bi bi-building"></i> Rooms</a>
      <a href="dashboard.php?page=add" class="nav-link <?= $page == 'add' ? 'active' : '' ?>"><i class="bi bi-plus-square"></i> Add Room</a>
      <a href="dashboard.php?page=bookings" class="nav-link <?= $page == 'bookings' ? 'active' : '' ?>"><i class="bi bi-clipboard-check"></i> Bookings</a>
      <hr class="text-light">
      <a href="logout.php" class="btn btn-outline-light btn-sm mt-3">Logout</a>
      <a href="/hotel-booking-system/index.php" class="btn btn-outline-success btn-sm mt-3">Home</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 content">
      <div class="header">
        <h3>Welcome, <?= htmlspecialchars($_SESSION['admin']) ?>!</h3>
      </div>

      <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">✅ Room added successfully!</div>
      <?php endif; ?>
      <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">✅ Room deleted successfully!</div>
      <?php endif; ?>
      <?php if (isset($_GET['cancelled'])): ?>
        <div class="alert alert-warning">⚠️ Booking cancelled successfully!</div>
      <?php endif; ?>

      <?php if ($page == 'add'): ?>
        <h4>➕ Add New Room</h4>
        <?php if ($roomError): ?><div class="alert alert-danger"><?= htmlspecialchars($roomError) ?></div><?php endif; ?>
        <form method="POST" class="row g-3 mb-4">
          <div class="col-md-4">
            <input name="room_type" placeholder="Room Type" class="form-control" required>
          </div>
          <div class="col-md-3">
            <input type="number" step="0.01" name="price" placeholder="Price" class="form-control" required>
          </div>
          <div class="col-md-4">
            <input name="description" placeholder="Description" class="form-control">
          </div>
          <div class="col-md-1">
            <button name="add_room" class="btn btn-success w-100">Add</button>
          </div>
        </form>

      <?php elseif ($page == 'rooms'): ?>
        <h4>🏨 All Rooms</h4>
        <table class="table table-hover table-striped">
          <thead class="table-dark">
            <tr><th>#</th><th>Type</th><th>Price</th><th>Description</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php $i = 1; while ($r = $rooms->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($r['room_type']) ?></td>
                <td>₹<?= $r['price'] ?></td>
                <td><?= htmlspecialchars($r['description']) ?></td>
                <td>
                  <a href="dashboard.php?delete_room=<?= $r['id'] ?>&page=rooms" class="btn btn-sm btn-danger" onclick="return confirm('Delete this room?')">🗑️ Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      <?php elseif ($page == 'bookings'): ?>
        <h4>📋 All Bookings</h4>
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>#</th><th>User</th><th>Email</th><th>Room</th><th>Check-In</th><th>Check-Out</th><th>ID Proof</th><th>Booked At</th><th>Actions</th>
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
                <td><a href="../uploads/<?= htmlspecialchars($b['id_proof']) ?>" target="_blank">View</a></td>
                <td><?= $b['created_at'] ?></td>
                <td>
                  <?php if ($b['status'] === 'cancelled'): ?>
                    <span class="badge bg-secondary">Cancelled</span>
                  <?php else: ?>
                    <a href="dashboard.php?cancel_booking=<?= $b['id'] ?>&page=bookings"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Cancel this booking?')">
                      ❌ Cancel
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      <?php else: ?>
        <p>Please select an option from the sidebar.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
