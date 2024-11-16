<?php
// Include database connection
$servername = "localhost"; // Change to your server name
$username = "root";        // Change to your database username
$password = "";            // Change to your database password
$dbname = "campus_placement"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to get admin details
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form values
    $userId = $_SESSION['user_id']; // Assuming you are storing the user_id in the session
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Sanitize the input data
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);

    // Prepare the update queries
    // Update the admin table (name and phone)
    $queryAdmin = "UPDATE admin SET name = '$name', phone_number = '$phone' WHERE user_id = '$userId'";

    // Update the login table (email)
    $queryLogin = "UPDATE login SET email = '$email' WHERE user_id = '$userId'";

    // Execute the queries
    if (mysqli_query($conn, $queryAdmin) && mysqli_query($conn, $queryLogin)) {
        // Redirect to a confirmation page or the updated profile view
        header("Location: profile_admin.php?update=success");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
