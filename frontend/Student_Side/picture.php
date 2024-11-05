<?php
// Start session to get user ID (if logged in user ID is stored in session)
session_start();
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
$user_id = $_SESSION['user_id']; // Assume user_id is stored in session

// Directory to save uploaded images
$target_dir = "../uploads/profile_pictures/";
$uploadOk = 1;

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $target_file = $target_dir . basename($_FILES["profilePicture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an actual image
    $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (optional limit, e.g., 500KB)
    if ($_FILES["profilePicture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Rename file with user ID to avoid filename conflicts
        $new_filename = $target_dir . "profile_" . $user_id . "." . $imageFileType;
        
        if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $new_filename)) {
            echo "The file " . htmlspecialchars(basename($_FILES["profilePicture"]["name"])) . " has been uploaded.";

            // Update the student's photo path in the database
            $stmt = $conn->prepare("UPDATE student SET photo = ? WHERE user_id = ?");
            $stmt->bind_param("ss", $new_filename, $user_id);
            if ($stmt->execute()) {
                echo "Profile picture updated successfully.";
            } else {
                echo "Error updating profile picture: " . $conn->error;
            }

            // Close statement and connection
            $stmt->close();
            $conn->close();

        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Profile Picture</title>
    <style>
        #profileImage {
            width: 150px; /* Set your desired size */
            height: 150px;
            border: 2px solid #ccc; /* Optional: Add a border */
            border-radius: 75px; /* Optional: Make it round */
            object-fit: cover; /* Maintain aspect ratio */
            cursor: pointer; /* Indicate that it's clickable */
        }
        #fileInput {
            display: none; /* Hide the file input */
        }
    </style>
    <script>
        function triggerFileInput() {
            document.getElementById('fileInput').click(); // Trigger the file input click
        }
    </script>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <img id="profileImage" src="../images/Customer.png" alt="Profile Picture" onclick="triggerFileInput()">
        <input type="file" name="profilePicture" id="fileInput" accept="image/*" onchange="updateImagePreview(this)" required>
        <input type="submit" name="submit" value="Upload">
    </form>

    <script>
        function updateImagePreview(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImage').src = e.target.result; // Update image source
                };
                reader.readAsDataURL(input.files[0]); // Read the file as a data URL
            }
        }
    </script>
</body>
</html>

