<?php
// fetch_profile_picture.php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // Assume user_id is stored in session

// Fetch the profile picture path from the database
$sql = "SELECT photo FROM student WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Set profile picture path or default image
$profilePicturePath = $row['photo'] && !empty($row['photo']) ? $row['photo'] : '../images/Customer.png';

$stmt->close();
$conn->close();

// Return the path
echo $profilePicturePath;
?>
