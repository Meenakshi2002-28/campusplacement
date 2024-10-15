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
    <title>Edit Job</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
       body {
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
    .logout a{
        font-size: 20px;
        margin-top: 160px;
    }
    .main-content {
        margin-left: 220px; 
        padding: 50px;
        font-size: 18px; /* Larger font size for main content */
        padding-top: 15px;
    }

    .job-form-container {
        flex: 1;
        padding: 30px;
        background-color:white;
        
        top: 0;           
        right: 0;         
        bottom: 0;        
        left: 250px;      
        overflow-y:auto; 
       
    }
    .job-form-container h3 {
            margin-bottom: 30px;
            text-align: justify;
            color:black;
            font-weight: 550;  
    }
            
            
    .job-form-container h2 {
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
            margin-left: -60px;
     }

    .job-form input[type="radio"] {
            margin-right: 3px;
            margin-left:1px;
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
            margin-left: -60px;
        }

    .job-form input[type="submit"] {
            grid-column:span 1;
            padding: 10px 25px;
            background-color: #27428f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 400px;
            font-size: 18px;
        }

    .job-form input[type="submit"]:hover {
            background-color: #1e165f;
        }

    img {
        height: 40px; 
        width: auto;
        display:flex;
    }
    .container {
        padding: 5px;
        display: flex;
        justify-content: flex-end; 
        align-items: center; 
    }
    .icon {
        margin-left: 1px; 
    }
    .dropdown-content {
    display: none;
    position: absolute;
    background-color: #2F5597;
    min-width: 150px;
    z-index: 1;
    top: 55px; /* Adjust this value as needed */
    border-radius: 3px;
}

    .dropdown-content a {
        color: white;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #1e165f;
        color: white;
        border-radius: 3px;
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

    </style>
</head>
<body>
    
    <div class="main-content">
        <div class="job-form-container">
            <h2>Edit Job</h2>
            <?php if ($job): ?>
            <form class="job-form" method="POST" action="job_edit.php?job_id=<?php echo $job_id; ?>">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($job['job_title']); ?>" required>

                <label for="company">Company</label>
                <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($job['company_name']); ?>" required>

                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>

                <label for="work-mode">Work Mode</label>
                <input type="text" id="work-mode" name="work-mode" value="<?php echo htmlspecialchars($job['work_environment']); ?>" required>

                <label for="salary">Salary</label>
                <input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($job['salary']); ?>" required>

                <label for="deadline">Application Deadline</label>
                <input type="date" id="deadline" name="deadline" value="<?php echo htmlspecialchars($job['application_deadline']); ?>" required>
                
                <label for="type">Type </label>
            <div>
                <input type="radio" id="job" name="type" value="job" checked>
                <label for="job">Job</label>
                <input type="radio" id="internship" name="type" value="internship">
                <label for="internship">Internship </label>
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
                <input type="text" id="job-status" name="job-status" value="<?php echo htmlspecialchars($job['job_status']); ?>">

                <h3>Hiring Workflow Rounds</h3>
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
        <script>
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
        const selectElement = document.getElementById('course');
    const selectedOptionsTextBox = document.getElementById('selectedOptions');

    // Add an event listener for when the user selects options
    selectElement.addEventListener('change', function() {
      const selectedOptions = Array.from(selectElement.selectedOptions)
                                   .map(option => option.text); // Get selected option text
      selectedOptionsTextBox.value = selectedOptions.join(', '); // Show selected options in text box
    });
    </script>
    </div>
</body>
</html>
