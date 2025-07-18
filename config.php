<?php

// Database configuration
define('DB_SERVER', 'localhost'); // Usually 'localhost' for local development
define('DB_USERNAME', 'root');   // Your MySQL username (e.g., 'root' for XAMPP/WAMP)
define('DB_PASSWORD', '');       // Your MySQL password (often empty for XAMPP/WAMP)
define('DB_NAME', 'smart_parking_db'); // The name of your database
define('DB_PORT', 3307);         // <--- ADD THIS LINE with your specific port number!

// Attempt to connect to MySQL database
// Add DB_PORT as the 5th argument
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
// else{
//     echo "connected succesfully.";
// }

?>