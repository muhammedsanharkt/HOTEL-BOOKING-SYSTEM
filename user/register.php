<?php
include('../includes/db.php');
session_start();

$name = $email = $password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = strtolower(trim($_POST["name"]));
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed);

        if ($stmt->execute()) {
            $_SESSION['user'] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Email already exists or error saving data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e0e7ff, #f1f5f9);
      font-family: 'Segoe UI', sans-serif;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .form-title {
      font-weight: 700;
      color: #1e293b;
    }

    .btn-custom {
      background-color: #1e40af;
      color: white;
      padding: 10px 25px;
      border-radius: 6px;
      border: none;
    }

    .btn-custom:hover {
      background-color: #1d4ed8;
    }

    .form-link {
      color: #1e40af;
      text-decoration: none;
    }

    .form-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="col-md-6">
    <div class="card p-4">
      <h2 class="text-center mb-4 form-title">Create Your Account</h2>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" class="form-control" id="password" required>
        </div>

        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-custom">Register</button>
        </div>
      </form>

      <div class="text-center mt-3">
        <small>Already have an account? <a href="login.php" class="form-link">Login here</a></small>
      </div>
    </div>
  </div>
</div>

</body>
</html>
