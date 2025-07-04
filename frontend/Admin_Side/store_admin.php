<?php
session_start(); // Start session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

// Database connection
$servername = "localhost";
$db_username = "root"; // Update with your database username
$db_password = "";     // Update with your database password
$dbname = "campus_placement"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user_id from session
    $user_id = $_SESSION['user_id'];

    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
   
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = "../uploads/profile_pictures/" . basename($_FILES['photo']['name']);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo)) {
            die("Error moving uploaded file.");
        }
    } else {
        die("File upload error: " . $_FILES['photo']['error']);
    }
    
    // Check the photo path
    echo "Photo path: " . $photo;

    // If no photo was uploaded, don't update the photo field
    if ($photo) {
        $sql = "INSERT INTO admin (name, phone_number, photo, user_id)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name), phone_number = VALUES(phone_number), photo = VALUES(photo)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $phone_number, $photo, $user_id);
    } else {
        $sql = "INSERT INTO admin (name, phone_number, user_id)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name), phone_number = VALUES(phone_number)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone_number, $user_id);
    }

    if ($stmt->execute()) {
        echo "Admin details saved successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
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
        body, html {
    margin: 0;
    padding: 0;
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
    padding: 15px;
    font-size: 22px;
    border-left: 3px solid transparent;
    transition: all 0.3s;
}

.sidebar a:hover {
    border-left: 3px solid #ffffff;
    background: #1e165f;
}
.logout a{
        font-size: 20px;
        margin-top: 160px;
    }

.sub-sidebar {
    width: 205px;
    height: 100vh;
    position: fixed;
    left: 32px;
    top: 0px;
    background-color: white;
    color: rgb(0, 0, 0);
    padding-top: 50px;
    margin-left: 200px;
    text-align: center;
    border-bottom: #1e165f;
}

.menu a {
    text-align: center;
    text-decoration: none;
    color: black;
    display: block;
    padding: 15px;
    font-size: 20px;
    transition: all 0.3s;
}

.menu a.active {
    border-left: 3px solid #000;
    background: #1e165f;
    color: white;
}

.profile img {
    height: 160px;
    width: 140px;
    padding-top: 40px;
    margin: 0;
    cursor: pointer;
}

.text {
    padding-top: 1px;
}

.text h4, p {
    margin: 2px;
    font-size: 18px;
    color: #000000;
}

.details {
    background-color: white;
    padding-left: 200px;
    padding: 30px;
    max-width: 700px;
    margin: auto;
    display: none;
}

.details.active {
    background-color: #ffffff;
    padding-left: 150px;
    display: block;
}

table {
    width: 100%;
    margin-bottom: 20px;
    border-collapse: collapse; /* Ensure table layout doesn't break */
}

table td {
    padding: 6px;
    font-size: 18px;
    white-space: nowrap;
    vertical-align: middle;
    text-align: left;
    border: none;
}

input, select {
    padding: 8px;
    border-radius: 3px;
    border: 1px solid #ddd;
    font-size: 16px;
    width: 100%;
}

input, select {
    padding: 8px;
    border-radius: 3px;
    border: 1px solid #ddd;
    font-size: 16px;
    width: 100%;
}

.button-container {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

button {
    padding: 10px 25px;
    background-color: #AFC8F3;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
}

button:hover {
    background-color: #1e165f;
    color: white;
}

.container {
    padding: 10px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    cursor: pointer;
}

img {
    height: 40px;
    width: auto;
}

.icon {
    margin-left: 1px;
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
        <input type="file" id="fileInput" name="photo" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">

        <i class="fas fa-caret-down fa-2x" aria-hidden="true" onclick="toggleDropdown()"></i>
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../Admin_Side/profile_admin.html"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>
<div class="sidebar">
    <a href="#home"><i class="fas fa-home"></i> Home</a>
    <a href="#jobs"><i class="fas fa-briefcase"></i> Jobs</a>
    <a href="#students"><i class="fas fa-user-graduate"></i> Students</a>
    <a href="#placements"><i class="fas fa-laptop-code"></i> Placements</a>
    <a href="company.html"><i class="fas fa-building"></i> Company</a>
    <a href="#profile"><i class="fas fa-user"></i> Profile</a>
    <a href="#feedback"><i class="fas fa-comment"></i> Feedback</a>
    <div class="logout">
        <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
    </div>
</div>
<div class="sub-sidebar">
    <div class="profile">
        <img src="../images/Customer.png" alt="profile picture" id="sidebarProfilePicture" name="photo"onclick="triggerFileInput()">
        <div class="text">
            <h4>Niranjana A S</h4>
            <p>Admin</p>
        </div>
    </div>
</div>
    <div id="personal" class="details active">
        <h2>Personal Details</h2>
        <form action="store_admin.php" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <td>Name </td><td><input type="text" id="name" name="name"></td>
                </tr>
                <tr>
                    <td>Email </td><td><input type="text" id="email" name="email"></td>
                </tr>
                <tr>
                    <td>Phone No </td><td><input type="text" id="phone_number" name="phone_number"></td>
                </tr>
            </table>
                <div class="button-container">
                    <button type="submit">SAVE</button>
                </div>
        </form>
    </div>
    <script>

        // Change profile image
        function triggerFileInput() {
                document.getElementById('fileInput').click();
            }
    
        function changeProfilePicture(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('sidebarProfilePicture').src = e.target.result; // Update the profile image in sidebar
                    document.getElementById('profileIcon').src = e.target.result; // Update profile icon
                };
                reader.readAsDataURL(file); // Read the image file
            }
        }
        let dropdownOpen = false;
        function toggleDropdown() {
            const dropdown = document.getElementById("dropdownMenu");
            dropdownOpen = !dropdownOpen;
            dropdown.style.display = dropdownOpen ? "block" : "none";
        }
    
        function goToProfile() {
            showSection('personal'); // Redirect to profile section
            toggleDropdown(); // Close the dropdown after redirection
        }
    </script>
</body>
</html>