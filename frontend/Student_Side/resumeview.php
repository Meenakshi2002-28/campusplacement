<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$servername = "localhost";
$username = "root"; // Update with your database username
$password = "";     // Update with your database password
$dbname = "campus_placement"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Prepare SQL statement
$sql = "SELECT resume,name FROM student WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($resume,$name);
$stmt->fetch();



$stmt->close();
$conn->close();

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

        .tabs {
            display: flex;
            flex-direction: column; /* Arrange tabs vertically */
            margin-bottom: 20px; /* Space between tabs and content */
            width: 200px;
        }

        .tab {
            padding: 10px;
            margin-bottom: 5px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }

        .tab.active {
            background-color: #1e3d7a; /* Active tab color */
            color: white;
        }

        .tab:hover {
            font-weight: bold;
        }

        .content-area {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Tab content */
        .tab-content {
            display: none; /* Hide all tab content by default */
        }

        .tab-content.active {
            display: block; /* Show active tab content */
        }

        /* Profile section styling */
        .profile-picture {
            width: 200px; /* Adjust width as needed */
            height: 200px; /* Ensure height equals width for a square */
            border-radius: 10px;/* Make it circular; use 0% for square */
            overflow: hidden; /* Hide overflow for perfect circle */
            border: 3px solid #1e3d7a; /* Optional border for profile picture */
            margin-bottom: 20px; /* Space below profile picture */
            position: relative;
            display: inline-block;
        }

        .profile-picture img {
            width: 100%; /* Ensure image fits the container */
            height: auto; /* Maintain aspect ratio */
        }

        /* Adjust sub-sidebar to float left */
        .sub-sidebar {
            float: left;
            width: 250px; /* Adjust width if needed */
            padding: 10px;
            margin-right: 20px; /* Spacing between sub-sidebar and form */
        }
        .text h4, p {
            margin: 2px;
            font-size: 18px;
            color: #000000;
        }

        /* Adjust details container */
        .details {
            flex: 1;
            background-color: white;
            padding: 0;
            height: 80vh;
            overflow-y: auto;
        }

        .details.active {
            background-color: #ffffff;
            padding-left: 50px;
            display: block;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        button {
            padding: 7px 25px;
            background-color: #AFC8F3;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
        }

        button:hover {
            background-color: #1e3d7e;
            color: white;
        }

        #editImageButton {
            position: absolute;
            top: 90%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            background-color: #AFC8F3;
            color: black;
            font-size: 15px;
            border: none;
            margin-bottom: 2px;
            width: 60px;
            height: 30px;
            padding: 0px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .profile-picture:hover #editImageButton {
            display: block;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 260px;
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .modal button{
            margin-left: 120px;
            margin-top: 5px;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #000;
        }
        .text h4{
            padding-left: 35px; 
            margin-bottom:15px; 
            margin-top :-12.5px;
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
        <a href="userapp.php"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php" class="active"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="feedbackview.php"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="sub-sidebar">
            <div class="profile-picture" onmouseover="showEditButton()" onmouseout="hideEditButton()">
                <img src="../images/Customer.png" alt="profile picture" id="sidebarProfilePicture">
                <button id="editImageButton" style="display: none;" onclick="openModal()">EDIT</button>
            </div>
            <div id="profileModal" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal()">&times;</span>
                    <h4>Profile Pic</h4>
                    <p>Use <a href="#" target="_blank">Background Removal</a> site for removing Background.<br>
                        Use 300 X 300 px image for profile pic.</p>

                    <!-- Form for file upload -->
                    <form id="uploadForm" action="picture.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="profilePicture" id="fileInput" accept="image/*" required>
                        <button type="submit" name="submit">Submit</button>
                    </form>
                </div>
            </div>
            <div class="text">
                <h4><?php echo htmlspecialchars($name); ?></h4> <!-- Admin's name -->  
            </div>

            <!-- Profile Picture Section -->
            <div class="tabs">
                <div class="tab" onclick="window.location.href=' ../profile_redirect.php'">Personal Details</div>
                <div class="tab" onclick="window.location.href='academic_redirect.php'">Academic Details</div>
                <div class="tab active" onclick="showSection('resume')">Resume</div>
            </div>
        </div>

        <div id="resume" class="details">
            <h2>Resume</h2>
            <form action="editresume.php" method="POST">
                <?php
                // Assuming $resume contains the resume link from the database
                if ($resume) {
                    echo "<a href='" . htmlspecialchars($resume) . "' target='_blank'>Download Resume</a>";
                } else {
                    echo "No resume available.";
                }
                ?>
            <div class="button-container">
                <button type="submit">EDIT</button>
            </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    function loadProfilePicture() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_profilepicture.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var profilePath = xhr.responseText.trim();
                document.getElementById('sidebarProfilePicture').src = profilePath;
                document.getElementById('profileIcon').src = profilePath;
            }
        };
        xhr.send();
    }

    window.onload = loadProfilePicture;
    function showEditButton() {
        document.getElementById('editImageButton').style.display = 'block';
    }

    function hideEditButton() {
        document.getElementById('editImageButton').style.display = 'none';
    }

    function openModal() {
        document.getElementById('profileModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    function uploadProfilePicture() {
        // Implement file upload logic here
        alert('Upload functionality goes here');
    }

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
