<?php
session_start();

// Include database connection
require_once 'config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php?open_modal=login"); // Redirect to general login or admin_login.php
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['id'];

// Fetch bookings for the logged-in user
$user_bookings = [];
$sql = "SELECT
            b.id AS booking_id,
            pl.name AS parking_name,
            pl.address,
            b.start_time,
            b.end_time,
            b.duration_hours,  -- This column should now be populated correctly
            b.amount AS total_amount, -- Fetch 'amount' from DB and alias it as 'total_amount' for display
            b.payment_status,
            b.booking_status,
            b.created_at AS booked_at -- Use created_at as booked_at as per your table structure
        FROM
            bookings b
        JOIN
            parking_locations pl ON b.parking_location_id = pl.id
        WHERE
            b.user_id = ?
        ORDER BY
            b.created_at DESC"; // Order by most recent booking first

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $user_bookings[] = $row;
        }
        mysqli_free_result($result);
    } else {
        error_log("Error fetching user bookings: " . mysqli_error($conn));
        $_SESSION['message'] = "Could not load your bookings. Please try again later.";
        $_SESSION['message_type'] = "danger";
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Error preparing statement for user bookings: " . mysqli_error($conn));
    $_SESSION['message'] = "An error occurred while preparing booking data.";
    $_SESSION['message_type'] = "danger";
}

mysqli_close($conn); // Close connection after fetching data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Smart Parking KTM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/4a6cf7/parking.png" />
    <style>
        /* Base Styles & Typography */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6fc; /* Light background from your design */
            color: #333;
            line-height: 1.6;
            padding-top: 90px; /* Adjust for fixed-top navbar */
            scroll-behavior: smooth;
        }

        /* Navbar Styling */
        .navbar {
            background: linear-gradient(90deg, #4a6cf7, #6a8bff) !important; /* Blue gradient */
            color: #fff;
            padding: 1.5rem 3rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            color: #fff !important;
            font-weight: 700;
        }

        .navbar-brand img {
            margin-right: 0.5rem;
            height: 40px;
            width: 40px;
            filter: invert(1); /* Make icon white */
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #d0e0ff !important;
        }

        .navbar .navbar-text {
            color: #fff !important;
            font-weight: 500;
        }

        .navbar .btn-outline-light {
            background: #fff;
            color: #4a6cf7;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: bold;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .navbar .btn-outline-light:hover {
            background-color: #e0e8ff;
            color: #4a6cf7;
        }

        /* Main Content Area */
        .container.mt-4 {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        h1 {
            color: #4a6cf7;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        /* Alerts */
        .alert {
            margin-top: 20px; /* Space from header */
            margin-bottom: 20px;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        /* Table Styling */
        .table-responsive {
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            overflow-x: auto; /* Ensure horizontal scroll on small screens */
            -webkit-overflow-scrolling: touch; /* For smoother scrolling on iOS */
        }

        .table {
            margin-bottom: 0; /* Remove default table margin */
            border-collapse: separate;
            border-spacing: 0; /* Important for rounded corners on table */
        }

        .table thead th {
            background-color: #e9ecef; /* Light grey header */
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-weight: 700;
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color 0.2s ease-in-out;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa; /* Lighter hover effect */
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }
        
        /* Specific column alignment for better readability */
        .table tbody td:nth-child(2), /* Parking Location */
        .table tbody td:nth-child(3) { /* Address */
            text-align: left;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: .4em .8em; /* Increased padding */
            border-radius: 1rem; /* More rounded pill shape */
            font-size: .8em; /* Slightly larger font */
            font-weight: 600; /* Medium bold */
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            transition: all 0.2s ease;
        }

        .status-badge.booked { background-color: #4a6cf7; color: white; } /* Primary blue */
        .status-badge.completed { background-color: #28a745; color: white; } /* Success green */
        .status-badge.cancelled { background-color: #dc3545; color: white; } /* Danger red */
        .status-badge.pending { background-color: #ffc107; color: #343a40; } /* Warning yellow with dark text */
        .status-badge.paid { background-color: #20c997; color: white; } /* Teal-like for paid */
        .status-badge.unpaid { background-color: #fd7e14; color: white; } /* Orange for unpaid */
        .status-badge.voided, .status-badge.refunded { background-color: #6c757d; color: white; } /* Grey */

        /* Action Button (Cancel) */
        .btn-sm.btn-danger {
            padding: .3rem .75rem;
            font-size: .875rem;
            border-radius: .4rem;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .btn-sm.btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .navbar {
                padding: 1rem 1.5rem;
            }
            .navbar-brand {
                font-size: 1.5rem;
            }
            .navbar-brand img {
                height: 35px;
                width: 35px;
            }
            body {
                padding-top: 75px;
            }
            .navbar-collapse {
                margin-top: 1rem;
            }
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
            }
            .navbar-nav .btn-outline-light {
                width: 100%;
                margin-top: 0.5rem;
            }
            .table thead {
                display: none; /* Hide table header on small screens */
            }
            .table tbody, .table tr, .table td {
                display: block; /* Make table elements act as blocks */
                width: 100%;
            }
            .table tr {
                margin-bottom: 1rem;
                border: 1px solid #e9ecef;
                border-radius: 1rem;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            }
            .table td {
                text-align: right !important; /* Align content to right */
                padding-left: 50%; /* Make space for pseudo-elements */
                position: relative;
            }
            .table td::before {
                content: attr(data-label); /* Use data-label for content */
                position: absolute;
                left: 10px;
                width: calc(50% - 20px);
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: 700;
                color: #6c757d;
            }
            /* Specific data-labels for mobile */
            td:nth-of-type(1)::before { content: "#"; }
            td:nth-of-type(2)::before { content: "Parking Location"; }
            td:nth-of-type(3)::before { content: "Address"; }
            td:nth-of-type(4)::before { content: "Start Time"; }
            td:nth-of-type(5)::before { content: "End Time"; }
            td:nth-of-type(6)::before { content: "Duration"; }
            td:nth-of-type(7)::before { content: "Amount"; }
            td:nth-of-type(8)::before { content: "Payment Status"; }
            td:nth-of-type(9)::before { content: "Booking Status"; }
            td:nth-of-type(10)::before { content: "Booked On"; }
            td:nth-of-type(11)::before { content: "Action"; }

            .alert {
                margin-top: 80px; /* Adjust alert position for smaller screens */
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <img src="https://img.icons8.com/ios-filled/50/4a6cf7/parking.png" alt="Parking Icon">
                SmartParking KTM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="my_bookings.php">My Bookings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            Welcome, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-outline-light">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>My Bookings</h1>

        <?php
        // Display general session messages (e.g., if there was a DB error fetching bookings)
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-' . ($_SESSION['message_type'] ?? 'info') . ' alert-dismissible fade show" role="alert">';
            echo $_SESSION['message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['message']); // Clear the message after displaying
            unset($_SESSION['message_type']);
        }
        ?>

        <?php if (empty($user_bookings)): ?>
            <div class="alert alert-info text-center py-4" role="alert">
                <h4 class="alert-heading">No Bookings Yet!</h4>
                <p>It looks like you haven't reserved any parking spots. Start exploring and find your perfect spot now!</p>
                <hr>
                <a href="dashboard.php" class="btn btn-primary">Book Your First Spot</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Parking Location</th>
                            <th>Address</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Booking Status</th>
                            <th>Booked On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; foreach ($user_bookings as $booking): ?>
                        <tr>
                            <td data-label="#"><?php echo $counter++; ?></td>
                            <td data-label="Parking Location"><?php echo htmlspecialchars($booking['parking_name']); ?></td>
                            <td data-label="Address"><?php echo htmlspecialchars($booking['address']); ?></td>
                            <td data-label="Start Time"><?php echo date('M d, Y H:i A', strtotime($booking['start_time'])); ?></td>
                            <td data-label="End Time"><?php echo date('M d, Y H:i A', strtotime($booking['end_time'])); ?></td>
                            <td data-label="Duration"><?php echo htmlspecialchars(number_format($booking['duration_hours'], 2)); ?> hrs</td>
                            <td data-label="Amount">NPR <?php echo htmlspecialchars(number_format($booking['total_amount'], 2)); ?></td>
                            <td data-label="Payment Status">
                                <?php
                                    $payment_status_class = '';
                                    switch (strtolower($booking['payment_status'])) {
                                        case 'paid': $payment_status_class = 'paid'; break;
                                        case 'unpaid': $payment_status_class = 'unpaid'; break;
                                        case 'voided': $payment_status_class = 'voided'; break;
                                        case 'refunded': $payment_status_class = 'refunded'; break;
                                        default: $payment_status_class = 'pending'; break;
                                    }
                                    $display_payment_status = !empty($booking['payment_status']) ? ucfirst($booking['payment_status']) : 'Unknown';
                                    echo '<span class="status-badge ' . $payment_status_class . '">' . htmlspecialchars($display_payment_status) . '</span>';
                                ?>
                            </td>
                            <td data-label="Booking Status">
                                <?php
                                    $raw_booking_status = $booking['booking_status'];
                                    $booking_status_class = '';
                                    switch (strtolower($raw_booking_status)) {
                                        case 'booked': $booking_status_class = 'booked'; break;
                                        case 'completed': $booking_status_class = 'completed'; break;
                                        case 'cancelled': $booking_status_class = 'cancelled'; break;
                                        case 'pending': $booking_status_class = 'pending'; break;
                                        default: $booking_status_class = 'pending'; break;
                                    }
                                    $display_booking_status = !empty($booking['booking_status']) ? ucfirst($booking['booking_status']) : 'Unknown';
                                    echo '<span class="status-badge ' . $booking_status_class . '">' . htmlspecialchars($display_booking_status) . '</span>';
                                ?>
                            </td>
                            <td data-label="Booked On"><?php echo date('M d, Y H:i A', strtotime($booking['booked_at'])); ?></td>
                            <td data-label="Action">
                                <?php
                                // Determine if cancellation is allowed:
                                // - Booking status must be 'pending' or 'booked'
                                // - Start time must be in the future (e.g., at least X minutes from now, to prevent last-minute cancellations)
                                $allow_cancel = false;
                                $current_time = new DateTime();
                                $booking_start_time = new DateTime($booking['start_time']);
                                $cancellation_cutoff_minutes = 60; // Allow cancellation up to 60 minutes before start time

                                if (in_array(strtolower($booking['booking_status']), ['pending', 'booked'])) {
                                    // Check if the booking start time is still in the future with a buffer
                                    $cancellation_deadline = (clone $booking_start_time)->modify("-$cancellation_cutoff_minutes minutes");
                                    if ($current_time < $cancellation_deadline) {
                                        $allow_cancel = true;
                                    }
                                }

                                if ($allow_cancel):
                                ?>
                                    <button class="btn btn-sm btn-danger cancel-btn" data-booking-id="<?php echo $booking['booking_id']; ?>">Cancel</button>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.cancel-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.dataset.bookingId;
                    
                    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                        fetch('cancel_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'booking_id=' + bookingId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                window.location.reload(); // Reload the page to show updated status
                            } else {
                                alert('Cancellation failed: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred during cancellation. Please try again.');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>