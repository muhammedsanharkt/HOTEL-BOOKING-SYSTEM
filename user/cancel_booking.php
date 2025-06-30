<?php
session_start();
include('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Validate booking ID
if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    $user_id = $_SESSION['user_id'];

    // Delete only if this booking belongs to this user
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=Booking+cancelled+successfully");
        exit;
    } else {
        echo "Error cancelling booking.";
    }
} else {
    echo "Invalid request.";
}
