<?php
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

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$target_dir = "../uploads/profile_pictures/";

if ($user_id) {
    // Fetch the current profile picture filename from the database
    $sql = "SELECT photo FROM student WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_picture);
    $stmt->fetch();
    $stmt->close();

    if ($current_picture) {
        // Check if the file exists in the directory and delete it
        $file_path = $current_picture; // Full path of the picture
        if (file_exists($file_path)) {
            unlink($file_path); // Delete the file
        }

        // Update the database to set the profile picture to NULL
        $sql = "UPDATE student SET photo = NULL WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();

        // Redirect or give feedback after deletion
        header("Location: personalview.php?user_id=" . urlencode($user_id)); // Redirect to profile page
        exit();
    } else {
        echo "No profile picture found to delete.";
    }
} else {
    echo "No user ID provided.";
}

$conn->close();
?>
