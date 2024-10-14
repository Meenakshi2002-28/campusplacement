<!DOCTYPE html>
<html>
<head>
    <title>Personal Details</title>
</head>
<body>
    <?php
    // Enable error reporting to debug issues
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Array to store error messages
    $errors = [];

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $department = isset($_POST['department']) ? trim($_POST['department']) : '';
        $course = isset($_POST['course']) ? trim($_POST['course']) : '';
        $current_year = isset($_POST['current_year']) ? trim($_POST['current_year']) : '';
        $passing_out_year = isset($_POST['passing_out_year']) ? trim($_POST['passing_out_year']) : '';
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
        $dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
        $contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';

        // Validate required fields
        if (empty($department)) {
            $errors[] = "Department is required.";
        }
        if (empty($course)) {
            $errors[] = "Course is required.";
        }
        if (empty($current_year)) {
            $errors[] = "Current Year is required.";
        }
        if (empty($passing_out_year)) {
            $errors[] = "Passing Out Year is required.";
        }
        if (empty($gender)) {
            $errors[] = "Gender is required.";
        }
        if (empty($dob)) {
            $errors[] = "Date of Birth is required.";
        }
        if (empty($contact_number)) {
            $errors[] = "Contact Number is required.";
        }

        // If there are no errors, process the form data
        if (empty($errors)) {
            // You can add your database insert query here
            // Example: Store data in the database
            // $query = "INSERT INTO students (department, course, current_year, passing_out_year, gender, dob, contact_number)
            //           VALUES ('$department', '$course', '$current_year', '$passing_out_year', '$gender', '$dob', '$contact_number')";
            // mysqli_query($conn, $query);

            echo "<p style='color:green;'>Details saved successfully!</p>";
        } else {
            // Display errors
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li style='color:red;'>$error</li>";
            }
            echo "</ul>";
        }
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
            <table></table>
    <form method="POST" action="">
        <h3>Personal Details</h3>

        <!-- Department (Mandatory) -->
        <label for="department">Department <span style="color:red;">*</span>:</label>
        <input type="text" id="department" name="department" value="<?php echo isset($_POST['department']) ? $_POST['department'] : ''; ?>">
        <br>

        <!-- Course (Mandatory) -->
        <label for="course">Course <span style="color:red;">*</span>:</label>
        <input type="text" id="course" name="course" value="<?php echo isset($_POST['course']) ? $_POST['course'] : ''; ?>">
        <br>

        <!-- Current Year (Mandatory) -->
        <label for="current_year">Current Year <span style="color:red;">*</span>:</label>
        <input type="text" id="current_year" name="current_year" value="<?php echo isset($_POST['current_year']) ? $_POST['current_year'] : ''; ?>">
        <br>

        <!-- Passing Out Year (Mandatory) -->
        <label for="passing_out_year">Passing Out Year <span style="color:red;">*</span>:</label>
        <input type="text" id="passing_out_year" name="passing_out_year" value="<?php echo isset($_POST['passing_out_year']) ? $_POST['passing_out_year'] : ''; ?>">
        <br>

        <!-- Gender (Mandatory) -->
        <label for="gender">Gender <span style="color:red;">*</span>:</label>
        <input type="radio" name="gender" value="Male" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Male') echo 'checked'; ?>> Male
        <input type="radio" name="gender" value="Female" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Female') echo 'checked'; ?>> Female
        <input type="radio" name="gender" value="Other" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Other') echo 'checked'; ?>> Other
        <br>

        <!-- Date of Birth (Mandatory) -->
        <label for="dob">Date of Birth <span style="color:red;">*</span>:</label>
        <input type="date" id="dob" name="dob" value="<?php echo isset($_POST['dob']) ? $_POST['dob'] : ''; ?>">
        <br>

        <!-- Contact Number (Mandatory) -->
        <label for="contact_number">Contact Number <span style="color:red;">*</span>:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?php echo isset($_POST['contact_number']) ? $_POST['contact_number'] : ''; ?>">
        <br>

        <button type="submit" name="submit">Save</button>
    </form>
</body>
</html>
