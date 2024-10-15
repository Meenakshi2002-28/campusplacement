<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to apply for a job.");
}
$user_id = $_SESSION['user_id']; 

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
   
} else {
    die('Job ID not set.');
}
// Fetch job details from the database
$query = "SELECT * FROM job WHERE job_id = $job_id";
$result = $conn->query($query);

$query = "SELECT job.*, course.course_name
          FROM job
          JOIN job_course ON job.job_id = job_course.job_id
          JOIN course ON job_course.course_id = course.course_id
          WHERE job.job_id = $job_id";

$result = $conn->query($query);

// Store the job details and courses in an array
$jobDetails = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobDetails[] = $row;  // Store each row (job + course data) into $jobDetails array
    }
} else {
    echo "Job not found.";
    exit;
}


// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #ffffff;
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
        padding: 15px; /* Adjust padding for better alignment */
        font-size: 22px; /* Smaller font size */
        border-left: 3px solid transparent;
        transition: all 0.3s;
    }

    .sidebar a:hover {
        border-left: 3px solid #ffffff;
        background: #1e165f;
    }

    img {
        height: 40px; 
        width: auto;
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
   
    .main-content {
        flex-grow: 1;
        padding: 40px;
        background-color:white;
        padding-left: 250px;
    }
    .job-details {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        margin-bottom: 30px;
    }

    .job-details h2 {
        margin-top: 0;
        color:black;
        margin-left: 20px;
    }

    .job-details p{
        margin-left: 120px;
    }

    .apply-btn {
        background-color: #1c4a82;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .apply-btn:hover {
        background-color: #0e3363;
        transition: 0.3s;
    }

    /* Eligibility section */
    .eligibility-section {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #cccccc;
    }

    .eligibility-section h3 {
        margin-top: 0;
        color: #333;
    }
    .eligibility-1{
        display:grid;
        grid-template-columns:repeat(3,50px); /* Three equal-width columns */
        gap: 30px; /* Spacing between columns */
        padding-left: 50px;
        margin-left:70px;
        column-gap: 280px;
        row-gap: 10px;
    }
    .eligibility-1 label{
        padding-left:2px;
        padding-right:0px;
        font-size: 18px;   
    }
    .form-group {
        display: inline-block;
        margin-bottom: 10px; /* Adds space between each row */   
    }

    .form-group label {
        display:inline-block;
        width: 80px; /* Set a fixed width for label */
        text-align:left;
        margin-right: 20px; /* Space between label and input */
        padding-top: 20px;
        padding-left: 20px;
    }

    .form-group input {
        display: inline-block;
        width: 150px; 
    }
    form label {
        font-weight: bold;
        margin-bottom: 10px;
        text-wrap:nowrap;
        padding-right: 40px;
        margin-left: 20px;
    }

    form input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color:white;
        font-size: 16px;
        width:100%;
        padding-left: 10px;
        padding-right: 10px;
        padding:5px;
        display:inline-block;
        margin-left: 20px;
        }
    .jobstatus input {
        background-color:#e2e2e2;
        border-radius: 10px;
        border: 1px solid rgb(197, 197, 197) ;
        width: 270px; 
        height: 25px; 
        font-size: 16px;
        margin-top: 20px;
        margin-left: 370px;
        text-align: center; 
        line-height: 40px; 
        padding: 0px; 
        box-sizing:content-box;
        font-weight: 600;
    }
    .jobimg a {
        display:inline-flexbox;
        text-decoration: none;
        color:black;
        padding: 60px;
        border-left: 3px solid transparent;  
    }
    .job-description {
        background-color: white;
        padding-left: 40px;
    }
    .job-description h4{
        color: black;
    }
    .job-description p{
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 7px;
    }

    /* Hiring workflow section */
    .workflow-section {
        background-color:white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        margin-left: 20px;    
    }
    .workflow-section h3 {
        margin-top: 0;
        color:black;
    }
    .workflow-rounds {
        margin-top: 30px;
    }
    .workflow-rounds label {
        font-weight: bold;
        padding: 10px;
    }
    .workflow-rounds input {
        margin-bottom: 10px;
        background-color: white;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 100%;
        margin-top: 5px;
    }

    /* Apply button */
    .apply-btn {
        background-color: #AFC8F3;
        color: black;
        padding: 10px 25px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        display: block;
        margin-left:420px;
        margin-top: 10px;
        font-size: 18px;
    }

    .apply-btn:hover {
        background-color: #1E165F;
        color: white;
    }
    .container {
        padding: 5px;
        display: flex;
        justify-content: flex-end; /* Aligns children to the right */
        align-items: center; /* Vertically centers the images */
        cursor: pointer;
    }
    .icon {
        margin-left: 1px; /* Adds spacing between the icons */
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
.logout a{
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
            <a href="../Student_Side/profile_std.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div> 

<div class="sidebar">
    <a href="dashboard_std.php"><i class="fa fa-fw fa-home"></i> Home</a>
    <a href="jobs.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
    <a href="#applications"><i class="fa fa-fw fa-envelope"></i> Applications</a>
    <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
    <a href="profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
    <a href="feedback.html"><i class="fa fa-fw fa-comment"></i> Feedback</a>
    <div class="logout">
        <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
    </div>
</div>
<div class="main-content">
    <div class="job-details">
        <h2><?php echo htmlspecialchars($jobDetails[0]['job_title']); ?></h2>
        <p><?php echo htmlspecialchars($jobDetails[0]['company_name']); ?></p>
    <div class="jobimg">
        <a href="#location-dot"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($jobDetails[0]['location']); ?></a>
        <a href="#briefacse"><i class="fa fa -fw fa-solid fa-briefcase"></i> Full Time</a>
        <a href="#indian-rupee-sign"><i class="fas fa-rupee-sign"></i><?php echo htmlspecialchars($jobDetails[0]['salary']); ?></a>
        <a href="#calendar-days"><i class="fa fa-fw  fa-solid fa-calendar"></i> Apply By <?php echo htmlspecialchars($jobDetails[0]['application_deadline']); ?></a>
    </div>
        <div class="jobstatus">
           <input type="text"id="jobstatus" value="Job Status: <?php echo htmlspecialchars($jobDetails[0]['job_status']); ?> for Applications" readonly>
        </div>
    </div>
<!-- Eligibility Section -->
<div class="eligibility-section">
        
        <form>
            <div class="eligibility-1">
            <label for="10th">10th</label>
            <label for="12th">12th</label>
            <label for="arrears">Max Current Arrears</label>
            
            <input type="text" id="12th" name="10th"value="<?php echo htmlspecialchars($jobDetails[0]['tenth_requirement']); ?>" readonly>
            <input type="text" id="10th" name="12th"value="<?php echo htmlspecialchars($jobDetails[0]['tweflth_requirement']); ?>" readonly>
            <input type="text" id="arrears" name="arrears" value="<?php echo htmlspecialchars($jobDetails[0]['max_arrears']); ?>" readonly>
            
            </div>
        
            <div class="form-group">
            <label for="gender">Gender </label>
            <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($jobDetails[0]['gender']); ?>" readonly>
            <br>

            
            <label for="passout-year">Pass Out Year </label>
            <input type="text" id="passout-year" name="passout-year"value="<?php echo htmlspecialchars($jobDetails[0]['passout_year']); ?>" readonly>
            <br>

            
            <label for="course">Course </label>
            <?php foreach ($jobDetails as $index => $detail) : ?>
                    <input type="text" id="course_<?php echo $index; ?>" name="course_<?php echo $index; ?>"
                           value="<?php echo htmlspecialchars($detail['course_name']); ?>" readonly>
                <?php endforeach; ?>
            </div>
        </form>
        
        <div class="job-description">
            <h4>Description </h4>
                    <p><?php echo htmlspecialchars($jobDetails[0]['description']); ?>
                    </p>
        </div>
            <p>
            <!-- Hiring Workflow Section -->
            <div class="workflow-section">
                
                <h3>Hiring Workflow Rounds</h3>
    
                    <div class="workflow-rounds">
                    <label for="round1">Round 1</label>
                    <input type="text" id="round1" name="round1" value="<?php echo htmlspecialchars($jobDetails[0]['round_1']); ?>" readonly>

                    <label for="round2">Round 2</label>
                    <input type="text" id="round2" name="round2" value="<?php echo htmlspecialchars($jobDetails[0]['round_2']); ?>" readonly>
                    
                    <label for="round3">Round 3</label>
                    <input type="text" id="round3" name="round3"value="<?php echo htmlspecialchars($jobDetails[0]['round_3']); ?>" readonly>
                    </div>
    
<div class="jobstatus">
    <form action="apply_job.php" method="POST">
    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($jobDetails[0]['job_id']); ?>">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>"> <!-- Assuming you have the user ID -->
        <button type="submit" class="apply-btn">Apply</button>
    </form>
</div>
                
            </div>
        </div>
    
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
    </script>
</body>
</html>
meeena 
