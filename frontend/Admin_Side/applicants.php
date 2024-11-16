<?php
// Start the session to use session variables
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch applications for a specific job
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    // Prepare and execute query
    $query = "SELECT sa.name, ja.user_id, ca.course_name ,ja.status
              FROM job_application ja
              JOIN student sa ON ja.user_id = sa.user_id
              JOIN course ca ON sa.course_id = ca.course_id
              WHERE ja.job_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Store applications in an array
    $applications = [];
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    $stmt->close();
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> <!-- SweetAlert CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Merienda&display=swap" rel="stylesheet">
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
    
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #d9e6f4;
            color: #333;
            overflow: hidden;
        }

        /* Sidebar styling */
        .sidebar {
            width: 220px;
            margin-top: 10px;
            margin-bottom: 10px;
            margin-left: 10px;
            border-radius: 10px;
            height: 97vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(135deg, #022a52fd, #063dc9);
            color: white;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5); /* Transparent glow effect */
            transition: width 0.4s ease-in-out;
            padding-top: 80px; /* Added padding for space at the top */
        }

        .sidebar .logo {
            position: absolute;
            top: 20px; /* Positions logo/title closer to the top */
            left: 50%;
            transform: translateX(-50%);
            font-size: 32px;
            font-weight: bold;
            color: white;
            text-align: center;
            font-family: 'Merienda', cursive;
        }

        .sidebar:hover {
            width: 250px; /* Expands sidebar on hover */
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 15px 25px;
            font-size: 18px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            position: relative;
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
        }

        /* Fade-in effect for sidebar links */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateX(-20px); }
            100% { opacity: 1; transform: translateX(0); }
        }

        /* Delayed animation for each link */
        .sidebar a:nth-child(2) { animation-delay: 0.1s; }
        .sidebar a:nth-child(3) { animation-delay: 0.2s; }
        .sidebar a:nth-child(4) { animation-delay: 0.3s; }
        .sidebar a:nth-child(5) { animation-delay: 0.4s; }
        .sidebar a:nth-child(6) { animation-delay: 0.5s; }
        .sidebar a:nth-child(7) { animation-delay: 0.6s; }
        .sidebar a:nth-child(8) { animation-delay: 0.7s; }

        .sidebar a i {
            margin-right: 15px;
            transition: transform 0.3s;
        }

        .sidebar a:hover {
            background-color: #1e3d7a;
            border-left: 4px solid #ffffff;
            padding-left: 30px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4); /* Glow effect */
        }

        .sidebar .logout {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
        }
        .sidebar a.active {
            background-color: #d9e6f4; /* Background color for active link */
            border-left: 4px solid #ffffff;
            padding-left: 30px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;
            color:#000000;
            position: relative;
            z-index: 1;
            height: 45px;
        }

        /* Main content styling */
        .main-content {
            margin-left: 245px;
            margin-top: 13px; 
            margin-right: 20px;/* Default margin for sidebar */
            padding: 40px;
            font-size: 18px;
            color: #333;
            border-radius: 10px;
            transition: margin-left 0.4s ease-in-out; /* Smooth transition for margin */
            background-color: #ffffff;
            height: 86.5vh;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); /* Add shadow effect */
            overflow-y: auto; 
        }

        .main-content h1 {
            color: #050505;
            font-size: 2.5rem; /* Increased font size */
            font-weight: bold;
            padding-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Profile section styling */
        .container {
            padding: 18px 20px;
            width: 1268px;
            height: 55px;
            margin-left: 245px; /* Default margin for container */
            margin-top: 12px;
            margin-right: 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            transition: margin-left 0.4s ease-in-out; /* Smooth transition for margin */
        }

        .icon {
            margin-left: 15px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        img {
            height: 40px; /* Adjust size as needed */
            width: auto;
        }

        /* Dropdown menu styling */
        .dropdown-content {
            display: none;
            opacity: 0;
            position: absolute;
            top: 70px;
            right: 25px;
            background: linear-gradient(135deg, #2F5597, #1e3d7a);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            z-index: 1;
            transition: opacity 0.3s ease;
            padding-left: 2px;
            padding-right: 2px;
        }

        .dropdown-content.show {
            display: block;
            opacity: 1;
        }

        .dropdown-content a {
            color: white;
            padding: 12px;
            text-decoration: none;
            display: block;
            transition: background-color 0.2s;
        }

        .dropdown-content a:hover {
            background-color: #1e3d7a;
        }

        /* Table Styling */
        .applicants {
            margin-top: 0px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px;
            background-color: #ffffff;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #ffffff;
            font-weight: bold;
        }

        /* Setting specific column widths */
        th:nth-child(1), td:nth-child(1) {
            width: 20%; /* Roll No Column */
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
            top: 100px;
            right: 30px;
            background-color: white;
            padding: 5px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            border: 0.5px solid #BBC4C2;
        }

        .filters label {
            display: block;
            padding: 1px;
            font-size: 14px;
        }

        .filters input[type="checkbox"] {
            margin-right: 20px;
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

        .filters button {
            position: relative;
            overflow: hidden;
            height: 1.7rem; /* Set desired height */
            width: 95px; /* Set desired width */
            padding: 4px; /* Set desired padding */
            border-radius: 1.5rem;
            background-color: #1e3d7a;
            background-size: 400%;
            color: white;
            border: none;
            cursor: pointer;
            display: block;
            margin-top: -10px;
            margin-left: auto;
            margin-right: auto;
            font-size: 16px;
            transition: all 0.475s;
            margin-bottom: 5px;
        }

        /* Add gradient animation effect */
        .filters button:hover::before {
            transform: scaleX(1);
        }

        .filters button .button-content {
            position: relative;
            z-index: 1;
        }

        .filters button::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            transform: scaleX(0);
            transform-origin: 0 50%;
            width: 100%;
            height: inherit;
            border-radius: inherit;
            background: linear-gradient(82.3deg, rgba(150, 93, 233, 1) 10.8%, rgba(99, 88, 238, 1) 94.3%);
            transition: all 0.475s;
        }

        input[type="file"]{
            padding-left: 550px;
        }

        /* Styling for the file upload section */
        form[action="status.php"] {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        form[action="status.php"] input[type="file"] {
            width: auto;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #d1d1d1;
            border-radius: 8px;
            background-color: #f9f9f9;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form[action="status.php"] input[type="submit"] {
            padding: 8px 16px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            background-color: #1e3d7a;
            color: #ffffff;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="../images/Customer.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">
        <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-content">
            <a href=" profile_admin.php"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        <a href="dashboard_admin.php" ><i class="fas fa-home"></i> Home</a>
        <a href="joblist_admin.php" class="active"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="view_students.php"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="placedstd.php"><i class="fas fa-laptop-code"></i> Placements</a>
        <a href="company.html"><i class="fas fa-building"></i> Company</a>
        <a href="profile_admin.php"><i class="fas fa-user"></i> Profile</a>
        <a href="feedbacklist.php"><i class="fas fa-comment"></i> Feedback</a>
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
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($applications)): ?>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($application['name']); ?></td>
                    <td><?php echo htmlspecialchars($application['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($application['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No applications found for this job.</td>
            </tr>
        <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Filter Section -->
         <form id="exportForm" method="POST" action="export.php">
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
            <div class="filters">
                <label><input type="checkbox" name="fields[]" value="name" checked> Name</label><br>
                <label><input type="checkbox" name="fields[]" value="user_id" checked> Roll Number</label><br>
                <label><input type="checkbox" name="fields[]" value="course_name" checked> Course</label><br>
                <label><input type="checkbox" name="fields[]" value="course_branch" checked> Branch</label><br>
                <label><input type="checkbox" name="fields[]" value="cgpa" checked> CGPA</label><br>
                <label><input type="checkbox" name="fields[]" value="email"> E-Mail ID</label><br>
                <label><input type="checkbox" name="fields[]" value="current_arrears" checked> Current Arrears</label><br>
                <label><input type="checkbox" name="fields[]" value="graduation_year" checked> Pass out Year</label><br>
                <label><input type="checkbox" name="fields[]" value="percentage_tenth"> Tenth Percentage</label><br>
                <label><input type="checkbox" name="fields[]" value="percentage_twelfth"> Twelfth Percentage</label><br>
                <label><input type="checkbox" name="fields[]" value="resume" checked> Resume</label><br>
 
            <button type="submit" class="button">
                <span class="button-content">Export</span>
            </button>
            </div>
        </form>
        <!-- HTML Form to upload Excel file -->
        <form action="status.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
                <input type="file" name="excel_file" required>
                <input type="submit" value="Upload Excel">

        </form>
<?php
    if (isset($_GET['status']) && $_GET['status'] == 'success') {
        echo "<script>
        Swal.fire({
            title: 'Good job!',
            text: 'Status Updation Successful!',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'joblist_admin.php'; // Replace with your desired URL
            }
        });
    </script>";
    }
    ?>
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
        // Dropdown toggle with smooth opening
    function toggleDropdown() {
            const dropdown = document.getElementById("dropdownMenu");
            dropdown.classList.toggle("show");
        }
    
        // Hide dropdown on click outside
        window.onclick = function(event) {
            if (!event.target.matches('.icon')) {
                const dropdown = document.getElementById("dropdownMenu");
                dropdown.classList.remove("show");
            }
        };
        document.addEventListener("DOMContentLoaded", function () {
            // Sidebar tab click effect
            const tabs = document.querySelectorAll('.sidebar a');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                });
            });
    
            // Set default active link on page load
            const defaultLink = document.querySelector('.sidebar a.active');
            if (defaultLink) {
                defaultLink.classList.add('active');
            }
    
            // Mobile nav handling (optional)
            const mobileTabs = document.querySelectorAll('.navbar-nav .nav-link');
            mobileTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    mobileTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                });
            });

            function goToProfile() {
                showSection('personal'); // Redirect to profile section
                toggleDropdown(); // Close the dropdown after redirection
            }
            // Adjust main content and container margin based on sidebar width
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const container = document.querySelector('.container');
    
            sidebar.addEventListener('mouseenter', () => {
                mainContent.style.marginLeft = '270px'; // Expanded sidebar width
                container.style.marginLeft = '270px'; // Adjust container margin
            });
    
            sidebar.addEventListener('mouseleave', () => {
                mainContent.style.marginLeft = '245px'; // Normal sidebar width
                container.style.marginLeft = '245px'; // Adjust container margin to align with sidebar
            });
        });
    </script>
</body>
</html>