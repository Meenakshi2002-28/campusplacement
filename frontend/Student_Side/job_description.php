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
// Fetch user's CGPA
$userCgpaQuery = "SELECT cgpa FROM student WHERE user_id = ?";
$stmt = $conn->prepare($userCgpaQuery);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($user_cgpa);
$stmt->fetch();
$stmt->close();

// Fetch job's CGPA requirement
$jobCgpaRequirement = $jobDetails[0]['cgpa_requirement'];


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavoro - Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> <!-- SweetAlert CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-align: center;
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
            background-color: #1e3d7a;
            /* Background color for active link */
            border-left: 4px solid #ffffff;
            padding-left: 30px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
        }

        /* Main content styling */


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
            margin-left: 15px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .icon:hover {
            transform: scale(1.1);
        }

        /* Dropdown menu styling */
        .dropdown-content {
            display: none;
            opacity: 0;
            position: absolute;
            top: 55px;
            right: 20px;
            background: linear-gradient(135deg, #2F5597, #1e3d7a);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            z-index: 1;
            transition: opacity 0.3s ease;
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
            top: 20px;
            /* Keep the same positioning */
            left: 50%;
            transform: translateX(-50%);
            font-size: 36px;
            /* Increase the font size here */
            font-weight: bold;
            color: white;

            text-align: center;
        }

        .main-content {
            margin-left: 245px;
            margin-top: 13px;
            margin-right: 20px;
            /* Default margin for sidebar */
            padding: 40px;

            color: #333;
            border-radius: 10px;
            transition: margin-left 0.4s ease-in-out;
            /* Smooth transition for margin */
            background-color: #ffffff;
            height: 86.5vh;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            /* Add shadow effect */
            overflow-y: auto;
            overflow-x: hidden;
        }

        .job-details {
            background-color: whitesmoke;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #cccccc;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .job-details h2 {
            margin-top: 0;
            color: black;
            margin-left: 20px;
        }

        .job-details p {
            margin-left: 120px;
        }

        .apply-btn {
            background-color: #AFC8F3;
            color: black;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: block;
            margin-left: 420px;
            margin-top: 10px;
            font-size: 18px;
        }

        .apply-btn:hover {
            background-color: #1E165F;
            color: white;
        }

        .eligibility-section {
            background-color: whitesmoke;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #cccccc;
            box-shadow: 0 4px 10px rgba(0, 0, 139, 0.3);
            /* Dark blue shadow */

        }

        .eligibility-section h3 {
            margin-top: 0;
            color: #333;
        }

        .eligibility-1 {
            display: grid;
            grid-template-columns: repeat(3, 50px);
            /* Three equal-width columns */
            gap: 30px;
            /* Spacing between columns */
            padding-left: 50px;
            margin-left: 70px;
            column-gap: 280px;
            row-gap: 10px;
        }

        .eligibility-1 label {
            padding-left: 2px;
            padding-right: 0px;
            font-size: 18px;
        }

        .form-group {
            display: inline-block;
            margin-bottom: 10px;
            /* Adds space between each row */
        }

        .form-group label {
            display: inline-block;
            width: 80px;
            /* Set a fixed width for label */
            text-align: left;
            margin-right: 66px;
            /* Space between label and input */
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
            text-wrap: nowrap;
            padding-right: 40px;
            margin-left: 20px;
        }

        form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            font-size: 16px;
            width: 100%;
            padding-left: 10px;
            padding-right: 10px;
            padding: 5px;
            display: inline-block;
            margin-left: 20px;
        }

        .jobstatus input {
            background-color: #e2e2e2;
            border-radius: 10px;
            border: 1px solid rgb(197, 197, 197);
            width: 270px;
            height: 25px;
            font-size: 16px;
            margin-top: 20px;
            margin-left: 370px;
            text-align: center;
            line-height: 40px;
            padding: 0px;
            box-sizing: content-box;
            font-weight: 600;
        }

        .jobimg a {
            display: inline-flexbox;
            text-decoration: none;
            color: black;
            padding: 60px;
            border-left: 3px solid transparent;
        }

        .job-description {
            background-color: whitesmoke;
            padding-left: 40px;
        }

        .job-description h4 {
            color: black;
        }

        .job-description p {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 7px;
        }

        /* Hiring workflow section */
        .workflow-section {
            background-color: whitesmoke;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            margin-left: 20px;
        }

        .workflow-section h3 {
            margin-top: 0;
            color: black;
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

        .small-icon {
            width: 50px;
            /* Set desired width */
            height: 50px;
            /* Set desired height */
            object-fit: cover;
            /* Ensures the image scales properly */
            border-radius: 50%;
            /* Makes the image circular */
        }
    </style>
</head>

<body>
    <!-- Profile Container -->
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="small-icon" id="profileIcon"
            onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*"
            onchange="changeProfilePicture(event)">
        <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../Student_Side/profile_std.html"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>    

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>

        <a href="#home" class="active"><i class="fa fa-home"></i> Home</a>
        <a href="#jobs"><i class="fa fa-search"></i> Jobs</a>
        <a href="#applications"><i class="fa fa-envelope"></i> Applications</a>
        <a href="#company"><i class="fa fa-building"></i> Company</a>
        <a href="#profile"><i class="fa fa-user"></i> Profile</a>
        <a href="#feedback"><i class="fa fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="job-details">
            <h2><?php echo htmlspecialchars($jobDetails[0]['job_title']); ?></h2>
            <p><?php echo htmlspecialchars($jobDetails[0]['company_name']); ?></p>
            <div class="jobimg">
                <a href="#location-dot"><i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($jobDetails[0]['location']); ?></a>
                <a href="#briefacse"><i class="fa fa -fw fa-solid fa-briefcase"></i> Full Time</a>
                <a href="#indian-rupee-sign"><i
                        class="fas fa-rupee-sign"></i><?php echo htmlspecialchars($jobDetails[0]['salary']); ?></a>
                <a href="#calendar-days"><i class="fa fa-fw  fa-solid fa-calendar"></i> Apply By
                    <?php echo htmlspecialchars($jobDetails[0]['application_deadline']); ?></a>
            </div>
            <div class="jobstatus">
                <input type="text" id="jobstatus"
                    value="Job Status: <?php echo htmlspecialchars($jobDetails[0]['job_status']); ?> for Applications"
                    readonly>
            </div>
        </div>
        <!-- Eligibility Section -->
        <div class="eligibility-section">

            <form>
                <div class="eligibility-1">
                    <label for="10th">10th</label>
                    <label for="12th">12th</label>
                    <label for="arrears">Max Current Arrears</label>

                    <input type="text" id="12th" name="10th"
                        value="<?php echo htmlspecialchars($jobDetails[0]['tenth_requirement']); ?>" readonly>
                    <input type="text" id="10th" name="12th"
                        value="<?php echo htmlspecialchars($jobDetails[0]['tweflth_requirement']); ?>" readonly>
                    <input type="text" id="arrears" name="arrears"
                        value="<?php echo htmlspecialchars($jobDetails[0]['max_arrears']); ?>" readonly>

                </div>

                <div class="form-group">
                    <label for="gender">Gender </label>
                    <input type="text" id="gender" name="gender"
                        value="<?php echo htmlspecialchars($jobDetails[0]['gender']); ?>" readonly>
                    <br>

                    <label for="cgpa">Cgpa Requirement </label>
                    <input type="text" id="cgpa" name="cgpa"
                        value="<?php echo htmlspecialchars($jobDetails[0]['cgpa_requirement']); ?>" readonly>
                    <br>

                    <label for="passout-year">Pass Out Year </label>
                    <input type="text" id="passout-year" name="passout-year"
                        value="<?php echo htmlspecialchars($jobDetails[0]['passout_year']); ?>" readonly>
                    <br>


                    <label for="course">Course </label>
                    <?php foreach ($jobDetails as $index => $detail): ?>
                        <input type="text" id="course_<?php echo $index; ?>" name="course_<?php echo $index; ?>"
                            value="<?php echo htmlspecialchars($detail['course_name']); ?>" readonly>
                    <?php endforeach; ?>
                </div>
            </form>

            <div class="job-description">
                <h4>Description </h4>
                <p>
                    <?php
                    // Fetch the description
                    $description = $jobDetails[0]['description'];

                    // Debugging: Print raw description for inspection
                    // Uncomment the line below for debugging purposes
                    // echo "Raw Description: " . htmlspecialchars($description);
                    
                    // Step 1: Unescape the double backslashes
                    $description = str_replace('\\\\', '', $description); // Removes the escaped backslashes
                    $description = str_replace('\r\n', "\n", $description); // Convert \r\n to newlines
                    
                    // Step 2: Replace occurrences of 'rn' with actual newlines
                    $description = str_replace('rn', "\n", $description); // Convert 'rn' to newlines
                    
                    // Step 3: Convert to HTML format for line breaks
                    echo nl2br(htmlspecialchars($description));
                    ?>
                </p>



            </div>

            <!-- Hiring Workflow Section -->
            <div class="workflow-section">

                <h3>Hiring Workflow Rounds</h3>

                <div class="workflow-rounds">
                    <label for="round1">Round 1</label>
                    <input type="text" id="round1" name="round1"
                        value="<?php echo htmlspecialchars($jobDetails[0]['round_1']); ?>" readonly>

                    <label for="round2">Round 2</label>
                    <input type="text" id="round2" name="round2"
                        value="<?php echo htmlspecialchars($jobDetails[0]['round_2']); ?>" readonly>

                    <label for="round3">Round 3</label>
                    <input type="text" id="round3" name="round3"
                        value="<?php echo htmlspecialchars($jobDetails[0]['round_3']); ?>" readonly>
                </div>

                <div class="jobstatus">
                    <form id="jobApplicationForm" action="apply_job.php" method="POST"
                        onsubmit="return confirmApplication();">
                        <input type="hidden" name="job_id"
                            value="<?php echo htmlspecialchars($jobDetails[0]['job_id']); ?>">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                        <!-- Assuming you have the user ID -->
                        <button type="submit" class="apply-btn" id="applyButton">Apply</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script>
        function confirmApplication() {
    const userCgpa = <?php echo json_encode($user_cgpa); ?>; // Get user's CGPA from PHP
    const jobCgpaRequirement = <?php echo json_encode($jobCgpaRequirement); ?>; // Get job's CGPA requirement from PHP

    if (userCgpa < jobCgpaRequirement) {
        // Use SweetAlert for a nicer alert
        Swal.fire({
            icon: 'error',
            title: 'Application Denied',
            text: 'You cannot apply for this job because your CGPA is below the requirement.',
            confirmButtonText: 'OK'
        });
        return false; // Prevent form submission
    }
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

            // Dashboard stats extraction

            // Animate counter values
            function animateCounter(element, endValue) {
                let startValue = 0;
                const duration = 2000; // Animation duration in milliseconds
                const incrementTime = Math.floor(duration / endValue);

                const counterInterval = setInterval(() => {
                    if (startValue < endValue) {
                        startValue++;
                        element.textContent = startValue;
                    } else {
                        clearInterval(counterInterval);
                    }
                }, incrementTime);
            }



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