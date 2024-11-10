<?php
// Start session to get the logged-in user's ID
session_start();

// Assuming you have stored the user's ID in session
$user_id = $_SESSION['user_id']; // Replace with the actual session key if different

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve application details
$sql = "
     SELECT j.company_name, j.job_title, ja.applied_date, ja.status
    FROM job_application ja
    JOIN job j ON ja.job_id = j.job_id
    WHERE ja.user_id = ?
    ORDER BY ja.applied_date DESC
";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all results into an array
$applications = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement
$stmt->close();
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

table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #ffffff;
            font-weight: bold;
        }

        /* Setting specific column widths */
        th:nth-child(1), td:nth-child(1) {
            width: 15%; /* Roll No Column */
        }

        th:nth-child(2), td:nth-child(2) {
            width: 25%; /* Name Column */
        }

        th:nth-child(3), td:nth-child(3) {
            width: 25%; /* Course Column */
        }
/* Filter Section */
.filters {
            position: absolute;
            top: 80px;
            right: 20px;
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        .filters label {
            display: block;
            margin-bottom: 10px;
        }

        .filters input[type="checkbox"] {
            margin-right: 10px;
        }
</style>
</head>

<body>
    <!-- Profile Container -->
    <div class="container">
    <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon">
    <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>
        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-content">
            <a href=" ../profile_redirect.php"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>    

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        <a href="dashboard_std.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="job.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="userapp.php"class="active"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="feedbackview.php"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <!-- Applicants Table -->
        <div class="applicants">
            <table>
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Position</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($applications)): ?>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($application['applied_date']); ?></td>
                    <td><?php echo htmlspecialchars($application['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No applications found.</td>
            </tr>
        <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Filter Section -->
        
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
// Change Profile Picture
function triggerFileInput() {
            document.getElementById('fileInput').click();
        }
    
        function changeProfilePicture(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
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