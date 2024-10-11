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
$plain_password = 'jesine123'; // Replace with the desired plain paSssword
$phone_number = '8075562315'; // Admin phone number
$name = 'Jesine Maria'; // Admin name

// Hash the password using bcrypt
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

// Check if user exists in login table
$sql = "SELECT * FROM login WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User does not exist, insert into login table
    $stmt = $conn->prepare("INSERT INTO login (user_id, password, role, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user_id, $hashed_password, $role, $email);

    // Execute the statement
    if ($stmt->execute()) {
        // Now insert into admin table
        $stmt = $conn->prepare("INSERT INTO admin (user_id, name, phone_number) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $user_id, $name, $phone_number); // Changed to 'ssi' to match data types

        // Execute the statement
        if ($stmt->execute()) {
            echo "New admin record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Admin already exists!";
}

// Close the statement
$stmt->close();

// Close the connection
$conn->close();
?>
