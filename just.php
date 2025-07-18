<?php

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Smart Parking Nepal</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  /* Remove browser default margin/padding */
  body, html {
    margin: 0;
    padding: 0;
  }


  .modal-header {
    background-color: #0d6efd;
    color: white;
  }

  .modal-footer {
    justify-content: center;
  }
</style>


</head>
<body>



<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">SmartParking KTM</a>
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

<section class="bg-primary text-white text-center py-5">
  <div class="container">
    <h1 class="display-4">Find Your Parking Spot, Instantly!</h1>
    <p class="lead">Save time, skip the traffic, and reserve your spot in seconds.</p>
    <button type="button" class="btn btn-light btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">Get Started</button>
  </div>
</section>

<section class="py-5 bg-light">
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

<section class="py-5 text-center">
  <div class="container">
    <h2 class="mb-4">Why Choose Us?</h2>
    <div class="row">
      <div class="col-md-4 mb-3">
        <h5>🚗 Save Time</h5>
        <p>No more circling the block for parking.</p>
      </div>
      <div class="col-md-4 mb-3">
        <h5>📍 Real-Time Availability</h5>
        <p>Know what’s free, when you need it.</p>
      </div>
      <div class="col-md-4 mb-3">
        <h5>✅ Easy & Reliable</h5>
        <p>Just book and park. Simple as that.</p>
      </div>
    </div>
  </div>
</section>

<footer class="bg-dark text-white text-center py-3">
  <p class="mb-0">&copy; 2025 SmartParking KTM | Developed by Tanu Yadav & Anjali Chaudhary</p>
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

<script>
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