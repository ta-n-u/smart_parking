<?php
// auto_cancel_bookings.php

// Disable time limit for script execution if it might take long, though unlikely for this
set_time_limit(0);

// Include your database configuration
require_once 'config.php';

// Define the grace period in minutes (e.g., 30 minutes)
$grace_period_minutes = 15;

// Log to a file for debugging purposes (optional but recommended)
function log_cancellation($message) {
    file_put_contents('auto_cancel_log.txt', date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

log_cancellation("Starting auto-cancellation check.");

// Start a transaction for atomicity
mysqli_autocommit($conn, FALSE);
$success = true;

try {
    // 1. Select bookings that are 'pending' or 'booked', whose start_time + grace_period is in the past, and are 'unpaid'
    $sql = "SELECT b.id, b.parking_location_id FROM bookings b
            WHERE b.booking_status IN ('pending', 'booked')
            AND b.payment_status = 'unpaid' -- Only cancel unpaid bookings
            AND DATE_ADD(b.start_time, INTERVAL ? MINUTE) < NOW() FOR UPDATE"; // Lock rows

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $grace_period_minutes);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $bookings_to_cancel = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings_to_cancel[] = $row;
        }
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);

        log_cancellation("Found " . count($bookings_to_cancel) . " bookings to cancel.");

        foreach ($bookings_to_cancel as $booking) {
            $booking_id = $booking['id'];
            $parking_location_id = $booking['parking_location_id'];

            // 2. Update booking status to 'cancelled' and payment_status to 'voided'
            $sql_update_booking = "UPDATE bookings SET booking_status = 'cancelled', payment_status = 'voided', updated_at = NOW() WHERE id = ?";
            if ($stmt_update_booking = mysqli_prepare($conn, $sql_update_booking)) {
                mysqli_stmt_bind_param($stmt_update_booking, "i", $booking_id);
                if (!mysqli_stmt_execute($stmt_update_booking)) {
                    throw new Exception("Failed to update booking status for ID: " . $booking_id . " - " . mysqli_error($conn));
                }
                mysqli_stmt_close($stmt_update_booking);
                log_cancellation("Cancelled booking ID: " . $booking_id);
            } else {
                throw new Exception("Failed to prepare update booking status statement: " . mysqli_error($conn));
            }

            // 3. Increment available_slots for the parking location
            $sql_update_slots = "UPDATE parking_locations SET available_slots = available_slots + 1 WHERE id = ?";
            if ($stmt_update_slots = mysqli_prepare($conn, $sql_update_slots)) {
                mysqli_stmt_bind_param($stmt_update_slots, "i", $parking_location_id);
                if (!mysqli_stmt_execute($stmt_update_slots)) {
                    throw new Exception("Failed to increment slots for parking ID: " . $parking_location_id . " - " . mysqli_error($conn));
                }
                mysqli_stmt_close($stmt_update_slots);
                log_cancellation("Incremented slot for parking ID: " . $parking_location_id);
            } else {
                throw new Exception("Failed to prepare update slots statement: " . mysqli_error($conn));
            }
        }
    } else {
        throw new Exception("Failed to prepare select bookings statement: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    log_cancellation("Auto-cancellation check completed successfully.");

} catch (Exception $e) {
    mysqli_rollback($conn);
    $success = false;
    log_cancellation("Auto-cancellation failed: " . $e->getMessage());
} finally {
    mysqli_autocommit($conn, TRUE);
    mysqli_close($conn);
}

// Output a simple response for cron job logging
if ($success) {
    echo "Auto-cancellation script ran successfully.\n";
} else {
    echo "Auto-cancellation script encountered errors. Check logs.\n";
}
?>