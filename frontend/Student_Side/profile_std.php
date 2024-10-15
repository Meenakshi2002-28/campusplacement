<?php
// Database connection
$servername = "localhost"; // Change this to your actual server details
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "campus_placement"; // Change to your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume the user_id is stored in session after the user logs in
session_start();
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to update your profile.");
}

$user_id = $_SESSION['user_id']; // The user_id of the logged-in student

// Handling the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting data from Personal Details form
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['number'];
    $cgpa = $_POST['cgpa'];
    $current_year = $_POST['year'];
    $pass_out_year = $_POST['pass_out_year'];
    $current_arrears = $_POST['current_arrears'];
    $dob = $_POST['dob'];
    
    // Handling file uploads (photo and resume)
    $photo = '';
    $resume = '';
    
    if (isset($_FILES['photo'])) {
        $photo = "uploads/" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }
    
    if (isset($_FILES['resume_file'])) {
        $resume = "uploads/" . basename($_FILES['resume_file']['name']);
        move_uploaded_file($_FILES['resume_file']['tmp_name'], $resume);
    }

    // Retrieve the selected course and branch from the dropdown
    $course_name = $_POST['course'];
    $course_branch = $_POST['branch'];

   

    // Query the course table to get the corresponding course_id based on course name and branch
    $sql_get_course_id = "SELECT course_id FROM course WHERE course_name='$course_name' AND branch_name='$course_branch'";
    $result_course = $conn->query($sql_get_course_id);

    if ($result_course->num_rows > 0) {
        // If a matching course is found, fetch the course_id
        $row_course = $result_course->fetch_assoc();
        $course_id = $row_course['course_id'];
        
        // Now update the student table with the course_id
        $sql_update_student = "UPDATE STUDENT 
                               SET name='$name', gender='$gender', course_id='$course_id', email='$email', 
                                   phone_number='$phone_number', cgpa='$cgpa', current_year='$current_year', 
                                   graduation_year='$pass_out_year', current_arrears='$current_arrears', 
                                   dob='$dob', photo='$photo', resume='$resume' 
                               WHERE user_id='$user_id'";

        if ($conn->query($sql_update_student) === TRUE) {
            echo "Personal details updated successfully.";
        } else {
            echo "Error updating personal details: " . $conn->error;
        }
    } else {
        echo "Error: Course and Branch not found in the database.";
    }

    // Similar logic for academic details (as before)
    // Check if academic details exist for this user
    $sql_check_academic = "SELECT * FROM ACADEMIC_DETAILS WHERE user_id='$user_id'";
    $result_academic = $conn->query($sql_check_academic);

    // Getting data from Academic Details form
    $school_tenth = $_POST['school_name_tenth'];
    $board_tenth = $_POST['board_tenth'];
    $percentage_tenth = $_POST['percentage_tenth'];
    $year_tenth = $_POST['pass_out_year_tenth'];
    
    $school_twelfth = $_POST['school_name_twelfth'];
    $board_twelfth = $_POST['board_twelfth'];
    $percentage_twelfth = $_POST['percentage_twelfth'];
    $year_twelfth = $_POST['pass_out_year_twelfth'];

    if ($result_academic->num_rows > 0) {
        // If academic details already exist, update them
        $sql_update_academic = "UPDATE ACADEMIC_DETAILS 
                                SET school_tenth='$school_tenth', board_tenth='$board_tenth', 
                                    percentage_tenth='$percentage_tenth', year_tenth='$year_tenth', 
                                    school_twelfth='$school_twelfth', board_twelfth='$board_twelfth', 
                                    percentage_twelfth='$percentage_twelfth', year_twelfth='$year_twelfth' 
                                WHERE user_id='$user_id'";

        if ($conn->query($sql_update_academic) === TRUE) {
            echo "Academic details updated successfully.";
        } else {
            echo "Error updating academic details: " . $conn->error;
        }
    } else {
        // If no academic details exist, insert new data
        $sql_insert_academic = "INSERT INTO ACADEMIC_DETAILS (user_id, school_tenth, board_tenth, percentage_tenth, year_tenth, 
                                  school_twelfth, board_twelfth, percentage_twelfth, year_twelfth) 
                                VALUES ('$user_id', '$school_tenth', '$board_tenth', '$percentage_tenth', '$year_tenth', 
                                        '$school_twelfth', '$board_twelfth', '$percentage_twelfth', '$year_twelfth')";

        if ($conn->query($sql_insert_academic) === TRUE) {
            echo "Academic details inserted successfully.";
        } else {
            echo "Error inserting academic details: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Student</title>
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
    padding: 0;
    margin: 0;
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
}

img {
    height: 40px;
    width: auto;
}

.icon {
    margin-left: 1px;
}
.logo-container {
        position: absolute;
        top: 10px;
        left: 10px;
        }
        .logo {
        height: 50px;
        width: auto;
        }
    </style>
</head>
<body>
<div class="logo-container">
        <img src="../images/logo1.png" alt="Logo" class="logo">
    </div>
    <!--Header_profile-->
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">

        <i class="fas fa-caret-down fa-2x" aria-hidden="true" onclick="toggleDropdown()"></i>
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../profile_std.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>  

    <!--Main Side Bar-->
    <div class="sidebar">
        <a href="dashboard_std.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="jobs.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="#applications"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="feedback.html"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
        <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
    </div>
    </div> 

    <!--Sub SideBar-->
    <div class="sub-sidebar">
        <div class="profile">
            <img src="../images/Customer.png" alt="profile picture">
            <div class="text">
                <h4></h4>
                <p></p>
            </div>
        </div>
        <div class="menu">
            <a href="#" onclick="showSection('personal')" class="active">Personal Details</a>
            <a href="#" onclick="showSection('academic')">Academic Details</a>
            <a href="#" onclick="showSection('resume')">Resume</a>
        </div>
    </div>

    <!-- Personal Details Section -->
    <div id="personal" class="details active">
        <h2>Personal Details</h2>
        <form action="profile_std.php"method="post" enctype="multipart/form-data">
            <table>
                
                <tr><td>Branch </td>
                    <td><select name="branch" id="branch">
                            <option value="CS">Computer Science</option>
                            <option value="Commerce">Commerce</option>
                            <option value="english">English</option>
                            <option value="physical sciences">Physical Sciences</option>
                            <option value="physics">Physics</option>
                            <option value="vm">Visual Media</option>
                        </select>
                    </td>
                </tr>
                <tr><td>Course </td>
                    <td><select name="course" id="course">
                            <option value="BCA">BCA</option>
                            <option value="BCA DataScience">BCA Data Science</option>
                            <option value="Int MCA">INT MCA</option>
                            <option value="bcom">B.com Taxation and Finance</option>
                            <option value="bba">BBA</option>
                            <option value="bcom fintech">B.com Fintech</option>
                            <option value="BA english">BA English and Literature</option>
                            <option value="Int ma">INT MA  English and Literature</option>
                            <option value="Int msc maths">INT M.Sc Mathematics</option>
                            <option value="Bdes">B.des(Hons.)in Communicative Design</option>
                            <option value="bsc in vm">B.Sc in Visual Media</option>
                            <option value="bca hons.">BCA(Hons.)</option>
                            <option value="bcom hons.">B.Com.(Hons.)in Taxation & Finance</option>
                            <option value="bcom fintech hons.">B.Com(Hons.)in FinTech</option>
                            <option value="bba hons.">BBA(Hons./Hons. with Research)</option>
                            <option value="bsc in vm hons">B.Sc(Hons.)in Visual Media</option>
                        </select>
                    </td>
                </tr>
                <tr><td>Current Year </td>
                    <td><input type="text" id="year" name="year"></td>
                </tr>
                <tr>
                    <td>Pass Out Year </td><td><input type="text" id="pass_out_year" name="pass_out_year"></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>
                        <div class="gender-options">
                            <label>
                                <input type="radio" name="gender" value="male"> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female"> Female
                            </label>
                            <label>
                                <input type="radio" name="gender" value="other"> Other
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Date of Birth </td><td><input type="date" id="dob" name="dob"></td>
                </tr>
            </table>
            <h4>Contact Information</h4>
            <table>
                <tr>
                    <td>Phone Number </td><td><input type="text" id="number" name="number"></td>
                </tr>
                <tr>
                    <td>Email </td><td><input type="text" id="email" name="email"></td>
                </tr>
            </table>
            
        </form>
    </div>

    <!-- Academic Details Section -->
    <div id="academic" class="details">
        <h2>Academic Details</h2>
        <form>
            <table>
                <th>UG Details</th>
                <tr><td>Branch </td>
                    <td><select name="branch" id="branch">
                            <option value="cs">Computer Science</option>
                            <option value="Commerce">Commerce</option>
                            <option value="english">English</option>
                            <option value="physical sciences">Physical Sciences</option>
                            <option value="physics">Physics</option>
                            <option value="vm">Visual Media</option>
                        </select>
                    </td>
                </tr>
                <tr><td>Course </td>
                    <td><select name="course" id="course">
                            <option value="BCA">BCA</option>
                            <option value="BCA DS">BCA Data Science</option>
                            <option value="Int MCA">INT MCA</option>
                            <option value="bcom">B.com Taxation and Finance</option>
                            <option value="bba">BBA</option>
                            <option value="bcom fintech">B.com Fintech</option>
                            <option value="BA english">BA English and Literature</option>
                            <option value="Int ma">INT MA  English and Literature</option>
                            <option value="Int msc maths">INT M.Sc Mathematics</option>
                            <option value="Bdes">B.des(Hons.)in Communicative Design</option>
                            <option value="bsc in vm">B.Sc in Visual Media</option>
                            <option value="bca hons.">BCA(Hons.)</option>
                            <option value="bcom hons.">B.Com.(Hons.)in Taxation & Finance</option>
                            <option value="bcom fintech hons.">B.Com(Hons.)in FinTech</option>
                            <option value="bba hons.">BBA(Hons./Hons. with Research)</option>
                            <option value="bsc in vm hons">B.Sc(Hons.)in Visual Media</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Current Year </td><td><input type="text" id="current_year" name="current_year"></td>
                </tr>
                <tr>
                    <td>Pass Out Year </td><td><input type="text" id="pass_out_year" name="pass_out_year"></td>
                </tr>
                <tr>
                    <td>Current Arrears </td><td><input type="text" id="current_arrears" name="current_arrears"></td>
                </tr>
                <tr>
                    <td>CGPA </td><td><input type="cgpa" id="cgpa" name="cgpa"></td>
                </tr>
            </table>
            <br>
            <table>
                <th>12th Details</th>
                <tr>
                    <td>School Name </td><td><input type="text" id="school_name_twelfth" name="school_name_twelfth"></td>
                </tr>
                <tr>
                    <td>Board </td><td><input type="text" id=board_twelfth name="board_twelfth"></td>
                </tr>
                <tr>
                    <td>Pass Out Year </td><td><input type="text" id=pass_out_year_twelfth name="pass_out_year_twelfth"></td>
                </tr>
                <tr>
                    <td>Percentage </td><td><input type="text" id=percentage_twelfth name="percentage_twelfth"></td>
                </tr>
            </table>
            <br>
            <table>
                <th>10th Details</th>
                <tr>
                    <td>School Name </td><td><input type="text" id="school_name_tenth" name="school_name_tenth"></td>
                </tr>
                <tr>
                    <td>Board </td><td><input type="text" id=board_tenth name="board_tenth"></td>
                </tr>
                <tr>
                    <td>Pass Out Year </td><td><input type="text" id=pass_out_year_tenth name="pass_out_year_tenth"></td>
                </tr>
                <tr>
                    <td>Percentage </td><td><input type="text" id=percentage_tenth name="percentage_tenth"></td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Resume Section -->
    <div id="resume" class="details">
        <h2>Resume</h2>
        <p>Upload your resume here.</p>
        <form>
            <input type="file" id="resume_file" name="resume_file">
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
        function showSection(sectionId) {
            document.querySelectorAll('.details').forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }

        function setActiveTab(event) {
            document.querySelectorAll('.menu a').forEach(item => item.classList.remove('active'));
            event.target.classList.add('active');
        }

        document.querySelectorAll('.menu a').forEach(item => {
            item.addEventListener('click', setActiveTab);
        });
    </script>

</body>
</html>


