<?php
// Database connection
$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "campus_placement"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT job_id, company_name, job_title, work_environment, salary FROM job";
$result = $conn->query($query);

$jobs = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobs[] = array(
            'id' => $row['job_id'],              // Include job_id
            'company' => $row['company_name'], 
            'title' => $row['job_title'],      
            'type' => $row['work_environment'], 
            'salary' => $row['salary']          
        );
    }
} else 
{
    $jobs = [];
}

header('Content-Type: application/json');
echo json_encode($jobs);

$conn->close();
?>
