<?php
session_start(); // Start the session to access session variables

// Check if user_id is set in the session
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

    // Initialize variables for user details and profile completion
    $name = '';
    $cgpa = 0.0;
    $profile_completion = 0;

    // 1. Fetch the user's name, CGPA, and resume from the student table
    $query = "SELECT name, cgpa, resume FROM student WHERE user_id = ?";
    $has_student_row = false;
    $resume = '';

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result($name, $cgpa, $resume);
        
        if ($stmt->fetch()) {
            $has_student_row = true;
            $profile_completion = 40; // Profile completion is 40% if row exists in student table
        }
        $stmt->close();
    }

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

    // Close the database connection
    $conn->close();

    // Now you have $name, $cgpa, $application_count, $active_job_count, $eligible_job_count, and $profile_completion
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

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
*{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

.wrapper{
    margin-top: 20px;
  width: 300px;
  height: 300px;
  overflow: hidden;
  background: #fff;
  border-radius: 10px;
  margin-left: 10px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}
.wrapper header{
  display: flex;
  align-items: center;
  padding: 25px 30px 10px;
  justify-content: space-between;
}
header .icons{
  display: flex;
}
header .icons span{
  height: 38px;
  width: 38px;
  margin: 0 1px;
  cursor: pointer;
  color: #082765;
  text-align: center;
  line-height: 38px;
  font-size: 1.9rem;
  user-select: none;
  border-radius: 50%;
}
.icons span:last-child{
  margin-right: -10px;
}
header .icons span:hover{
  background: #f2f2f2;
}
header .current-date{
  font-size: .5rem;
  font-weight: 500;
}
.calendar{
  padding: 20px;
}
.calendar ul{
  display: flex;
  flex-wrap: wrap;
  list-style: none;
  text-align: center;
}
.calendar .days{
  margin-bottom: 20px;
}
.calendar li{
  color: #333;
  width: calc(100% / 7);
  font-size: .5rem;
}
.calendar .weeks li{

  font-weight: 500;
  cursor: default;
}
.calendar .days li{
  z-index: 1;
  cursor: pointer;
  position: relative;
  margin-top: 30px;
}
.days li.inactive{
  color: #aaa;
}
.days li.active{
  color: #fff;
}
.days li::before{
  position: absolute;
  content: "";
  left: 50%;
  top: 50%;
  height: 40px;
  width: 40px;
  z-index: -1;
  border-radius: 50%;
  transform: translate(-50%, -50%);
}
.days li.active::before{
  background: #082765;
}
.days li:not(.active):hover::before{
  background: #f2f2f2;
}
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
.small-icon {
    width: 50px; /* Set desired width */
    height: 50px; /* Set desired height */
    object-fit: cover; /* Ensures the image scales properly */
    border-radius: 50%;
     /* Makes the image circular */
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
        /* Card styling with hover effects */
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

        /* Counter animation */
        .counter {
            font-size: 1.5rem;
            font-weight: bold;
            color: #04070b;
            transition: transform 0.3s ease-in-out;
        }
        .sidebar .logo {
    position: absolute;
    top: 20px; /* Keep the same positioning */
    left: 50%;
    transform: translateX(-50%);
    font-size: 36px; /* Increase the font size here */
    font-weight: bold;
    color: white;
    text-align: center;
}
.container h3{
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
    height: 30px; /* Adjust logo height */
    margin: 0 15px; /* Spacing between logos */
    object-fit: contain;
    transition: transform 0.3s;
}

.scrolling-logos .logo:hover {
    transform: scale(1.1); /* Slight zoom on hover */
}

/* Scrolling Animation */
@keyframes slide {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

    </style>
</head>
<body>
<div class="container">
        <h3>Welcome to Lavaro</h3>
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">
        <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>
        <!-- Dropdown Menu -->
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
                        <p class="card-text"><i class="fas fa-file-alt"></i> <span class="counter" id="total-applications"> <?php echo $application_count; ?></span> Applications</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Active Jobs</h5>
                        <p class="card-text"><i class="fas fa-briefcase"></i> <span class="counter" id="active-jobs"> <?php echo $active_job_count; ?></span> Open Positions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Eligible Jobs</h5>
                        <p class="card-text"><i class="fas fa-check-circle"></i> <span class="counter" id="eligible-jobs"><?php echo $eligible_job_count; ?></span> Eligible Positions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Profile Completion</h5>
                        <p class="card-text"><i class="fas fa-check-circle"></i> <span class="counter" id="profile-completion"> <?php echo $profile_completion; ?>%</span> Complete</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="wrapper">
            <header>
                <p class="current-date"></p>
                <div class="icons">
                    <span id="prev" class="material-symbols-rounded">chevron_left</span>
                    <span id="next" class="material-symbols-rounded">chevron_right</span>
                </div>
            </header>
            <div class="calendar">
                <ul class="weeks">
                    <li>Sun</li>
                    <li>Mon</li>
                    <li>Tue</li>
                    <li>Wed</li>
                    <li>Thu</li>
                    <li>Fri</li>
                    <li>Sat</li>
                </ul>
                <ul class="days"></ul>
            </div>
        </div>

        <!-- Scrolling Marquee Section for Company Logos -->
        <div class="scrolling-section">
    <div class="scrolling-logos">
        <img src="../images/company_logo/infosys.png" alt="Company 1" class="logo">
        <img src="../images/company_logo/tcs.png" alt="Company 2" class="logo">
        <img src="https://framerusercontent.com/images/bNcmzTEX4AQx6bHzeTNLOAvPhM.png" alt="Company 3" class="logo">
        <img src="https://framerusercontent.com/images/BP0vuq7mtsXsInJhngcYqwUFk4.png" alt="Company 4" class="logo">
        <img src="../images/company_logo/infosys.png" alt="Company 1" class="logo">
        <img src="../images/company_logo/tcs.png" alt="Company 2" class="logo">
        <img src="https://framerusercontent.com/images/bNcmzTEX4AQx6bHzeTNLOAvPhM.png" alt="Company 3" class="logo">
        <img src="https://framerusercontent.com/images/BP0vuq7mtsXsInJhngcYqwUFk4.png" alt="Company 4" class="logo">
     
    </div>
</div>

    </div>
    <script>
        const daysTag = document.querySelector(".days"),
currentDate = document.querySelector(".current-date"),
prevNextIcon = document.querySelectorAll(".icons span");

// getting new date, current year and month
let date = new Date(),
currYear = date.getFullYear(),
currMonth = date.getMonth();

// storing full name of all months in array
const months = ["January", "February", "March", "April", "May", "June", "July",
              "August", "September", "October", "November", "December"];

const renderCalendar = () => {
    let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(), // getting first day of month
    lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(), // getting last date of month
    lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(), // getting last day of month
    lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate(); // getting last date of previous month
    let liTag = "";

    for (let i = firstDayofMonth; i > 0; i--) { // creating li of previous month last days
        liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
    }

    for (let i = 1; i <= lastDateofMonth; i++) { // creating li of all days of current month
        // adding active class to li if the current day, month, and year matched
        let isToday = i === date.getDate() && currMonth === new Date().getMonth() 
                     && currYear === new Date().getFullYear() ? "active" : "";
        liTag += `<li class="${isToday}">${i}</li>`;
    }

    for (let i = lastDayofMonth; i < 6; i++) { // creating li of next month first days
        liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`
    }
    currentDate.innerText = `${months[currMonth]} ${currYear}`; // passing current mon and yr as currentDate text
    daysTag.innerHTML = liTag;
}
renderCalendar();

prevNextIcon.forEach(icon => { // getting prev and next icons
    icon.addEventListener("click", () => { // adding click event on both icons
        // if clicked icon is previous icon then decrement current month by 1 else increment it by 1
        currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;

        if(currMonth < 0 || currMonth > 11) { // if current month is less than 0 or greater than 11
            // creating a new date of current year & month and pass it as date value
            date = new Date(currYear, currMonth, new Date().getDate());
            currYear = date.getFullYear(); // updating current year with new date year
            currMonth = date.getMonth(); // updating current month with new date month
        } else {
            date = new Date(); // pass the current date as date value
        }
        renderCalendar(); // calling renderCalendar function
    });
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
    
            // Dashboard stats extraction
            const dashboardStats = {
                totalApplications:  <?php echo $application_count; ?>,     // Total Applications
                activeJobs:  <?php echo $active_job_count; ?>,  
                eligibleJobs:<?php echo $eligible_job_count; ?>,            // Active Jobs
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