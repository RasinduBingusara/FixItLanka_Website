<?php
include("Database.php");

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required fields are set
if (isset($data['PID'], $data['UID'])) {
    $PID = intval($data['PID']);
    $UID = intval($data['UID']);

    // Prepare the SQL statement to insert into the shared_post table
    $stmt = $conn->prepare("INSERT INTO `shared_post` (`UID`, `PID`) VALUES (?, ?)");
    $stmt->bind_param("ii", $UID, $PID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
}

$conn->close();
?>
