<?php
session_start();

// Include database connection
require_once 'config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php?open_modal=login");
    exit();
}

// Fetch parking data from the database
$parking_spots = [];
$sql = "SELECT id, name, address, latitude, longitude, total_slots, available_slots, price_per_hour FROM parking_locations WHERE status = 'active'";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $parking_spots[] = $row;
    }
    mysqli_free_result($result);
} else {
    // Log error if query fails, but don't stop the page from loading
    error_log("Error fetching parking locations: " . mysqli_error($conn));
    // Optionally set a session message
    $_SESSION['message'] = "Could not load parking data. Please try again later.";
    $_SESSION['message_type'] = "danger";
}

mysqli_close($conn); // Close connection after fetching data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Parking Nepal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/4a6cf7/parking.png" />
    <style>
        /* Global Styles (matching index.php) */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f4f6fc; /* Light background */
            color: #333;
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        /* Navbar Styling (matching index.php) */
        .navbar {
            background: linear-gradient(90deg, #4a6cf7, #6a8bff) !important;
            color: #fff;
            padding: 1rem 3rem; /* Adjusted for consistency, slightly less than index.php's 1.5rem for dashboard's functional feel */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.6rem; /* Slightly smaller than index.php's hero brand */
            display: flex;
            align-items: center;
            color: #fff !important;
            font-weight: 700;
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

        /* Adjust body padding for fixed header */
        body {
            padding-top: 80px; /* Adjust based on navbar height */
        }

        /* Alert Container */
        .alert-container {
            position: fixed;
            top: 80px; /* Below navbar */
            left: 50%;
            transform: translateX(-50%);
            width: 90%; /* Wider alert for better visibility */
            max-width: 700px; /* Max width for readability */
            z-index: 1050;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow */
            border-radius: 0.5rem;
        }

        /* Main Content Styling */
        .container.mt-4 { /* This targets the main content container below the alert */
            padding-top: 20px; /* Add some top padding below potential alert */
        }

        h1, h2, h3, h4, h5, h6 {
            color: #333;
            font-weight: 700;
        }

        .lead {
            color: #555;
            font-size: 1.1rem;
        }

        /* Map Styling */
        .map-container {
            margin-top: 30px; /* More spacing */
            margin-bottom: 30px;
            background-color: #fff;
            padding: 1.5rem; /* Padding around the map */
            border-radius: 1rem; /* Rounded corners */
            box-shadow: 0 8px 24px rgba(0,0,0,0.1); /* Enhanced shadow */
        }

        #map {
            height: 500px; /* Adjusted height for better balance with list */
            width: 100%;
            border-radius: 0.75rem; /* Slightly more rounded than container */
            border: 1px solid #e0e0e0; /* Subtle border */
        }

        /* Parking Spot Card Styling */
        .card {
            border: none;
            border-radius: 1rem; /* Consistent rounded corners */
            box-shadow: 0 6px 18px rgba(0,0,0,0.08); /* Softer shadow for cards */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.15);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: #4a6cf7; /* Highlight with primary color */
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            color: #777;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .card-text strong {
            font-weight: 600;
        }

        .btn-primary {
            background-color: #4a6cf7;
            border-color: #4a6cf7;
            border-radius: 0.5rem;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3956d9;
            border-color: #3956d9;
        }

        /* Footer Styling (matching index.php) */
        footer {
            background: #eaeaea !important;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-top: 50px; /* Add margin to separate from content */
        }

        /* Modal Styling (matching index.php) */
        .modal-header {
            background: linear-gradient(90deg, #4a6cf7, #6a8bff);
            color: white;
            border-bottom: none;
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .modal-footer {
            justify-content: center;
            border-top: none;
            padding-top: 0.5rem;
        }

        .modal-footer p {
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }
        .modal-footer a {
            color: #4a6cf7;
            text-decoration: none;
        }
        .modal-footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            body {
                padding-top: 70px; /* Adjust for smaller navbar */
            }
            .alert-container {
                top: 70px;
                width: 95%;
            }
            #map {
                height: 400px; /* Shorter map on mobile */
            }
            .map-container {
                padding: 1rem;
            }
            .card-title {
                font-size: 1.2rem;
            }
            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <img src="https://img.icons8.com/ios-filled/50/4a6cf7/parking.png" alt="Parking Icon" style="margin-right: 0.5rem; height: 30px; width: 30px; filter: invert(1);">
                Smart Parking Nepal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php">My Bookings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item d-flex align-items-center me-3">
                        <span class="navbar-text text-white">
                            Welcome, <strong class="text-warning"><?php echo htmlspecialchars($_SESSION["full_name"]); ?></strong>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-outline-light">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="alert-container">
            <?php
            // Display general session messages (success/danger alerts)
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-' . ($_SESSION['message_type'] ?? 'info') . ' alert-dismissible fade show" role="alert">';
                echo $_SESSION['message'];
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
                unset($_SESSION['message']); // Clear the message after displaying
                unset($_SESSION['message_type']);
            }
            ?>
        </div>

        <div class="container mt-4" data-aos="fade-up">
            <h1 class="mb-4 text-center">Your Smart Parking Dashboard</h1>
            <p class="lead text-center mb-5">Seamlessly find and book your ideal parking spot.</p>

            <div class="map-container" data-aos="zoom-in">
                <h2 class="mb-3 text-center">Live Parking Map</h2>
                <div id="map"></div>
            </div>

            <h2 class="mt-5 mb-4 text-center">Available Parking Spots</h2>
            <?php if (empty($parking_spots)): ?>
                <div class="alert alert-info text-center" data-aos="fade-in">No parking spots available at the moment. Please check back later!</div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                    <?php foreach ($parking_spots as $spot): ?>
                        <div class="col" data-aos="fade-up" data-aos-delay="<?php echo (array_search($spot, $parking_spots) * 100); ?>">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($spot['name']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($spot['address']); ?></h6>
                                    <p class="card-text">
                                        Total Slots: <strong><?php echo htmlspecialchars($spot['total_slots']); ?></strong><br>
                                        Available Slots: <strong class="text-<?php echo ($spot['available_slots'] > 0 ? 'success' : 'danger'); ?>"><?php echo htmlspecialchars($spot['available_slots']); ?></strong><br>
                                        Price: NPR <?php echo number_format($spot['price_per_hour'], 2); ?>/hour
                                    </p>
                                    <button class="btn btn-primary w-100 mt-2" onclick="openBookingModal(<?php echo htmlspecialchars($spot['id']); ?>, '<?php echo htmlspecialchars($spot['name']); ?>', <?php echo htmlspecialchars($spot['available_slots']); ?>, <?php echo htmlspecialchars($spot['price_per_hour']); ?>)" <?php echo ($spot['available_slots'] <= 0 ? 'disabled' : ''); ?>>
                                        <?php echo ($spot['available_slots'] <= 0 ? 'Fully Booked' : 'Book Now'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">© 2025 Smart Parking Nepal | Developed by Tanu Yadav & Anjali Chaudhary</p>
    </footer>

    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book Parking Spot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" action="process_booking.php" method="POST">
                        <input type="hidden" id="bookingParkingLocationId" name="parking_location_id">
                        <input type="hidden" id="bookingUserId" name="user_id" value="<?php echo htmlspecialchars($_SESSION['id']); ?>">

                        <div class="mb-3">
                            <label for="bookingLocationName" class="form-label">Parking Location</label>
                            <input type="text" class="form-control" id="bookingLocationName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="bookingAvailableSlots" class="form-label">Available Slots</label>
                            <input type="text" class="form-control" id="bookingAvailableSlots" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="bookingPricePerHour" class="form-label">Price per Hour</label>
                            <input type="text" class="form-control" id="bookingPricePerHour" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="bookingStartTime" class="form-label">Start Date & Time</label>
                            <input type="datetime-local" class="form-control" id="bookingStartTime" name="start_time" required>
                            <div class="invalid-feedback" id="startTimeFeedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="bookingEndTime" class="form-label">End Date & Time</label>
                            <input type="datetime-local" class="form-control" id="bookingEndTime" name="end_time" required>
                            <div class="invalid-feedback" id="endTimeFeedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="bookingDuration" class="form-label">Duration (hours)</label>
                            <input type="text" class="form-control" id="bookingDuration" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="bookingTotalAmount" class="form-label">Total Amount (NPR)</label>
                            <input type="text" class="form-control" id="bookingTotalAmount" readonly>
                        </div>
                        
                        <div id="bookingMessage" class="alert d-none mt-3" role="alert"></div>

                        <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800, // Reduced duration for faster animations
            once: true,
            offset: 50 // Trigger animations earlier
        });
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEmi--sEAoIGO-F3BSc3GYw1I_YWyYZ5U&callback=initMap"></script>

    <script>
        // Data passed from PHP to JavaScript
        var parkingData = <?php echo json_encode($parking_spots); ?>;

        function initMap() {
            // Lalitpur, Bagmati Province, Nepal coordinates for initial map center
            const lalitpurLatLng = { lat: 27.6718, lng: 85.3168 };

            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: lalitpurLatLng,
                mapTypeId: 'roadmap', // Can be 'roadmap', 'satellite', 'hybrid', 'terrain'
                styles: [ // Optional: A slightly more modern map style
                    {
                        "featureType": "poi",
                        "stylers": [
                            { "visibility": "off" }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "labels",
                        "stylers": [
                            { "visibility": "off" }
                        ]
                    }
                ]
            });

            // Loop through parkingData and add markers
            parkingData.forEach(function(spot) {
                const markerPosition = { lat: parseFloat(spot.latitude), lng: parseFloat(spot.longitude) };
                let iconUrl = '';

                // Choose marker icon based on availability
                if (spot.available_slots > 5) {
                    iconUrl = 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'; // Plenty of spots (green)
                } else if (spot.available_slots > 0) {
                    iconUrl = 'https://maps.google.com/mapfiles/ms/icons/orange-dot.png'; // Few spots left (orange)
                } else {
                    iconUrl = 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'; // Full (red)
                }

                const marker = new google.maps.Marker({
                    position: markerPosition,
                    map: map,
                    title: spot.name,
                    icon: iconUrl // Use custom icons directly
                });

                // Info window content
                const infoWindowContent = `
                    <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #333; max-width: 200px;">
                        <h5 style="margin-bottom: 5px; color: #4a6cf7; font-weight: 700;">${spot.name}</h5>
                        <p style="margin-bottom: 3px;"><strong>Address:</strong> ${spot.address}</p>
                        <p style="margin-bottom: 3px;"><strong>Available Slots:</strong> <span style="font-weight: bold; color: ${spot.available_slots > 0 ? '#28a745' : '#dc3545'};">${spot.available_slots}</span> / ${spot.total_slots}</p>
                        <p style="margin-bottom: 5px;"><strong>Price:</strong> NPR ${parseFloat(spot.price_per_hour).toFixed(2)}/hour</p>
                        <button class="btn btn-primary btn-sm mt-2" onclick="openBookingModal(${spot.id}, '${spot.name}', ${spot.available_slots}, ${spot.price_per_hour})" ${spot.available_slots <= 0 ? 'disabled' : ''}>
                            ${spot.available_slots <= 0 ? 'Fully Booked' : 'Book Now'}
                        </button>
                    </div>
                `;

                const infoWindow = new google.maps.InfoWindow({
                    content: infoWindowContent
                });

                marker.addListener('click', function() {
                    // Close any currently open info window before opening a new one (optional, but good UX)
                    if (window.currentInfoWindow) {
                        window.currentInfoWindow.close();
                    }
                    infoWindow.open(map, marker);
                    window.currentInfoWindow = infoWindow; // Store reference to current info window
                });
            });

            // Close info window when clicking on the map itself (optional)
            map.addListener('click', function() {
                if (window.currentInfoWindow) {
                    window.currentInfoWindow.close();
                }
            });
        }

        // Variable to store the currently selected spot for booking
        let currentBookingSpot = null;

        function openBookingModal(id, name, availableSlots, pricePerHour) {
            currentBookingSpot = { id, name, availableSlots, pricePerHour };

            document.getElementById('bookingParkingLocationId').value = id;
            document.getElementById('bookingLocationName').value = name;
            document.getElementById('bookingAvailableSlots').value = availableSlots;
            document.getElementById('bookingPricePerHour').value = `NPR ${parseFloat(pricePerHour).toFixed(2)}`;

            // Clear previous inputs and messages
            document.getElementById('bookingStartTime').value = '';
            document.getElementById('bookingEndTime').value = '';
            document.getElementById('bookingDuration').value = '';
            document.getElementById('bookingTotalAmount').value = '';
            document.getElementById('bookingMessage').classList.add('d-none');
            document.getElementById('bookingMessage').classList.remove('alert-success', 'alert-danger');

            // Remove validation feedback
            document.getElementById('bookingStartTime').classList.remove('is-invalid');
            document.getElementById('bookingEndTime').classList.remove('is-invalid');
            document.getElementById('startTimeFeedback').innerText = '';
            document.getElementById('endTimeFeedback').innerText = '';

            // Disable book button if no slots
            const confirmButton = document.querySelector('#bookingModal button[type="submit"]');
            if (availableSlots <= 0) {
                confirmButton.disabled = true;
                bookingMessageDiv.classList.remove('d-none');
                bookingMessageDiv.classList.add('alert-danger');
                bookingMessageDiv.innerText = 'This parking location currently has no available slots.';
            } else {
                confirmButton.disabled = false;
            }

            var bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            bookingModal.show();
        }

        // --- Calculation Logic for Booking Form ---
        const bookingStartTimeInput = document.getElementById('bookingStartTime');
        const bookingEndTimeInput = document.getElementById('bookingEndTime');
        const bookingDurationInput = document.getElementById('bookingDuration');
        const bookingTotalAmountInput = document.getElementById('bookingTotalAmount');
        const bookingMessageDiv = document.getElementById('bookingMessage'); // Moved here for broader scope

        function calculateBookingDetails() {
            const start = new Date(bookingStartTimeInput.value);
            const end = new Date(bookingEndTimeInput.value);
            const pricePerHour = currentBookingSpot ? parseFloat(currentBookingSpot.pricePerHour) : 0;

            // Reset validation
            bookingStartTimeInput.classList.remove('is-invalid');
            bookingEndTimeInput.classList.remove('is-invalid');
            document.getElementById('startTimeFeedback').innerText = '';
            document.getElementById('endTimeFeedback').innerText = '';
            bookingMessageDiv.classList.add('d-none'); // Hide message on recalculation

            if (start && end && start < end) {
                const diffMs = end - start; // Difference in milliseconds
                const diffHours = diffMs / (1000 * 60 * 60); // Convert to hours

                // Basic validation: Minimum 15 minutes, up to 24 hours
                if (diffHours < 0.25) { // 0.25 hours = 15 minutes
                    bookingEndTimeInput.classList.add('is-invalid');
                    document.getElementById('endTimeFeedback').innerText = 'Booking duration must be at least 15 minutes.';
                    bookingDurationInput.value = '';
                    bookingTotalAmountInput.value = '';
                    return;
                }
                if (diffHours > 24) {
                    bookingEndTimeInput.classList.add('is-invalid');
                    document.getElementById('endTimeFeedback').innerText = 'Maximum booking duration is 24 hours.';
                    bookingDurationInput.value = '';
                    bookingTotalAmountInput.value = '';
                    return;
                }

                // Ensure future time for booking
                const now = new Date();
                if (start < now) {
                    bookingStartTimeInput.classList.add('is-invalid');
                    document.getElementById('startTimeFeedback').innerText = 'Start time cannot be in the past.';
                    bookingDurationInput.value = '';
                    bookingTotalAmountInput.value = '';
                    return;
                }


                bookingDurationInput.value = diffHours.toFixed(2); // Show duration with 2 decimal places
                bookingTotalAmountInput.value = (diffHours * pricePerHour).toFixed(2);
            } else if (start && end && start >= end) {
                bookingEndTimeInput.classList.add('is-invalid');
                document.getElementById('endTimeFeedback').innerText = 'End time must be after start time.';
                bookingDurationInput.value = '';
                bookingTotalAmountInput.value = '';
            } else {
                bookingDurationInput.value = '';
                bookingTotalAmountInput.value = '';
            }
        }

        bookingStartTimeInput.addEventListener('change', calculateBookingDetails);
        bookingEndTimeInput.addEventListener('change', calculateBookingDetails);

        // --- AJAX Form Submission for Booking ---
        const bookingForm = document.getElementById('bookingForm');
        // const bookingMessageDiv = document.getElementById('bookingMessage'); // Already declared above

        bookingForm.addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent default form submission

            // Clear previous messages
            bookingMessageDiv.classList.add('d-none');
            bookingMessageDiv.classList.remove('alert-success', 'alert-danger');

            // Basic client-side validation for dates
            const start = new Date(bookingStartTimeInput.value);
            const end = new Date(bookingEndTimeInput.value);
            
            let isValid = true;
            
            // Re-run full calculation to apply all validation
            calculateBookingDetails(); // This will re-apply is-invalid and feedback

            // Check if any fields are still invalid after calculation
            if (bookingStartTimeInput.classList.contains('is-invalid') || bookingEndTimeInput.classList.contains('is-invalid')) {
                isValid = false;
            }

            if (!bookingStartTimeInput.value) {
                bookingStartTimeInput.classList.add('is-invalid');
                document.getElementById('startTimeFeedback').innerText = 'Start time is required.';
                isValid = false;
            }

            if (!bookingEndTimeInput.value) {
                bookingEndTimeInput.classList.add('is-invalid');
                document.getElementById('endTimeFeedback').innerText = 'End time is required.';
                isValid = false;
            }


            if (currentBookingSpot && currentBookingSpot.availableSlots <= 0) {
                bookingMessageDiv.classList.remove('d-none');
                bookingMessageDiv.classList.add('alert-danger');
                bookingMessageDiv.innerText = 'This parking location currently has no available slots.';
                isValid = false;
            }

            if (!isValid) {
                return; // Stop if client-side validation fails
            }

            const formData = new FormData(bookingForm);
            
            try {
                const response = await fetch(bookingForm.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                bookingMessageDiv.classList.remove('d-none');
                if (result.success) {
                    bookingMessageDiv.classList.add('alert-success');
                    bookingMessageDiv.innerText = result.message;
                    setTimeout(() => { location.reload(); }, 2000); // Reload after 2 seconds
                } else {
                    bookingMessageDiv.classList.add('alert-danger');
                    bookingMessageDiv.innerText = result.message;

                    // If specific field errors are returned, show them
                    if (result.errors) {
                        for (const field in result.errors) {
                            const inputElement = document.getElementById(`booking${field.charAt(0).toUpperCase() + field.slice(1)}`);
                            // Handle 'start_time' and 'end_time' specifically for feedback IDs
                            let feedbackId = `${field}Feedback`;
                            if (field === 'start_time') feedbackId = 'startTimeFeedback';
                            if (field === 'end_time') feedbackId = 'endTimeFeedback';

                            if (inputElement) {
                                inputElement.classList.add('is-invalid');
                                const feedbackDiv = document.getElementById(feedbackId);
                                if (feedbackDiv) {
                                    feedbackDiv.innerText = result.errors[field];
                                }
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                bookingMessageDiv.classList.remove('d-none');
                bookingMessageDiv.classList.add('alert-danger');
                bookingMessageDiv.innerText = 'An unexpected error occurred. Please try again.';
            }
        });
    </script>
</body>
</html>