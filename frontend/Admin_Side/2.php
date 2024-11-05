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

    // Retrieve form data
    $cgpa = $_POST['cgpa'];
    $current_arrears = $_POST['current_arrears'];
    $course_branch=$_POST['course_branch'];
    $course_name=$_POST['course_name'];
    $current_year=$_POST['current_year'];
    $graduation_year=$_POST['graduation_year'];
    $cgpa=$_POST['cgpa'];
    $percentage_tenth=$_POST['$percentage_tenth'];
    $percentage_twelfth=$_POST['$percentage_twelfth'];
    $sql = "SELECT course_id FROM course WHERE course_name = ? AND course_branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $course_name, $course_branch);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($course_id);
    $stmt->fetch();
    $stmt->close();

    // Check if a course_id was found
    if (!$course_id) {
        echo "Invalid course or branch selected.";
        exit; // Stop script execution and return a graceful message
    } else {
        echo "Selected Course ID: " . $course_id; // Debugging output
    }
    $sql_update_student = "UPDATE STUDENT 
                       SET cgpa = ?, course_id = ?, graduation_year = ?, current_year = ?, current_arrears = ? 
                       WHERE user_id = ?";
$stmt = $conn->prepare($sql_update_student);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
// Note: Bind parameters in the correct order as specified in the SQL query
$stmt->bind_param("dissss", $cgpa, $course_id, $graduation_year, $current_year, $current_arrears, $user_id);
if (!$stmt->execute()) {
    echo "Error updating student record: " . $stmt->error;
} else {
    echo "Student record updated successfully.";
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

    // Check if an academic record already exists for the user
    $sql_check = "SELECT COUNT(*) FROM ACADEMIC_DETAILS WHERE user_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $user_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        // Record exists, perform an update
        $sql_update_academic = "UPDATE ACADEMIC_DETAILS 
                                SET school_tenth = ?, board_tenth = ?, percentage_tenth = ?, year_tenth = ?, 
                                    school_twelfth = ?, board_twelfth = ?, percentage_twelfth = ?, year_twelfth = ? 
                                WHERE user_id = ?";
        $stmt = $conn->prepare($sql_update_academic);
        $stmt->bind_param("sssssssss", $school_tenth, $board_tenth, $percentage_tenth, $year_tenth, 
                          $school_twelfth, $board_twelfth, $percentage_twelfth, $year_twelfth, $user_id);
    } else {
        // No record exists, perform an insert
        $sql_insert_academic = "INSERT INTO ACADEMIC_DETAILS 
                                (user_id, school_tenth, board_tenth, percentage_tenth, year_tenth, 
                                 school_twelfth, board_twelfth, percentage_twelfth, year_twelfth) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert_academic);
        $stmt->bind_param("sssssssss", $user_id, $school_tenth, $board_tenth, $percentage_tenth, $year_tenth, 
                          $school_twelfth, $board_twelfth, $percentage_twelfth, $year_twelfth);
    }

    // Execute the statement and handle any errors
    if ($stmt->execute()) {
        header("Location: adminacademicview.php?user_id=" . urlencode($user_id));// Redirect to the desired page
        exit();
    } else {
        echo "Error updating/inserting academic details: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
