<?php
header('Content-Type: application/json');

if (!isset($_GET['UID']) || !isset($_GET['PID'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$UID = intval($_GET['UID']);
$PID = intval($_GET['PID']);

// Database connection
include('Database.php');

// Fetch the user's vote
$stmt = $conn->prepare("SELECT Vote_direction FROM `vote` WHERE UID = ? AND PID = ?");
$stmt->bind_param("ii", $UID, $PID);
$stmt->execute();
$stmt->bind_result($userVote);
$voteExists = $stmt->fetch();
$stmt->close();

if ($voteExists) {
    echo json_encode(['success' => true, 'userVote' => $userVote]);
} else {
    echo json_encode(['success' => true, 'userVote' => 0]); // No vote
}

$conn->close();
?>
