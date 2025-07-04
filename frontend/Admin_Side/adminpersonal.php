<?php
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    $user_id = $_POST['user_id'];

 

    // Retrieve and sanitize form data
    $gender = htmlspecialchars(trim($_POST['gender']));
    $course_name = htmlspecialchars(trim($_POST['course']));
    $branch = htmlspecialchars(trim($_POST['branch']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['number']));
    $graduation_year = htmlspecialchars(trim($_POST['pass_out_year']));
    $current_year = htmlspecialchars(trim($_POST['current_year']));
    $dob = htmlspecialchars(trim($_POST['dob']));

    // Check if any required field is empty
    if (empty($gender) || empty($course_name) || empty($branch) || empty($email) || empty($phone_number) || empty($graduation_year) || empty($current_year) || empty($dob))
     {
        echo "All fields are required.";
        // Stop script execution and return a graceful message
    }
    
    // Prepare statement to get course_id based on course_name and branch
    $sql = "SELECT course_id FROM course WHERE course_name = ? AND course_branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $course_name, $branch);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($course_id);
    $stmt->fetch();
    $stmt->close();

    // Check if a course_id was found
    if (!$course_id) {
        echo "Invalid course or branch selected.";
        exit; // Stop script execution and return a graceful message
    }

    // Prepare and bind statement for updating data in STUDENT table
    $sql = "UPDATE STUDENT 
            SET gender = ?, 
                course_id = ?, 
                email = ?, 
                phone_number = ?, 
                graduation_year = ?, 
                current_year = ?, 
                dob = ? 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $gender, $course_id, $email, $phone_number, $graduation_year, $current_year, $dob, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: adminpersonalview.php?user_id=" . urlencode($user_id));// Redirect to the desired page
        exit();
    } else {
        echo "Error updating/inserting academic details: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
