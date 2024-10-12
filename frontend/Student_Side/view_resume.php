<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

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

// Fetch the resume path from the STUDENT table
$sql = "SELECT resume FROM STUDENT WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($resume_path);
$stmt->fetch();
$stmt->close();
$conn->close();

// Check if a resume path was fetched
if (!empty($resume_path)) {
    // Ensure the file exists on the server
    if (file_exists($resume_path)) {
        // Display the resume (provide a download link or view option)
        echo "<h2>Your Uploaded Resume</h2>";
        echo "<a href='$resume_path' download>Download Resume</a>";
        echo "<br><iframe src='$resume_path' width='100%' height='600px'></iframe>"; // Display PDF in an iframe
    } else {
        // If the file path exists in the database but the file isn't found
        echo "Resume file not found on the server. Please upload it again.";
    }
} else {
    // If no resume path is found in the database
    echo "No resume found in the database. Please upload one.";
}
?>
