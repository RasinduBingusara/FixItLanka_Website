<?php
header('Content-Type: application/json');

// Get the JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if required data is provided
if (!isset($data['UID']) || !isset($data['PID']) || !isset($data['Comment_text'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$UID = $data['UID'];
$PID = $data['PID'];
$Comment_text = $data['Comment_text'];

// Database connection
include('Database.php');

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO `comment`(`UID`, `PID`, `Message`, `Created_at`) VALUES (?, ?, ?, NOW())");
if ($stmt) {
    $stmt->bind_param("iis", $UID, $PID, $Comment_text);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Statement preparation failed.']);
}

$conn->close();
?>
