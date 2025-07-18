<?php
session_start();
header('Content-Type: application/json'); // Return JSON response

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to cancel a booking.'
    ]);
    exit();
}

$user_id = $_SESSION['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['booking_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit();
}

$booking_id = $_POST['booking_id'];

if (!is_numeric($booking_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID.'
    ]);
    exit();
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Check if booking belongs to user and is cancellable
    $stmt = $conn->prepare("SELECT booking_status FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        throw new Exception("Booking not found or you do not have permission to cancel this booking.");
    }

    $stmt->bind_result($booking_status);
    $stmt->fetch();
    $stmt->close();

    // Only allow cancellation if status is pending or booked
    if (!in_array(strtolower($booking_status), ['pending', 'booked'])) {
        throw new Exception("This booking cannot be cancelled.");
    }

    // Update booking_status to cancelled
    $update_stmt = $conn->prepare("UPDATE bookings SET booking_status = 'cancelled' WHERE id = ?");
    $update_stmt->bind_param("i", $booking_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows === 0) {
        throw new Exception("Failed to cancel the booking or it is already cancelled.");
    }

    $update_stmt->close();

    mysqli_commit($conn);

    echo json_encode([
        'success' => true,
        'message' => 'Booking cancelled successfully.'
    ]);
} catch (Exception $e) {
    mysqli_rollback($conn);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
