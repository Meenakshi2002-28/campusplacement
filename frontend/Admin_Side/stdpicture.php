<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = $_POST['user_id'] ?? '';

$target_dir = "../uploads/profile_pictures/";
$uploadOk = 1;

if (isset($_POST["submit"])) {
    $target_file = $target_dir . basename($_FILES["profilePicture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["profilePicture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        $new_filename = $target_dir . "profile_" . $user_id . "." . $imageFileType;

        if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $new_filename)) {
            $stmt = $conn->prepare("UPDATE student SET photo = ? WHERE user_id = ?");
            $stmt->bind_param("ss", $new_filename, $user_id);
            if ($stmt->execute()) {
                header("Location: " . $_SESSION['current_page']);
    exit();
            } else {
                echo "Error updating profile picture: " . $conn->error;
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
