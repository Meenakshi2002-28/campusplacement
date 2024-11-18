<?php
// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path if PHPMailer is installed elsewhere

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch pending students
$sql = "
    SELECT l.user_id, l.email, s.name,l.approval_status
    FROM login l
    JOIN student s ON l.user_id = s.user_id
    
";

$result = $conn->query($sql);
$students = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    $update_sql = "UPDATE login SET approval_status = ? WHERE user_id = ?";
    $approval_status = ($action == 'approve') ? 'approved' : 'rejected';

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ss", $approval_status, $user_id);

    if ($stmt->execute()) {
        // Fetch user details for email
        $query = $conn->prepare("SELECT l.email, s.name FROM login l JOIN student s ON l.user_id = s.user_id WHERE l.user_id = ?");
        $query->bind_param("s", $user_id);
        $query->execute();
        $user_result = $query->get_result();

        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $email = $user['email'];
            $name = $user['name'];

            // Initialize PHPMailer
            $mail = new PHPMailer(true);

            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'meenakshiasas45@gmail.com'; // Your Gmail address
                $mail->Password = 'xpxr ottm oljg aine'; // Your app-specific password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465; // Port for SSL (465)

                // Sender and recipient settings
                $mail->setFrom('no-reply@yourwebsite.com', 'Lavoro'); // Update sender details
                $mail->addAddress($email, $name);

                // Email content
                $mail->isHTML(true);
                $subject = $action == 'approve' ? "Application Approved" : "Application Rejected";
                $message = $action == 'approve'
                    ? "Dear $name,<br><br>Your application for the Lavoro website has been <strong>approved</strong>.<br><br>Regards,<br>Admin Team"
                    : "Dear $name,<br><br>Your application for the Lavoro website has been <strong>rejected</strong>.<br><br>Regards,<br>Admin Team";

                $mail->Subject = $subject;
                $mail->Body = $message;

                // Send email
                $mail->send();
                echo "Email sent successfully to $email.";
            } catch (Exception $e) {
                echo "Failed to send email. Error: {$mail->ErrorInfo}";
            }
        }

        header("Location: allusers.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

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
            /* Transparent glow effect */
            transition: width 0.4s ease-in-out;
            padding-top: 80px;
            /* Added padding for space at the top */
        }

        .sidebar .logo {
            position: absolute;
            top: 20px;
            /* Positions logo/title closer to the top */
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
            padding: 5px 20px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th,
        td {
            padding: 7px 10px;
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
            margin-left: 350px;
        }

        /* Style the search bar */
        .search-bar {
            padding: 5px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 250px;
        }

        .search-bar:focus {
            outline: none;
            border-color: #363636;
        }

        .search-bar-container button {
            padding: 5px 15px;
            background-color: #1e165f;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
        }

        .approval button {
            margin-left: 950px;
            padding: 7px 20px;
            background-color: #AFC8F3;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            margin-top: -100px;
        }

        button {
            padding: 5px 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 500;
        }

        button:hover {
            font-weight: 700;
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
      
       
        <div class="students">
            <table>
                <thead>
                    <tr>
                        <th>Roll no</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Account Status</th>
                    </tr>
                </thead>
                <tbody id="studentData">
                    <!-- Student data will be populated here using JavaScript -->
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                                <td><?php echo $student['user_id']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td><?php echo $student['email']; ?></td>
                                <td>
                                    <form action="" method="post">

                                        <input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">

                                        <!-- Accept button -->
                                        <button type="submit" name="action" value="approve" style="<?php
                                        echo ($student['approval_status'] === 'approved')
                                            ? 'background-color: grey; color: white; cursor: not-allowed;'
                                            : 'background-color: #0aad0a; color: white;'; ?>" <?php echo ($student['approval_status'] === 'approved') ? 'disabled' : ''; ?>>
                                            Accept
                                        </button>

                                        <!-- Reject button -->
                                        <button type="submit" name="action" value="reject" style="<?php
                                        echo ($student['approval_status'] === 'rejected')
                                            ? 'background-color: grey; color: white; cursor: not-allowed;'
                                            : 'background-color: #e81313; color: white;'; ?>" <?php echo ($student['approval_status'] === 'rejected') ? 'disabled' : ''; ?>>
                                            Reject
                                        </button>
                                    </form>


                                </td>
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