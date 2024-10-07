<?php
// update_supervisor.php

// Start the session
session_start();

// Include database connection file
include("Database.php");

// Check if request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the input from the request
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (isset($input['UID'], $input['Username'], $input['Email'], $input['ContactNumber'])) {
        $uid = intval($input['UID']);
        $username = trim($input['Username']);
        $email = trim($input['Email']);
        $contactNumber = trim($input['ContactNumber']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit();
        }

        // Validate contact number format
        if (!preg_match("/^\+?[0-9]{7,15}$/", $contactNumber)) {
            echo json_encode(['success' => false, 'message' => 'Invalid contact number format. It should contain 7 to 15 digits and may start with "+".']);
            exit();
        }

        // Prepare the SQL query to update the supervisor's account
        $sqlUpdate = "UPDATE useraccount 
                      SET Username = ?, Email = ?, ContactNumber = ?
                      WHERE UID = ? AND UserType = 'Supervisor'";

        if ($stmt = $conn->prepare($sqlUpdate)) {
            // Bind parameters and execute the query
            $stmt->bind_param("sssi", $username, $email, $contactNumber, $uid);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating supervisor: ' . $conn->error]);
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
