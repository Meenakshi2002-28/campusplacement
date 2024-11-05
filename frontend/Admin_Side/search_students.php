<?php
// Include your database connection details
$servername = "localhost";
$db_username = "root"; // MySQL username
$db_password = ""; // MySQL password
$dbname = "campus_placement"; // Replace with your database name

// Create connection using MySQLi
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query
$query = $_GET['q'] ?? '';

// Prepare the SQL statement to prevent SQL injection
// Assuming 'STUDENT' has 'course_id' and 'COURSE' has 'course_id' and 'course_name'
$sql = "
    SELECT s.user_id, s.name, s.graduation_year, c.course_name
    FROM STUDENT s
    LEFT JOIN COURSE c ON s.course_id = c.course_id
    WHERE s.user_id LIKE ? OR s.name LIKE ?";
$stmt = $conn->prepare($sql);

// Create search parameters
$searchParam = '%' . $conn->real_escape_string($query) . '%';
$stmt->bind_param('ss', $searchParam, $searchParam);

// Execute the statement
$stmt->execute();

// Fetch the results
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);

// Check if any results were found
if ($students) {
    echo "<table>";
    
    foreach ($students as $student) {
        echo "<tr onclick=\"window.location.href='profileredirect.php?user_id=" . urlencode($student['user_id']) . "'\">";
        echo "<td>" . htmlspecialchars($student['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($student['name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['graduation_year']) . "</td>";
        echo "<td>" . htmlspecialchars($student['course_name'] ?? 'N/A') . "</td>"; // Display 'N/A' if course_name is null
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No results found.</p>";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
