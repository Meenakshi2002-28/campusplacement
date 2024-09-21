<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root"; // Update with your database username
$password = "";     // Update with your database password
$dbname = "campus_placement"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user_id from session
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];

// Check if current_year and phone_number exist for the user_id in the STUDENT table
$sql = "SELECT current_year, phone_number FROM STUDENT WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($current_year, $phone_number);
$stmt->fetch();

// If current_year and phone_number exist, redirect to view.php
if ($stmt->num_rows > 0 && !empty($current_year) && !empty($phone_number)) {
    header("Location:Student_Side/view.php"); // Redirect to view details
    exit();
} else {
    // If no records found, redirect to storepr_std.php
    header("Location:Student_Side/storepr_std.php"); // Redirect to store details
    exit();
}

// Close connection
$stmt->close();
$conn->close();
?>
