<?php
header('Content-Type: application/json');

if (!isset($_GET['PID'])) {
    echo json_encode([]);
    exit;
}

$PID = $_GET['PID'];

// Database connection
include('Database.php');

// Fetch comments for the post
$stmt = $conn->prepare("SELECT c.Message, u.Username
                        FROM comment c
                        JOIN useraccount u ON c.UID = u.UID
                        WHERE c.PID = ?
                        ORDER BY c.Created_at DESC");
if ($stmt) {
    $stmt->bind_param("i", $PID);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = [
            'username' => htmlspecialchars($row['Username'], ENT_QUOTES),
            'text' => htmlspecialchars($row['Message'], ENT_QUOTES),
        ];
    }
    echo json_encode($comments);
    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>
