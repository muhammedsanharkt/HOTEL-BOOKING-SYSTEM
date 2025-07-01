<?php
// db.php - Secure Database Connection using .env

// Load .env vars
$dotenv = parse_ini_file(__DIR__ . '/../.env');

$servername = $dotenv['DB_HOST'];
$username   = $dotenv['DB_USER'];
$password   = $dotenv['DB_PASS'];
$database   = $dotenv['DB_NAME'];

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
