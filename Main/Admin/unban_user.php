<?php
// unban_user.php

// Set the response content type to JSON
header('Content-Type: application/json');

// Start the session to access session variables
session_start();

// Check if the admin user is logged in
if (!isset($_SESSION["UserData"][0])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate the received data
if (!isset($data['UID']) || !is_numeric($data['UID'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid User ID.']);
    exit();
}

$uid = intval($data['UID']);

// Include the database connection file
include("../Database.php");

// Prepare the SQL statement to update Acc_Status to 'Active'
$sqlUpdate = "UPDATE `useraccount` SET `Acc_Status` = 'Active' WHERE `UID` = ? AND `Acc_Status` = 'Banned'";

if ($stmt = $conn->prepare($sqlUpdate)) {
    $stmt->bind_param("i", $uid);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'User has been unbanned.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found or is not banned.']);
        }
    } else {
        // Execution failed
        echo json_encode(['success' => false, 'message' => 'Failed to unban user.']);
    }
    $stmt->close();
} else {
    // Preparation failed
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>
