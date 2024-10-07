<?php
// ModeratorDashboard.php

// Start the session to access session variables
session_start();

// Include the database connection file
include("Database.php");

// Check if the moderator user is logged in
if (!isset($_SESSION["UserData"][0])) {
    // Redirect to login page or display an error
    header("Location: login.php");
    exit();
}

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
    <!-- Head content -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Dashboard Page</title>
    <link rel="stylesheet" href="css/ModeratorReviewPage.css">
</head>
<body>

    <!-- Include Moderator Navigation Bar -->
    <?php include 'ModeratorNavBar.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <h1>Moderator Dashboard</h1>
        </header>

        <!-- Display Success Message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert success">
                Post has been updated successfully.
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="panel-container">
            <!-- Posts Section -->
            <div class="posts-section">
                <?php if (empty($posts)): ?>
                    <p class="no-data">No posts pending moderation.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-item" id="post-<?php echo htmlspecialchars($post['PID']); ?>">
                            <!-- Post Image -->
                            <div class="post-image">
                                <?php if (!empty($post['Image'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['Image']); ?>" alt="Post Image" class="post-img">
                                <?php else: ?>
                                    <img src="pics/defaultPost.png" alt="Default Image" class="post-img">
                                <?php endif; ?>
                                <div class="post-label">Post</div>
                            </div>

                            <!-- Information Sections -->
                            <div class="info-section">
                                <!-- User Information -->
                                <div class="user-info">
                                    <span class="icon"><i class="fas fa-user"></i></span>
                                    <span><?php echo htmlspecialchars($post['PostCreatorUsername']); ?> (<?php echo htmlspecialchars($post['Email']); ?>)</span>
                                </div>

                                <!-- Description -->
                                <div class="description">
                                    <span class="icon"><i class="fas fa-align-left"></i></span>
                                    <span><?php echo nl2br(htmlspecialchars($post['Description'])); ?></span>
                                </div>

                                <!-- Category Information -->
                                <div class="category-info">
                                    <span class="icon"><i class="fas fa-tags"></i></span>
                                    <span>Category: <?php echo htmlspecialchars($post['Category_Name']); ?></span>
                                </div>

                                <!-- Status Message -->
                                <?php if (!empty($post['Status_Message'])): ?>
                                    <div class="status-message">
                                        <span class="icon"><i class="fas fa-info-circle"></i></span>
                                        <span>Message: <?php echo htmlspecialchars($post['Status_Message']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button class="btn approve-post" onclick="updateStatus(<?php echo htmlspecialchars($post['PID']); ?>, 'Approved')">
                                    <i class="fas fa-check-circle"></i> Approve
                                </button>
                                <button class="btn reject-post" onclick="updateStatus(<?php echo htmlspecialchars($post['PID']); ?>, 'Rejected')">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                                <button class="btn needs-revision-post" onclick="updateStatus(<?php echo htmlspecialchars($post['PID']); ?>, 'Needs Revision')">
                                    <i class="fas fa-edit"></i> Needs Revision
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- JavaScript for action buttons and responsive navigation -->
    <script>
        // Function to update post status
        function updateStatus(pid, status) {
            // Prompt for a status message
            let statusMessage = prompt("Enter a message for the status update:");
            if (statusMessage === null) {
                // User canceled the prompt
                return;
            }
            statusMessage = statusMessage.trim();
            if (statusMessage === "") {
                alert("Status message cannot be empty.");
                return;
            }

            if (!confirm(`Are you sure you want to mark this post as "${status}"?`)) {
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
                    // Remove the post from the list
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
