<?php
include("Database.php");

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required fields are set
if (isset($data['PID'], $data['UID'])) {
    $PID = intval($data['PID']);
    $UID = intval($data['UID']);

    // Prepare the SQL statement to check if the post has been shared by the user
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `shared_post` WHERE `PID` = ? AND `UID` = ?");
    $stmt->bind_param("ii", $PID, $UID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // Return whether the post has been shared
    echo json_encode(['shared' => $count > 0]);
} else {
    echo json_encode(['shared' => false]);
}

$conn->close();
?>
