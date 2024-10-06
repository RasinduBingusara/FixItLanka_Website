<?php
// update_status.php

// Set the response content type to JSON
header('Content-Type: application/json');

// Start the session to access session variables
session_start();

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate the received data
if (!isset($data['PID']) || !is_numeric($data['PID'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid Post ID.']);
    exit();
}

if (!isset($data['Status']) || empty($data['Status'])) {
    echo json_encode(['success' => false, 'message' => 'Status is required.']);
    exit();
}

if (!isset($data['Status_Message']) || empty(trim($data['Status_Message']))) {
    echo json_encode(['success' => false, 'message' => 'Status message is required.']);
    exit();
}

$pid = intval($data['PID']);
$status = $data['Status'];
$status_message = trim($data['Status_Message']);

// Validate the status value
$validStatuses = ['Approved', 'Rejected', 'Needs Revision'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit();
}

// Include the database connection file
include("Database.php");

// Prepare the SQL statement to update the post status
$sqlUpdate = "UPDATE `post` SET `Status` = ?, `Status_Message` = ? WHERE `PID` = ?";

if ($stmt = $conn->prepare($sqlUpdate)) {
    $stmt->bind_param("ssi", $status, $status_message, $pid);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Post status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Post not found or no changes made.']);
        }
    } else {
        // Execution failed
        echo json_encode(['success' => false, 'message' => 'Failed to update post status.']);
    }

    $stmt->close();
} else {
    // Preparation failed
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>
