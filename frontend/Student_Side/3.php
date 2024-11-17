<?php
session_start(); // Start the session
$success = false;

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
        echo "<script>alert('Session expired. Please log in again.'); window.location.href='login.php';</script>";
        exit;
    }
    $user_id = $_SESSION['user_id'];

    // Retrieve and sanitize form data
    $gender = htmlspecialchars(trim($_POST['gender']));
    $course_name = htmlspecialchars(trim($_POST['course']));
    $branch = htmlspecialchars(trim($_POST['branch']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['number']));
    $graduation_year = htmlspecialchars(trim($_POST['pass_out_year']));
    $current_year = htmlspecialchars(trim($_POST['current_year']));
    $dob = htmlspecialchars(trim($_POST['dob']));

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
        echo "<script>Swal.fire('Error!', 'Invalid course or branch selected.', 'error');</script>";
        exit;
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
        $success = true; // Always consider the operation successful, regardless of affected rows
    } else {
        echo "<script>Swal.fire('Error!', 'Error occurred during update: " . $stmt->error . "', 'error');</script>";
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Merienda&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php if ($success): ?>
        <script>
            Swal.fire({
                title: 'Updated!',
                text: 'Personal Details Successfully Updated!',
                icon: 'success',
                iconColor: '#022a52fd',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'personalview.php'; // Replace with your desired URL
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
