<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader if PHPMailer is installed via Composer
require '../vendor/autoload.php';

// Database connection
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
         // Generate a unique token for the password reset link
         $token = bin2hex(random_bytes(50));
        
         // Set token expiration time (e.g., 1 hour from now)
         date_default_timezone_set('Asia/Kolkata');
         $expiresAt = date("Y-m-d H:i:s", strtotime('+1 hour'));
 
         // Update the database with the reset token and expiration time
         $stmt = $conn->prepare("UPDATE login SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?");
         $stmt->bind_param("sss", $token, $expiresAt, $email);
         $stmt->execute();

        // Create the reset link
        $resetLink = "http://localhost/campus_placement/frontend/new_password.php?token=" . $token;

        // Send email with PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Google SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'meenakshiasas45@gmail.com'; // Your Gmail address
            $mail->Password = 'xpxr ottm oljg aine'; // Your app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465; // Port for SSL (465)

            // Recipients
            $mail->setFrom('no-reply@yourwebsite.com', 'Lavoro');
            $mail->addAddress($email); // Add recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Please click the link below to reset your password:<br><br><a href='" . $resetLink . "'>Reset Password</a>";
            $mail->AltBody = "Please click the link below to reset your password:\n\n" . $resetLink;

            if ($mail->send()) {
                // Redirect to the forgot-password page with a success message
                header("Location: forgot_password.php?status=success&email=" . urlencode($email));
                exit;
            }
        } catch (Exception $e) {
            // Redirect with an error message
            header("Location: forgot_password.php?status=error");
            exit;
        }
        } else {
            // Redirect with a message that no account was found
            header("Location: forgot_password.php?status=not_found");
            exit;
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
    <link href="https://fonts.googleapis.com/css2?family=Merienda&display=swap" rel="stylesheet">
    <title>Campus Recruitment System</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: white;
}

.forgot-password-container {
    display: flex;
    align-items: center;
    padding-left: 350px;
}

.forgot-password-box {
    background-color: #281f63; /* Purple background */
    color: white;
    padding-top: 40px;
    border-radius: 30px;
    padding: 50px;
    text-align: center;
    width: 400px;
    height: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.forgot-password-box h1 {
    margin: 0;
    margin-bottom: 20px;
    font-size: 26px;
}

.forgot-password-box p {
    font-size: 16px;
    margin-bottom: 30px;
}

.forgot-password-box input {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: none;
    outline: none;
}

.forgot-password-box button {
    width: 100%;
    padding: 12px;
    background-color: #ffffff;
    color: rgb(0, 0, 0);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.forgot-password-box button:hover {
    background-color: #ffffff;
}

.back-to-login {
    display: block;
    margin-top: 20px;
    color: #f8f8f8;
    text-decoration: none;
}

.back-to-login:hover {
    color: #ffffff;
}

.illustration {
            flex-grow: 1;
            display:flex;
            justify-content:left;
            align-items:flex;
            border-spacing:5mm;
            padding-block:10px;
        }

    .illustration img {
            width: 380px;
            height:450px;
            padding-top:200px;
            padding-right: 10px;
        }
        .logo-container {
            position: absolute;
            top: 10px; /* Positions logo/title closer to the top */
            left: 30px;
            font-size: 30px;
            font-weight:bold;
            color: #1e3d7a;
            text-align: center;
            font-family: 'Merienda', cursive;
    }
    .logo {
    height: 55px;
    width: auto;
    }

    </style>
</head>
<body>
<div class="logo-container">Lavoro</div>
    <center>
    <div class="forgot-password-container">
        <div class="forgot-password-box">
        <?php
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

    if ($status === 'success') {
        echo "<p style='color: #0f0;'>An email has been sent to $email with a link to reset your password.</p>";
    } elseif ($status === 'error') {
        echo "<p style='color: #f00;'>There was an error sending the email. Please try again later.</p>";
    }
    else{
        echo "<p style='color: #f00;'>There is not account under this email address.</p>";
    }
}
?>

            <h1>Forgot your password?</h1>
            <p>Please enter your E-mail ID to get instructions to reset your password</p>
            <form action="forgot_password.php" method="POST">
                <input type="email"  name="email"  placeholder="Email Address" required>
                <button type="submit">SUBMIT</button>
            </form>
            <a href="login.php" class="back-to-login">← Back to Login</a>
        </div>
        <div class="illustration">
            <img src="images/password.png" alt="Illustration">
        </div>
    </div>
    </center>
</body>
</html>
