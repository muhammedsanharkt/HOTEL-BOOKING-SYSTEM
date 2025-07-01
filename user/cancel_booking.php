<?php
session_start();
include('../includes/db.php');

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    $user_id = $_SESSION['user_id'];

    // ✅ Set status to 'cancelled'
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
}

// ✅ Redirect back to dashboard bookings page
header("Location: dashboard.php?page=bookings");
exit;
?>
