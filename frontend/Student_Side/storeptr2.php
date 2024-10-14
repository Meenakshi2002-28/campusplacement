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
    $current_year = htmlspecialchars(trim($_POST['year']));
    $dob = htmlspecialchars(trim($_POST['dob']));

    // Check if any required field is empty
    if (empty($gender) || empty($course_name) || empty($branch) || empty($email) || empty($phone_number) || empty($graduation_year) || empty($current_year) || empty($dob)) {
        echo "All fields are required.";
        exit; // Stop script execution and return a graceful message
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
            echo "Record successfully updated.";
        } else {
            echo "No changes made. Ensure the data is different from existing values.";
        }
    } else {
        echo "Error: " . $stmt->error;
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
        <a href="#home"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="#jobs"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="#applications"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="#company"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="#profile"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="#feedback"><i class="fa fa-fw fa-comment"></i> Feedback</a>
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
        <form action="storepr_std.php" method="post">
            <table>
                
                <tr><td>Bran</td>
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
                <tr><td>Course </td>
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
            <div class="button-container">
              
                <button type="submit">SAVE</button>
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
    <td>Branch<span style="color:red">*</span></td>
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
    <td>Course</td>
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
                    <td>Current Year </td><td><input type="text" id="current_year" name="year"></td>
                </tr>
                <tr>
                    <td>Pass Out Year </td><td><input type="text" id="pass_out_year" name="pass_out_year"></td>
                </tr>
                <tr>
                    <td>Current Arrears </td><td><input type="text" id="current_arrears" name="current_arrears"></td>
                </tr>
                <tr>
                    <td>CGPA </td><td><input type="number" id="cgpa" name="cgpa"></td>
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
    </script>

   

   

</body>
</html>


