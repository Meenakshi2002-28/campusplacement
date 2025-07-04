<?php
session_start(); 


if (isset($_SESSION['user_id'])) {
    $servername = "localhost";
    $db_username = "root"; 
    $db_password = ""; 
    $dbname = "campus_placement"; 

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the user ID from the session
    $user_id = $_SESSION['user_id'];

    $query = "SELECT name ,resume,cgpa FROM student WHERE user_id = ?";
    $name = '';
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result($name,$resume,$cgpa);
        $stmt->fetch(); // Fetch the result immediately
        $stmt->close(); // Close the statement after use
    }
    $query = "SELECT phone_number, email FROM student WHERE user_id = ?";
    $phone_number = '';
    $email = '';
    $has_student_row = false;

    $profile_completion = 0; // Default to 0 if phone_number or email is missing
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result( $phone_number, $email);
    
        if ($stmt->fetch()) {
            // Check if both phone_number and email are stored
            if (!empty($phone_number) && !empty($email)) {
                $profile_completion = 40; // Set profile completion to 40% if both fields are stored
            }
        }
    
        $stmt->close();
    }
    
    // You now have $name, $phone_number, $email, and $profile_completion available
    

    // 2. Check if there is a row for the user in the academic_details table
    if ($has_student_row) { // Only check if student row exists
        $query_academic = "SELECT user_id FROM academic_details WHERE user_id = ?";

        if ($stmt = $conn->prepare($query_academic)) {
            $stmt->bind_param("s", $user_id);
            $stmt->execute();

            if ($stmt->fetch()) {
                $profile_completion = 80; // Profile completion is 80% if row exists in academic_details table
            }
            $stmt->close();
        }
    }

    // 3. Check if the resume is filled in the student table
    if (!empty($resume)) {
        $profile_completion = 100;
    }

    // 4. Count the number of applications for the user in the job_application table
    $application_count = 0;
    $query_applications = "SELECT COUNT(*) FROM job_application WHERE user_id = ?";

    if ($stmt = $conn->prepare($query_applications)) {
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result($application_count);
        $stmt->fetch();
        $stmt->close();
    }

    // 5. Count the number of active jobs in the job table
    $active_job_count = 0;
    $query_active_jobs = "SELECT COUNT(*) FROM job WHERE is_active = 1";

    if ($stmt = $conn->prepare($query_active_jobs)) {
        $stmt->execute();
        $stmt->bind_result($active_job_count);
        $stmt->fetch();
        $stmt->close();
    }

    // 6. Count the number of jobs with cgpa_requirement <= user's CGPA
    $eligible_job_count = 0;
    $query_eligible_jobs = "SELECT COUNT(*) FROM job WHERE cgpa_requirement <= ? AND is_active = 1";

    if ($stmt = $conn->prepare($query_eligible_jobs)) {
        $stmt->bind_param("d", $cgpa); // Bind CGPA as a double
        $stmt->execute();
        $stmt->bind_result($eligible_job_count);
        $stmt->fetch();
        $stmt->close();
    }

    // 7. Fetch the number of students placed per company and the company names
    $companies = [];
    $students_placed = [];
    $query = "
        SELECT j.company_name, COUNT(p.user_id) AS students_placed
        FROM placement p
        JOIN job j ON p.job_id = j.job_id
        GROUP BY p.job_id
        ORDER BY students_placed  DESC
    LIMIT 4
    ";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $companies[] = $row['company_name'];
        $students_placed[] = $row['students_placed'];
    }

    // Close the database connection
    $conn->close();
} else {
    // If no session is set, redirect to the login page
    header("Location: login.php");
    exit();
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link href="https://fonts.googleapis.com/css2?family=Merienda&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            transition: width 0.4s ease-in-out;
            padding-top: 80px;
        }

        .sidebar .logo {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 32px;
            font-weight: bold;
            color: white;
            text-align: center;
            font-family: 'Merienda', cursive;
        }

        .sidebar:hover {
            width: 250px;
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
        }

        .sidebar .logout {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
        }

        .sidebar a.active {
            background-color: #d9e6f4;
            border-left: 4px solid #ffffff;
            padding-left: 30px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;
            color: #000000;
            position: relative;
            z-index: 1;
            height: 45px;
        }

        /* Main content styling */
        .main-content {
            margin-left: 245px;
            margin-top: 13px;
            margin-right: 20px;
            padding: 40px;
            font-size: 18px;
            color: #333;
            border-radius: 10px;
            transition: margin-left 0.4s ease-in-out;
            background-color: #ffffff;
            height: 86.5vh;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .main-content h1 {
            color: #050505;
            font-size: 2.5rem;
            font-weight: bold;
            padding-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Profile section styling */
        .container {
            padding: 18px 20px;
            width: 1268px;
            height: 55px;
            margin-left: 245px;
            margin-top: 12px;
            margin-right: 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            transition: margin-left 0.4s ease-in-out;
        }

        .icon {
            margin-left: 1px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        img {
            height: 40px;
            width: auto;
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

        /* Card styling with hover effects */
        .card {
            background: linear-gradient(135deg, #a2c4fb, #9babcd);
            color: #000000;
            transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .card-text i {
            margin-right: 10px;
            font-size: 1.8rem;
            color: #082765;
        }

        .card:hover {
            transform: scale(1.05);
            background-color: #e0e0ee;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        /* Counter animation */
        .counter {
            font-size: 1.5rem;
            font-weight: bold;
            color: #04070b;
            transition: transform 0.3s ease-in-out;
        }

        .container h3 {
            margin-right: 450px;
            font-weight: 700;
        }

        /* Scrolling Section Styling */
        .scrolling-section {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
            border-radius: 10px;
            margin-top: 60px;
        }

        .scrolling-logos {
            display: inline-block;
            animation: slide 30s linear infinite;
            white-space: nowrap;
        }

        .scrolling-logos .logo {
            height: 30px;
            margin: 0 15px;
            object-fit: contain;
            transition: transform 0.1s;
        }

        .scrolling-logos .logo:hover {
            transform: scale(1.1);
        }

        /* Scrolling Animation */
        @keyframes slide {
            0% {
                transform: translateX(100%);
            }
            100%{
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Welcome to Lavoro</h3>
        <img src="../images/Customer.png" alt="Profile Icon" class="icon" id="profileIcon">
        <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>
        <!-- Dropdown  Menu-->
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../profile_redirect.php"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        <a href="dashboard_std.php" class="active"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="job.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="userapp.php"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="feedbackview.php"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>

        <!-- Dashboard Statistics Cards -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Applications</h5>
                        <p class="card-text"><i class="fas fa-file-alt"></i> 
                            <span class="counter" id="total-applications"> <?php echo $application_count; ?></span> Applications
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Active Jobs</h5>
                        <p class="card-text"><i class="fas fa-briefcase"></i> <span class="counter" id="active-jobs">
                            <?php echo $active_job_count; ?></span> Open Positions</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Eligible Jobs</h5>
                        <p class="card-text"><i class="fas fa-check-circle"></i> <span class="counter" id="eligible-jobs">
                            <?php echo $eligible_job_count; ?></span> Eligible Positions</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Profile Completion</h5>
                        <p class="card-text"><i class="fas fa-check-circle"></i> <span class="counter" id="profile-completion"> 
                            <?php echo $profile_completion; ?>%</span><b> %</b>Complete</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <canvas id="placementChart"
            style="width: 100%; max-width: 450px; height: 120px; float:left; margin-top: 50px;">
        </canvas>

        <!-- Scrolling Marquee Section for Company Logos -->
        <div class="scrolling-section">
            <div class="scrolling-logos">
                <img src="../images/company_logo/infosys.png" alt="Company 1" class="logo">
                <img src="../images/company_logo/tcs.png" alt="Company 2" class="logo">
                <img src="../images/company_logo/microsoft.jpg" alt="Company 3" class="logo">
                <img src="../images/company_logo/Accenture.svg.png" alt="Company 4" class="logo">
                <img src="../images/company_logo/cisco.png" alt="Company 5" class="logo">
                <img src="../images/company_logo/cognizant.jpg" alt="Company 6" class="logo">
                <img src="../images/company_logo/meta.jpg" alt="Company 7" class="logo">
                <img src="../images/company_logo/Deloitte.png" alt="Company 8" class="logo">
                <img src="../images/company_logo/federal bank.png" alt="Company 9" class="logo">
                <img src="../images/company_logo/intel.png" alt="Company 10" class="logo">
                <img src="../images/company_logo/LTImindtree.png" alt="Company 11" class="logo">
                <img src="../images/company_logo/wipro.png" alt="Company 12" class="logo">
                <img src="../images/company_logo/teal.jpg" alt="Company 13" class="logo">
                <img src="../images/company_logo/tech mahindra.png" alt="Company 14" class="logo">
                <img src="../images/company_logo/youtube.jpg" alt="Company 15" class="logo">
            </div>
        </div>

    </div>
<script>
     function loadProfilePicture() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_profilepicture.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var profilePath = xhr.responseText.trim();
                
                document.getElementById('profileIcon').src = profilePath;
            }
        };
        xhr.send();
    }
    window.onload = loadProfilePicture;
    const companies = <?php echo json_encode($companies); ?>;
    const studentsPlaced = <?php echo json_encode($students_placed); ?>;

    const colors = [
        'rgba(0, 51, 102, 0.8)', // Dark Blue
        'rgba(0, 76, 153, 0.8)', // Medium Dark Blue
        'rgba(51, 102, 204, 0.8)', // Standard Blue
        'rgba(102, 153, 255, 0.8)', // Light Blue
        'rgba(153, 204, 255, 0.8)', // Lighter Blue
        'rgba(204, 229, 255, 0.8)' // Very Light Blue
    ];

    const datasets = companies.map((company, index) => ({
        label: company,
        data: [studentsPlaced[index]], // Single data point for each company
        backgroundColor: colors[index % colors.length], // Cycle through colors
        borderColor: colors[index % colors.length].replace('0.8', '1'), // Fully opaque border
        borderWidth: 1
    }));

    const ctx = document.getElementById('placementChart').getContext('2d');
    const placementChart = new Chart(ctx, {
        type: 'bar', // Bar chart type
        data: {
            labels: ['Number of Students Placed'], // Generic label for x-axis
            datasets: datasets // Array of datasets, one for each company
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false // Hide vertical grid lines
                    }
                },

                y: {
                    grid: {
                        display: false // Hide horizontal grid lines
                    },
                    beginAtZero: true
                }
            },
        }
    });
    
    // Change profile image
    function triggerFileInput() {
        document.getElementById('fileInput').click();
    }

    function changeProfilePicture(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
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
        const dashboardStats = {
            totalApplications: <?php echo $application_count; ?>,     // Total Applications
            activeJobs: <?php echo $active_job_count; ?>,
            eligibleJobs: <?php echo $eligible_job_count; ?>,            // Active Jobs
            profileCompletion: " <?php echo $profile_completion; ?>%",    // Profile Completion
        };

        // Animate counter values
        function animateCounter(element, endValue) {
            let startValue = 0;
            const duration = 800; // Animation duration in milliseconds
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

        // Call animateCounter for each stat
        animateCounter(document.getElementById('total-applications'), dashboardStats.totalApplications);
        animateCounter(document.getElementById('active-jobs'), dashboardStats.activeJobs);
        animateCounter(document.getElementById('eligible-jobs'), dashboardStats.eligibleJobs);
        animateCounter(document.getElementById('profile-completion'), parseInt(dashboardStats.profileCompletion));

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

        // Example of how to log these values
        console.log("Total Applications:", dashboardStats.totalApplications);
        console.log("Active Jobs:", dashboardStats.activeJobs);
        console.log("Profile Completion:", dashboardStats.profileCompletion);
    });
</script>
</body>
</html>