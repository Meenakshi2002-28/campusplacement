<?php
$servername = "localhost"; // Server name
$db_username = "root"; // MySQL username
$db_password = ""; // MySQL password
$dbname = "campus_placement"; // Database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback from the database
$sql = "SELECT feedback.user_id,feedback.feedback_id, feedback.feedback,  feedback.submission_date,student.name 
        FROM feedback 
        JOIN student ON feedback.user_id = student.user_id
         ORDER BY feedback.submission_date DESC";
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
.feedback-container {
    margin-top: -10px;
    padding: 5px;
}

.feedback-item {
    background-color: #ffffff;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    padding: 4px;
    margin-bottom: 20px;
    border-radius: 20px;
    border-style:linear-gradient(130deg, #f5f7fa,rgb(181, 181, 255));
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.feedback-item:hover {
        transform: scale(1.01);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
@keyframes gradientAnimation {
         0% { background-position: 0% 50%; }
        100% { background-position: 100% 50%; }
        }
        

        .feedback-item:hover {
    background: linear-gradient(130deg, #f5f7fa, rgb(181, 181, 255));
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

    

.feedback-item h3 {
    color: #000000;
    margin-top: 3px;
}

.feedback-item p {
    color: #000000;
    font-size: 16px;
    margin-left: 50px;
}

button {
    background-color:#ffc107; 
    border: none;
    color: white ;
    padding: 5px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 10px;
    margin-top:-70px;
    margin-left:1000px;
    font-weight: 600;
}
.logo-container {
        position: absolute;
        top: 10px;
        left: 10px;
        }
        .logo {
        height: 50px;
        width: auto;
        }
        .user-info {
    display: flex;
    align-items:center ; /* Vertically centers the icon and name */
    gap: 10px; /* Adjust spacing between icon and name as needed */
}

.user-info img{
    margin-left: 5px;
    margin-top: 5px;
    width: 35px; /* Set the size for the icon */
    height: 35px;
    border-radius: 50%; /* Makes the icon circular if it's a square */
}
.user-info h5{
    padding-top: 7px;
}
    </style>
</head>
<body>
    <!-- Profile Container -->
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
<input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">
<i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>
        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-content">
            <a href=" profile_admin.php"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>
   

    <!-- Sidebar -->
        <!-- Sidebar -->
        <div class="sidebar">
    <!-- Logo or Website Name -->
    <div class="logo">Lavoro</div>
    <a href="dashboard_admin.php" ><i class="fas fa-home"></i> Home</a>
    <a href="joblist_admin.php"><i class="fas fa-briefcase"></i> Jobs</a>
    <a href="view_students.php"><i class="fas fa-user-graduate"></i> Students</a>
    <a href="placedstd.php"><i class="fas fa-laptop-code"></i> Placements</a>
    <a href="company.html"><i class="fas fa-building"></i> Company</a>
    <a href="profile_admin.php"><i class="fas fa-user"></i> Profile</a>
    <a href="feedbacklist.php"  class="active"><i class="fas fa-comment"></i> Feedback</a>
    <div class="logout">
        <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
    </div>
</div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="feedback-container">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo '<div class="feedback-item">';
                    echo '<div class="user-info">';
                    echo '<img src="../images/profile.png" alt="User Profile">'; // Replace with actual user profile image if available
                    echo '<h5>' . htmlspecialchars($row["name"]) . '</h5>';
                    echo '</div>';
                    echo '<p>' . htmlspecialchars($row["feedback"]) . '</p>';
                    echo '<button onclick="window.location.href=\'feedback_response.php?user_id=' . urlencode($row["user_id"]) . '&feedback_id=' . urlencode($row["feedback_id"]) . '\'">RESPONSE</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>No feedback available.</p>';
            }
            ?>
            </div>
        </div>
        
       
        
    </div>

    <!-- JavaScript -->
    <script>
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
<?php
// Close the database connection
$conn->close();
?>    
</body>
</html>

