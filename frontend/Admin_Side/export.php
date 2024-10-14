<?php
session_start();
require 'vendor/autoload.php'; // Include PhpSpreadsheet autoload file

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch selected fields
$fields = isset($_POST['fields']) ? $_POST['fields'] : [];
$job_id = $_GET['job_id']; // Ensure job_id is passed or validated

// Prepare SQL query
$fieldQueryParts = [];
$selectFields = [];

// Base fields
if (in_array('name', $fields)) {
    $selectFields[] = 'sa.name';
    $fieldQueryParts[] = 'sa.name AS name';
}
if (in_array('user_id', $fields)) {
    $selectFields[] = 'ja.user_id';
    $fieldQueryParts[] = 'ja.user_id AS user_id';
}
if (in_array('course_name', $fields)) {
    $selectFields[] = 'ca.course_name';
    $fieldQueryParts[] = 'ca.course_name AS course_name';
}
if (in_array('course_branch', $fields)) {
    $selectFields[] = 'ca.course_branch';
    $fieldQueryParts[] = 'ca.course_branch AS course_branch';
}
if (in_array('cgpa', $fields)) {
    $selectFields[] = 'sa.cgpa';
    $fieldQueryParts[] = 'sa.cgpa AS cgpa';
}
if (in_array('email', $fields)) {
    $selectFields[] = 'sa.email';
    $fieldQueryParts[] = 'sa.email AS email';
}
if (in_array('current_arrears', $fields)) {
    $selectFields[] = 'sa.current_arrears';
    $fieldQueryParts[] = 'sa.current_arrears AS current_arrears';
}
if (in_array('graduation_year', $fields)) {
    $selectFields[] = 'sa.graduation_year';
    $fieldQueryParts[] = 'sa.graduation_year AS graduation_year';
}
if (in_array('percentage_tenth', $fields)) {
    $selectFields[] = 'ad.percentage_tenth';
    $fieldQueryParts[] = 'ad.percentage_tenth AS percentage_tenth';
}
if (in_array('percentage_twelfth', $fields)) {
    $selectFields[] = 'ad.percentage_twelfth';
    $fieldQueryParts[] = 'ad.percentage_twelfth AS percentage_twelfth';
}
if (in_array('resume', $fields)) {
    $selectFields[] = 'sa.resume';
    $fieldQueryParts[] = 'sa.resume AS resume';
}

// Prepare the SQL query
$query = "SELECT " . implode(", ", $fieldQueryParts) . "
          FROM job_application ja
          JOIN student sa ON ja.user_id = sa.user_id
          LEFT JOIN academic_details ad ON ja.user_id = ad.user_id
          JOIN course ca ON sa.course_id = ca.course_id
          WHERE ja.job_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header
$sheet->setRowHeight(1, 20);
foreach ($fields as $index => $field) {
    $sheet->setCellValueByColumnAndRow($index + 1, 1, ucfirst(str_replace('_', ' ', $field)));
}

// Populate data
$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    foreach ($fields as $index => $field) {
        $sheet->setCellValueByColumnAndRow($index + 1, $rowIndex, $row[$field]);
    }
    $rowIndex++;
}

// Save the Excel file
$writer = new Xlsx($spreadsheet);
$filename = "applicants_export_" . date('Ymd_His') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save('php://output');

// Close the connection
$conn->close();
exit;
?>
