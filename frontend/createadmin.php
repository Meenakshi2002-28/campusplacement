<?php
// Database connection
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

// Admin details
$user_id = 'JESINE';
$role = 'admin';
$email = 'jesine@gmail.com';
$plain_password = 'jesine123'; // Replace with the desired plain password

// Hash the password using bcrypt
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

// Check if admin already exists
$sql = "SELECT * FROM login WHERE user_id = 'JESINE'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Admin already exists!";
} else {
    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO login (user_id, password, role, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user_id, $hashed_password, $role, $email);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New admin record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
