<?php
session_start();
include('../includes/db.php');

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$success = $error = "";

// Get selected room ID from URL (if any)
$selected_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

// Handle booking form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST["room_id"];
    $check_in = $_POST["check_in"];
    $checkout_date = $_POST["check_out"];
    $user_id = $_SESSION["user_id"];

    // File upload
    $fileName = $_FILES["id_proof"]["name"];
    $tmpName = $_FILES["id_proof"]["tmp_name"];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (!in_array($fileType, $allowed)) {
        $error = "Only JPG, JPEG, PNG, or PDF files are allowed.";
    } else {
        $newName = uniqid() . "." . $fileType;
        $targetPath = "../uploads/" . $newName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, checkout_date, id_proof) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $user_id, $room_id, $check_in, $checkout_date, $newName);

            if ($stmt->execute()) {
                $success = "✅ Room booked successfully!";
            } else {
                $error = "❌ Failed to save booking. Please try again.";
            }
        } else {
            $error = "❌ Failed to upload ID proof.";
        }
    }
}

// Fetch available rooms
$rooms = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Book a Room</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>🛏️ Book a Room</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="room_id" class="form-label">Room Type</label>
      <select name="room_id" id="room_id" class="form-control" required>
        <option value="">-- Select Room --</option>
        <?php while($room = $rooms->fetch_assoc()): ?>
          <option value="<?= $room['id'] ?>" <?= ($room['id'] == $selected_room_id) ? 'selected' : '' ?>>
            <?= htmlspecialchars($room['room_type']) ?> - ₹<?= $room['price'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="check_in" class="form-label">Check-In Date</label>
      <input type="date" name="check_in" id="check_in" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="check_out" class="form-label">Check-Out Date</label>
      <input type="date" name="check_out" id="check_out" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="id_proof" class="form-label">Upload ID Proof (PDF/Image)</label>
      <input type="file" name="id_proof" id="id_proof" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Book Now</button>
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
    <a href="/hotel-booking-system/index.php" class="btn btn-success">Home-page</a>
  </form>
</div>
</body>
</html>
