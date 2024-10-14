<?php
// hide_post.php

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
if (!isset($data['PID']) || empty($data['PID'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID is required.']);
    exit();
}

$PID = intval($data['PID']);

// Include the database connection file
include("../Database.php");

// Prepare the SQL statement to update the post's visibility
$sqlHidePost = "UPDATE `post` SET `Visibility` = 'Hide' WHERE `PID` = ?";

if ($stmt = $conn->prepare($sqlHidePost)) {
    $stmt->bind_param("i", $PID);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Post hidden successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Post not found or already hidden.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to hide the post.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare statement.']);
}

$conn->close();
?>
