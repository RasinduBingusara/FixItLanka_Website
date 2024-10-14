<?php
// updateModerator.php

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

if (!isset($data['Username']) || empty(trim($data['Username']))) {
    echo json_encode(['success' => false, 'message' => 'Name is required.']);
    exit();
}

if (!isset($data['Email']) || empty(trim($data['Email']))) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit();
}

if (!isset($data['ContactNumber']) || empty(trim($data['ContactNumber']))) {
    echo json_encode(['success' => false, 'message' => 'Contact Number is required.']);
    exit();
}

$uid = intval($data['UID']);
$username = trim($data['Username']);
$email = trim($data['Email']);
$contactNumber = trim($data['ContactNumber']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit();
}

// Validate contact number format
if (!preg_match("/^\+?[0-9]{7,15}$/", $contactNumber)) {
    echo json_encode(['success' => false, 'message' => 'Invalid contact number format.']);
    exit();
}

include("../Database.php");

// Check if the new email already exists for another user
$sqlCheckEmail = "SELECT UID FROM useraccount WHERE Email = ? AND UID != ?";
if ($stmtCheck = $conn->prepare($sqlCheckEmail)) {
    $stmtCheck->bind_param("si", $email, $uid);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
        $stmtCheck->close();
        $conn->close();
        exit();
    }
    $stmtCheck->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    $conn->close();
    exit();
}

// Prepare the SQL statement to update the moderator
$sqlUpdate = "UPDATE `useraccount` SET `Username` = ?, `Email` = ?, `ContactNumber` = ? WHERE `UID` = ?";

if ($stmt = $conn->prepare($sqlUpdate)) {
    $stmt->bind_param("sssi", $username, $email, $contactNumber, $uid);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Moderator account updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made or moderator not found.']);
        }
    } else {
        // Execution failed
        echo json_encode(['success' => false, 'message' => 'Failed to update moderator account.']);
    }

    $stmt->close();
} else {
    // Preparation failed
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>
