<?php
// Database connection
$servername = "localhost";
$username = "root"; // MySQL username
$password = ""; // MySQL password
$dbname = "campus_placement"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rollno = $_POST['rollno'];
    $password = $_POST['password'];
    
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into login table
    $sql_login = "INSERT INTO login (user_id, email, password) VALUES ('$rollno', '$email', '$hashed_password')";
    
    if ($conn->query($sql_login) === TRUE) {
        // After successfully inserting into the login table, insert data into student table
        $sql_student = "INSERT INTO student (user_id, name) VALUES ('$rollno', '$name')";
        if ($conn->query($sql_student) === TRUE) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $sql_student . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql_login . "<br>" . $conn->error;
    }
    
    $conn->close();
}
?>
