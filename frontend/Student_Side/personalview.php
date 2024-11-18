<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

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
$stmt->bind_result(
    $school_tenth,
    $board_tenth,
    $percentage_tenth,
    $year_tenth,
    $school_twelfth,
    $board_twelfth,
    $percentage_twelfth,
    $year_twelfth
);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merienda&display=swap" rel="stylesheet">
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
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            /* Transparent glow effect */
            transition: width 0.4s ease-in-out;
            padding-top: 80px;
            /* Added padding for space at the top */
        }

        .sidebar .logo {
            position: absolute;
            top: 20px;
            /* Positions logo/title closer to the top */
            left: 50%;
            transform: translateX(-50%);
            font-size: 32px;
            font-weight: bold;
            color: white;
            text-align: center;
            font-family: 'Merienda', cursive;
        }

        .sidebar:hover {
            width: 250px;
            /* Expands sidebar on hover */
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
            0% {
                opacity: 0;
                transform: translateX(-20px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Delayed animation for each link */
        .sidebar a:nth-child(2) {
            animation-delay: 0.1s;
        }

        .sidebar a:nth-child(3) {
            animation-delay: 0.2s;
        }

        .sidebar a:nth-child(4) {
            animation-delay: 0.3s;
        }

        .sidebar a:nth-child(5) {
            animation-delay: 0.4s;
        }

        .sidebar a:nth-child(6) {
            animation-delay: 0.5s;
        }

        .sidebar a:nth-child(7) {
            animation-delay: 0.6s;
        }

        .sidebar a i {
            margin-right: 15px;
            transition: transform 0.3s;
        }

        .sidebar a:hover {
            background-color: #1e3d7a;
            border-left: 4px solid #ffffff;
            padding-left: 30px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
            /* Glow effect */
        }

        .sidebar .logout {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
        }

        .sidebar a.active {
            background-color: #d9e6f4;
            /* Background color for active link */
            border-left: 4px solid #ffffff;
            padding-left: 30px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;
            color: #000000;
            position: relative;
            z-index: 1;
            height: 45px;
        }

        /* Main content styling */
        .main-content {
            margin-left: 245px;
            margin-top: 13px;
            margin-right: 20px;
            /* Default margin for sidebar */
            padding: 40px;
            font-size: 18px;
            color: #333;
            border-radius: 10px;
            transition: margin-left 0.4s ease-in-out;
            /* Smooth transition for margin */
            background-color: #ffffff;
            height: 86.5vh;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            /* Add shadow effect */
        }

        .main-content h1 {
            color: #050505;
            font-size: 2.5rem;
            /* Increased font size */
            font-weight: bold;
            padding-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Profile section styling */
        .container {
            padding: 18px 20px;
            width: 1268px;
            height: 55px;
            margin-left: 245px;
            /* Default margin for container */
            margin-top: 12px;
            margin-right: 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            transition: margin-left 0.4s ease-in-out;
            /* Smooth transition for margin */
        }

        .icon {
            margin-left: 1px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        img {
            height: 40px;
            /* Adjust size as needed */
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

        .tabs {
            display: flex;
            flex-direction: column;
            /* Arrange tabs vertically */
            margin-bottom: 20px;
            /* Space between tabs and content */
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
            background-color: #1e3d7a;
            /* Active tab color */
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
            display: none;
            /* Hide all tab content by default */
        }

        .tab-content.active {
            display: block;
            /* Show active tab content */
        }

        /* Profile section styling */
        .profile-picture {
            width: 200px;
            /* Adjust width as needed */
            height: 200px;
            /* Ensure height equals width for a square */
            border-radius: 10px;
            /* Make it circular; use 0% for square */
            overflow: hidden;
            /* Hide overflow for perfect circle */
            border: 3px solid #1e3d7a;
            /* Optional border for profile picture */
            margin-bottom: 20px;
            /* Space below profile picture */
            position: relative;
            display: inline-block;
        }

        .profile-picture img {
            width: 100%;
            /* Ensure image fits the container */
            height: auto;
            /* Maintain aspect ratio */
        }

        .text {
            padding-top: 1px;
        }

        /* Adjust sub-sidebar to float left */
        .sub-sidebar {
            float: left;
            width: 250px;
            /* Adjust width if needed */
            padding: 10px;
            margin-right: 20px;
            /* Spacing between sub-sidebar and form */
        }

        /* Adjust details container */
        .details {
            flex: 1;
            background-color: white;
            padding: 0;
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
            border-collapse: collapse;
            /* Ensure table layout doesn't break */
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
            padding-right: 20px;
            /* Adjust for alignment between label and input */
        }

        input[type="radio"] {
            margin-right: 2px;
            /* Adds space between radio button and label */
        }

        .gender-options {
            display: flex;
            /* Ensures horizontal layout */
            gap: 5px;
            /* Adds space between radio button groups */
            align-items: center;
            /* Aligns radio buttons with labels */
        }

        .gender-options label {
            display: flex;
            align-items: center;
            gap: 1px;
            /* Adds space between radio button and its label */
        }

        input,
        select {
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
            padding: 7px 20px;
            background-color: #AFC8F3;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
        }

        button:hover {
            background-color: #1e3d7e;
            color: white;
            font-weight: 600;
        }

        .profile-picture1 {
            width: 100px;
            /* Adjust width */
            height: 100px;
            /* Adjust height */
            overflow: hidden;
            /* Ensures the image fits within the div */
            border-radius: 50%;
            /* Makes the image circular */
        }

        .profile-picture1 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Ensures the image scales properly within the div */
        }

        .profile-picture1 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Ensures the image scales properly within the div */
        }

        #editImageButton {
            position: absolute;
            top: 90%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            background-color: #AFC8F3;
            color: black;
            font-size: 15px;
            border: none;
            margin-bottom: 2px;
            width: 60px;
            height: 30px;
            padding: 0px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .profile-picture:hover #editImageButton {
            display: block;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 300px;
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
            overflow: hidden;
        }

        .modal button {
            margin-left: 70px;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #000;
        }

        .text h4 {
            padding-left: 35px;
            margin-bottom: 15px;
            margin-top: -12.5px;
            font-size: 18px;
            color: black;
        }

        .fas.fa-trash-alt {
            margin-left: 190px;
            margin-top: -50px
        }

        .modal-content form {
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* Align items to the left */
    gap: 10px; /* Space between items */
}

.modal-content button {
    margin-left: 0; /* Remove the margin applied earlier */
    display: inline-block; /* Keep buttons inline */
}

#deleteForm button {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: transparent;
    border: none;
    cursor: pointer;
    margin-left: 0;
}

#uploadForm button{
        margin-left: 90px;
}

    </style>
</head>

<body>
    <!-- Profile Container -->
    <div class="container">
        <img src="../images/Customer.png" alt="Profile Icon" class="icon" id="profileIcon">
        <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-content">
            <a href=" ../profile_redirect.php"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        <a href="dashboard_std.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="job.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="userapp.php"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php" class="active"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="feedbackview.php"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="sub-sidebar">
<<<<<<< HEAD
<<<<<<< HEAD
            <div class="profile-picture">
                <img src="../images/Customer.png" alt="profile picture" id="sidebarProfilePicture" onclick="triggerFileInput()"> 
                <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="ProfilePicture(event)">
                <!-- UPLOAD button initially hidden -->
            </div>

            <button id="uploadButton" style="display: none;" onclick="uploadProfilePicture()">UPLOAD</button>
            <div class="text">
                <h4><?php echo htmlspecialchars($name); ?></h4> <!-- Students's name -->
            </div>

=======
        <div class="profile-picture" onmouseover="showEditButton()" onmouseout="hideEditButton()">
    <img src="../images/Customer.png" alt="profile picture" id="sidebarProfilePicture">
    <button id="editImageButton" style="display: none;" onclick="openModal()">Edit</button>
</div>
=======
            <div class="profile-picture" onmouseover="showEditButton()" onmouseout="hideEditButton()">
                <img src="../images/Customer.png" alt="profile picture" id="sidebarProfilePicture">
                <button id="editImageButton" style="display: none;" onclick="openModal()">EDIT</button>
            </div>
>>>>>>> 88861f1 (change)

            <!-- Modal Structure -->
            <div id="profileModal" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal()">&times;</span>
                    <h4>Profile Pic</h4>
                    <p>Use <a href="#" target="_blank">Background Removal</a> site for removing Background.<br>
                        Use 300 X 300 px image for profile pic.</p>

                    <!-- Form for file upload -->
                    <form id="uploadForm" action="picture.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="profilePicture" id="fileInput" accept="image/*" required>
                        <button type="submit" name="submit">Submit</button>
                    </form>

                    <!-- Delete Profile Picture Button -->
                    <form id="deleteForm" action="delete_pic.php" method="POST">
                        <input type="hidden" name="user_id"
                            value="<?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" name="delete" style="background-color:transparent">
                            <i class="fas fa-trash-alt" style="color: red; font-size: 24px;"></i>
                        </button>

                    </form>
                </div>
            </div>

<<<<<<< HEAD
                    <!-- Form for file upload -->
                    <form id="uploadForm" action="picture.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="profilePicture" id="fileInput" accept="image/*" required>
                        <button type="submit" name="submit">Submit</button>
                    </form>
                </div>
            </div>
<<<<<<< HEAD
        </div>
<<<<<<< HEAD
>>>>>>> def5bba (...)
=======
=======
>>>>>>> 72f94ac (done)
=======
>>>>>>> 2a8caa0 (..)

            <div class="text">
                <h4><?php echo htmlspecialchars($name); ?></h4> <!-- Admin's name -->
            </div>

<<<<<<< HEAD
>>>>>>> e055d5f (done)
         <!-- Profile Picture Section -->
=======
            <!-- Profile Picture Section -->
>>>>>>> 72f94ac (done)
            <div class="tabs">
                <div class="tab active" onclick="showSection('personal')">Personal Details</div>
                <div class="tab" onclick="window.location.href='academic_redirect.php'">Academic Details</div>
                <div class="tab" onclick="window.location.href='resume_redirect.php'">Resume</div>
            </div>
        </div>

        <!-- Personal Details Section -->
        <div id="personal" class="details active">
            <form action="editpersonal.php" method="post"> <!-- Change action to point to the view details script -->
<<<<<<< HEAD
            <table>     
                <tr>
                    <td>Branch:</td>
                    <td><input type="text" name="course_branch" value="<?php echo htmlspecialchars($course_branch); ?>" readonly/></td>
                </tr>
                <tr>
                    <td>Course </td>
                    <td><input type="text" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>"readonly /></td>
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
                    <td>Gender </td>
                    <td><input type="text" name="gender" value="<?php echo htmlspecialchars($gender); ?>" readonly/></td>
                </tr>
                <tr>
                    <td>Date of Birth:</td>
                    <td><input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>" readonly/></td>
                </tr>
            </table>
            <h4>Contact Information</h4>
            <table>
                <tr>
                    <td>Phone Number </td><td><input type="text" id="number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" readonly/></td>
                </tr>
                <tr>
                    <td>Email </td> <td><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly/></td>
                </tr>
            </table>
           
            <div class="button-container">
            <button type="submit">EDIT</button>
            </div>
<<<<<<< HEAD
<<<<<<< HEAD
            </form>
        </div>
    </div>
=======
            </div>
>>>>>>> 4171639 (..)
=======
        </div>
    </div>    
>>>>>>> e055d5f (done)
=======
                <table>
                    <tr>
                        <td>Branch:</td>
                        <td><input type="text" name="course_branch"
                                value="<?php echo htmlspecialchars($course_branch); ?>" readonly /></td>
                    </tr>
                    <tr>
                        <td>Course </td>
                        <td><input type="text" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>"
                                readonly /></td>
                    </tr>
                    <tr>
                        <td>Current Year </td>
                        <td><input type="text" name="current_year"
                                value="<?php echo htmlspecialchars($current_year); ?>" readonly /></td>
                    </tr>
                    <tr>
                        <td>Pass Out Year </td>
                        <td><input type="text" name="graduation_year"
                                value="<?php echo htmlspecialchars($graduation_year); ?>" readonly /></td>
                    </tr>
                    <tr>
                        <td>Gender </td>
                        <td><input type="text" name="gender" value="<?php echo htmlspecialchars($gender); ?>"
                                readonly /></td>
                    </tr>
                    <tr>
                        <td>Date of Birth:</td>
                        <td><input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>" readonly /></td>
                    </tr>
                </table>
                <h4>Contact Information</h4>
                <table>
                    <tr>
                        <td>Phone Number </td>
                        <td><input type="text" id="number" name="phone_number"
                                value="<?php echo htmlspecialchars($phone_number); ?>" readonly /></td>
                    </tr>
                    <tr>
                        <td>Email </td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly />
                        </td>
                    </tr>
                </table>

                <div class="button-container">
                    <button type="submit">EDIT</button>
                </div>
        </div>
    </div>
>>>>>>> 83d30e0 (set)
    <!-- JavaScript -->
    <script>
        function loadProfilePicture() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_profilepicture.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var profilePath = xhr.responseText.trim();
                    document.getElementById('sidebarProfilePicture').src = profilePath;
                    document.getElementById('profileIcon').src = profilePath;
                }
            };
            xhr.send();
        }

        window.onload = loadProfilePicture;
        function showEditButton() {
            document.getElementById('editImageButton').style.display = 'block';
        }

        function hideEditButton() {
            document.getElementById('editImageButton').style.display = 'none';
        }

        function openModal() {
            document.getElementById('profileModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('profileModal').style.display = 'none';
        }

        function uploadProfilePicture() {
            // Implement file upload logic here
            alert('Upload functionality goes here');
        }

        // Change Profile Picture
        function triggerFileInput() {
            document.getElementById('fileInput').click();
        }

        function changeProfilePicture(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profileIcon').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function ProfilePicture(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('sidebarProfilePicture').src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Display the UPLOAD button after selecting the file
                document.getElementById('uploadButton').style.display = 'inline-block';
            }
        }

        // Dropdown toggle with smooth opening
        function toggleDropdown() {
            const dropdown = document.getElementById("dropdownMenu");
            dropdown.classList.toggle("show");
        }

        // Hide dropdown on click outside
        window.onclick = function (event) {
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