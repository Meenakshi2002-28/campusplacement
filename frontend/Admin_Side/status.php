<?php
session_start();
require 'vendor/autoload.php'; // Include the PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\IOFactory;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_placement";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Step 1: Upload the Excel file
    $file = $_FILES['excel_file'];

    // Check if file was uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("File upload error.");
    }

    $filePath = $file['tmp_name'];

    // Step 2: Read the Excel file
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();

    // Array to store roll numbers
    $rollNumbers = [];

    // Step 3: Fetch roll numbers from the first column
    foreach ($worksheet->getRowIterator() as $row) {
        // Get the cell iterator for the current row
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // This is important for empty cells

        // Fetch the first cell in the row (first column)
        $cell = $cellIterator->current(); // This gets the first cell in the row
        if (!is_null($cell) && !is_null($cell->getValue())) {
            $rollNumbers[] = $cell->getValue(); // Store the roll number
        }
    }

    foreach ($rollNumbers as $rollNo) {
        $userId = trim($rollNo); // Get current user ID
        
        // Check if user_id exists in the STUDENT table
        $checkQuery = "SELECT COUNT(*) FROM STUDENT WHERE user_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        
        if ($checkStmt) {
            $checkStmt->bind_param("s", $userId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $count = $checkResult->fetch_row()[0];

            if ($count > 0) {
                // Update the status to 'Accepted' for the specific user
                $jobId = (int)$_POST['job_id']; // Get the job ID from POST request
                $updateQueryAccept = "UPDATE job_application SET status = 'Accepted' WHERE user_id = ? AND job_id = ?";
                $updateStmtAccept = $conn->prepare($updateQueryAccept);
                
                if ($updateStmtAccept) {
                    $updateStmtAccept->bind_param("si", $userId, $jobId);
                    $updateStmtAccept->execute();

                    // Insert into PLACEMENT table for the current user
                    $placementDate = date('Y-m-d'); // Get the current date
                    $insertQuery = "INSERT INTO PLACEMENT (user_id, job_id, placement_date) VALUES (?, ?, ?)";
                    $insertStmt = $conn->prepare($insertQuery);

                    if ($insertStmt) {
                        $insertStmt->bind_param("sis", $userId, $jobId, $placementDate);
                        $insertStmt->execute();

                        // Reject other applicants, but exclude already accepted ones
                        $updateQueryReject = "UPDATE job_application SET status = 'Rejected' WHERE job_id = ? AND user_id != ? AND status != 'Accepted'";
                        $updateStmtReject = $conn->prepare($updateQueryReject);

                        if ($updateStmtReject) {
                            $updateStmtReject->bind_param("is", $jobId, $userId);
                            $updateStmtReject->execute();

                            // Close the reject statement
                            $updateStmtReject->close();
                        }

                        // Close the insert statement
                        $insertStmt->close();
                    }

                    // Close the accept statement
                    $updateStmtAccept->close();
                }
            }

            // Close the check statement
            $checkStmt->close();
        }
    }

    $conn->close();
    header("Location: applicants.php?job_id=" . urlencode($job_id) . "&status=success");
    exit();
}
?>
