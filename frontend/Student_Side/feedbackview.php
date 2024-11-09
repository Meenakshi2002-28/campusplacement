<?php
session_start();
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
// Include your database connection file

// Assuming you have the user_id stored in session after login
$user_id = $_SESSION['user_id'];

// Fetch feedback and user details
$query = "SELECT f.feedback, s.name ,f.response,f.feedback_id
          FROM feedback f 
          JOIN student s ON f.user_id = s.user_id 
          WHERE f.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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
.company-info {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 30px;
    padding: 10px;
}
/* New styles for animations and effects */

/* Fade-in effect on load for company cards */
@keyframes fadeInCards {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Scale-up hover effect with shadow */
.company-card {
    background-color: #ffffff;
    border: 1px solid #aaaaaa;
    padding: 20px;
    border-radius: 20px;
    font-size: 16px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    opacity: 0;
    animation: fadeInCards 0.6s ease forwards;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    color: #000000;
}

.company-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    background: linear-gradient(135deg, #f5feff, #ffffff);
}

/* Add a delay to each card to create a staggered animation effect */
.company-card:nth-child(1) { animation-delay: 0.1s; }
.company-card:nth-child(2) { animation-delay: 0.2s; }
.company-card:nth-child(3) { animation-delay: 0.3s; }
.company-card:nth-child(4) { animation-delay: 0.4s; }
.company-card:nth-child(5) { animation-delay: 0.5s; }
.company-card:nth-child(6) { animation-delay: 0.6s; }
.company-card:nth-child(7) { animation-delay: 0.7s; }
.company-card:nth-child(8) { animation-delay: 0.8s; }
.company-card:nth-child(9) { animation-delay: 0.9s; }
.company-card:nth-child(10) { animation-delay: 0.10s; }
.company-card:nth-child(11) { animation-delay: 0.11s; }
/* Subtle hover animation for the View Website link */
.company-card a {
    display: inline-block;
    padding: 8px 12px;
    color: #000000;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.3s ease;
    font-weight: 500;
    font-size: 14px;
}

.company-card a:hover {
    color: #1e3d7a;
    transform: translateY(-3px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    font-weight: bold;
}

/* Subtle image zoom-in on hover */
.company-card img {
    align-items: center;
}

.company-card h3 {
    padding-top: 5px;
    font-size: 15px /* Adjust the size as needed */
}


.search {
    padding-top: 1px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.search h2 {
    margin: 0; /* Removes default margin */
    text-align: center; /* Center-aligns the text */
    width: 100%; /* Ensures the heading takes up full width */
    margin-top: 0;
}

.search-bar {
    margin: 0;
}

#companySearch {
    width: 350px; /* Fixed width or adjust as needed */
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 15px;
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
        top:20px; /* Keep the same positioning */
        left: 50%;
        transform: translateX(-50%);
        font-size: 36px; /* Increase the font size here */
        font-weight: bold;
        color: white;
        text-align: center;
    }
    .feedback-section {
            padding: 5px;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top:-30px;
        }

        .feedback-header button {
            background-color: #1e165f;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .feedback-card {
            border: 1px solid inherit;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 5px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(12deg, #f5f7fa,rgb(181, 181, 255));
        }
        @keyframes gradientAnimation {
         0% { background-position: 0% 50%; }
        100% { background-position: 100% 50%; }
        }
       

        .feedback-card .user-info {
            display: flex;
            align-items: center;

        }

        .feedback-card .user-info img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-left: 5px;
            margin-top: 5px;
        }

        .feedback-card .user-info .name {
            font-weight: 500;
            font-size: 20px;
            margin-left: 7px;
        }

        .feedback-card .feedback-text {
            font-size: 17px;
            color: black;
            margin-left: 45px;
            margin-bottom: 5px;
        }

        .feedback-card .view-response-btn {
            margin-top: 0px;
            text-align: right;
            margin-bottom: 5px;
           margin-right: 5px;
        }

        .feedback-card .view-response-btn button {
            background-color:#FFC107;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        .feedback-card:hover {
         transform: scale(1.02);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .feedback-card .view-response-btn button:hover {
            background-color: #1e165f;
        }

        .admin-response {
            display: none; /* Initially hide the admin response */
            padding: 15px;
            background-color: white;
            margin-top: 10px;
            border-radius: 20px;
            font-size: 16px;
            color: black;
            margin-bottom: 5px;
            margin-left: 2.5px;
            margin-right: 2.5px;

        }
        .feedback-header button{
            font-size: 18px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-weight: 600;
            border-radius: 10px;
            margin-bottom: 5px;

        }
        .feedback-header button:hover{
            transform: scale(1.03);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);

        }
        @keyframes gradientAnimation {
         0% { background-position: 0% 50%; }
        100% { background-position: 100% 50%; }
        }
        .feedback-card .view-response-btn button:hover {
            background-color:#FFC107;

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
    </style>
</head>
<body>
    <!-- Profile Container -->
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon"
            onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*"
            onchange="changeProfilePicture(event)">
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
        <a href="userapp.php"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="feedbackview.php" class="active"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Main Content -->
    
        <div class="main-content">
            <div class="feedback-section">
                <div class="feedback-header">
                    <h2>Feedbacks</h2>
                    <a href="feedback.php">
                        <button>+ Add new</button>
                    </a>
                    
                </div>
    
                <!-- Feedback Card 1 -->
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php $responseId = 'response' . $row['feedback_id']; // Unique response ID ?>
                <div class="feedback-card">
                    <div class="user-info">
                        <img src="../images/profile.png" alt="User Profile">
                        <span class="name"><?php echo htmlspecialchars($row['name']); ?></span>
                    </div>
                    <div class="feedback-text">
                        <?php echo htmlspecialchars($row['feedback']); ?>
                    </div>
                    <div class="view-response-btn">
                        <button onclick="toggleResponse('<?php echo $responseId; ?>')">VIEW RESPONSE</button>
                    </div>
                    <div id="<?php echo $responseId; ?>" class="admin-response" style="display: none;">
                        <?php echo htmlspecialchars($row['response']); ?>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
    

           
    </div>

    <!-- JavaScript -->
    <script>
        // Change Profile Picture
        function triggerFileInput() {
            document.getElementById('fileInput').click();
        }
        function toggleFeedback(element) {
            const fullText = element.querySelector('.feedback-full');
            fullText.style.display = fullText.style.display === 'none' ? 'block' : 'none';
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
        // Change profile image
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
    
            let dropdownOpen = false;
            function toggleDropdown() {
                const dropdown = document.getElementById("dropdownMenu");
                dropdownOpen = !dropdownOpen;
                dropdown.style.display = dropdownOpen ? "block" : "none";
            }
    
            // Toggle response visibility
            function toggleResponse(responseId) {
            const response = document.getElementById(responseId);
            if (response.style.display === "none" || response.style.display === "") {
                response.style.display = "block";  // Show the response
            } else {
                response.style.display = "none";  // Hide the response
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
