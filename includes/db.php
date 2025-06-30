<?php
// db.php - Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "hotel booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
