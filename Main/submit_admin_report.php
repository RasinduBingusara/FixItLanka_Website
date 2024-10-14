<?php
include("Database.php");

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required fields are set
if (isset($data['PID'], $data['UID'], $data['Report_Message'])) {
    $PID = intval($data['PID']);
    $UID = intval($data['UID']);
    $reportMessage = $conn->real_escape_string($data['Report_Message']); // Escape special characters for SQL

    // Prepare the SQL statement to insert the report
    $stmt = $conn->prepare("INSERT INTO `admin_report`(`PID`, `UID`, `Report_Message`, `Created_at`) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $PID, $UID, $reportMessage);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Report submitted successfully
        echo json_encode(['success' => true]);
    } else {
        // There was an error during submission
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    // Required fields are missing
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
}

$conn->close();
?>
