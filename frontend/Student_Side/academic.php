<?php
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Retrieve user_id from session
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in.");
    }
    $user_id = $_SESSION['user_id'];

    // Retrieve form data
    $cgpa = $_POST['cgpa'];
    $current_arrears = $_POST['current_arrears'];

    // Prepare and execute SQL query to update STUDENT table
    $sql_update = "UPDATE STUDENT 
                   SET cgpa = ?, 
                       current_arrears = ?
                   WHERE user_id = ?";
    $stmt = $conn->prepare($sql_update);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("dss", $cgpa, $current_arrears, $user_id);
    if ($stmt->execute()) {
        
    } else {
        echo "Error updating student record: " . $stmt->error;
    }

    // Retrieve additional form data for academic details
    $school_tenth = $_POST['school_name_tenth'];
    $board_tenth = $_POST['board_tenth'];
    $percentage_tenth = $_POST['percentage_tenth'];
    $year_tenth = $_POST['pass_out_year_tenth'];
    
    $school_twelfth = $_POST['school_name_twelfth'];
    $board_twelfth = $_POST['board_twelfth'];
    $percentage_twelfth = $_POST['percentage_twelfth'];
    $year_twelfth = $_POST['pass_out_year_twelfth'];

    // Prepare and execute SQL query to insert into ACADEMIC_DETAILS table
    $sql_insert_academic = "INSERT INTO ACADEMIC_DETAILS 
                            (user_id, school_tenth, board_tenth, percentage_tenth, year_tenth, 
                             school_twelfth, board_twelfth, percentage_twelfth, year_twelfth) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert_academic);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssssssss", $user_id, $school_tenth, $board_tenth, $percentage_tenth, $year_tenth, 
                      $school_twelfth, $board_twelfth, $percentage_twelfth, $year_twelfth);
    if ($stmt->execute()) {
        header("Location: academicview.php"); // Change "anotherfile.php" to the desired file
        exit(); 
    } else {
        echo "Error inserting academic details: " . $stmt->error;
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
    <title>Campus Recruitment System</title>
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
        }

        .main-content h1 {
            color: #050505;
            font-size: 2.5rem; /* Increased font size */
            font-weight: bold;
            padding-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .icon {
            margin-left: 1px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .icon:hover {
            transform: scale(1.1);
        }

        img{
            height: 40px;
            width:auto;
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

        .profile-picture {
            width: 200px; /* Adjust width as needed */
            height: 200px; /* Ensure height equals width for a square */
            border-radius: 10px;/* Make it circular; use 0% for square */
            overflow: hidden; /* Hide overflow for perfect circle */
            border: 3px solid #1e3d7a; /* Optional border for profile picture */
            margin-bottom: 20px; /* Space below profile picture */
            position: relative;
            display: inline-block;
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
            height: 260px;
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .modal button{
            margin-left: 120px;
            margin-top: 5px;
        }        

        .close-button {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #000;
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
        <div class="profile-picture" onmouseover="showEditButton()" onmouseout="hideEditButton()">
                <img src="../images/Customer.png" alt="profile picture" id="sidebarProfilePicture">
                <button id="editImageButton" style="display: none;" onclick="openModal()">EDIT</button>
            </div>

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
                </div>
            </div>

         <!-- Profile Picture Section -->
            <div class="tabs">
                <div class="tab" onclick="window.location.href=' ../profile_redirect.php'">Personal Details</div>
                <div class="tab active" onclick="showSection('academic')">Academic Details</div>
                <div class="tab" onclick="window.location.href='resume_redirect.php'">Resume</div>
            </div>
        </div>

        <!-- Academic Details Section -->
        <div id="academic" class="details ">
            <form action="academic.php" method="post" onsubmit="return validateForm2()">
                <table>
                    <th>UG Details</th>
                    <tr>
                        <td>Branch<span style="color:red;">*</span></td>
                        <td><select name="branch" id="branch">
                                <option value="CS">Computer Science</option>
                                <option value="COMMERCE">Commerce</option>
                                <option value="ENGLISH">English</option>
                                <option value="PHYSICAL SCIENCES">Physical Sciences</option>
                                <option value="PHYSICS">Physics</option>
                                <option value="VM">Visual Media</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Course<span style="color:red;">*</span></td>
                        <td><select name="course" id="course">
                                <option value="BCA">BCA</option>
                                <option value="BCA DataScience">BCA Data Science</option>
                                <option value="Int MCA">INT MCA</option>
                                <option value="B.com taxation and finance">B.com Taxation and Finance</option>
                                <option value="BBA">BBA</option>
                                <option value="B.com Fintech">B.com Fintech</option>
                                <option value="BA English and Literature">BA English and Literature</option>
                                <option value="INT MA English and Literature">INT MA English and Literature</option>
                                <option value="INT M.Sc Mathematics">INT M.Sc Mathematics</option>
                                <option value="B.des(Hons.) in Communicative Design">B.des(Hons.) in Communicative Design</option>
                                <option value="B.Sc in Visual Media">B.Sc in Visual Media</option>
                                <option value="BCA(Hons.)">BCA(Hons.)</option>
                                <option value="B.Com.(Hons.) in Taxation & Finance">B.Com.(Hons.) in Taxation & Finance</option>
                                <option value="B.Com(Hons.) in FinTech">B.Com(Hons.) in FinTech</option>
                                <option value="BBA(Hons./Hons. with Research)">BBA(Hons./Hons. with Research)</option>
                                <option value="B.Sc(Hons.) in Visual Media">B.Sc(Hons.) in Visual Media</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Current Year<span style="color:red;">*</span></td>
                        <td><select name="current_year" id="current_year">
                                <option value="">Select year</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Pass Out Year<span style="color:red;">*</span></td>
                        <td> <select name="pass_out_year" id="pass_out_year">
                                <option value="">Select Year</option> <!-- Empty option for prompt -->
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Current Arrears<span style="color:red;">*</span></td>
                        <td><input type="text" id="current_arrears" name="current_arrears"></td>
                    </tr>
                    <tr>
                        <td>CGPA<span style="color:red;">*</span></td>
                        <td>
                            <input type="number" id="cgpa" name="cgpa" step="0.01">
                            <div id="cgpa-error" class="error-message" style="color:red; font-size:12px;"></div> <!-- Error below input -->
                        </td>
                    </tr>
                </table>
                <br>
                <table>
                    <th>12th Details</span></th>
                    <tr>
                        <td>School Name<span style="color:red;">*</span> </td>
                        <td><input type="text" id="school_name_twelfth" name="school_name_twelfth"></td>
                    </tr>
                    <tr>
                        <td>Board<span style="color:red;">*</span></td><td>
                            <input type="text" id=board_twelfth name="board_twelfth">
                        </td>
                    </tr>
                    <tr>
                        <td>Pass Out Year<span style="color:red;">*</span></td>
                        <td><input type="text" id=pass_out_year_twelfth name="pass_out_year_twelfth"></td>
                    </tr>
                    <tr>
                        <td>Percentage<span style="color:red;">*</span></td>
                        <td>
                            <input type="text" id="percentage_twelfth" name="percentage_twelfth"step="0.01">
                            <div id="percentage12th-error" class="error-message" style="color:red; font-size:12px;"></div> <!-- Error below input -->
                        </td>
                    </tr>
                </table>
                <br>
                <table>
                    <th>10th Details<span style="color:red;">*</span></th>
                    <tr>
                        <td>School Name<span style="color:red;">*</span></td>
                        <td><input type="text" id="school_name_tenth" name="school_name_tenth"></td>
                    </tr>
                    <tr>
                        <td>Board<span style="color:red;">*</span></td><td>
                            <input type="text" id=board_tenth name="board_tenth">
                        </td>
                    </tr>
                    <tr>
                        <td>Pass Out Year<span style="color:red;">*</span></td>
                        <td><input type="text" id=pass_out_year_tenth name="pass_out_year_tenth"></td>
                    </tr>
                    <tr>
                        <td>Percentage<span style="color:red;">*</span></td>
                        <td>
                            <input type="text" id="percentage_tenth" name="percentage_tenth"step="0.01">
                            <div id="percentage10th-error" class="error-message" style="color:red; font-size:12px;"></div> <!-- Error below input -->
                        </td>
                    </tr>
                </table>
                <div class="button-container">
                    <button type="submit">SAVE</button>
                </div>
            </form>
        </div>
    </div>

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
        // 
      
    function validateDOB() {
        const dob = document.getElementById('dob').value;
        const dobError = document.getElementById('dob-error');
        const minDate = new Date('1990-01-01');
        const maxDate = new Date('2009-01-01');
        const selectedDate = new Date(dob);

        if (selectedDate < minDate || selectedDate > maxDate) {
            dobError.textContent = "Date of birth must be between 1st Jan 1990 and 1st Jan 2009.";
            return false;
        } else {
            dobError.textContent = ""; // Clear error
            return true;
        }
    }

    // Validate Phone Number
    function validatePhone() {
        const phone = document.getElementById('number').value;
        const phoneError = document.getElementById('phone-error');
        const phoneRegex = /^[0-9]{10}$/; // Regex for 10 digits

        if (!phoneRegex.test(phone)) {
            phoneError.textContent = "Phone number must be a 10-digit number.";
            return false;
        } else {
            phoneError.textContent = ""; 
            return true;// Clear error
        }
    }

    // Validate Email
    function validateEmail() {
        const email = document.getElementById('email').value;
        const emailError = document.getElementById('email-error');
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/; // Simple email regex

        if (!emailRegex.test(email)) {
            emailError.textContent = "Please enter a valid email address.";
            return false;
        } else {
            emailError.textContent = "";
            return true; // Clear error
        }
    }
    function isNumeric(value) {
        return !isNaN(value) && value.trim() !== ""; // Check if the value is a number and not empty
    }

    function validateCGPA() {
        const cgpa = document.getElementById('cgpa');
        const errorContainer = document.getElementById('cgpa-error');
    
        // Clear previous error message
        errorContainer.textContent = ""; 

        // Check if CGPA is a numeric value
        if (!isNumeric(cgpa.value)) {
            errorContainer.textContent = "CGPA must be a numeric value.";
            return false; // Validation failed
        }

         // Check if CGPA is within the range of 0 to 10
        const cgpaValue = parseFloat(cgpa.value);
        if (cgpaValue < 0 || cgpaValue > 10) {
            errorContainer.textContent = "CGPA must be between 0 and 10.";
            return false; // Validation failed
        }
        return true; // Validation passed
    }

    function validatePercentage12th() {
        const percentage12th = document.getElementById('percentage_twelfth');
        const errorContainer = document.getElementById('percentage12th-error');
    
        // Clear previous error message
        errorContainer.textContent = ""; 

        if (!isNumeric(percentage12th.value)) {
            errorContainer.textContent = "Percentage in 12th must be a numeric value.";
            return false; // Validation failed
        }
        return true; // Validation passed
    }

    function validatePercentage10th() {
        const percentage10th = document.getElementById('percentage_tenth');
        const errorContainer = document.getElementById('percentage10th-error');
    
        // Clear previous error message
        errorContainer.textContent = ""; 

        if (!isNumeric(percentage10th.value)) {
            errorContainer.textContent = "Percentage in 10th must be a numeric value.";
            return false; // Validation failed
        }
        return true; // Validation passed
    }
    window.onload = function() {
        document.getElementById('cgpa').onblur = validateCGPA;
        document.getElementById('percentage_twelfth').onblur = validatePercentage12th;
        document.getElementById('percentage_tenth').onblur = validatePercentage10th;
    };


    function validateForm2() {
        let isValid = true;
        const errorMessage = "All fields are required.";

        // Clear previous error messages
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(function (element) {
            element.textContent = ""; // Clear any previous error message
        });

        // Get all required fields and validate them
        const branch = document.getElementById('branch').value.trim();
        const course = document.getElementById('course').value.trim();
    
        const currentArrears = document.getElementById('current_arrears').value.trim();
        const cgpa = document.getElementById('cgpa').value.trim();

        const schoolName12 = document.getElementById('school_name_twelfth').value.trim();
        const board12 = document.getElementById('board_twelfth').value.trim();
        const passOutYear12 = document.getElementById('pass_out_year_twelfth').value.trim();
        const percentage12 = document.getElementById('percentage_twelfth').value.trim();

        const schoolName10 = document.getElementById('school_name_tenth').value.trim();
        const board10 = document.getElementById('board_tenth').value.trim();
        const passOutYear10 = document.getElementById('pass_out_year_tenth').value.trim();
        const percentage10 = document.getElementById('percentage_tenth').value.trim();

        // Validate UG fields
        if ( !currentArrears || !cgpa) {
            document.getElementById('cgpa-error').textContent = errorMessage;
            isValid = false;
        }

        // Validate 12th fields
        if (!schoolName12 || !board12 || !passOutYear12 || !percentage12) {
            document.getElementById('percentage12th-error').textContent = errorMessage;
            isValid = false;
        }

        // Validate 10th fields
        if (!schoolName10 || !board10 || !passOutYear10 || !percentage10) {
            document.getElementById('percentage10th-error').textContent = errorMessage;
            isValid = false;
        }
        return isValid; // Form submission will only proceed if true
    }

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