<?php
// Database connection
session_start();
$servername = "localhost";
$username = "root"; // Update with your database username
$password = "";     // Update with your database password
$dbname = "campus_placement"; // Update with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = 'JESINE'; 
$role_query = "SELECT role FROM login WHERE user_id = ?";
$user_role = "";

if ($stmt = $conn->prepare($role_query)) {
    $stmt->bind_param("s", $user_id); // Assuming user_id is a string
    $stmt->execute();
    $stmt->bind_result($user_role);
    $stmt->fetch();
    $stmt->close();
}
// If the user is not an admin, display unauthorized access message
if ($user_role !== 'admin') {
    echo "<h1>Unauthorized Access</h1>";
    exit(); // Stop further script execution
}


// SQL query to fetch user_id, name, graduation_year, and course_name for 10 students
$sql = "
    SELECT s.user_id, s.name, s.graduation_year, c.course_name
    FROM student s
    JOIN course c ON s.course_id = c.course_id
    LIMIT 11
";

$result = $conn->query($sql);

// Check if there are results and output them
if ($result->num_rows > 0) {
    // Store results in an array
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row; // Add each row to the students array
    }
} else {
    $students = []; // No students found
}

// Close connection
$conn->close();

// Only return JSON if the request is an AJAX call
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($students);
    exit; // Terminate the script to prevent HTML output
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
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            /* Transparent glow effect */
            transition: width 0.4s ease-in-out;
            padding-top: 80px;
            /* Added padding for space at the top */
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
            /* Expands sidebar on hover */
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
            /* Glow effect */
        }

        .sidebar .logout {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
        }

        .sidebar a.active {
            background-color: #d9e6f4;
            /* Background color for active link */
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
            /* Default margin for sidebar */
            padding: 40px;
            font-size: 18px;
            color: #333;
            border-radius: 10px;
            transition: margin-left 0.4s ease-in-out;
            /* Smooth transition for margin */
            background-color: #ffffff;
            height: 86.5vh;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            /* Add shadow effect */
            overflow-y: auto;

        }

        .main-content h1 {
            color: #050505;
            font-size: 2.5rem;
            /* Increased font size */
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
            /* Default margin for container */
            margin-top: 12px;
            margin-right: 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            transition: margin-left 0.4s ease-in-out;
            /* Smooth transition for margin */
        }

        .icon {
            margin-left: 1px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        img {
            height: 40px;
            /* Adjust size as needed */
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

        .students {
            margin-top: 20px;
        }

        /* Card styling with hover effects */
        .card {
            background: linear-gradient(135deg, #a2c4fb, #9babcd);
            /* Gradient background */
            color: #000000;
            /* White text for better contrast */
            transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            /* Soft shadow effect */
        }

        .card-text i {
            margin-right: 10px;
            font-size: 1.8rem;
            color: #082765;
            /* Icon color */
        }

        .card:hover {
            transform: scale(1.05);
            /* Scale effect on hover */
            background-color: #e0e0ee;
            /* Light blue background on hover */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            /* Shadow effect */
        }

        /* Counter animation */
        .counter {
            font-size: 1.5rem;
            font-weight: bold;
            color: #04070b;
            transition: transform 0.3s ease-in-out;
        }

        .container h3 {
            margin-right: 450px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 10px 20px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px 20px;
        }

        /* Style the top bar container */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 10px 20px;
            position: relative;
        }

        /* Style the search bar container */
        .search-bar-container {
            margin-left: 320px;
        }

        /* Style the search bar */
        .search-bar {
            padding: 5px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 350px;
        }

        .search-bar:focus {
            outline: none;
            border-color: #363636;
        }

        .search-bar-container button {
            padding: 5px 12px;
            background-color: #1e165f;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
        }

        .approval{
            margin-top: -35px;
            margin-left: 900px;
        }

        .approval button{
            padding: 5px 12px;
            background-color: #1e165f;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
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
        <a href="view_students.php" class="active"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="placedstd.php"><i class="fas fa-laptop-code"></i> Placements</a>
        <a href="company.html"><i class="fas fa-building"></i> Company</a>
        <a href="profile_admin.php"><i class="fas fa-user"></i> Profile</a>
        <a href="feedbacklist.php"><i class="fas fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>
    <div class="main-content">
        <div class="search-bar-container">
            <input type="text" id="search-input" class="search-bar" placeholder="Search by Roll No or Name...">
            <button onclick="performSearch()">Search</button>
        </div>
        <div class="approval">
            <button onclick="window.location.href='acc_approval.php'">Account Approvals</button>
        </div>
        <div class="students">
            <table>
                <thead>
                    <tr>
                        <th>Roll no</th>
                        <th>Name</th>
                        <th>Graduation Year</th>
                        <th>Course Name</th>
                    </tr>
                </thead>
                <tbody id="studentData">
                    <!-- Student data will be populated here using JavaScript -->
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <tr onclick="window.location.href='profileredirect.php?user_id=<?php echo $student['user_id']; ?>'">
                                <td><?php echo $student['user_id']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td><?php echo $student['graduation_year']; ?></td>
                                <td><?php echo $student['course_name']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <script>
            function loadProfilePicture() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_adminprofilepicture.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var profilePath = xhr.responseText.trim();
                
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
            function performSearch() {
                const query = document.getElementById('search-input').value;

                // Use AJAX to send the search request
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'search_students.php?q=' + encodeURIComponent(query), true);
                xhr.onload = function () {
                    if (this.status === 200) {
                        // Update results container with the response
                        document.getElementById('studentData').innerHTML = this.responseText;
                    }
                };
                xhr.send();
            }        // Fetch student data
            fetch(window.location.href, { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('studentData');
                    data.forEach(student => {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td>${student.user_id}</td><td>${student.name}</td><td>${student.graduation_year}</td><td>${student.course_name}</td>`;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching student data:', error));




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