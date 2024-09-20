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
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in.");
    }
    $user_id = $_SESSION['user_id'];

    // Retrieve form data
    $cgpa = $_POST['cgpa'];
    $current_arrears = $_POST['current_arrears'];

    // Prepare and execute SQL query to update STUDENT table
    $sql_update = "UPDATE STUDENT 
                   SET cgpa = ?, 
                       current_arrears = ?
                   WHERE user_id = ?";
    $stmt = $conn->prepare($sql_update);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $cgpa, $current_arrears, $user_id);
    if ($stmt->execute()) {
        echo "Student record updated successfully.";
    } else {
        echo "Error updating student record: " . $stmt->error;
    }

    // Retrieve additional form data for academic details
    $school_tenth = $_POST['school_name_tenth'];
    $board_tenth = $_POST['board_tenth'];
    $percentage_tenth = $_POST['percentage_tenth'];
    $year_tenth = $_POST['pass_out_year_tenth'];
    
    $school_twelfth = $_POST['school_name_twelfth'];
    $board_twelfth = $_POST['board_twelfth'];
    $percentage_twelfth = $_POST['percentage_twelfth'];
    $year_twelfth = $_POST['pass_out_year_twelfth'];

    // Prepare and execute SQL query to insert into ACADEMIC_DETAILS table
    $sql_insert_academic = "INSERT INTO ACADEMIC_DETAILS 
                            (user_id, school_tenth, board_tenth, percentage_tenth, year_tenth, 
                             school_twelfth, board_twelfth, percentage_twelfth, year_twelfth) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert_academic);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssssssss", $user_id, $school_tenth, $board_tenth, $percentage_tenth, $year_tenth, 
                      $school_twelfth, $board_twelfth, $percentage_twelfth, $year_twelfth);
    if ($stmt->execute()) {
        echo "Academic details inserted successfully.";
    } else {
        echo "Error inserting academic details: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
