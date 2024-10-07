<?php
// delete_supervisor.php

// Start the session
session_start();

// Include database connection file
include("Database.php");

// Check if request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the input from the request
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (isset($input['UID'])) {
        $uid = intval($input['UID']);

        // Prepare the SQL query to delete the supervisor's account
        $sqlDelete = "DELETE FROM useraccount WHERE UID = ? AND UserType = 'Supervisor'";

        if ($stmt = $conn->prepare($sqlDelete)) {
            // Bind parameters and execute the query
            $stmt->bind_param("i", $uid);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting supervisor: ' . $conn->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
