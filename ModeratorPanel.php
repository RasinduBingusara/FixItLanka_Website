<?php
// ModeratorDashboard.php

// Start the session to access session variables
session_start();

// Include the database connection file
include("Database.php");

// Initialize an array to hold posts
$posts = array();

// Fetch posts that require moderation (e.g., Status = 'Pending')
$sqlPosts = "
    SELECT 
        p.PID,
        p.UID AS PostUID,
        p.Description,
        p.Image,
        p.Status,
        p.Status_Message,
        p.Visibility,
        p.Created_at AS PostCreatedAt,
        u.Username AS PostCreatorUsername,
        u.Email,
        u.ContactNumber,
        c.Category_Name
    FROM 
        post p
    JOIN 
        useraccount u ON p.UID = u.UID
    JOIN 
        category c ON p.CategoryID = c.Category_ID
    WHERE 
        p.Status = 'Pending' AND p.Visibility != 'Hide'
    ORDER BY 
        p.Created_at DESC
";

// Prepare and execute the query
if ($stmt = $conn->prepare($sqlPosts)) {
    $stmt->execute();
    $resultPosts = $stmt->get_result();

    if ($resultPosts && $resultPosts->num_rows > 0) {
        while ($post = $resultPosts->fetch_assoc()) {
            $posts[] = $post;
        }
    }
    $stmt->close();
} else {
    // Handle query preparation error
    error_log("Error preparing statement: " . $conn->error);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Dashboard Page</title>
    <link rel="stylesheet" href="css/ModeratorPanel.css">
    <!-- Optional: Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnH1zpsaqe7Nj3U5ZL9vFSJ0YB8HlvBl4ipnJ8FgVsz8w+/X7z1+Nl7OY2r/kH8G4nD4rJ4Fw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <!-- Include Moderator Navigation Bar -->
    <?php include 'ModeratorNavBar.php'; ?>

    <div class="header">Moderator Dashboard Page</div>

    <div class="panel-container">
        <!-- Posts Section -->
        <div class="posts-section">
            <?php if (empty($posts)): ?>
                <p>No posts pending moderation.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-item" id="post-<?php echo htmlspecialchars($post['PID']); ?>">
                        <div class="post-image">
                            <?php if (!empty($post['Image'])): ?>
                                <img src="<?php echo htmlspecialchars($post['Image']); ?>" alt="Post Image" class="post-img">
                            <?php else: ?>
                                <img src="pics/defaultPost.png" alt="Default Image" class="post-img">
                            <?php endif; ?>
                            <div class="post-label">Post</div>
                        </div>
                        <div class="info-section">
                            <div class="user-info">
                                <span class="icon"><i class="fas fa-user"></i></span>
                                <span><?php echo htmlspecialchars($post['PostCreatorUsername']); ?> (<?php echo htmlspecialchars($post['Email']); ?>)</span>
                            </div>
                            <div class="description">
                                <span class="icon"><i class="fas fa-align-left"></i></span>
                                <span><?php echo nl2br(htmlspecialchars($post['Description'])); ?></span>
                            </div>
                            <div class="status">
                                <form class="status-form" onsubmit="updateStatus(event, <?php echo htmlspecialchars($post['PID']); ?>)">
                                    <label for="status-select-<?php echo htmlspecialchars($post['PID']); ?>">Status:</label>
                                    <select id="status-select-<?php echo htmlspecialchars($post['PID']); ?>" name="status" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="Approved">Approve</option>
                                        <option value="Rejected">Reject</option>
                                        <option value="Needs Revision">Needs Revision</option>
                                    </select>
                                    <label for="status-message-<?php echo htmlspecialchars($post['PID']); ?>" class="status-message-label">Message:</label>
                                    <input type="text" id="status-message-<?php echo htmlspecialchars($post['PID']); ?>" name="status_message" placeholder="Enter status message" required>
                                    <button type="submit" class="submit-button">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript for handling status updates -->
    <script>
        function updateStatus(event, pid) {
            event.preventDefault(); // Prevent form from submitting normally

            const statusSelect = document.getElementById(`status-select-${pid}`);
            const statusMessageInput = document.getElementById(`status-message-${pid}`);
            const status = statusSelect.value;
            const statusMessage = statusMessageInput.value.trim();

            if (status === "") {
                alert("Please select a status.");
                return;
            }

            if (statusMessage === "") {
                alert("Please enter a status message.");
                return;
            }

            // Send AJAX request to update_status.php
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    PID: pid,
                    Status: status,
                    Status_Message: statusMessage
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Post status updated successfully.');
                    // Optionally, remove the post from the list or update its display
                    const postDiv = document.getElementById(`post-${pid}`);
                    if (postDiv) {
                        postDiv.remove();
                    }
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred while updating the status.');
            });
        }
    </script>

</body>
</html>
