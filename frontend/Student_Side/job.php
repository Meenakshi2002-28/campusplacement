<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get all job postings
$sql = "SELECT job_id, company_name, job_title, work_environment, salary, job_status 
        FROM job 
        WHERE application_deadline >= CURDATE()"; // Ensure the job's deadline is not passed

$result = $conn->query($sql);
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
.job-card {
    width: 130vh;
    display: flex; /* Use flexbox for layout */
    justify-content: space-between; /* Space out items */
    align-items: center; /* Align items vertically */
    margin-bottom: 20px;
    /* Set the width of the card */
    height: 110px; /* Adjust height based on content */
    padding: 20px; /* Add padding inside the card */
    border: 1px solid #ddd; /* Optional: border for visual separation */
    border-radius: 5px; /* Optional: rounded corners */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); /* Optional: subtle shadow for depth */
    transition: transform 0.3s, box-shadow 0.3s;
     /* Smooth transition for transform and box-shadow */
     margin-left:50px;
     background-color: white;
     position:relative;
}

.job-card:hover {
    transform: translateY(-5px); /* Lift the card up on hover */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Enhance shadow on hover */
    border-color: #063dc9;
}

.job-details {
    flex: 1; /* Take up available space */
}

.job-info {
    text-align: right; /* Align text to the right */
}

.company-name {
    font-size: 1.5rem; /* Increase company name font size */
    font-weight: bold;
}

.position {
    font-size: 1.2rem; /* Position font size */
}

.salary {
    font-size: 1.2rem; /* Salary font size */
    font-weight: bold; /* Make salary bold */
    margin-right: 175px;
    margin-top: 25px;
}

.apply-now {
    background-color: #0056b3; /* Button background color */
    color: white; /* Button text color */
    border: none; /* Remove border */
    height: 45px;
    border-radius: 5px; /* Rounded corners for button */
    cursor: pointer; 
    margin-bottom: 25px;
   
    /* Pointer on hover */
}

.apply-now:hover {
    background-color: #0056b3; /* Darker blue on hover */
}
.remote {
    background-color:white; /* Green background for remote label */
    color: black; /* White text color */
    padding: 3px 8px; /* Padding around the label */
     /* Rounded corners */
    font-size: 0.8rem; /* Smaller font size */
    /* Increased space between company name and remote label */
    vertical-align: middle; /* Align vertically with text */
    font-weight: bold;
    font-size: 16px;
    margin-left: 230px;
}
.open {
    position: absolute;
    top: 50px; /* Adjust as needed */
    left: 250px; /* Adjust as needed */
    background-color: #075138;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.8rem;
    margin-left: 130px;
}

.company-logo {
    width: 50px; /* Adjust as needed */
    height: 50px; /* Adjust as needed */
    margin-right: 20px; /* Space between logo and job details */
    border-radius: 5px; /* Optional: rounded edges */
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
        <a href="job.php" class="active"><i class="fa fa-fw fa-search"></i> Jobs</a>
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
        <?php
    if ($result->num_rows > 0) {
        // Output data of each job row
        while ($row = $result->fetch_assoc()) {
            ?>
              <div class="job-card" onclick="window.location.href='job_description.php?job_id=<?php echo $row['job_id']; ?>'">
               
                <div class="job-details">
                    <div class="company-name"><?php echo htmlspecialchars($row['company_name']); ?></div>
                    <div class="position"><?php echo htmlspecialchars($row['job_title']); ?></div>
                    <div class="remote"><?php echo htmlspecialchars($row['work_environment']); ?></div>
                    <?php if ($row['job_status'] == 'Open') { ?>
                        <span class="open">Open for Applicants</span>
                    <?php } else { ?>
                        <span class="open">Closed for Applicants</span>
                    <?php } ?>
                </div>
                <div class="job-info">
                    <div class="salary">Salary: <?php echo htmlspecialchars($row['salary']); ?></div>
                    <button class="apply-now">view details</button>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No job postings available at the moment.</p>";
    }

    // Close the database connection
    $conn->close();
    ?>

  </div>
  
  
    

    <!-- JavaScript -->
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
