<?php
// deleteModerator.php

// Set the response content type to JSON
header('Content-Type: application/json');

// Start the session to access session variables
session_start();

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate the received data
if (!isset($data['UID']) || !is_numeric($data['UID'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid Moderator ID.']);
    exit();
}

$uid = intval($data['UID']);

include("../Database.php");

// Prepare the SQL statement to delete the moderator
$sqlDelete = "DELETE FROM `useraccount` WHERE `UID` = ? AND `UserType` = 'Moderator'";

if ($stmt = $conn->prepare($sqlDelete)) {
    $stmt->bind_param("i", $uid);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Moderator account deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Moderator not found or already deleted.']);
        }
    } else {
        // Execution failed
        echo json_encode(['success' => false, 'message' => 'Failed to delete moderator account.']);
    }

    $stmt->close();
} else {
    // Preparation failed
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>
