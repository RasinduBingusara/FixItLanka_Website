<?php
header('Content-Type: application/json');

// Get the JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if required data is provided
if (!isset($data['UID']) || !isset($data['PID']) || !isset($data['Vote_direction'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid input.']);
  exit;
}

$UID = intval($data['UID']);
$PID = intval($data['PID']);
$Vote_direction = intval($data['Vote_direction']); // 1 for upvote, -1 for downvote

// Database connection
include('Database.php');

// Begin transaction
$conn->begin_transaction();

try {
  // Check if the user has already voted on this post
  $stmt = $conn->prepare("SELECT Vote_direction FROM `vote` WHERE UID = ? AND PID = ?");
  $stmt->bind_param("ii", $UID, $PID);
  $stmt->execute();
  $stmt->bind_result($existingVote);
  $voteExists = $stmt->fetch();
  $stmt->close();

  if ($voteExists) {
    if ($existingVote == $Vote_direction) {
      // User is removing their vote (toggle off)
      $stmt = $conn->prepare("DELETE FROM `vote` WHERE UID = ? AND PID = ?");
      $stmt->bind_param("ii", $UID, $PID);
      $stmt->execute();
      $stmt->close();
      $userVote = 0; // No current vote
    } else {
      // User is changing their vote
      $stmt = $conn->prepare("UPDATE `vote` SET Vote_direction = ?, Created_at = NOW() WHERE UID = ? AND PID = ?");
      $stmt->bind_param("iii", $Vote_direction, $UID, $PID);
      $stmt->execute();
      $stmt->close();
      $userVote = $Vote_direction;
    }
  } else {
    // User is voting for the first time on this post
    $stmt = $conn->prepare("INSERT INTO `vote` (UID, PID, Vote_direction, Created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $UID, $PID, $Vote_direction);
    $stmt->execute();
    $stmt->close();
    $userVote = $Vote_direction;
  }

  // Fetch the updated vote counts
  $upVoteStmt = $conn->prepare("SELECT COUNT(*) as totalUpVotes FROM `vote` WHERE PID = ? AND Vote_direction = 1");
  $upVoteStmt->bind_param("i", $PID);
  $upVoteStmt->execute();
  $upVoteResult = $upVoteStmt->get_result();
  $upVoteRow = $upVoteResult->fetch_assoc();
  $totalUpVotes = $upVoteRow['totalUpVotes'];
  $upVoteStmt->close();

  $downVoteStmt = $conn->prepare("SELECT COUNT(*) as totalDownVotes FROM `vote` WHERE PID = ? AND Vote_direction = 0");
  $downVoteStmt->bind_param("i", $PID);
  $downVoteStmt->execute();
  $downVoteResult = $downVoteStmt->get_result();
  $downVoteRow = $downVoteResult->fetch_assoc();
  $totalDownVotes = $downVoteRow['totalDownVotes'];

  // Commit transaction
  $conn->commit();

  echo json_encode([
    'success' => true,
    'totalUpVotes' => $totalUpVotes,
    'totalDownVotes' => $totalDownVotes,
    'userVote' => $userVote,
  ]);
} catch (Exception $e) {
  // Rollback transaction on error
  $conn->rollback();
  error_log("Error updating vote: " . $e->getMessage());
  echo json_encode(['success' => false, 'message' => 'Database error.']);
}

$conn->close();
