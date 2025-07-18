<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php"); // Redirect to general login or admin_login.php
    exit;
}

// Check if the user is an admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    // If not an admin, redirect them away or show an access denied message
    header("location: dashboard.php"); // Redirect to user dashboard
    exit;
}

require_once 'config.php'; // Include your database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Parking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_locations.php">Manage Locations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_bookings.php">Manage Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_users.php">Manage Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Welcome to the Admin Dashboard, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        <p>This is your central control panel for the Smart Parking system.</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center p-3 shadow-sm">
                    <h3>Total Parking Locations</h3>
                    <?php
                    $sql_locations = "SELECT COUNT(*) FROM parking_locations";
                    $result_locations = mysqli_query($conn, $sql_locations);
                    $row_locations = mysqli_fetch_array($result_locations);
                    echo "<p class='display-4'>" . $row_locations[0] . "</p>";
                    ?>
                    <a href="admin_manage_locations.php" class="btn btn-primary">Manage Locations</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3 shadow-sm">
                    <h3>Total Bookings</h3>
                    <?php
                    $sql_bookings = "SELECT COUNT(*) FROM bookings";
                    $result_bookings = mysqli_query($conn, $sql_bookings);
                    $row_bookings = mysqli_fetch_array($result_bookings);
                    echo "<p class='display-4'>" . $row_bookings[0] . "</p>";
                    ?>
                    <a href="admin_manage_bookings.php" class="btn btn-info">View Bookings</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3 shadow-sm">
                    <h3>Registered Users</h3>
                    <?php
                    $sql_users = "SELECT COUNT(*) FROM users";
                    $result_users = mysqli_query($conn, $sql_users);
                    $row_users = mysqli_fetch_array($result_users);
                    echo "<p class='display-4'>" . $row_users[0] . "</p>";
                    ?>
                    <a href="admin_manage_users.php" class="btn btn-success">Manage Users</a>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-5">
            **Note:** The links "Manage Locations," "Manage Bookings," and "Manage Users" will lead to pages that you'll create next to add specific functionalities. For now, they will just show a "File not found" error if clicked.
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>