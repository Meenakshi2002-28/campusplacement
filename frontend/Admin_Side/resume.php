<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}// Include database connection

// Assuming you have user_id stored in session after login
$user_id = $_GET['user_id'] ?? null; // Use null coalescing to handle missing user_id
$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];  // Store the current page URL


// Check if user_id is set
if (!$user_id) {
    die("No user ID provided.");
} // Retrieve user_id from session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resume_link = $_POST['resume_link'];

    // SQL to update resume link
    $sql = "UPDATE student SET resume = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $resume_link, $user_id);

    if ($stmt->execute()) {
        header("Location: adminresumeview.php?user_id=" . urlencode($user_id));// Redirect to the desired page
        exit();
    } else {
        echo "Error inserting resume: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
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
    <link href="https://fonts.googleapis.com/css2?family=Merienda&display=swap" rel="stylesheet">
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
            top: 20px; /* Keep the same positioning */
            left: 50%;
            transform: translateX(-50%);
            font-size: 32px; /* Increase the font size here */
            font-weight: bold;
            color: white;
            text-align: center;
            font-family: 'Merienda', cursive;
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

        .icon {
            margin-left: 1px;
            cursor: pointer;
            transition: transform 0.3s;
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

        .tabs {
            display: flex;
            flex-direction: column;
            /* Arrange tabs vertically */
            margin-bottom: 20px;
            /* Space between tabs and content */
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
            background-color: #1e3d7a;
            /* Active tab color */
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
            display: none;
            /* Hide all tab content by default */
        }

        .tab-content.active {
            display: block;
            /* Show active tab content */
        }

        .profile-picture {
            width: 200px;
            /* Adjust width as needed */
            height: 200px;
            /* Ensure height equals width for a square */
            border-radius: 10px;
            /* Make it circular; use 0% for square */
            overflow: hidden;
            /* Hide overflow for perfect circle */
            border: 3px solid #1e3d7a;
            /* Optional border for profile picture */
            margin-bottom: 20px;
            /* Space below profile picture */
            position: relative;
            display: inline-block;

        }

        .profile-picture img {
            width: 100%;
            /* Ensure image fits the container */
            height: auto;
            /* Maintain aspect ratio */
        }

        .text {
            padding-top: 1px;
        }

        .text h4,
        p {
            margin: 2px;
            font-size: 18px;
            color: #000000;
            position: center;
        }

        /* Adjust sub-sidebar to float left */
        .sub-sidebar {
            float: left;
            width: 250px;
            /* Adjust width if needed */
            padding: 10px;
            margin-right: 20px;
            /* Spacing between sub-sidebar and form */
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

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            /* Ensure table layout doesn't break */
        }

        table td {
            padding: 6px;
            font-size: 18px;
            white-space: nowrap;
            vertical-align: middle;
            text-align: left;
            border: none;
        }

        table td:first-child {
            width: 30%;
            text-align: left;
            padding-right: 20px;
            /* Adjust for alignment between label and input */
        }

        input[type="radio"] {
            margin-right: 2px;
            /* Adds space between radio button and label */
        }

        .gender-options {
            display: flex;
            /* Ensures horizontal layout */
            gap: 5px;
            /* Adds space between radio button groups */
            align-items: center;
            /* Aligns radio buttons with labels */
        }

        .gender-options label {
            display: flex;
            align-items: center;
            gap: 1px;
            /* Adds space between radio button and its label */
        }

        input,
        select {
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #ddd;
            font-size: 16px;
            width: 100%;
        }

        input,
        select {
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #ddd;
            font-size: 16px;
            width: 100%;
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

        .modal button {
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
    </style>
</head>

<body>
    <!-- Profile Container -->
    <div class="container">
    <img src="../images/Customer.png" alt="Profile Icon" class="icon" id="profile_Icon"
            onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*"
            onchange="changeProfilePicture1(event)">
        <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-content">
            <a href=" profile_admin.php"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        <a href="dashboard_admin.php"><i class="fas fa-home"></i> Home</a>
        <a href="joblist_admin.php"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="view_students.php"  class="active"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="placedstd.php"><i class="fas fa-laptop-code"></i> Placements</a>
        <a href="company.html"><i class="fas fa-building"></i> Company</a>
        <a href="profile_admin.php"><i class="fas fa-user"></i> Profile</a>
        <a href="feedbacklist.php"><i class="fas fa-comment"></i> Feedback</a>
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

            <!-- Modal Structure -->
            <div id="profileModal" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal()">&times;</span>
                    <h4>Profile Pic</h4>
                    <p>Use <a href="#" target="_blank">Background Removal</a> site for removing Background.<br>
                        Use 300 X 300 px image for profile pic.</p>

                    <!-- Form for file upload -->
                    <form id="uploadForm" action="stdpicture.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id"
                            value="<?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="file" name="profilePicture" id="fileInput" accept="image/*" required>
                        <button type="submit" name="submit">Submit</button>
                    </form>
                </div>
            </div>

            <!-- Profile Picture Section -->
            <div class="tabs">
                <div class="tab"
                    onclick="window.location.href='profileredirect.php?user_id=<?php echo urlencode($user_id); ?>'">
                    Personal Details</div>
                <div class="tab"
                    onclick="window.location.href='academicredirect.php?user_id=<?php echo urlencode($user_id); ?>'">
                    Academic Details</div>
                <div class="tab active" onclick="showSection('resume')">Resume</div>
            </div>
        </div>
        <div id="resume" class="details">
            <h2>Resume</h2>
            <p>Paste the public link to your resume (Google Drive link). Make sure the link has public access
                permissions.</p>
            <form action="" method="POST">
                <input type="url" id="resume_link" name="resume_link" placeholder="Enter your resume link" required>
                <div class="button-container">
                    <button type="submit">SUBMIT</button>
                </div>
            </form>
        </div>

    </div>

    <!-- JavaScript -->
    <script>
    function loadProfilePicture1() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_adminprofilepicture.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var profilePath = xhr.responseText.trim();

                    document.getElementById('profile_Icon').src = profilePath;
                }
            };
            xhr.send();
        }



        var user_id = '<?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?>';
        function loadProfilePicture() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_stdprofilepicture.php?user_id=' + encodeURIComponent(user_id), true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var profilePath = xhr.responseText.trim();
                    document.getElementById('sidebarProfilePicture').src = profilePath;
                    document.getElementById('profileIcon').src = profilePath;
                }
            };
            xhr.send();
        }

        function loadAll() {
            loadProfilePicture();
            loadProfilePicture1();
        }
        window.onload = loadAll;
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