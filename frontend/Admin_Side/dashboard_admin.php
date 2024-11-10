<?php
session_start(); // Start the session to access session variables

// Assuming you have already set the user_id or email in the session during login
if (isset($_SESSION['user_id'])) {
    $servername = "localhost";
    $db_username = "root"; // MySQL username
    $db_password = ""; // MySQL password
    $dbname = "campus_placement"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Prepare and execute a SQL query to fetch the admin's name
    $query = "SELECT name FROM admin WHERE user_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_id); // Assuming user_id is a string
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
    }

    // Query to count total students
    $total_students_query = "SELECT COUNT(*) AS total_students FROM student";
    $total_students_result = $conn->query($total_students_query);
    $total_students = $total_students_result->fetch_assoc()['total_students'];

    // Query to count active jobs
    $active_jobs_query = "SELECT COUNT(*) AS active_jobs FROM job WHERE is_active = 1";
    $active_jobs_result = $conn->query($active_jobs_query);
    $active_jobs = $active_jobs_result->fetch_assoc()['active_jobs'];

    // Query to count total placements
    $total_placements_query = "SELECT COUNT(*) AS total_placements FROM placement";
    $total_placements_result = $conn->query($total_placements_query);
    $total_placements = $total_placements_result->fetch_assoc()['total_placements'];

    // Query to get company names and number of students placed per job
    $companies = [];
    $students_placed = [];
    $query = "
        SELECT j.company_name, COUNT(p.user_id) AS students_placed
        FROM placement p
        JOIN job j ON p.job_id = j.job_id
        GROUP BY p.job_id
        ORDER BY students_placed DESC
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
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.5); /* Transparent glow effect */
    transition: width 0.4s ease-in-out;
    padding-top: 80px; /* Added padding for space at the top */
}

.sidebar .logo {
    position: absolute;
    top: 20px; /* Positions logo/title closer to the top */
    left: 50%;
    transform: translateX(-50%);
    font-size: 24px;
    font-weight: bold;
    color: white;
    text-align: center;
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

.container h3{
    margin-right: 450px;
    font-weight: 700;
}

.small-icon {
    width: 50px; /* Set desired width */
    height: 50px; /* Set desired height */
    object-fit: cover; /* Ensures the image scales properly */
    border-radius: 50%;/* Makes the image circular */
}

.icon {
    margin-left: 1px;
    cursor: pointer;
    transition: transform 0.3s;
}

.icon:hover {
    transform: scale(1.1);
}

img {
    height: 40px; /* Adjust size as needed */
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

.card {
     background: linear-gradient(135deg, #a2c4fb, #9babcd); /* Gradient background */
    color: #000000; /* White text for better contrast */
    transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);/* Soft shadow effect */
}

.card-text i {
    margin-right: 10px;
    font-size: 1.8rem;
    color: #082765; /* Icon color */
}

.card:hover {
    transform: scale(1.05); /* Scale effect on hover */
    background-color: #e0e0ee; /* Light blue background on hover */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Shadow effect */
}
 /* Scrolling Section Styling */
 .scrolling-section {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            /* background-color: #ffffff; Background color matching main content */
            padding: 10px 0;
            /* box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); */
            border-radius: 10px;
            /* margin-top: 20px; */
            margin-top: 60px;
        }

        .scrolling-logos {
            display: inline-block;
            animation: slide 30s linear infinite;
            white-space: nowrap;
        }

        .scrolling-logos .logo {
            height: 30px;
            /* Adjust logo height */
            margin: 0 15px;
            /* Spacing between logos */
            object-fit: contain;
            transition: transform 0.3s;
        }

        .scrolling-logos .logo:hover {
            transform: scale(1.1);
            /* Slight zoom on hover */
        }

        /* Scrolling Animation */
        @keyframes slide {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }
</style>
</head>
<body>
<!-- Profile Container -->
<div class="container">
    <h3>Welcome to Lavoro</h3>
    <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
    <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">
    <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>

    <!-- Dropdown Menu -->
    <div id="dropdownMenu" class="dropdown-content">
        <a href=" profile_admin.php"><i class="fa fa-user-circle"></i> Profile</a>
        <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
    </div>
</div>    


<div class="sidebar">
    <!-- Logo or Website Name -->
    <div class="logo">Lavoro</div>
    <a href="dashboard_admin.php" class="active"><i class="fas fa-home"></i> Home</a>
    <a href="joblist_admin.php"><i class="fas fa-briefcase"></i> Jobs</a>
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
    <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>

    <!-- Dashboard Statistics Cards -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"> Students</h5>
                    <p class="card-text"><i class="fas fa-file-alt"></i> <span class="counter" id="total-students"></span> Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"> Active Jobs</h5>
                    <p class="card-text"><i class="fas fa-briefcase"></i> <span class="counter" id="active-jobs"></span> Open Positions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"> Placements</h5>
                    <p class="card-text"><i class="fas fa-check-circle"></i> <span class="counter" id="placed-students"></span> Placed Students</p>
                </div>
            </div>
        </div>
    </div>
    <canvas id="placementChart"
    style="width: 100%; max-width: 450px; height: 120px; float:left; margin-top: 50px;"></canvas>
    <div class="scrolling-section">
            <div class="scrolling-logos">
                <img src="../images/company_logo/infosys.png" alt="Company 1" class="logo">
                <img src="../images/company_logo/tcs.png" alt="Company 2" class="logo">
                <img src="../images/company_logo/accenture.png" alt="Company 3" class="logo">
                <img src="../images/company_logo/cisco.png" alt="Company 4" class="logo">
                <img src="../images/company_logo/cognizant.jpg" alt="Company 4" class="logo">
                <img src="../images/company_logo/Deloitte.png" alt="Company 5" class="logo">
                <img src="../images/company_logo/federal bank.png" alt="Company 6" class="logo">
                <img src="../images/company_logo/intel.png" alt="Company 7" class="logo">
                <img src="../images/company_logo/LTImindtree.png" alt="Company 8" class="logo">
                <img src="../images/company_logo/wipro.png" alt="Company 9" class="logo">

            </div>
        </div>
</div>

<script>
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
        reader.onload = function(e) {
            document.getElementById('sidebarProfilePicture').src = e.target.result; // Update the profile image in sidebar
            document.getElementById('profileIcon').src = e.target.result; // Update profile icon
        };
        reader.readAsDataURL(file); // Read the image file
    }
}
 // Dashboard stats extraction
 const dashboardStats = {
            totalApplications: <?php echo $total_students; ?>,     // Total Applications
            activeJobs: <?php echo $active_jobs; ?>,
            eligibleJobs: <?php echo $total_placements; ?>,            // Active Jobs
               
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
        animateCounter(document.getElementById('total-students'), dashboardStats.totalApplications);
        animateCounter(document.getElementById('active-jobs'), dashboardStats.activeJobs);
        animateCounter(document.getElementById('placed-students'), dashboardStats.eligibleJobs);
       

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
            learInterval(counterInterval);
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