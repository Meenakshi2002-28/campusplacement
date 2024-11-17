<?php
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
$show_success_message = false;

// Check if job_id is set for editing
$job = null;
$selected_courses = []; // Array to hold selected courses
if (isset($_GET['job_id'])) {
    $job_id = (int)$_GET['job_id'];

    // Fetch job details from the database
    $query = "SELECT * FROM JOB WHERE job_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();

        // Fetch eligible courses for the job
        $course_query = "SELECT course.course_name FROM job_course 
                         JOIN course ON job_course.course_id = course.course_id 
                         WHERE job_course.job_id = ?";
        $course_stmt = $conn->prepare($course_query);
        $course_stmt->bind_param("i", $job_id);
        $course_stmt->execute();
        $course_result = $course_stmt->get_result();

        while ($course_row = $course_result->fetch_assoc()) {
            $selected_courses[] = $course_row['course_name']; // Collect selected courses
        }

        $course_stmt->close();
    }
    $stmt->close();
}

// Check if form is submitted for updating the job
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $title = $conn->real_escape_string($_POST['title']);
    $company = $conn->real_escape_string($_POST['company']);
    $location = $conn->real_escape_string($_POST['location']);
    $work_mode = $conn->real_escape_string($_POST['work-mode']);
    $salary = (int)$_POST['salary'];
    $deadline = $conn->real_escape_string($_POST['deadline']);
    $cgpa = (float)$_POST['cgpa'];
    $pass_out_year = (int)$_POST['pass_out_year'];
    $description = $conn->real_escape_string($_POST['description']);
    $max_arrears = isset($_POST['max-arrears']) ? (int)$_POST['max-arrears'] : 0;
    $gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : '';
    $tenth_req = isset($_POST['tenth-req']) ? (int)$_POST['tenth-req'] : 0;
    $twelfth_req = isset($_POST['twelfth-req']) ? (int)$_POST['twelfth-req'] : 0;
    $job_status = isset($_POST['job-status']) ? $conn->real_escape_string($_POST['job-status']) : '';
    $round_1 = isset($_POST['round-1']) ? $conn->real_escape_string($_POST['round-1']) : '';
    $round_2 = isset($_POST['round-2']) ? $conn->real_escape_string($_POST['round-2']) : '';
    $round_3 = isset($_POST['round-3']) ? $conn->real_escape_string($_POST['round-3']) : '';

    // Update job in the database
    $update_sql = "UPDATE JOB SET company_name = ?, job_title = ?, location = ?, work_environment = ?, 
                   salary = ?, application_deadline = ?, cgpa_requirement = ?, max_arrears = ?, 
                   passout_year = ?, description = ?, gender = ?, tenth_requirement = ?, 
                   tweflth_requirement = ?, job_status = ?, round_1 = ?, round_2 = ?, round_3 = ? 
                   WHERE job_id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssisdiissiissssi", $company, $title, $location, $work_mode, 
                              $salary, $deadline, $cgpa, $max_arrears, $pass_out_year, 
                              $description, $gender, $tenth_req, $twelfth_req, 
                              $job_status, $round_1, $round_2, $round_3, $job_id);
    
    if ($update_stmt->execute()) {
        // Update eligible courses
        $course_ids = []; // To store selected course IDs

        // Fetch selected course IDs
        if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $courseName) {
                $courseQuery = "SELECT course_id FROM course WHERE course_name = ?";
                $courseStmt = $conn->prepare($courseQuery);
                $courseStmt->bind_param("s", $courseName);
                $courseStmt->execute();
                $courseResult = $courseStmt->get_result();

                if ($courseResult->num_rows > 0) {
                    $courseRow = $courseResult->fetch_assoc();
                    $course_ids[] = $courseRow['course_id']; // Collect course IDs
                }

                $courseStmt->close();
            }
        }

        // Clear existing courses for this job
        $delete_courses_query = "DELETE FROM job_course WHERE job_id = ?";
        $delete_courses_stmt = $conn->prepare($delete_courses_query);
        $delete_courses_stmt->bind_param("i", $job_id);
        $delete_courses_stmt->execute();
        $delete_courses_stmt->close();

        // Insert updated courses
        foreach ($course_ids as $course_id) {
            $jobCourseSql = "INSERT INTO job_course (job_id, course_id) VALUES (?, ?)";
            $jobCourseStmt = $conn->prepare($jobCourseSql);
            $jobCourseStmt->bind_param("ii", $job_id, $course_id);
            $jobCourseStmt->execute();
            $jobCourseStmt->close();
        }

        $show_success_message = true; // Set this flag
        header("Location: joblist_admin.php");
        exit;
    } else {
        echo "Error: " . $update_stmt->error;
    }
    
    $update_stmt->close();
}

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
            margin-left: 1px;
            cursor: pointer;
            transition: transform 0.3s;
        }

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

        .job-form-container {
            flex: 1;
            padding-top: 0px;
            padding-left: 15px;
            background-color:white;
            top: 0;           
            right: 0;         
            bottom: 0;        
            left: 300px;      
            overflow-y:auto;  
        }

        .job-form-container h3 {
            margin-top: 5px;
            margin-bottom: 20px;
            text-align: justify;
            color:black;
            font-weight: 550;  
        }
            
         
        .job-form-container h2 {
            margin-left: 500px;
            margin-bottom: 30px;
            color:black;
            font-weight: 600;      
        }

        .job-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .job-form label {
            font-weight:0;
            padding: 10px;
        }

        .job-form input[type="text"], 
        .job-form input[type="number"], 
        .job-form input[type="date"],
        .job-form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-left: -177px;
        }

        .job-form textarea {
            grid-column: span 2;
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
            height: 100px;
            font-family: Arial, Helvetica, sans-serif;
            margin-left: -175px;
        }

        .job-form input[type="submit"] {
            grid-column:span 1;
            padding: 5px 10px;
            background-color: #27428f;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-left: 500px;
            font-size: 18px;
            margin-top: 20px;
            font-weight: 600;
        }

        .job-form input[type="submit"]:hover {
            background-color: #1e165f;
        }

        img {
            height: 40px; 
            width: auto;
        }
    

        .success-message {
            display: none; /* Hidden by default */
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #2F5597;
            color: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            font-size: 16px;
        }

        .success-message.show {
            display: block;
        }

        .success-message .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            margin-left: 10px;
            cursor: pointer;
        }

        .error-message {
            color: red;
            font-size: 12px;
            height: 20px; /* Set a fixed height */
            margin-top: 5px; /* Space between input and error message */
        }
    </style>
</head>
<body>
    <!-- Profile Container -->
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
    
    <div class="main-content">
        <div class="job-form-container">
            <h2>Edit Job Details</h2>
            <?php if ($job): ?>
            <form class="job-form" method="POST" action="job_edit.php?job_id=<?php echo $job_id; ?>" onsubmit="return validateForm()">
                <label for="title">Title</label>
                <div>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($job['job_title']); ?>">
                    <span id="title-error" class="error-message" style="color:red; font-size:12px;"></span>
                </div>

                <label for="company">Company</label>
                <div>
                    <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($job['company_name']); ?>">
                    <span id="company-error" class="error-message" style="color:red; font-size:12px;"></span>
                </div>

                <label for="location">Location</label>
                <div>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($job['location']); ?>">
                    <span id="location-error" class="error-message" style="color:red; font-size:12px;"></span>
                </div>

                <label for="work-mode">Work Mode</label>
                <div>
                    <input type="text" id="work-mode" name="work-mode" value="<?php echo htmlspecialchars($job['work_environment']); ?>" >
                    <span id="work-mode-error" class="error-message" style="color:red; font-size:12px;"></span>
                </div>

                <label for="salary">Salary</label>
                <input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($job['salary']); ?>" required>

                <label for="deadline">Application Deadline</label>
                <div>
                    <input type="date" id="deadline" name="deadline" value="<?php echo htmlspecialchars($job['application_deadline']); ?>" onblur="validateApplicationDeadline()">
                    <span id="deadline-error" class="error-message" style="color:red; font-size:12px;"></span>
                </div>

                <label for="cgpa">Required CGPA</label>
                <input type="number" id="cgpa" name="cgpa" step="0.01" value="<?php echo htmlspecialchars($job['cgpa_requirement']); ?>" required>
                
                <label for="course">Eligible Courses </label>
                <div>
                    <input type="text" id="selectedOptions" name="selectedOptions" readonly placeholder="Select Eligible Courses">
                        <select name="options[]" id="course" multiple>
                            <option value="B.com taxation and finance" <?php echo in_array('B.com taxation and finance', $selected_courses) ? 'selected' : ''; ?>>B.com Taxation and Finance</option>
                            <option value="BBA" <?php echo in_array('BBA', $selected_courses) ? 'selected' : ''; ?>>BBA</option>
                            <option value="B.com fintech" <?php echo in_array('B.com fintech', $selected_courses) ? 'selected' : ''; ?>>B.com Fintech</option>
                            <option value="Int MCA" <?php echo in_array('Int MCA', $selected_courses) ? 'selected' : ''; ?>>INT MCA</option>
                            <option value="BCA" <?php echo in_array('BCA', $selected_courses) ? 'selected' : ''; ?>>BCA</option>
                            <option value="BCA DataScience" <?php echo in_array('BCA DataScience', $selected_courses) ? 'selected' : ''; ?>>BCA Data Science</option>
                            <option value="BA English and Literature" <?php echo in_array('BA English and Literature', $selected_courses) ? 'selected' : ''; ?>>BA English and Literature</option>
                            <option value="Int MA English and Literature" <?php echo in_array('Int MA English and Literature', $selected_courses) ? 'selected' : ''; ?>>INT MA English and Literature</option>
                            <option value="Int MSC mathematics" <?php echo in_array('Int MSC mathematics', $selected_courses) ? 'selected' : ''; ?>>INT M.Sc Mathematics</option>
                            <option value="Int Physics" <?php echo in_array('Int Physics', $selected_courses) ? 'selected' : ''; ?>>INT Physics</option>
                            <option value="Int Msc Physics" <?php echo in_array('Int Msc Physics', $selected_courses) ? 'selected' : ''; ?>>INT M.Sc Physics</option>
                            <option value="Int Msc Mathematics" <?php echo in_array('Int Msc Mathematics', $selected_courses) ? 'selected' : ''; ?>>INT M.Sc Mathematics</option>
                        </select>
                </div>
                
                <label for="pass_out_year">Pass Out Year</label>
                <div>
                    <select name="pass_out_year" id="pass_out_year">
                        <option value="2024" <?php echo ($job['passout_year'] == 2024) ? 'selected' : ''; ?>>2024</option>
                        <option value="2025" <?php echo ($job['passout_year'] == 2025) ? 'selected' : ''; ?>>2025</option>
                        <option value="2026" <?php echo ($job['passout_year'] == 2026) ? 'selected' : ''; ?>>2026</option>
                    </select>
                </div>

                <label for="description">Description</label>
                <div>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                </div>

                <label for="max-arrears">Maximum Arrears</label>
                <input type="text" id="max-arrears" name="max-arrears" value="<?php echo htmlspecialchars($job['max_arrears']); ?>">

                <label for="gender">Gender</label>
                <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($job['gender']); ?>">

                <label for="tenth-req">10th Requirement</label>
                <input type="text" id="tenth-req" name="tenth-req" value="<?php echo htmlspecialchars($job['tenth_requirement']); ?>">
                
                <label for="twelfth-req">12th Requirement</label>
                <input type="text" id="twelfth-req" name="twelfth-req" value="<?php echo htmlspecialchars($job['tweflth_requirement']); ?>">
               
                <label for="job-status">Job Status</label>
                <div>
                <input type="text" id="job-status" name="job-status" value="<?php echo htmlspecialchars($job['job_status']); ?>"placeholder="Applications Open/Closed">
                <span id="job-status-error" class="error-message" style="color:red; font-size:12px;"></span>
               </div>
                <h4>Hiring Workflow Rounds</h4>
                <br>
                <label for="round-1">Round 1</label>
                <div>
                    <input type="text" id="round-1" name="round-1" value="<?php echo htmlspecialchars($job['round_1']); ?>">
                </div>

                <label for="round-2">Round 2</label>
                <div>
                    <input type="text" id="round-2" name="round-2" value="<?php echo htmlspecialchars($job['round_2']); ?>">
                </div>

                <label for="round-3">Round 3</label>
                <div>
                    <input type="text" id="round-3" name="round-3" value="<?php echo htmlspecialchars($job['round_3']); ?>">
                </div>

                <input type="submit" value="Update Job">
            </form>
            <?php else: ?>
            <p>Job not found.</p>
            <?php endif; ?>
        </div>
        <div class="success-message" id="successMessage">
        Job has been successfully updated.
        <button class="close-btn" onclick="hideSuccessMessage()">×</button>
    </div>
</div>
<script>
      function loadProfilePicture() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_adminprofilepicture.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var profilePath = xhr.responseText.trim();
                
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
    const selectElement = document.getElementById('course');
    const selectedOptionsTextBox = document.getElementById('selectedOptions');

    // Add an event listener for when the user selects options
    selectElement.addEventListener('change', function() {
      const selectedOptions = Array.from(selectElement.selectedOptions)
                                   .map(option => option.text); // Get selected option text
      selectedOptionsTextBox.value = selectedOptions.join(', '); // Show selected options in text box
    });

    function isFutureDate(dateString) {
    const today = new Date();
    const inputDate = new Date(dateString);
    
    // Set time to 00:00:00 for comparison
    today.setHours(0, 0, 0, 0);
    return inputDate >= today; // Returns true if input date is today or in the future
}

function validateApplicationDeadline() {
    const deadline = document.getElementById('deadline');
    const errorContainer = document.getElementById('deadline-error');

    // Clear previous error message
    errorContainer.textContent = "";

    if (!isFutureDate(deadline.value)) {
        errorContainer.textContent = "Application deadline must be today or in the future.";
        return false; // Validation failed
    }
    return true; // Validation passed
}

function validateForm() {
    let isValid = true;

    // Clear previous error messages
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(function (element) {
        element.textContent = ""; // Clear any previous error message
    });

    // Validate title, company, location, work mode, application deadline, and job status
    const title = document.getElementById('title').value;
    const company = document.getElementById('company').value;
    const location = document.getElementById('location').value;
    const workMode = document.getElementById('work-mode').value;
    const applicationDeadline = document.getElementById('deadline').value;
    const jobStatus = document.getElementById('job-status').value;

    if (!title) {
        document.getElementById('title-error').textContent = " Job title is required.";
        isValid = false;
    }
    if (!company) {
        document.getElementById('company-error').textContent = "Company name is required.";
        isValid = false;
    }
    if (!location) {
        document.getElementById('location-error').textContent = "Location is required.";
        isValid = false;
    }
    if (!workMode) {
        document.getElementById('work-mode-error').textContent = "Work mode is required.";
        isValid = false;
    }
    if (!applicationDeadline) {
        document.getElementById('deadline-error').textContent = "Application deadline is required.";
        isValid = false;
    } else {
        if (!validateApplicationDeadline()) {
            isValid = false; // Validation for application deadline failed
        }
    }
    if (!jobStatus) {
        document.getElementById('job-status-error').textContent = "Job status is required.";
        isValid = false;
    }

    return isValid; // Return overall validity
}


function showSuccessMessage() {
    var successMessage = document.getElementById('successMessage');
    successMessage.classList.add('show');
    
    // Auto-hide the message after 5 seconds
    setTimeout(function() {
        successMessage.classList.remove('show');
    }, 5000);
}

// Function to hide the success message manually
function hideSuccessMessage() {
    var successMessage = document.getElementById('successMessage');
    successMessage.classList.remove('show');
}

// Display success message if PHP flag is true
<?php if ($show_success_message): ?>
    showSuccessMessage();
<?php endif; ?>

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

const selectElement = document.getElementById('course');
const selectedOptionsTextBox = document.getElementById('selectedOptions');

// Add an event listener for when the user selects options
selectElement.addEventListener('change', function() {
    const selectedOptions = Array.from(selectElement.selectedOptions)
                                .map(option => option.text); // Get selected option text
    selectedOptionsTextBox.value = selectedOptions.join(', '); // Show selected options in text box
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
