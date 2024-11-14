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

// Get the user_id from the URL
$user_id = $_GET['user_id'] ?? null; // Use null coalescing to handle missing user_id

// Check if user_id is set
if (!$user_id) {
    die("No user ID provided.");
}

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
    // Close the statement and connection before redirecting
    $stmt->close();
    $conn->close();
    header("Location: adminpersonalview.php?user_id=" . urlencode($user_id)); // Redirect to view details
    exit();
} else {
    // If no records found, redirect to storepr_std.php
    $stmt->close();
    $conn->close();
    header("Location: Student_Side/personal.php?user_id=" . urlencode($user_id)); // Redirect to store details
    exit();
}
?>
