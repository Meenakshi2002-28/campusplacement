<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted and file is uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['resume_file'])) {

    // Directory where the resume will be saved
    $target_dir = "../uploads/resumes/";  // You can change the directory path based on your project structure
    $file_name = basename($_FILES['resume_file']['name']);
    $target_file = $target_dir . $user_id . "_" . $file_name;  // Storing file with user_id for uniqueness

    // Check if the directory exists, if not create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create the directory with proper permissions
    }

    // Check for file upload errors
    if ($_FILES['resume_file']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading file. Code: " . $_FILES['resume_file']['error']);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['resume_file']['tmp_name'], $target_file)) {
        // File uploaded successfully, now save the path to the database

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

        // Update the resume path in the STUDENT table
        $sql = "UPDATE STUDENT SET resume = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("ss", $target_file, $user_id);

        // Execute query
        if ($stmt->execute()) {
            echo "Resume uploaded and saved successfully.";
        } else {
            echo "Error updating resume in the database: " . $stmt->error;
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    } else {
        // Error in moving file
        die("Error moving the uploaded file.");
    }
} else {
    die("No file uploaded.");
}
?>
