<?php
include('../includes/db.php');

session_start();


$email = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = strip_tags($password);

    if (empty($email) || empty($password)) {
        $error = "Both email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format. Please enter a valid email like: example@domain.com.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (strlen($password) > 255) {
        $error = "Password is too long.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user["password"])) {
                    session_regenerate_id(true);
                    $_SESSION['user'] = $user["name"];
                    $_SESSION['user_id'] = $user["id"];
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Wrong password. Please try again.";
                }
            } else {
                $error = "No account found for this email.";
            }
            $stmt->close();
        } else {
            $error = "Server error. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Modern User Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../includes/userlogin.css">
  <style>
    body {
      background: #f8fafc;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-container {
      max-width: 400px;
      margin: 80px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .btn-custom {
      background-color: #1e40af;
      color: #ffffff;
    }
    .btn-custom:hover {
      background-color: #1d4ed8;
    }
    .alert-modern {
      position: relative;
      background: #ffe5e5;
      border: 1px solid #fca5a5;
      border-left: 5px solid #ef4444;
      color: #991b1b;
      border-radius: 6px;
      padding: 12px 16px;
      margin-bottom: 20px;
      font-size: 15px;
      display: flex;
      align-items: center;
    }
    .alert-modern svg {
      flex-shrink: 0;
      margin-right: 10px;
    }
    .alert-modern .close-btn {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      border: none;
      background: transparent;
      font-size: 20px;
      cursor: pointer;
      line-height: 1;
      color: #991b1b;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h2 class="mb-4 text-center">User Login</h2>

  <?php if ($error): ?>
    <div class="alert-modern">
      <!-- Example icon (Heroicons exclamation) -->
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" height="24" width="24" viewBox="0 0 24 24" stroke="#991b1b">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0z"/>
      </svg>
      <span><?= htmlspecialchars($error) ?></span>
      <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label for="email" class="form-label">Email address:</label>
      <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email) ?>" >
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password:</label>
      <input type="password" name="password" id="password" class="form-control"  >
    </div>

    <div class="d-grid mb-3">
      <button type="submit" class="btn btn-custom">Login</button>
    </div>

    <div class="d-grid mb-2">
      <a href="register.php" class="btn btn-info">Create Account</a>
    </div>

    <div class="d-grid">
      <a href="/hotel-booking-system/index.php" class="btn btn-primary">Home Page</a>
    </div>
  </form>
</div>

</body>
</html>
