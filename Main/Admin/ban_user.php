<?php
// ban_user.php

// Start the session to access session variables
session_start();

// Set the response header to JSON
header('Content-Type: application/json');

// Check if the admin user is logged in
if (!isset($_SESSION["UserData"][0])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate the received data
if (!isset($data['UID']) || empty($data['UID'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required.']);
    exit();
}

$UID = intval($data['UID']);

// Include the database connection file
include("../Database.php");

// Prepare the SQL statement to update the user's account status
$sqlBanUser = "UPDATE `useraccount` SET `Acc_Status` = 'Banned' WHERE `UID` = ?";

if ($stmt = $conn->prepare($sqlBanUser)) {
    $stmt->bind_param("i", $UID);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'User banned successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found or already banned.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to ban the user.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare statement.']);
}

$conn->close();
?>
