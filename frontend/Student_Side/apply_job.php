<?php
// Start the session to use session variables
session_start();

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

// Get form data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = $_POST['job_id'];
    $user_id = $_POST['user_id'];
    $applied_date = date('Y-m-d'); // Current date

    // Check if job_id exists in job table
    $checkJobStmt = $conn->prepare("SELECT COUNT(*) FROM job WHERE job_id = ?");
    $checkJobStmt->bind_param("i", $job_id);
    $checkJobStmt->execute();
    $checkJobStmt->bind_result($exists);
    $checkJobStmt->fetch();
    $checkJobStmt->close();

    if ($exists == 0) {
        // Store error message in session if the job does not exist
        $_SESSION['message'] = "Job ID does not exist. Please check and try again.";
        $_SESSION['message_type'] = "error";
    } else {
        // Check if the user has already applied for this job
        $checkApplicationStmt = $conn->prepare("SELECT COUNT(*) FROM job_application WHERE user_id = ? AND job_id = ?");
        $checkApplicationStmt->bind_param("ii", $user_id, $job_id);
        $checkApplicationStmt->execute();
        $checkApplicationStmt->bind_result($applicationExists);
        $checkApplicationStmt->fetch();
        $checkApplicationStmt->close();

        if ($applicationExists > 0) {
            // Store message in session if the user has already applied
            $_SESSION['message'] = "You have already applied for this job.";
            $_SESSION['message_type'] = "error";
        } else {
            // Prepare and bind for inserting new application
            $stmt = $conn->prepare("INSERT INTO job_application (job_id, user_id, applied_date, status) VALUES (?, ?, ?, 'applied')");
            $stmt->bind_param("iss", $job_id, $user_id, $applied_date);

            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['message'] = "Application submitted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['message_type'] = "error";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Redirect to the same page to display the message
    header("Location: " . $_SERVER['PHP_SELF']);
    exit; // Stop execution after redirection
}

// Display message
$message = "";
$message_type = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']); // Clear the message after displaying it
}

// Close the connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Application</title>
<style>
    /* Styling for the message box */
    .message-box {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px 20px;
        display: none; /* Hidden by default */
        z-index: 1000;
    }
    .message-box.success {
        border-color: green;
        color: green;
    }
    .message-box.error {
        border-color: red;
        color: red;
    }
    .close-btn {
        cursor: pointer;
        margin-left: 10px;
        color: #007bff;
    }
</style>
<script>
    // Function to show the message box
    function showMessage() {
        const messageBox = document.getElementById('messageBox');
        messageBox.style.display = 'block';
    }

    // Function to close the message box
    function closeMessage() {
        const messageBox = document.getElementById('messageBox');
        messageBox.style.display = 'none';
    }

    window.onload = function() {
        <?php if ($message) { ?>
            showMessage();
        <?php } ?>
    };
</script>
</head>
<body>

<!-- Message Box -->
<div id="messageBox" class="message-box <?php echo $message_type; ?>">
    <?php echo $message; ?>
    <span class="close-btn" onclick="closeMessage()">X</span>
</div>


</body>
</html>
