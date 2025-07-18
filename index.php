<?php

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SmartParking KTM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/4a6cf7/parking.png" />
    <style>
    /* Reset and Base Styles */
    body, html {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif; /* Apply Inter font globally */
        background-color: #f4f6fc; /* Light background from the new design */
        color: #333;
        line-height: 1.6;
        scroll-behavior: smooth;
    }

    /* Header (Navbar) Styling - Mimicking the new design's header */
    .navbar {
        background: linear-gradient(90deg, #4a6cf7, #6a8bff) !important; /* Override Bootstrap bg-dark */
        color: #fff;
        padding: 1.5rem 3rem; /* Match new design padding */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        position: fixed; /* Make header fixed */
        width: 100%;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .navbar-brand {
        font-size: 1.8rem; /* Adjust font size */
        display: flex; /* Allow icon next to text */
        align-items: center;
        color: #fff !important; /* Ensure brand color is white */
        font-weight: 700; /* Bold text */
    }

    .navbar-brand img { /* Add specific styling for the icon if you add one */
        margin-right: 0.5rem;
        height: 40px; /* Adjust size of the icon */
        width: 40px;
        filter: invert(1); /* Make the icon white if it's dark */
    }

    .navbar-nav .nav-link { /* For future nav links if you add them */
        color: #fff !important;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #d0e0ff !important;
    }

    .navbar .btn-outline-light {
        background: #fff;
        color: #4a6cf7;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 2rem; /* Pill shape */
        font-weight: bold;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .navbar .btn-outline-light:hover {
        background-color: #e0e8ff;
        color: #4a6cf7;
    }

    .navbar .btn-light {
        background-color: #4a6cf7; /* Changed to primary color from new design */
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 2rem; /* Pill shape */
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .navbar .btn-light:hover {
        background-color: #3956d9; /* Darker shade on hover */
        color: #fff;
    }

    /* Adjust body padding for fixed header */
    body {
        padding-top: 90px; /* Adjust based on your header's height */
    }

    /* Hero Section Styling - Targeting existing .bg-primary */
    .bg-primary {
        background-image: url('https://source.unsplash.com/1600x900/?parking,city,car'); /* Example image */
        background-size: cover;
        background-position: center;
        height: 70vh; /* Adjust height */
        display: flex; /* Use flexbox for vertical centering */
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #fff !important; /* Ensure text is white */
        position: relative;
        padding-top: 0 !important; /* Remove Bootstrap default padding */
        padding-bottom: 0 !important; /* Remove Bootstrap default padding */
    }

    .bg-primary::before { /* Overlay for background image */
        content: "";
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 1;
    }

    .bg-primary > .container { /* Style the container within hero for content styling */
        z-index: 2;
        padding: 3rem;
        background: rgba(255, 255, 255, 0.05); /* Slightly transparent background */
        border-radius: 1rem;
        backdrop-filter: blur(1px); /* Blur effect */
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .bg-primary h1 {
        font-size: 3rem; /* Larger font size */
        margin-bottom: 1rem;
        color: #fff !important; /* Ensure heading is white */
    }

    .bg-primary .lead {
        font-size: 1.25rem; /* Standard lead font size */
        margin-bottom: 2rem; /* More space below lead text */
    }

    .bg-primary .btn-light {
        background-color: #4a6cf7; /* Primary blue for hero button */
        color: #fff;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem; /* Slightly rounded corners */
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .bg-primary .btn-light:hover {
        background-color: #3956d9; /* Darker blue on hover */
    }

    /* How It Works Section - Targeting existing .bg-light */
    .bg-light {
        background: #f0f3f8 !important; /* Lighter background from new design */
        padding: 3rem 1rem;
        text-align: center;
    }

    .bg-light h2 {
        margin-bottom: 1.5rem; /* More space below heading */
        font-size: 2rem;
    }

    .bg-light .p-3 { /* Targeting the inner divs with p-3 */
        background: #fff;
        padding: 2rem !important; /* Increase padding */
        border-radius: 1rem; /* More rounded corners */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06); /* Softer shadow */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none !important; /* Remove default border */
    }

    .bg-light .p-3:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    }

    .bg-light h4 {
        font-size: 1.5rem; /* Larger heading in cards */
        margin-bottom: 0.75rem;
    }

    /* Why Choose Us Section - Targeting the general text-center py-5 section */
    section.py-5:not(.bg-primary):not(.bg-light) { /* Exclude hero and how-it-works */
        background: #f4f6fc; /* Match body background for this section */
        padding: 3rem 1rem;
        text-align: center;
    }

    section.py-5:not(.bg-primary):not(.bg-light) h2 {
        margin-bottom: 2rem; /* More space below heading */
        font-size: 2rem;
    }

    section.py-5:not(.bg-primary):not(.bg-light) .col-md-4 {
        margin-bottom: 1.5rem; /* Add some margin for vertical spacing on smaller screens */
    }

    section.py-5:not(.bg-primary):not(.bg-light) h5 {
        font-size: 1.3rem; /* Slightly larger feature heading */
        margin-bottom: 0.5rem;
        color: #4a6cf7; /* Highlight with primary color */
        display: flex; /* For icon if added */
        align-items: center;
        justify-content: center;
    }

    section.py-5:not(.bg-primary):not(.bg-light) h5 img {
        margin-right: 0.5rem;
        width: 30px;
        height: 30px;
    }

    /* Footer Styling */
    footer {
        background: #eaeaea !important; /* Lighter background */
        padding: 1.5rem; /* More padding */
        text-align: center;
        font-size: 0.9rem;
        color: #666;
    }

    /* Modal Styling */
    .modal-header {
        background: linear-gradient(90deg, #4a6cf7, #6a8bff); /* Blue gradient header */
        color: white;
        border-bottom: none; /* Remove border */
    }

    .modal-header .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%); /* White close button */
    }

    .modal-footer {
        justify-content: center;
        border-top: none; /* Remove border */
        padding-top: 0.5rem;
        flex-wrap: wrap; /* Allow items to wrap on smaller screens */
    }

    .modal-footer p {
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }
    .modal-footer a {
        color: #4a6cf7; /* Use primary color for links */
        text-decoration: none;
    }
    .modal-footer a:hover {
        text-decoration: underline;
    }

    /* PHP Session Message Alerts (Adjust positioning for fixed header) */
    .alert {
        margin-top: 100px; /* Offset for fixed header */
        margin-left: auto;
        margin-right: auto;
        max-width: 90%;
        z-index: 999; /* Below header, above content */
        position: sticky; /* Make it sticky so it's always visible after scroll */
        top: 90px; /* Position below the fixed header */
    }


    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .navbar {
            padding: 1rem;
        }
        .navbar-toggler { /* For future mobile toggler if you enable it */
            order: 1; /* Place toggler first */
        }
        .navbar .ms-auto { /* Adjust button alignment on mobile */
            width: 100%;
            display: flex;
            flex-direction: column;
            margin-top: 1rem;
        }
        .navbar .btn-outline-light,
        .navbar .btn-light {
            width: 100%;
            margin-left: 0;
            margin-bottom: 0.5rem;
        }

        body {
            padding-top: 150px; /* More padding for smaller header on mobile */
        }

        .bg-primary h1 {
            font-size: 2.5rem;
        }

        .bg-primary > .container {
            padding: 1.5rem;
        }

        .bg-light .col-md-4 {
            margin-bottom: 1.5rem; /* Add spacing between cards */
        }

        section.py-5:not(.bg-primary):not(.bg-light) .col-md-4 {
            margin-bottom: 1.5rem; /* Add spacing between features */
        }

        .alert {
            margin-top: 150px; /* Adjust alert position for smaller screens */
        }
    }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="https://img.icons8.com/ios-filled/50/4a6cf7/parking.png" alt="Parking Icon" style="margin-right: 0.5rem; height: 30px; width: 30px; filter: invert(1);">
            SmartParking KTM
        </a>
        <div class="ms-auto">
            <button type="button" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
        </div>
    </div>
</nav>

<div class="container">
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

<section class="bg-primary text-white text-center py-5" data-aos="fade-in">
    <div class="container">
        <h1 class="display-4">Find Your Parking Spot, Instantly!</h1>
        <p class="lead">Save time, skip the traffic, and reserve your spot in seconds.</p>
        <button type="button" class="btn btn-light btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">Get Started</button>
    </div>
</section>

<section class="py-5 bg-light" data-aos="fade-up">
    <div class="container text-center">
        <h2 class="mb-4">How It Works</h2>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="p-3 border rounded bg-white shadow-sm">
                    <h4>1. Search</h4>
                    <p>View available parking spots near you.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border rounded bg-white shadow-sm">
                    <h4>2. Reserve</h4>
                    <p>Choose your spot and reserve it online.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border rounded bg-white shadow-sm">
                    <h4>3. Park</h4>
                    <p>Reach the location and park hassle-free.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-center" data-aos="zoom-in">
    <div class="container">
        <h2 class="mb-4">Why Choose Us?</h2>
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5><img src="https://img.icons8.com/ios-filled/50/4a6cf7/time.png" alt="Time Icon"> Save Time</h5>
                <p>No more circling the block for parking.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5><img src="https://img.icons8.com/ios-filled/50/4a6cf7/place-marker.png" alt="Location Icon"> Real-Time Availability</h5>
                <p>Know what’s free, when you need it.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5><img src="https://img.icons8.com/ios-filled/50/4a6cf7/checked--v1.png" alt="Check Icon"> Easy & Reliable</h5>
                <p>Just book and park. Simple as that.</p>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; 2025 Smart Parking Nepal | Developed by Tanu Yadav & Anjali Chaudhary</p>
</footer>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_login.php" method="POST">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                        </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                        </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
            <div class="modal-footer">
                <p class="mb-0">Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register here</a></p>
                <p class="mb-0"><a href="#">Forgot Password?</a></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Create Your Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_register.php" method="POST">
                    <div class="mb-3">
                        <label for="registerFullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="registerFullName" name="fullName" required>
                        </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="registerEmail" name="email" required>
                        </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="registerPassword" name="password" required>
                        </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">I agree to the <a href="#">Terms & Conditions</a></label>
                        </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
            <div class="modal-footer">
                <p class="mb-0">Already have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });

    document.addEventListener('DOMContentLoaded', function() {
        <?php
        // Check if there are errors from a previous form submission
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            $modal_to_open = $_GET['open_modal'] ?? ''; // Get which modal to open from URL parameter

            // Handle Register Modal errors
            if ($modal_to_open === 'register') {
                echo 'var registerModal = new bootstrap.Modal(document.getElementById("registerModal"));';
                echo 'registerModal.show();'; // Show the register modal

                // Decode PHP errors and old input into JavaScript variables
                echo 'var errors = ' . json_encode($_SESSION['errors']) . ';';
                echo 'var oldInput = ' . json_encode($_SESSION['old_input'] ?? []) . ';';

                // Loop through errors and display them
                echo 'for (var field in errors) {';
                
                echo '    var inputElementId = "register" + field.charAt(0).toUpperCase() + field.slice(1);';
                echo '    if (field === "confirm_password") { inputElementId = "confirmPassword"; }'; // Specific case for confirmPassword
                echo '    if (field === "terms") { inputElementId = "terms"; }'; // Specific case for terms checkbox

                echo '    var inputElement = document.getElementById(inputElementId);';

                echo '    if (inputElement) {';
                
                echo '        inputElement.classList.add("is-invalid");';

                
                echo '        var feedback = document.createElement("div");';
                echo '        feedback.classList.add("invalid-feedback");';
                echo '        feedback.innerText = errors[field];';
                echo '        inputElement.parentNode.appendChild(feedback);';
                echo '    }';
                echo '}';

                // Restore old input values for register form
                echo 'for (var field in oldInput) {';
                echo '    var inputElementId = "register" + field.charAt(0).toUpperCase() + field.slice(1);';
                echo '    if (field === "confirm_password") { inputElementId = "confirmPassword"; }'; // Specific case for confirmPassword

                echo '    var inputElement = document.getElementById(inputElementId);';
                echo '    if (inputElement) {';
                echo '        inputElement.value = oldInput[field];';
                echo '    }';
                echo '}';

            // Handle Login Modal errors
            } else if ($modal_to_open === 'login') {
                echo 'var loginModal = new bootstrap.Modal(document.getElementById("loginModal"));';
                echo 'loginModal.show();'; // Show the login modal

                echo 'var errors = ' . json_encode($_SESSION['errors']) . ';';
                // Assuming login errors are typically generic or target the email field
                echo 'var inputElement = document.getElementById("loginEmail");'; // Usually highlight email for login errors
                echo 'if (inputElement) {';
                echo '    inputElement.classList.add("is-invalid");';
                echo '    var feedback = document.createElement("div");';
                echo '    feedback.classList.add("invalid-feedback");';
                
                echo '    feedback.innerText = errors["login_error"] || "Invalid credentials.";';
                echo '    inputElement.parentNode.appendChild(feedback);';
                echo '}';
                // Restore old email value if stored
                echo 'var oldLoginEmail = ' . json_encode($_SESSION['old_input']['email'] ?? '') . ';';
                echo 'if (document.getElementById("loginEmail")) { document.getElementById("loginEmail").value = oldLoginEmail; }';
            }
        }
        // Clear session errors/old input after they have been processed by JavaScript
        // This PHP block is intentionally outside the JS output to ensure it executes only once
        if (isset($_SESSION['errors'])) {
            unset($_SESSION["errors"]);
            unset($_SESSION["old_input"]);
        }
        ?>
    });
</script>
</body>
</html>