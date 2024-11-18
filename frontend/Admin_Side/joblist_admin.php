<?php
// Database connection using mysqli
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$show_success_message = false;
// Handle delete request
if (isset($_POST['delete_job_id'])) {
    $job_id = $_POST['delete_job_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update the job to set is_active to 0
        $softDeleteSql = "UPDATE job SET is_active = 0 WHERE job_id = ?";
        $stmt = $conn->prepare($softDeleteSql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        $show_success_message = true;

    } catch (Exception $e) {
        // Rollback transaction if there's an error
        $conn->rollback();
        echo "Error removing job: " . $e->getMessage();
    }
}

// Fetch jobs from the database
$sql = "SELECT job_id,work_environment,job_title, company_name, location, salary, application_deadline 
        FROM job 
        WHERE is_active = 1 
        ORDER BY posted_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
            width: 1250px;
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
            /* Smooth transition for margin */
            
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

        .icon {
            margin-left: 15px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .icon:hover {
            transform: scale(1.1);
        }


        .text {
            padding-top: 1px;
        }

        .tabs {
            display: flex;
            align-items: center;
            word-break: keep-all;
        }

        .tab-button {
            background-color: white;
            border: 1px solid #000000;
            padding: 0px;
            border-radius: 50px;
            margin-right: 50px;
            cursor: pointer;
            font-size: 16px;
            height: 26px;
            width: 130px;
        }

        .tab-button.active {
            background-color: #1c4a82;
            color: white;
        }

        .job-table {
            border-collapse: collapse;
        }

        .job-table th,
        .job-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            padding-left: 200px;
        }

        .job-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid black;
        }

        .job-table td {
            font-size: 15px;
            word-break: break-all;
        }

        .job-table tr:hover {
            background-color: #f1f1f1;
        }

        .job-details {
            position: relative;
            background-color: white;
            padding: 15px;
            padding-bottom: 0px;
            border-radius: 10px;
            border: 1px solid #cccccc;
            margin-bottom: 30px;
            margin-right: 70px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .job-details:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-color: #063dc9;
        }

        .job-details h3 {
            margin-top: 0;
            color: black;
            margin-left: 20px;
        }

        .job-details p {
            margin-left: 100px;
            font-size: ;
        }

        .jobimg a {
            display: inline-flexbox;
            text-decoration: none;
            color: black;
            padding: 60px;
            border-left: 3px solid transparent;
        }

        .delete-btn {
            background-color: transparent;
            border: none;
            width: 50px;
            cursor: pointer;
            padding: 10px;
            font-size: 20px;
            margin-left: 10px;
            color: #ff0000;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #ffffff;
        }

        .jobstatus {
            padding-left: 10px;
            display: flex;
            align-items: center;
            margin-left: 810px;
        }

        .jobstatus input {
            background-color: #e2e2e2;
            border-radius: 10px;
            border: 1px solid rgb(197, 197, 197);
            width: 270px;
            height: 25px;
            font-size: 16px;
            margin-top: 20px;
            margin-left: 370px;
            text-align: center;
            line-height: 40px;
            padding: 0px;
            padding-left: 20px;
            box-sizing: content-box;
            font-weight: 600;
        }

        .create-button {
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 30px;
            cursor: pointer;
            border: 0;
            background-color: #d9e6f4;
            box-shadow: rgb(0 0 0 / 5%) 0 0 8px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 17px;
            transition: all 0.5s ease;
        }

        .create-button:hover {
            letter-spacing: 2.5px;
            background-color: hsl(261deg 80% 48%);
            color: hsl(0, 0%, 100%);
        }

        /* Pencil icon styling (Edit option) */
        .edit-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: rgb(95, 95, 95);
            font-size: 18px;
            transition: color 0.3s ease;
        }

        /* Pencil icon hover effect */
        .edit-icon:hover {
            color: rgb(95, 95, 95);
            /* Blue color on hover */
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

        .fas fa-trash-alt {
            text-align: center;
        }

        .success-message {
            display: none;
            /* Hidden by default */
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #2F5597;
            color: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            font-size: 16px;
        }

        .success-message.show {
            display: block;
        }

        .success-message .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            margin-left: 10px;
            cursor: pointer;
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
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
            <a href="../profile_admin.php"><i class="fa fa-fw fa-user"></i> Profile</a>
=======
            <a href="../Admin_Side/"><i class="fa fa-fw fa-user"></i> Profile</a>
>>>>>>> 549e4c0 (change)
=======
            <a href="../Admin_Side/profile_admin.php"><i class="fa fa-fw fa-user"></i> Profile</a>
>>>>>>> 7a7f448 (change)
=======
=======
>>>>>>> 25737cb (...)
            <a href="../Student_Side/profile_std.html"><i class="fa fa-user-circle"></i> Profile</a>
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>    

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        
        <a href="dashboard_admin.php"><i class="fas fa-home"></i> Home</a>
        <a href="joblist_admin.php" class="active"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="#students"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="#placements"><i class="fas fa-laptop-code"></i> Placements</a>
        <a href="company.html"><i class="fas fa-building"></i> Company</a>
        <a href="profile_admin.php"><i class="fas fa-user"></i> Profile</a>
        <a href="feedback_list.html"><i class="fas fa-comment"></i> Feedback</a>
        <div class="logout">
<<<<<<< HEAD
>>>>>>> a2b6d47 (changes)
=======
>>>>>>> 25737cb (...)
=======
            <a href=" profile_admin.php"><i class="fa fa-user-circle"></i> Profile</a>
>>>>>>> 5575968 (change)
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo or Website Name -->
        <div class="logo">Lavoro</div>
        <a href="dashboard_admin.php"><i class="fas fa-home"></i> Home</a>
        <a href="joblist_admin.php" class="active"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="view_students.php"><i class="fas fa-user-graduate"></i> Students</a>
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
        <div class="tabs">
            <button class="create-button" onclick="window.location.href='job_creation.php'">
                Create <i class="fas fa-plus"></i>
            </button>
        </div>

        <!-- Job List Table -->
        <table class="job-table" id="jobTable">
            <div class="maincontent">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($job = $result->fetch_assoc()): ?>
                        <div class="job-details">
                            <a href="job_edit.php?job_id=<?php echo $job['job_id']; ?>" title="Edit" class="edit-icon-link">
                                <i class="fas fa-pencil-alt edit-icon"></i>
                            </a>
                            <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                            <p><?php echo htmlspecialchars($job['company_name']); ?></p>
                            <div class="jobimg">
                                <a href="#location-dot"><i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($job['location']); ?></a>
                                <a href="#briefacse"><i class="fa fa-fw fa-solid fa-briefcase"></i><?php echo htmlspecialchars($job['work_environment']); ?></a>
                                <a href="#indian-rupee-sign"><i class="fas fa-rupee-sign"></i>
                                    <?php echo htmlspecialchars($job['salary']); ?></a>
                                <a href="#calendar-days"><i class="fa fa-fw fa-solid fa-calendar"></i> Apply By
                                    <?php echo htmlspecialchars($job['application_deadline']); ?></a>
                            </div>
                            <div class="jobstatus">
                                <form method="POST" action="" id="deleteForm<?php echo $job['job_id']; ?>">
                                    <input type="hidden" name="delete_job_id" value="<?php echo $job['job_id']; ?>">
                                    <button class="delete-btn" type="button"
                                        onclick="confirmDeletion('<?php echo $job['job_id']; ?>', event)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                <a href="applicants.php?job_id=<?php echo htmlspecialchars($job['job_id']); ?>"
                                    class="view-btn"><b>View Applicants</b></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No jobs available</p>
                <?php endif; ?>
            </div>
        </table>
    </div>
    <!-- Success message box -->
    <div class="success-message" id="successMessage">
        Job deleted successfully
        <button class="close-btn" onclick="hideSuccessMessage()">×</button>
    </div>





    <!-- JavaScript -->
    <script>
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
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
            
=======
    function showSuccessMessage() {
=======
=======
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
>>>>>>> 2a03051 (..)
function confirmDeletion(jobId, event) {
    // Prevent the form submission
    event.preventDefault();
=======
        function confirmDeletion(jobId, event) {
            // Prevent the form submission
            event.preventDefault();
>>>>>>> b0cb182 (change)

            // Show the confirmation dialog
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this record!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Submit the form if user confirms
                    document.getElementById('deleteForm' + jobId).submit();
                    swal("Poof! Your record has been deleted!", {
                        icon: "success",
                    });
                } 
            });
        }
        function showSuccessMessage() {
>>>>>>> 536c971 (..)
            var successMessage = document.getElementById('successMessage');
            successMessage.classList.add('show');

            // Auto-hide the message after 5 seconds
            setTimeout(function () {
                successMessage.classList.remove('show');
            }, 5000);
        }

        // Function to hide the success message manually
        function hideSuccessMessage() {
            var successMessage = document.getElementById('successMessage');
            successMessage.classList.remove('show');
        }

        // Display success message if PHP flag is true
        <?php if ($show_success_message): ?>
            showSuccessMessage();
        <?php endif; ?>

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
<<<<<<< HEAD
    
<<<<<<< HEAD
            // Dashboard stats extraction
            
>>>>>>> 25737cb (...)
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
    
          
=======
        
>>>>>>> 5575968 (change)
    
=======



>>>>>>> 536c971 (..)
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