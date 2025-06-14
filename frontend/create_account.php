<?php
// Database connection
$servername = "localhost";
$username = "root"; // MySQL username
$password = ""; // MySQL password
$dbname = "campus_placement"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rollno = $_POST['rollno'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($rollno) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } else {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT * FROM login WHERE email = ? OR user_id = ?");
        $stmt->bind_param("ss", $email, $rollno);

        if (!$stmt->execute()) {
            echo "<script>alert('Error executing query.');</script>";
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<script>alert('User with this email or roll number already exists.');</script>";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert data into login table
                $stmt = $conn->prepare("INSERT INTO login (user_id, email, password,role,approval_status) VALUES (?, ?, ?,'student','pending')");
                $stmt->bind_param("sss", $rollno, $email, $hashed_password);

                if ($stmt->execute()) {
                    // Insert data into student table
                    $stmt = $conn->prepare("INSERT INTO student (user_id, name) VALUES (?, ?)");
                    $stmt->bind_param("ss", $rollno, $name);

                    if ($stmt->execute()) {
                        // Redirect to success page
                        header("Location: ../frontend/acc_pending.html");
                        exit();
                    } else {
                        echo "<script>alert('Error inserting into student table.');</script>";
                    }
                } else {
                    echo "<script>alert('Error inserting into login table.');</script>";
                }
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Merienda&display=swap" rel="stylesheet">
    <title>Campus Recruitment System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: white;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .signup-box {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            padding: 50px;
            width: 350px;
            background-color: #281f63;
            color: white;
            padding-top: 10px;
            padding-right: 60px;
        }

        .signup-box h2 {
            margin-bottom: 20px;
            font-family: Georgia;
            font-size: 30px;
        }

        .signup-box label{
            margin-left: 10px;
        }

        .signup-box input[type="text"],
        .signup-box input[type="email"],
        .signup-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 2.5px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .signup-box button {
            width: 105%;
            padding: 10px;
            margin-top: 20px;
            justify-content: center;
            background-color: white;
            color: #1e165f;
            border: none;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .error-message {
            color: red;
            font-size: 12px;
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

    <script>
        function validateEmail() {
            var email = document.getElementById('email').value;
            var emailError = document.getElementById('email-error');
            var emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

            if (!email.match(emailPattern)) {
                emailError.textContent = "Please enter a valid email address.";
            } else {
                emailError.textContent = ""; // Clear error
            }
        }

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
        function validateForm() {
            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var rollno = document.getElementById('rollno').value;
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm-password').value;

            if (name == "" || email == "" || rollno == "" || password == "" || confirmPassword == "") {
                alert("All fields are required.");
                return false;
            }

            if (password != confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <div class="logo-container">Lavoro</div>
    <div class="container">
        <div class="signup-box">
            <center>
                <h2>Create your account</h2>
            </center>
            <form method="POST" action="" onsubmit="return validateForm()">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Full name" required>

                <label for="email">Email ID</label>
                <input type="email" id="email" name="email" placeholder="Email ID" required onblur="validateEmail()">
                <div id="email-error" class="error-message"></div>

                <label for="rollno">University Roll No</label>
                <input type="text" id="rollno" name="rollno" placeholder="University Roll No(eg: KHENU3CDS22001)" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required
                    onblur="validatePassword()">
                <div id="password-error" class="error-message"></div>

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password"
                    required onblur="validateConfirmPassword()">
                <div id="confirm-password-error" class="error-message"></div>

                <button type="submit">SIGN UP</button>
            </form>
        </div>
    </div>
</body>

</html>