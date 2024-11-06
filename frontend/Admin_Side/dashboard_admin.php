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
    } // Make sure this file contains your DB connection code

    // Retrieve the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Prepare and execute a SQL query to fetch the user's name
    $query = "SELECT name FROM admin WHERE user_id = ?";
    
    // Using prepared statements to prevent SQL injection
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_id); // Assuming user_id is an integer
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
    }
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
</style>
</head>
<body>
<!-- Profile Container -->
<div class="container">
    <h3>Welcome to Lavaro</h3>
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
                    <p class="card-text"><i class="fas fa-file-alt"></i> <span class="counter" id="total-applications"></span> Total Students Registerd</p>
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
                    <p class="card-text"><i class="fas fa-check-circle"></i> <span class="counter" id="eligible-jobs"></span> Placed Students</p>
                </div>
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