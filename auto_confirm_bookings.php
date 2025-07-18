<?php
require_once 'config.php';  // Make sure path is correct for your setup

$sql = "UPDATE bookings 
        SET booking_status = 'confirmed' 
        WHERE booking_status = 'pending' 
          AND created_at <= (NOW() - INTERVAL 10 MINUTE)";

if ($conn->query($sql) === TRUE) {
    echo "Pending bookings older than 10 minutes confirmed successfully.";
} else {
    echo "Error updating bookings: " . $conn->error;
}

$conn->close();
