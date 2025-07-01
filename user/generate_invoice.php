<?php
session_start();
include('../includes/db.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($booking_id <= 0) {
    die("Invalid booking ID.");
}

// Fetch booking details
$stmt = $conn->prepare("
  SELECT b.id, u.name, u.email, r.room_type, r.price, b.check_in, b.checkout_date
  FROM bookings b
  JOIN users u ON b.user_id = u.id
  JOIN rooms r ON b.room_id = r.id
  WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

// Generate simple invoice as PDF header
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=invoice_booking_{$booking_id}.txt");

echo "===== HOTEL BOOKING INVOICE =====\n";
echo "Booking ID: " . $booking['id'] . "\n";
echo "Name: " . $booking['name'] . "\n";
echo "Email: " . $booking['email'] . "\n";
echo "Room Type: " . $booking['room_type'] . "\n";
echo "Price per Night: ₹" . $booking['price'] . "\n";
echo "Check-In Date: " . $booking['check_in'] . "\n";
echo "Check-Out Date: " . $booking['checkout_date'] . "\n";
echo "----------------------------------\n";
echo "Thank you for booking with us!\n";
?>
