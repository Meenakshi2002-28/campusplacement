<?php
// Database connection
$servername = "localhost"; // Change to your server name
$username = "root";        // Change to your database username
$password = "";            // Change to your database password
$dbname = "campus_placement"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming you have the user_id available (for example, from a session)
$user_id = 'JESINE'; // Replace with the actual user ID

// Fetch admin details
$sql = "SELECT a.name, a.phone_number, l.email FROM admin a JOIN login l ON a.user_id = l.user_id WHERE a.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$adminDetails = null;
if ($result->num_rows > 0) {
    $adminDetails = $result->fetch_assoc();
}

// Close the connection
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

        .sidebar a:nth-child(8) {
            animation-delay: 0.7s;
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
            overflow-y: auto;
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

        .profile-picture {
            width: 200px; /* Adjust width as needed */
            height: 200px; /* Ensure height equals width for a square */
            border-radius: 10px;/* Make it circular; use 0% for square */
            overflow: hidden; /* Hide overflow for perfect circle */
            margin-bottom: 20px; /* Space below profile picture */
            position: relative;
            display: inline-block;
            margin-top: 10px;
            margin-left: 15px;
            
        }

        .profile-picture img {
            width: 100%; /* Ensure image fits the container */
            height: auto; /* Maintain aspect ratio */
        }

        .text {
            padding-top: -10px;
            padding-left: 20px;
        }

        .text p {
            padding-left: 55px;
            font-size: 22px;
        }

        .text h4 {
            font-size: 23px;
            padding-left: -10px;
        }

        .text h4,p {
            margin: 2px;
            color: #000000;
        }

        .details {
            background-color: white;
            max-width: 700px;
            margin: auto;
            margin-top: -230px;
            display: none;
        }

        .details.active {
            background-color: #ffffff;
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
            margin-left: 40px;
        }

        input,select {
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #ddd;
            font-size: 16px;
            width: 100%;
            margin-left: 20px;
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
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
        }

        button:hover {
            background-color: #1e165f;
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
            top: 45%;
            left: 47%;
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
    </style>
</head>

<body>
    <!-- Profile Container -->
    <div class="container">
        <img src="../images/Customer.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*"
            onchange="changeProfilePicture(event)">
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
        <a href="view_students.php"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="placedstd.php"><i class="fas fa-laptop-code"></i> Placements</a>
        <a href="company.html"><i class="fas fa-building"></i> Company</a>
        <a href="profile_admin.php" class="active"><i class="fas fa-user"></i> Profile</a>
        <a href="feedbacklist.php"><i class="fas fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>
    <div class="main-content">
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
                    <form id="uploadForm" action="adminpicture.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="profilePicture" id="fileInput" accept="image/*" required>
                        <button type="submit" name="submit">Submit</button>
                    </form>
                </div>
            </div>
            <div class="text">
                <h4><?php echo htmlspecialchars($adminDetails['name']); ?></h4> <!-- Admin's name -->
                <p>Admin</p>
            </div>
       

        <div id="personal" class="details active">
            <h2>Personal Details</h2>
            <form method="POST" action="profileedit.php">
            <table>
                    <tr>
                        <td>Name </td>
                        <td>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($adminDetails['name']); ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>Email </td>
                        <td>
                            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($adminDetails['email']); ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>Phone No </td>
                        <td>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($adminDetails['phone_number']); ?>" readonly>
                        </td>
                    </tr>
                </table>
                <div class="button-container">
                <button type="button" onclick="enableEdit()">EDIT</button> <!-- Add an edit button -->
                <button type="submit" id="saveBtn" style="display: none;">SAVE</button> <!-- Save button initially hidden -->
                </div>
            </form>
        </div>
    </div>

    <script>
         function enableEdit() {
        // Get all input fields
        var inputs = document.querySelectorAll('#name, #email, #phone');
        
        // Loop through inputs and remove the 'readonly' attribute
        inputs.forEach(function(input) {
            input.removeAttribute('readonly');
        });

        // Show the Save button
        document.querySelector('#saveBtn').style.display = 'inline-block';

        // Change button text to "Save"
        document.querySelector('.button-container button').textContent = "Cancel";
        document.querySelector('.button-container button').setAttribute('type', 'button');
        document.querySelector('.button-container button').setAttribute('onclick', 'cancelEdit()');
    }

    function cancelEdit() {
        // Get all input fields
        var inputs = document.querySelectorAll('#name, #email, #phone');
        
        // Loop through inputs and reset the value to original
        inputs.forEach(function(input) {
            input.value = input.defaultValue;
            input.setAttribute('readonly', 'readonly');
        });

        // Hide the Save button
        document.querySelector('#saveBtn').style.display = 'none';

        // Change button text back to "Edit"
        document.querySelector('.button-container button').textContent = "EDIT";
        document.querySelector('.button-container button').setAttribute('onclick', 'enableEdit()');
    }
             function loadProfilePicture() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_adminprofilepicture.php', true);
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