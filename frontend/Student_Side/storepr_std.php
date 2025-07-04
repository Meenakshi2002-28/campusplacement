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

    // Retrieve and sanitize form data
    $gender = htmlspecialchars(trim($_POST['gender']));
    $course_name = htmlspecialchars(trim($_POST['course']));
    $branch = htmlspecialchars(trim($_POST['branch']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['number']));
    $graduation_year = htmlspecialchars(trim($_POST['pass_out_year']));
    $current_year = htmlspecialchars(trim($_POST['current_year']));
    $dob = htmlspecialchars(trim($_POST['dob']));

    // Check if any required field is empty
    if (empty($gender) || empty($course_name) || empty($branch) || empty($email) || empty($phone_number) || empty($graduation_year) || empty($current_year) || empty($dob))
     {
        echo "All fields are required.";
        // Stop script execution and return a graceful message
    }
    
    // Prepare statement to get course_id based on course_name and branch
    $sql = "SELECT course_id FROM course WHERE course_name = ? AND course_branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $course_name, $branch);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($course_id);
    $stmt->fetch();
    $stmt->close();

    // Check if a course_id was found
    if (!$course_id) {
        echo "Invalid course or branch selected.";
        exit; // Stop script execution and return a graceful message
    }

    // Prepare and bind statement for updating data in STUDENT table
    $sql = "UPDATE STUDENT 
            SET gender = ?, 
                course_id = ?, 
                email = ?, 
                phone_number = ?, 
                graduation_year = ?, 
                current_year = ?, 
                dob = ? 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $gender, $course_id, $email, $phone_number, $graduation_year, $current_year, $dob, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: storepr_std.php"); // Change "anotherfile.php" to the desired file
            exit(); // Always use exit() after header redirection
        } else {
            echo "<script>displayMessage('No changes made. Ensure the data is different from existing values.');</script>";
        }
    } else {
        echo "<script>displayMessage('Error: " . $stmt->error . "');</script>";
    }

    
    // Close connection
    $stmt->close();
    $conn->close();
}
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

.message-box {
    display: none; /* Hidden by default */
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #28a745; /* Success message color */
    color: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    font-size: 18px;
    text-align: center;
    z-index: 1000;
}

.message-box i {
    margin-left: 10px;
    cursor: pointer;
}
.logout a {
            font-size: 20px;
            margin-top: 210px;
            padding-left: 5px;
            padding-right: 75px;
        }
        .error-message {
            color: red;
            font-size: 12px; /* Make the error message text smaller */
            margin-top: 5px; /* Add space between input and error message */
        }

    </style>
</head>
<body>
    <!--Header_profile-->
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon">
        <img src="../images/down_arrow.png" alt="Expand Arrow" class="icon">
    </div>

    <!--Main Side Bar-->
    <div class="sidebar">
        <a href="dashboard_std.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="jobs.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="#applications"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
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
        <form action="storepr_std.php" method="post" onsubmit="return validateForm()">
        <div id="form-error" class="error-message" style="color:red;"></div>
            <table>
                 <tr>
                    <td>Branch<span style="color:red;">*</span></td>
                    <td><select name="branch" id="branch">
                    <option value="">Select a branch</option>
                            <option value="CS">Computer Science</option>
                            <option value="COMMERCE">Commerce</option>
                            <option value="ENGLISH">English</option>
                            <option value="PHYSICAL SCIENCES">Physical Sciences</option>
                            <option value="PHYSICS">Physics</option>
                            <option value="VM">Visual Media</option>
                        </select>
                    </td>
                </tr>
                <tr><td>Course<span style="color:red;">*</span></td>
                    <td><select name="course" id="course">
                    <option value="">Select a course</option>
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
                <tr><td>Current Year<span style="color:red;">*</span></td>
                    <td><select name="current_year" id="current_year">
                    <option value="">Select year</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </td>
                </tr>
                <tr>
                    <td>Pass Out Year<span style="color:red;">*</span> </td>
                    <td>
        <select name="pass_out_year" id="pass_out_year">
            <option value="">Select Year</option> <!-- Empty option for prompt -->
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
        </select>
    </td>
                </tr>
                <tr>
                    <td>Gender<span style="color:red;">*</span></td>
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
                    <td>Date of Birth<span style="color:red;">*</span></td><td><input type="date" id="dob" name="dob"onblur="validateDOB()"></td>
                    <div id="dob-error" class="error-message"></div>
                </tr>
            </table>
            <h4>Contact Information</h4>
            <table>
                <tr>
                    <td>Phone Number<span style="color:red;">*</span> </td><td><input type="text" id="number" name="number"onblur="validatePhone()"></td>
                    <div id="phone-error" class="error-message"></div> <!-
                </tr>
                <tr>
                    <td>Email<span style="color:red;">*</span></td><td><input type="text" id="email" name="email"onblur="validateEmail()"></td>
                    <div id="email-error" class="error-message"></div> 
                </tr>
            </table>
            <div class="button-container">
              
                <button type="submit">SAVE</button>
            </div>
        </form>
        <div id="messageBox" class="message-box">
          Record updated successfully.
      <i class="fas fa-times" onclick="closeMessage()"></i> <!-- Font Awesome icon for close button -->
</div>

         

    </div>
      <!-- Academic Details Section -->
      <div id="academic" class="details">
        <h2>Academic Details</h2>
        <form action="1.php" method="post" onsubmit="return validateForm2()">
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
                        </select></td>
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
                    </select></td>
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
        </select></td>
                </tr>
                <tr>
                    <td>Current Arrears<span style="color:red;">*</span></td><td><input type="text" id="current_arrears" name="current_arrears"></td>
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
                    <td>School Name<span style="color:red;">*</span> </td><td><input type="text" id="school_name_twelfth" name="school_name_twelfth"></td>
                </tr>
                <tr>
                    <td>Board<span style="color:red;">*</span></td><td><input type="text" id=board_twelfth name="board_twelfth"></td>
                </tr>
                <tr>
                    <td>Pass Out Year<span style="color:red;">*</span></td><td><input type="text" id=pass_out_year_twelfth name="pass_out_year_twelfth"></td>
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
                    <td>School Name<span style="color:red;">*</span></td><td><input type="text" id="school_name_tenth" name="school_name_tenth"></td>
                </tr>
                <tr>
                    <td>Board<span style="color:red;">*</span></td><td><input type="text" id=board_tenth name="board_tenth"></td>
                </tr>
                <tr>
                    <td>Pass Out Year<span style="color:red;">*</span></td><td><input type="text" id=pass_out_year_tenth name="pass_out_year_tenth"></td>
                </tr>
                <tr>
                    <td>Percentage<span style="color:red;">*</span></td>
                    <td>
                    <input type="text" id="percentage_tenth" name="percentage_tenth"step="0.01">
                    <div id="percentage10th-error" class="error-message" style="color:red; font-size:12px;"></div> <!-- Error below input -->
                </td>
            </table>
            <div class="button-container">
              
              <button type="submit">SAVE</button>
          </div>
        </form>
    </div>
     <!-- Resume Section -->
     <div id="resume" class="details">
        <h2>Resume</h2>
        <p>Upload your resume here.</p>
        <form action="upload_resume.php" method="POST" enctype="multipart/form-data">
        <input type="file" id="resume_file" name="resume_file">
        <div class="button-container">
            <button type="submit">SAVE</button>
        </div>
    </form>
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
function validateForm() {
    let isValid = true;
    const errorContainer = document.getElementById('form-error');
    errorContainer.textContent = ""; // Clear previous error message

    // Check if branch is selected
    const branch = document.getElementById('branch').value;
    if (branch === "") {
        isValid = false;
    }

    // Check if course is selected
    const course = document.getElementById('course').value;
    if (course === "") {
        isValid = false;
    }

    // Check if current year is filled
    const current_year = document.getElementById('current_year').value; // Corrected ID
    if (current_year === "") {
        isValid = false;
    }

    // Check if pass out year is filled
    const passOutYear = document.getElementById('pass_out_year').value;
    if (passOutYear === "") {
        isValid = false;
    }

    // Check if gender is selected
    const gender = document.querySelector('input[name="gender"]:checked');
    if (!gender) {
        isValid = false;
    }

    // If any field is missing, display the unified error message
    if (!isValid) {
        errorContainer.textContent = "All fields are required."; // Unified error message
    }

    return isValid; // If all validations pass, form will submit
}


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


    </script>
</body>
</html>


