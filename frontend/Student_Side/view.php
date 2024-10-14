<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

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
    padding-left: 300px;
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
.logout a {
            font-size: 20px;
            margin-top: 210px;
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
        <a href="dashboard_std.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="jobs.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="#applications"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="#company"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="#feedback"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div> 

    <!--Sub SideBar-->
    <div class="sub-sidebar">
        <div class="profile">
            <img src="../images/Customer.png" alt="profile picture">
            <div class="text">
                <h4><?php echo htmlspecialchars($name); ?></h4>
                <p><?php echo htmlspecialchars($user_id); ?></p>
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
        <form action="view.php" method="post"> <!-- Change action to point to the view details script -->
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
            <a href="edit_profile.php"><button>Edit Profile</button></a>
        </div>
        </form>
    </div>
    <!-- Academic Details Section -->
    <div id="academic" class="details">
        <h2>Academic Details</h2>
        <form action="1.php" method="post">
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
              
              <button type="submit">SAVE</button>
          </div>
</form>
</div>
<!-- Resume Section -->
<<!-- Resume Section -->
<div id="resume" class="details">
    <h2>Resume</h2>

    <?php
    // Fetch the resume path from the STUDENT table
    $user_id = $_SESSION['user_id'];
    $conn = new mysqli($servername, $username, $password, $dbname);
    $sql = "SELECT resume FROM STUDENT WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($resume_path);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Check if the resume exists
    if (!empty($resume_path) && file_exists($resume_path)) {
        echo "<iframe src='$resume_path' width='100%' height='600px'></iframe>"; // Display PDF in an iframe
    } else {
      
        echo '<p>Upload your resume here.</p>';
        echo '<form action="upload_resume.php" method="POST" enctype="multipart/form-data">';
        echo '<input type="file" id="resume_file" name="resume_file" required>';
        echo '<div class="button-container">';
        echo '<button type="submit">SAVE</button>';
        echo '</div>';
        echo '</form>';
    }
    ?>
</div>


    <script>
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

