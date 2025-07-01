<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);
$error = "";
$success = "";

// Fetch room details
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    echo "Room not found.";
    exit;
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = trim($_POST['room_type']);
    $price = trim($_POST['price']);
    $desc = trim($_POST['description']);

    if (!empty($type) && is_numeric($price)) {
        $update = $conn->prepare("UPDATE rooms SET room_type = ?, price = ?, description = ? WHERE id = ?");
        $update->bind_param("sdsi", $type, $price, $desc, $id);
        if ($update->execute()) {
            $success = "Room updated successfully.";
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Update failed.";
        }
    } else {
        $error = "Room type and valid price are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Room</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Edit Room</h2>

  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label>Room Type</label>
      <input type="text" name="room_type" class="form-control" value="<?= htmlspecialchars($room['room_type']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Price</label>
      <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($room['price']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Description</label>
      <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($room['description']) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update Room</button>
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
  </form>
</body>
</html>
