<?php
session_start();
require_once 'config.php'; // Include your database connection

header('Content-Type: application/json'); // Tell the browser to expect JSON

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// 1. Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $response['message'] = 'User not logged in. Please log in to book a spot.';
    echo json_encode($response);
    exit();
}

// 2. Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit();
}

// Get data from POST request
$user_id = $_SESSION['id'];
$parking_location_id = $_POST['parking_location_id'] ?? null;
$start_time_str = $_POST['start_time'] ?? null;
$end_time_str = $_POST['end_time'] ?? null;

$errors = [];

// 3. Validate inputs
if (empty($parking_location_id) || !is_numeric($parking_location_id)) {
    $errors['parkingLocationId'] = 'Parking location is missing or invalid.';
}
if (empty($start_time_str)) {
    $errors['startTime'] = 'Start time is required.';
}
if (empty($end_time_str)) {
    $errors['endTime'] = 'End time is required.';
}

// Convert string dates to DateTime objects for comparison and database insertion
$start_time = new DateTime($start_time_str);
$end_time = new DateTime($end_time_str);
$current_time = new DateTime();

if ($start_time <= $current_time) {
    $errors['startTime'] = 'Start time must be in the future.';
}
if ($end_time <= $start_time) {
    $errors['endTime'] = 'End time must be after start time.';
}

// Calculate duration in hours
$interval = $start_time->diff($end_time);
$duration_minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
$duration_hours = $duration_minutes / 60;

// Minimum 15 minutes (0.25 hours), Maximum 24 hours
if ($duration_hours < 0.25 || $duration_hours > 24) {
    $errors['duration'] = 'Booking duration must be between 15 minutes and 24 hours.';
}

// If validation fails, send back errors
if (!empty($errors)) {
    $response['message'] = 'Validation failed.';
    $response['errors'] = $errors;
    echo json_encode($response);
    exit();
}

// 4. Check parking spot availability and get price
$sql_check_spot = "SELECT available_slots, price_per_hour FROM parking_locations WHERE id = ? AND status = 'active' FOR UPDATE"; // FOR UPDATE locks the row
if ($stmt = mysqli_prepare($conn, $sql_check_spot)) {
    mysqli_stmt_bind_param($stmt, "i", $parking_location_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $available_slots, $price_per_hour);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (empty($available_slots) || $available_slots <= 0) {
        $response['message'] = 'No available slots at this parking location.';
        echo json_encode($response);
        exit();
    }
} else {
    $response['message'] = 'Database error checking spot availability.';
    error_log("SQL Error: " . mysqli_error($conn));
    echo json_encode($response);
    exit();
}

$amount = $duration_hours * $price_per_hour;

// 5. Start a transaction
mysqli_autocommit($conn, FALSE); // Disable autocommit

try {
    // 6. Insert booking into bookings table
    // Corrected SQL: 'duration_hours' and 'amount' get '?' placeholders.
    // 'booking_status' and 'payment_status' also get '?' placeholders.
    // Added 'created_at' and 'updated_at' if they exist in your table for auto-timestamps.
    $sql_insert_booking = "INSERT INTO bookings (user_id, parking_location_id, start_time, end_time, duration_hours, amount, booking_status, payment_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    if ($stmt_booking = mysqli_prepare($conn, $sql_insert_booking)) {
        $start_time_db = $start_time->format('Y-m-d H:i:s');
        $end_time_db = $end_time->format('Y-m-d H:i:s');

        // Define booking and payment status variables (if not already defined outside this block)
        $booking_status = 'pending'; // Or 'pending', depending on your default
        $payment_status = 'unpaid';

        // Corrected bind_param: "iissddss" for all 8 parameters
        // i: user_id
        // i: parking_location_id
        // s: start_time_db
        // s: end_time_db
        // d: duration_hours (decimal/double)
        // d: amount (decimal/double)
        // s: booking_status
        // s: payment_status
        mysqli_stmt_bind_param($stmt_booking, "iissddss",
            $user_id,
            $parking_location_id,
            $start_time_db,
            $end_time_db,
            $duration_hours, // This is the variable for duration_hours
            $amount,         // This is the variable for amount
            $booking_status, // This is the variable for booking_status
            $payment_status  // This is the variable for payment_status
        );
        
        if (!mysqli_stmt_execute($stmt_booking)) {
            throw new Exception("Error inserting booking: " . mysqli_stmt_error($stmt_booking));
        }
        mysqli_stmt_close($stmt_booking);
    } else {
        throw new Exception("Error preparing booking statement: " . mysqli_error($conn));
    }

    // 7. Decrement available_slots in parking_locations table
    $sql_update_slots = "UPDATE parking_locations SET available_slots = available_slots - 1 WHERE id = ?";
    if ($stmt_update = mysqli_prepare($conn, $sql_update_slots)) {
        mysqli_stmt_bind_param($stmt_update, "i", $parking_location_id);
        if (!mysqli_stmt_execute($stmt_update)) {
            throw new Exception("Error updating available slots: " . mysqli_stmt_error($stmt_update));
        }
        mysqli_stmt_close($stmt_update);
    } else {
        throw new Exception("Error preparing update slots statement: " . mysqli_error($conn));
    }

    // 8. Commit the transaction
    mysqli_commit($conn);
    $response = ['success' => true, 'message' => 'Booking confirmed successfully!', 'amount' => $amount];

} catch (Exception $e) {
    // 9. Rollback on error
    mysqli_rollback($conn);
    $response['message'] = 'Booking failed: ' . $e->getMessage();
    error_log("Booking Transaction Error: " . $e->getMessage());
} finally {
    mysqli_autocommit($conn, TRUE); // Re-enable autocommit
    mysqli_close($conn); // Close connection
}

echo json_encode($response);
exit();
?>