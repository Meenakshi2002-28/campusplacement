<?php
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $servername = "localhost";
    $db_username = "root"; // MySQL username
    $db_password = ""; // MySQL password
    $dbname = "campus_placement"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the username and password from the form (renamed variables to avoid conflict)
    $form_username = $_POST['username'];
    $form_password = $_POST['password'];

    // SQL query to check the user in the database
    $query = "SELECT user_id, password, role FROM login WHERE user_id = ? LIMIT 1";
    
    // Prepare the statement to prevent SQL injection
    if ($stmt = $conn->prepare($query)) {
        // Bind the username parameter
        $stmt->bind_param('s', $form_username);
        $stmt->execute();
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($db_user_id, $db_password, $db_role);
            $stmt->fetch();

            // Verify the password
            if (password_verify($form_password, $db_password)) {
                // Password matches, create session variables
                $_SESSION['user_id'] = $db_user_id;
                $_SESSION['role'] = $db_role;

                // Redirect based on the role
                if ($db_role == 'admin') {
                    header('Location: Admin_Side/dashboard_admin.html');
                } elseif ($db_role == 'student') {
                    header('Location: Student_Side/dashboard_std.html');
                } else {
                    echo "Invalid role.";
                }
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No account found with that username.";
        }

        $stmt->close(); // Close the statement
    } else {
        echo "Database query error.";
    }

    // Close the database connection
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Recruitment System - Login</title>
    <style>
        body {
            margin: 0;
            padding:0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            display: flex;
            justify-content:space-evenly;
            width: 70%;
            max-width: 800px;
            padding-left:350px;
            height:480px;
            
        }

        .login-box {
            background-color: #2a2a7c;
            color: white;
            padding: 60px 40px;
            border-radius:30px;
            width: 350px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            
        }

        .login-box h2 {
            margin: 0;
            font-size: 50px;
            
        }
        .login-box h1 {
            margin:0;
            font-size: 35px;
            
        }

        .login-box form {
            margin-top: 30px;
        }

        .login-box input[type="text"], 
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            padding-left:15px;
            padding-right:1px;
        }

        .login-box .forgot-password {
            text-align:end;
            color: white;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .login-box .forgot-password a {
            color: #ddd;
            text-decoration: none;
        }

        .login-box .forgot-password a:hover {
            text-decoration: underline;
        }

        .login-box button {
            width: 100%;
            padding: 15px;
            background-color: white;
            color: #2a2a7c;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .login-box button:hover {
            background-color: #ddd;
        }

        .login-box .signup {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }

        .login-box .signup a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .login-box .signup a:hover {
            text-decoration: underline;
        }

        /* Right side image */
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
            height:280px;
            padding-top:200px;
            padding-right:10px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Login Box -->
        <div class="login-box">
            <center>
            <h2><b>Welcome!</b></h2><br><b><h1>Login</b></h1>
            </center>
            <form action="" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password"  name="password"placeholder="Password" required>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit"><b>LOGIN</b></button>
            </form>
            <div class="signup">
                Don't have an account yet? <a href="create_account.php">Sign Up</a>
            </div>
        </div>

        <!-- Illustration Image -->
        <div class="illustration">
            <img src="images/login.png" alt="Login Illustration">
        </div>
    </div>

</body>
</html>
