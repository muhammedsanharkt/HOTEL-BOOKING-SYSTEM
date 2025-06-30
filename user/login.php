<?php
include('../includes/db.php');
session_start();

$email = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION['user'] = $user["name"];
            $_SESSION['user_id'] = $user["id"];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Login</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2>User Login</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label>Email:</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>">
    </div>
    <div class="mb-3">
      <label>Password:</label>
      <input type="password" name="password" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Login</button>
    <a href="register.php" class="btn btn-info">Create Account</a>
    <a href="/hotel-booking-system/index.php" class="btn btn-primary">Home-page</a>
  </form>
</div>
</body>
</html>
