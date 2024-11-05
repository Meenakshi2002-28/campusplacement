<?php
// Include database connection file
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

if (isset($_GET['user_id']) && isset($_GET['feedback_id'])) {
    $user_id = $_GET['user_id'];
    $feedback_id = $_GET['feedback_id'];

    // Query to fetch the feedback and existing response
    $sql = "SELECT f.feedback, f.response, s.name 
            FROM feedback f 
            JOIN student s ON f.user_id = s.user_id 
            WHERE f.user_id = ? AND f.feedback_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $user_id, $feedback_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the feedback record exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $feedback_text = htmlspecialchars($row['feedback']);
        $response_text = htmlspecialchars($row['response']);
        $user_name = htmlspecialchars($row['name']);
    } else {
        echo "Feedback not found.";
        exit();
    }
    $stmt->close();
}
$success = false;
// Handle form submission to update response
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['response'])) {
    $response = $_POST['response'];
   

    // Update the response in the feedback table
    $update_sql = "UPDATE feedback SET response = ? WHERE user_id = ? AND feedback_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $response, $user_id, $feedback_id);

    if ($stmt->execute()) {
        $success = true;
    } else {
        echo "Error updating response: " . $conn->error;
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
    <title>Lavoro - Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> <!-- SweetAlert CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    background: #2a2185;
    color: white;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.5); /* Transparent glow effect */
    transition: width 0.4s ease-in-out;
    padding-top: 80px; /* Added padding for space at the top */
    overflow: hidden;
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
            margin-left: 245px; /* Default margin for container */
            margin-top: 12px;
            margin-right: 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            transition: margin-left 0.4s ease-in-out; /* Smooth transition for margin */
        }

        .icon {
            margin-left: 15px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .icon:hover {
            transform: scale(1.1);
        }

        /* Dropdown menu styling */
        .dropdown-content {
            display: none;
            opacity: 0;
            position: absolute;
            top: 55px;
            right: 20px;
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
    top: 20px; /* Keep the same positioning */
    left: 50%;
    transform: translateX(-50%);
    font-size: 36px; /* Increase the font size here */
    font-weight: bold;
    color: white;
    text-align: center;
}
.feedback-response{
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 20px;
    background:linear-gradient(130deg, #f5f7fa,rgb(181, 181, 255));

}


        .feedback-response .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .feedback-response .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .feedback-response .user-info .name {
            font-weight: bold;
            font-size: 18px;
        }

        .feedback-response p {
            color: #333;
            font-size: 16px;
        }

        .response-container {
            margin-top: 20px;
        }

        textarea {
            width: 100%;
            height: 200px;
            padding: 10px;
            border-radius: 10px;
            border:1px solid #9ba2fd;
            font-size: 16px;
            margin-top: 10px;
            resize: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
            @keyframes gradientAnimation {
         0% { background-position: 0% 50%; }
        100% { background-position: 100% 50%; }
    }
        textarea:hover {
         transform: scale(1.03);
         box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
            
            
        
        button { 
            margin-top: 20px;
            padding: 15px 40px;
            border-radius: 50px;
            cursor: pointer;
            border-width: 3px;
            border:0;
            box-shadow: rgba(255, 255, 255, 0.05) 0 0 8px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 15px;
            transition: all 0.5s ease;
            margin-left:400px;
}

 button:hover {
  letter-spacing: 3px;
  background-color:#1e3d7a;
  color: hsl(0, 0%, 100%);
  box-shadow: rgb(44, 11, 105) 0px 7px 29px 0px;
}

        /* Top-right profile and dropdown */
        .container {
            padding: 5px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            cursor: pointer;
        }

        .container img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #2F5597;
            min-width: 150px;
            z-index: 1;
            top: 55px;
            right: 0;
            border-radius: 3px;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #1e165f;
            color: white;
            border-radius: 3px;
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
    </style>
</head>
<body>
    <!-- Profile Container -->
    <div class="container">
        <img src="../profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">
        <i class="fas fa-caret-down fa-lg icon" aria-hidden="true" onclick="toggleDropdown()"></i>
        
        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../Student_Side/profile_std.html"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>    

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        
        <a href="#home" class="active"><i class="fa fa-home"></i> Home</a>
        <a href="#jobs"><i class="fa fa-search"></i> Jobs</a>
        <a href="#placement"><i class="fas fa-laptop-code"></i>Placements</a>
        <a href="#company"><i class="fa fa-building"></i> Company</a>
        <a href="#profile"><i class="fa fa-user"></i> Profile</a>
        <a href="#feedback"><i class="fa fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="feedback-response">
            <div class="user-info">
                <img src="../images/profile.png" alt="User Profile">
                <span class="name"><?php echo $user_name; ?></span>
            </div>
            <p><?php echo $feedback_text; ?></p>
        </div>
    
        <div class="response-container">
            <form method="POST" action="">
                <label for="response">Your Response:</label>
                <textarea name="response" id="response" required><?php echo $response_text; ?></textarea>
                <button type="submit">SUBMIT</button>
            </form>
            </div>
<?php if ($success): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: 'Response Saved!',
            icon: 'success',
            iconColor: '#022a52fd',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'feedbacklist.php'; // Redirect to your desired page
            }
        });
    </script>
<?php endif; ?>

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
    
</body>
</html>
