<?php
// Start a session
session_start();

// Include database connection
require_once 'config.php';

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Initialize variables
    $email = $password = "";
    $errors = [];

    // Get and Sanitize Input
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // Validate Input
    if (empty($email)) {
        $errors['email'] = "Please enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors['password'] = "Please enter your password.";
    }

    // If no specific input errors, try to login
    if (empty($errors)) {
        // Prepare a SELECT statement to retrieve user
        $sql = "SELECT id, full_name, email, password_hash FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                // Check if email exists, then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $full_name, $email, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_regenerate_id(true); // Prevent session fixation

                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['email'] = $email;
                            $_SESSION['full_name'] = $full_name;

                            // Update last login time (optional)
                            $update_last_login_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
                            if ($update_stmt = mysqli_prepare($conn, $update_last_login_sql)) {
                                mysqli_stmt_bind_param($update_stmt, "i", $id);
                                mysqli_stmt_execute($update_stmt);
                                mysqli_stmt_close($update_stmt);
                            }

                            // Redirect user to dashboard or a protected page
                            $_SESSION['message'] = "Welcome back, " . $full_name . "!";
                            $_SESSION['message_type'] = "success";
                            header("location: dashboard.php"); // You'll create this page next!
                            exit();
                        } else {
                            // Password is not valid
                            $errors['login_error'] = "Invalid email or password.";
                        }
                    }
                } else {
                    // Email doesn't exist
                    $errors['login_error'] = "Invalid email or password.";
                }
            } else {
                $errors['general'] = "Oops! Something went wrong. Please try again later.";
                error_log("Login SQL execution error: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt);
        }
    }

    // If login failed, store errors in session and redirect back to homepage
    $_SESSION['errors'] = $errors;
    $_SESSION['message'] = $errors['login_error'] ?? "Login failed. Please try again.";
    $_SESSION['message_type'] = "danger";
    header("location: index.php?open_modal=login"); // Reopen login modal
    exit();

} else {
    // If someone tried to access this page directly without POST, redirect
    header("location: index.php");
    exit();
}

// Close connection
mysqli_close($conn);
?>