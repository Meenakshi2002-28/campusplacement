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
// Handle delete request
if (isset($_POST['delete_job_id'])) {
    $job_id = $_POST['delete_job_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete from job_course table first
        $deleteJobCourseSql = "DELETE FROM job_course WHERE job_id = ?";
        $stmt = $conn->prepare($deleteJobCourseSql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();

        // Delete from job table
        $deleteJobSql = "DELETE FROM job WHERE job_id = ?";
        $stmt = $conn->prepare($deleteJobSql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        $show_success_message = true;
        
    } catch (Exception $e) {
        // Rollback transaction if there's an error
        $conn->rollback();
        echo "Error deleting job: " . $e->getMessage();
    }
}

// Fetch jobs from the database
$sql = "SELECT job_id, job_title, company_name, location,  salary, application_deadline FROM job";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job List Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        .sidebar {
            width: 198px;
            height: 610px;
            position: fixed;
            left: 10px;
            top: 85px;
            background-color: #2F5597;
            color: white;
            padding: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 15px;
            font-size: 22px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            border-left: 3px solid #ffffff;
            background: #1e165f;
        }

        img {
            height: 40px;
            width: auto;
        }

        .container {
            padding: 5px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .icon {
            margin-left: 1px;
        }

        .tabs {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-left: 145px;
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

        .job-table th, .job-table td {
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
        .main-content {
        flex-grow: 1;
        padding: 40px 10px 0 10px;
        margin-left: 120px;
        background-color:white;
        }

        .maincontent {
                flex-grow: 1;
            margin-top: 20px;
            padding: 5px;
            padding-right: 50px;
            background-color:white;
            padding-left: 130px;
        }

        .job-details {
        position: relative;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        margin-bottom: 30px;
        margin-right: 100px;
        }

        .job-details h2 {
            margin-top: 0;
            color:black;
            margin-left: 20px;
        }

        .job-details p{
            margin-left: 100px;
        }

        .jobimg a {
            display:inline-flexbox;
            text-decoration: none;
            color:black;
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
            color: #ff0000; /* Black color for trash icon */
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #ffffff;
        }

        .jobstatus{
            padding-left: 10px;
            display: flex;
            align-items: center;
            margin-left: 810px;
        }

        .jobstatus input {
            background-color:#e2e2e2;
            border-radius: 10px;
            border: 1px solid rgb(197, 197, 197) ;
            width: 270px; 
            height: 25px; 
            font-size: 16px;
            margin-top: 20px;
            margin-left: 370px;
            text-align: center; 
            line-height: 40px; 
            padding: 0px; 
            padding-left: 20px;
            box-sizing:content-box;
            font-weight: 600;
        }

        .create-button {
            background-color: #ddd;
            color: black;
            font-size: 17px;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-bottom: 20px;
            border-radius: 15px;
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
            color: rgb(95, 95, 95); /* Blue color on hover */
        }


        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #2F5597;
            min-width: 150px;
            z-index: 1;
            top: 55px;
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
        }

        .logout{
            position: absolute;
            bottom: 20px;
            width: 100%;
        }

        .logout a {
            font-size: 20px;
            margin-top: 210px;
        }
        .fas fa-trash-alt{
            text-align: center;
        }
        .success-message {
    display: none; /* Hidden by default */
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
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">

        <i class="fas fa-caret-down fa-2x" aria-hidden="true" onclick="toggleDropdown()"></i>
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../Admin_Side/"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div> 

    <div class="sidebar">
        <a href="#home"><i class="fas fa-home"></i> Home</a>
        <a href="#jobs"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="#students"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="#placements"><i class="fas fa-laptop-code"></i> Placements</a>
        <a href="#company"><i class="fas fa-building"></i> Company</a>
        <a href="#profile"><i class="fas fa-user"></i> Profile</a>
        <a href="#feedback"><i class="fas fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="#logout"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>
    <div class="main-content">
        <div class="tabs">
            <button class="create-button"><i class="fas fa-plus"></i> Create</button>
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
                            <h2><?php echo htmlspecialchars($job['job_title']); ?></h2>
                            <p><?php echo htmlspecialchars($job['company_name']); ?></p>
                            <div class="jobimg">
                                <a href="#location-dot"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></a>
                                <a href="#briefacse"><i class="fa fa-fw fa-solid fa-briefcase"></i> Full Time</a>
                                <a href="#indian-rupee-sign"><i class="fas fa-rupee-sign"></i> <?php echo htmlspecialchars($job['salary']); ?></a>
                                <a href="#calendar-days"><i class="fa fa-fw fa-solid fa-calendar"></i> Apply By <?php echo htmlspecialchars($job['application_deadline']); ?></a>
                            </div>
                            <div class="jobstatus">
                                <form method="POST" action="">
                                    <input type="hidden" name="delete_job_id" value="<?php echo $job['job_id']; ?>">
                                    <button class="delete-btn" type="submit" onclick="return confirm('Are you sure you want to delete this job?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                <a href="applicants.php?job_id=<?php echo htmlspecialchars($job['job_id']); ?>" class="view-btn"><b>View Applicants</b></a>
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

    <script>
        // Change profile image
        function triggerFileInput() {
                document.getElementById('fileInput').click();
            }
    
        function changeProfilePicture(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('sidebarProfilePicture').src = e.target.result; // Update the profile image in sidebar
                    document.getElementById('profileIcon').src = e.target.result; // Update profile icon
                };
                reader.readAsDataURL(file); // Read the image file
            }
        }
        let dropdownOpen = false;
        function toggleDropdown() {
            const dropdown = document.getElementById("dropdownMenu");
            dropdownOpen = !dropdownOpen;
            dropdown.style.display = dropdownOpen ? "block" : "none";
        }
    
        function goToProfile() {
            showSection('personal'); // Redirect to profile section
            toggleDropdown(); // Close the dropdown after redirection
        }
        // Function to show the success message
        function showSuccessMessage() {
            var successMessage = document.getElementById('successMessage');
            successMessage.classList.add('show');
            
            // Auto-hide the message after 5 seconds
            setTimeout(function() {
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
    </script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
