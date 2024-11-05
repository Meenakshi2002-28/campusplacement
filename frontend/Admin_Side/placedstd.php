<?php
// Start session to get the logged-in user's ID
session_start();

// Assuming you have stored the user's ID in session
$user_id = $_SESSION['user_id']; // Replace with the actual session key if different

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "
SELECT j.company_name, j.job_title, p.user_id, s.name
    FROM placement p
    JOIN job j ON p.job_id = j.job_id
    JOIN student s ON p.user_id = s.user_id
    ORDER BY s.name ASC
";

// Execute the query
$result = $conn->query($sql);

// Fetch all results into an array
$placed_students = $result->fetch_all(MYSQLI_ASSOC);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> <!-- SweetAlert CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
            *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
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

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 220px;
            padding: 20px;
        }

        /* Table Styling */
        .applicants {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #ffffff;
            font-weight: bold;
        }

        /* Setting specific column widths */
        th:nth-child(1), td:nth-child(1) {
            width: 15%; /* Roll No Column */
        }

        th:nth-child(2), td:nth-child(2) {
            width: 25%; /* Name Column */
        }

        th:nth-child(3), td:nth-child(3) {
            width: 25%; /* Course Column */
        }

        /* Filter Section */
        .filters {
            position: absolute;
            top: 80px;
            right: 20px;
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        .filters label {
            display: block;
            margin-bottom: 10px;
        }

        .filters input[type="checkbox"] {
            margin-right: 10px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #2F5597;
            min-width: 150px;
            z-index: 1;
            top: 55px;
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
        }

        .logout{
            position: absolute;
            bottom: 20px;
            width: 100%;
        }

        .logout a {
            font-size: 20px;
            margin-top: 210px;
        }
        img {
            height: 40px;
            width: auto;
        }

        .container {
            padding: 5px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

    </style>
</head>
<body>
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">

        <i class="fas fa-caret-down fa-2x" aria-hidden="true" onclick="toggleDropdown()"></i>
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../Admin_Side/profile_admin.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div> 

    <div class="sidebar">
        <a href="dashboard_admin.php"><i class="fas fa-home"></i> Home</a>
        <a href="joblist_admin.php"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="#students"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="#placements"><i class="fas fa-laptop-code"></i>Placements</a>
        <a href="#company"><i class="fas fa-building"></i> Company</a>
        <a href="profile_admin.php"><i class="fas fa-user"></i> Profile</a>
        <a href="#feedback"><i class="fas fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Applicants Table -->
        <div class="applicants">
            <table>
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Position</th>
                        <th>Student</th>
                        <th>Roll no</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($placed_students)): ?>
            <?php foreach ($placed_students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['user_id']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No placed students found.</td>
            </tr>
        <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Filter Section -->
        
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
