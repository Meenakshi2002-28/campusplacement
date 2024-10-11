<?php
session_start(); // Start the session to access session variables

// Assuming you have already set the user_id or email in the session during login
if (isset($_SESSION['user_id'])) {
    $servername = "localhost";
    $db_username = "root"; // MySQL username
    $db_password = ""; // MySQL password
    $dbname = "campus_placement"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } // Make sure this file contains your DB connection code

    // Retrieve the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Prepare and execute a SQL query to fetch the user's name
    $query = "SELECT name FROM student WHERE user_id = ?";
    
    // Using prepared statements to prevent SQL injection
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_id); // Assuming user_id is an integer
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
    }
} else {
    // If no session is set, redirect to the login page
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    
    body {
        font-family: Arial, sans-serif;
    }

    .sidebar {
        width: 198px;
        height: 610px;
        position: fixed;
        left: 10px;
        top: 85px;
        background-color: #2F5597;
        color: white;
        padding: 10px;
    }

    .sidebar a {
        text-decoration: none;
        color: white;
        display: block;
        padding: 15px; /* Adjust padding for better alignment */
        font-size: 22px; /* Smaller font size */
        border-left: 3px solid transparent;
        transition: all 0.3s;
    }

    .sidebar a:hover {
        border-left: 3px solid #ffffff;
        background: #1e165f;
    }
    .logout a{
        font-size: 20px;
        margin-top: 210px;
        text-align: center;
    }
    .main-content {
        margin-left: 220px; /* Adjust left margin */
        padding: 50px;
        font-size: 18px; /* Larger font size for main content */
        padding-top: 15px;
    }

    img {
        height: 40px; /* Adjust size as needed */
        width: auto;
    }
    .container {
        padding: 5px;
        display: flex;
        justify-content: flex-end; /* Aligns children to the right */
        align-items: center; /* Vertically centers the images */
        cursor: pointer;
    }
    .icon {
        margin-left: 1px; /* Adds spacing between the icons */
    }
    .dropdown-content {
    display: none;
    position: absolute;
    background-color: #2F5597;
    min-width: 150px;
    z-index: 1;
    top: 55px; /* Adjust this value as needed */
    border-radius: 3px;
}

.dropdown-content a {
    color: white;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #1e165f;
    color: white;
    border-radius: 3px;
    }
</style>
</head>
<body>
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">

        <i class="fas fa-caret-down fa-2x" aria-hidden="true" onclick="toggleDropdown()"></i>
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../Student_Side/profile_std.html"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>    

<div class="sidebar">
    <a href="#home"><i class="fa fa-fw fa-home"></i> Home</a>
    <a href="jobs.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
    <a href="#applications"><i class="fa fa-fw fa-envelope"></i> Applications</a>
    <a href="#company"><i class="fa fa-fw fa-building"></i> Company</a>
    <a href="../profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
    <a href="#feedback"><i class="fa fa-fw fa-comment"></i> Feedback</a>
    <a href="../logout.php">Logout</a>

</div>
<div class="main-content">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
    </div>
</body>
</html>