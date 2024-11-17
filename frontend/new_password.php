<?php
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

// Get token from URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and hasn't expired
    $stmt = $conn->prepare("SELECT email FROM login WHERE reset_token_hash = ? AND reset_token_expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];  // Get the email corresponding to the token

        // Show the "New Password" form
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the login table
                $updateStmt = $conn->prepare("UPDATE login SET password = ? WHERE email = ?");
                $updateStmt->bind_param("ss", $hashedPassword, $email);

                if ($updateStmt->execute()) {
                    $message = "Your password has been successfully updated.";
                    $clearTokenStmt = $conn->prepare("UPDATE login SET reset_token_hash = NULL, reset_token_expires_at = NULL WHERE email = ?");
                    $clearTokenStmt->bind_param("s", $email);
                    $clearTokenStmt->execute();
                } else {
                    $message = "Error updating password. Please try again.";
                }
            } else {
                $message = "Passwords do not match.";
            }
        }
    } else {
        $message = "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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
            background-color: #f7f7f7;
        }

        .password-box {
            background-color: #281f63; /* Purple background */
            color: white;
            padding: 40px;
            border-radius: 20px;
            width: 360px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .password-box h1 {
            font-size: 26px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .password-box form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .password-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: none;
            outline: none;
            position: relative;
        }

        .password-box input::placeholder {
            color: #aaa;
        }

        .password-box button {
            width: 100%;
            padding: 10px;
            background-color: #ffffff;
            color: #281f63;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 17px;
        }

        .password-box button:hover {
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

        /* Styles for eye icon (to toggle password visibility) */
        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .toggle-visibility {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .toggle-visibility img {
            padding-bottom: 15px;
            width: 25px;
            height: 35px;
        }
        .logo-container {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .logo {
            height: 55px;
            width: auto;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="../frontend/images/logo1.png" alt="Logo" class="logo">
    </div>
    <div class="password-box">
        <h1>Change Password</h1>
        <?php if (!empty($message)): ?>
        <p style="background-color: #ffffff; color: #281f63; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>
        <form action="#" method="POST">
            <div class="input-wrapper">
                <input type="password" name="new_password"placeholder="New Password" id="new-password" required>
                <span class="toggle-visibility" onclick="togglePasswordVisibility('new-password', this)"></span>
            </div>
            
            <div class="input-wrapper">
                <input type="password" placeholder="Confirm Password" name="confirm_password" id="confirm-password" required>
                <span class="toggle-visibility" onclick="togglePasswordVisibility('confirm-password', this)"></span>
            </div>
            <button type="submit">SUBMIT</button>
        </form>
        <a href="login.php" class="back-to-login">← Back to Login</a>
    </div>
    <script>
          function validatePassword() {
            var password = document.getElementById('password').value;
            var passwordError = document.getElementById('password-error');

            if (password.length > 0 && password.length < 8) {
                passwordError.textContent = "Password must be at least 8 characters long.";
            } else {
                passwordError.textContent = ""; // Clear error
            }
        }
        function validateConfirmPassword() {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm-password').value;
            var confirmPasswordError = document.getElementById('confirm-password-error');

            if (password !== confirmPassword) {
                confirmPasswordError.textContent = "Passwords do not match.";
            } else {
                confirmPasswordError.textContent = ""; // Clear error
            }
        }
       
    </script>

</body>
</html>
