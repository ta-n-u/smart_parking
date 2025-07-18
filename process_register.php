<?php
// Start a session (useful for messages, even if not logged in yet)
session_start();

// Include database connection
require_once 'config.php';

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Initialize variables and set to empty string
    $full_name = $email = $password = $confirm_password = "";
    $errors = []; // Array to store validation errors

    // 1. Get and Sanitize Input
    $full_name = trim($_POST["fullName"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirmPassword"] ?? '';
    $terms_agreed = isset($_POST["terms"]); // Check if terms checkbox is checked

    // 2. Validate Input
    if (empty($full_name)) {
        $errors['full_name'] = "Please enter your full name.";
    }

    if (empty($email)) {
        $errors['email'] = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $errors['email'] = "This email is already registered.";
                }
            } else {
                $errors['general'] = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (empty($password)) {
        $errors['password'] = "Please enter a password.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (!$terms_agreed) {
        $errors['terms'] = "You must agree to the terms and conditions.";
    }

    // 3. If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an INSERT statement
        $sql = "INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $param_full_name, $param_email, $param_password_hash);

            // Set parameters
            $param_full_name = $full_name;
            $param_email = $email;
            $param_password_hash = $hashed_password;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Registration successful, redirect to homepage with success message
                $_SESSION['message'] = "Registration successful! You can now log in.";
                $_SESSION['message_type'] = "success";
                header("location: index.php"); // Redirect back to homepage
                exit();
            } else {
                $_SESSION['message'] = "Something went wrong. Please try again later.";
                $_SESSION['message_type'] = "danger";
                // You might want to log this error
                error_log("Registration SQL error: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt);
        }
    }

    // If there are errors, store them in session and redirect back to index.php
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = ['fullName' => $full_name, 'email' => $email]; // Keep old input for user convenience
    $_SESSION['message'] = "Please correct the errors below.";
    $_SESSION['message_type'] = "danger";

    header("location: index.php?open_modal=register"); // Reopen register modal if there were errors
    exit();

} else {
    // If someone tried to access this page directly without POST, redirect
    header("location: index.php");
    exit();
}

// Close connection (good practice, though PHP closes it automatically at script end)
mysqli_close($conn);
?>