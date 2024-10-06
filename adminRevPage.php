<?php
// adminRevPage.php

// Start the session to access session variables
session_start();

// Include the database connection file
include("Database.php");

// Check if the admin user is logged in
if (!isset($_SESSION["UserData"][0])) {
    // Redirect to login page or display an error
    header("Location: login.php");
    exit();
}

// Initialize an array to hold reports
$reports = array();

// Fetch data from admin_report, post, and useraccount tables using JOINs
$sqlReports = "
    SELECT 
    ar.PID, 
    ar.UID AS ReportedUID, 
    ar.Report_Message, 
    ar.Created_at AS ReportCreatedAt,
    p.UID AS PostUID,
    p.CategoryID, 
    p.ComplaintID, 
    p.RegionID, 
    p.Description, 
    p.Longitude, 
    p.Latitude, 
    p.Status, 
    p.Status_Message, 
    p.Is_Anonymouse, 
    p.Image, 
    p.Visibility, 
    p.Created_at AS PostCreatedAt,
    u.Username AS ReportedUsername, 
    u.Email, 
    u.ContactNumber,
    pu.Username AS PostCreatorUsername, -- Original Post Creator's Username
    (SELECT COUNT(*) FROM vote WHERE vote.PID = p.PID AND vote.Vote_direction = 1) AS totalUpVotes,
    (SELECT COUNT(*) FROM vote WHERE vote.PID = p.PID AND vote.Vote_direction = -1) AS totalDownVotes,
    (SELECT COUNT(*) FROM comment WHERE comment.PID = p.PID) AS totalComments
FROM 
    admin_report ar
JOIN 
    post p ON ar.PID = p.PID
JOIN 
    useraccount u ON ar.UID = u.UID -- Reported User
JOIN 
    useraccount pu ON p.UID = pu.UID -- Original Post Creator
WHERE 
    p.Visibility != 'Hide'
ORDER BY 
    ar.Created_at DESC ;


";

// Prepare and execute the query
if ($stmtReports = $conn->prepare($sqlReports)) {
    $stmtReports->execute();
    $resultReports = $stmtReports->get_result();

    if ($resultReports && $resultReports->num_rows > 0) {
        while ($report = $resultReports->fetch_assoc()) {
            $reports[] = $report;
        }
    }
    $stmtReports->close();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Review Page</title>
    <link rel="stylesheet" href="css/adminRevPage.css">
    <!-- Optional: Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnH1zpsaqe7Nj3U5ZL9vFSJ0YB8HlvBl4ipnJ8FgVsz8w+/X7z1+Nl7OY2r/kH8G4nD4rJ4Fw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <!-- Include Admin Navigation Bar -->
    <?php include 'AdminNavigationBar.php'; ?>

    <div class="header">Admin Review Page</div>

    <div class="review-container">
        <?php if (empty($reports)): ?>
            <p>No reports found.</p>
        <?php else: ?>
            <?php foreach ($reports as $report): ?>
                <div class="review-item">
                    <!-- Post Block -->
                    <div class="post-block">
                        <?php
                        // Set variables for Post.php
                        $PID = $report["PID"];
                        $postUsername = $report["PostCreatorUsername"];
                        $PostUID = $report["ReportedUID"];
                        $postDescription = $report["Description"];
                        $postImage = $report["Image"]; // Assuming this is a file path. If it's a blob, adjust accordingly.
                        $totalUpVotes = $report["totalUpVotes"];
                        $totalDownVotes = $report["totalDownVotes"];
                        $totalComments = $report["totalComments"];
                        $currentUserUID = $_SESSION["UserData"][0]; // Logged-in admin UID

                        include("Post.php");
                        ?>
                    </div>

                    <!-- Other Sections -->
                    <div class="info-section">
                        <!-- Reported User Information -->
                        <div class="report-info">
                            <span class="icon"><i class="fas fa-user"></i></span>
                            <span>Reported User: <?php echo htmlspecialchars($report['ReportedUsername']); ?> (UID: <?php echo htmlspecialchars($report['ReportedUID']); ?>)</span>
                        </div>

                        <!-- Report Description -->
                        <div class="description">
                            <span class="icon"><i class="fas fa-file-alt"></i></span>
                            <span><?php echo nl2br(htmlspecialchars($report['Report_Message'])); ?></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button class="hide-post" onclick="hidePost(<?php echo htmlspecialchars($report['PID']); ?>)">Hide Post</button>
                        <button class="ban-user" onclick="banUser(<?php echo htmlspecialchars($report['PostUID']); ?>)">Ban User</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- JavaScript for action buttons -->
    <script>
        // Function to hide a post
        function hidePost(PID) {
            if (confirm("Are you sure you want to hide this post?")) {
                fetch('hide_post.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            PID: PID
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Post hidden successfully.');
                            // Optionally, you can remove the post from the DOM or update its status
                            // For example, reload the page to reflect changes
                            location.reload();
                        } else {
                            alert('Error hiding post: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while hiding the post.');
                    });
            }
        }

        // Function to ban a user
        function banUser(UID) {
            if (confirm("Are you sure you want to ban this user?")) {
                fetch('ban_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            UID: UID
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('User banned successfully.');
                            // Optionally, you can remove the user's posts or update the UI accordingly
                            // For example, reload the page to reflect changes
                            location.reload();
                        } else {
                            alert('Error banning user: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while banning the user.');
                    });
            }
        }

        // Function to escape HTML characters to prevent XSS
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }
    </script>

</body>

</html>