<?php
session_start(); // Start the session

$user_id = $_GET['user_id'] ?? null; // Use null coalescing to handle missing user_id

// Check if user_id is set
if (!$user_id) {
    die("No user ID provided.");
}
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

// Prepare statement to fetch student details
$sql = "SELECT name,gender, email, phone_number, graduation_year, current_year, dob, 
        course.course_name, course.course_branch 
        FROM STUDENT 
        JOIN course ON STUDENT.course_id = course.course_id
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($name, $gender, $email, $phone_number, $graduation_year, $current_year, $dob, $course_name, $course_branch);
$stmt->fetch();

// If no records are found
if (empty($gender) && empty($email) && empty($phone_number)) {
    die("No student details found for this user.");
}

$stmt->close();
// Retrieve CGPA and current arrears from the STUDENT table
$sql_student = "SELECT cgpa, current_arrears FROM STUDENT WHERE user_id = ?";
$stmt = $conn->prepare($sql_student);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($cgpa, $current_arrears);
$stmt->fetch();
$stmt->close();

// Retrieve academic details (tenth and twelfth grades) from the ACADEMIC_DETAILS table
$sql_academic = "SELECT school_tenth, board_tenth, percentage_tenth, year_tenth, 
                        school_twelfth, board_twelfth, percentage_twelfth, year_twelfth 
                 FROM ACADEMIC_DETAILS WHERE user_id = ?";
$stmt = $conn->prepare($sql_academic);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($school_tenth, $board_tenth, $percentage_tenth, $year_tenth, 
                   $school_twelfth, $board_twelfth, $percentage_twelfth, $year_twelfth);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavoro - Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
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
    font-size: 24px;
    font-weight: bold;
    color: white;
    text-align: center;
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

.container h3{
    margin-right: 450px;
    font-weight: 700;
}

.small-icon {
    width: 50px; /* Set desired width */
    height: 50px; /* Set desired height */
    object-fit: cover; /* Ensures the image scales properly */
    border-radius: 50%;/* Makes the image circular */
}

.icon {
    margin-left: 1px;
    cursor: pointer;
    transition: transform 0.3s;
}

.icon:hover {
    transform: scale(1.1);
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
        
        .sidebar .logo {
    position: absolute;
    top: 20px; /* Keep the same positioning */
    left: 50%;
    transform: translateX(-50%);
    font-size: 36px; /* Increase the font size here */
    font-weight: bold;
    color: white;
    text-align: center;
}
.tabs {
    display: flex;
    flex-direction: column; /* Arrange tabs vertically */
    margin-bottom: 20px; /* Space between tabs and content */
    width: 200px;
}

.tab {
    padding: 10px;
    margin-bottom: 5px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    transition: background-color 0.3s;
}

.tab.active {
    background-color: #1e3d7a; /* Active tab color */
    color: white;
}

.tab:hover {
    font-weight: bold;
}

.content-area {
    padding: 20px;
    background-color: #ffffff;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Tab content */
.tab-content {
    display: none; /* Hide all tab content by default */
}

.tab-content.active {
    display: block; /* Show active tab content */
}
  /* Profile section styling */
        .profile-picture {
            width: 200px; /* Adjust width as needed */
            height: 200px; /* Ensure height equals width for a square */
            border-radius: 10px;/* Make it circular; use 0% for square */
            overflow: hidden; /* Hide overflow for perfect circle */
            border: 3px solid #1e3d7a; /* Optional border for profile picture */
            margin-bottom: 20px; /* Space below profile picture */
            
        }

        .profile-picture img {
            width: 100%; /* Ensure image fits the container */
            height: auto; /* Maintain aspect ratio */
        }
        .text {
    padding-top: 1px;
}

.text h4, p {
    margin: 2px;
    font-size: 18px;
    color: #000000;
}

/* Adjust sub-sidebar to float left */
.sub-sidebar {
    float: left;
    width: 250px; /* Adjust width if needed */
    padding: 10px;
    margin-right: 20px; /* Spacing between sub-sidebar and form */
}

/* Adjust details container */
.details {
            flex: 1;
            background-color: white;
            padding: 0px;
            height: 80vh;
            overflow-y: auto;
        }


.details.active {
    background-color: #ffffff;
    padding-left: 50px;
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

table td:first-child {
    width: 30%;
    text-align: left;
    padding-right: 20px; /* Adjust for alignment between label and input */
}

input[type="radio"] {
    margin-right: 2px; /* Adds space between radio button and label */
}

.gender-options {
    display: flex; /* Ensures horizontal layout */
    gap: 5px; /* Adds space between radio button groups */
    align-items: center; /* Aligns radio buttons with labels */
}

.gender-options label {
    display: flex;
    align-items: center;
    gap: 1px; /* Adds space between radio button and its label */
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
    padding: 7px 25px;
    background-color: #AFC8F3;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 600;
}

button:hover {
    background-color: #1e3d7e;
    color: white;
}
    </style>
</head>
<body>
    <!-- Profile Container -->
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
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
    <a href="joblist_admin.php"><i class="fas fa-briefcase"></i> Jobs</a>
    <a href="view_students.php"  class="active"><i class="fas fa-user-graduate"></i> Students</a>
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
        <div class="sub-sidebar">
            <div class="profile-picture">
                <img src="../images/customer.png" alt="Profile Picture"> <!-- Add your profile picture source here -->
            </div>
         <!-- Profile Picture Section -->
            <div class="tabs">
                <div class="tab" onclick="window.location.href='profileredirect.php?user_id=<?php echo urlencode($user_id); ?>'">Personal Details</div>
                <div class="tab active" onclick="showSection('academic')">Academic Details</div>
                <div class="tab" onclick="window.location.href='resumeredirect.php?user_id=<?php echo urlencode($user_id); ?>'">Resume</div>

            </div>
        </div>
        <!-- Academic Details Section -->
    <div id="academic" class="details ">
        <form action="admineditacademic.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <table>
                <th>UG Details</th>
                <tr>
    <td>Branch</td>
    <td><input type="text" name="course_branch" value="<?php echo htmlspecialchars($course_branch); ?>" readonly/></td>
</tr>

                <tr>
    <td>Course</td>
    <td><input type="text" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>" readonly/></td>
</tr>

                <tr>
                    <td>Current Year </td>
                    <td><input type="text" name="current_year" value="<?php echo htmlspecialchars($current_year); ?>"readonly /></td>
                </tr>
                <tr>
                    <td>Pass Out Year </td>
                    <td><input type="text" name="graduation_year" value="<?php echo htmlspecialchars($graduation_year); ?>" readonly/></td>
                </tr>
                <tr>
                    <td>Current Arrears </td>
                    <td>
            <input type="text" id="current_arrears" name="current_arrears" value="<?php echo htmlspecialchars($current_arrears); ?>"readonly>
        </td>
                </tr>
                <tr>
                    <td>CGPA </td>
                    <td>
            <input type="number" id="cgpa" name="cgpa" value="<?php echo htmlspecialchars($cgpa); ?>"readonly>
        </td>
                </tr>
            </table>
            <br>
            <table>
                <th>12th Details</th>
                <tr>
                    <td>School Name </td>
                    <td>
            <input type="text" id="school_name_twelfth" name="school_name_twelfth" value="<?php echo htmlspecialchars($school_twelfth); ?>"readonly>
        </td>
                </tr>
                <tr>
                    <td>Board </td>
                    <td>
            <input type="text" id="board_twelfth" name="board_twelfth" value="<?php echo htmlspecialchars($board_twelfth); ?>"readonly>
        </td>
    </tr>
    <tr>
        <td>Pass Out Year </td>
        <td>
            <input type="text" id="pass_out_year_twelfth" name="pass_out_year_twelfth" value="<?php echo htmlspecialchars($year_twelfth); ?>"readonly>
        </td>
    </tr>
    <tr>
        <td>Percentage </td>
        <td>
            <input type="text" id="percentage_twelfth" name="percentage_twelfth" value="<?php echo htmlspecialchars($percentage_twelfth); ?>"readonly>
        </td>
    </tr>
</table>
<br>
<table>
    <th>10th Details</th>
    <tr>
        <td>School Name </td>
        <td>
            <input type="text" id="school_name_tenth" name="school_name_tenth" value="<?php echo htmlspecialchars($school_tenth); ?>"readonly>
        </td>
    </tr>
    <tr>
        <td>Board </td>
        <td>
            <input type="text" id="board_tenth" name="board_tenth" value="<?php echo htmlspecialchars($board_tenth); ?>"readonly>
        </td>
    </tr>
    <tr>
        <td>Pass Out Year </td>
        <td>
            <input type="text" id="pass_out_year_tenth" name="pass_out_year_tenth" value="<?php echo htmlspecialchars($year_tenth); ?>"readonly>
        </td>
    </tr>
    <tr>
        <td>Percentage </td>
        <td>
            <input type="text" id="percentage_tenth" name="percentage_tenth" value="<?php echo htmlspecialchars($percentage_tenth); ?>"readonly>
        </td>
    </tr>
</table>
            <div class="button-container">
              
              <button type="submit">EDIT</button>
          </div>
</form>
</div>

    <!-- JavaScript -->
    <script>
        // Change Profile Picture
        function triggerFileInput() {
            document.getElementById('fileInput').click();
        }
    
        function changeProfilePicture(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileIcon').src = e.target.result;
                };
                reader.readAsDataURL(file);
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
    
            // Dashboard stats extraction
            
            // Animate counter values
            function animateCounter(element, endValue) {
                let startValue = 0;
                const duration = 2000; // Animation duration in milliseconds
                const incrementTime = Math.floor(duration / endValue);
                
                const counterInterval = setInterval(() => {
                    if (startValue < endValue) {
                        startValue++;
                        element.textContent = startValue;
                    } else {
                        clearInterval(counterInterval);
                    }
                }, incrementTime);
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
