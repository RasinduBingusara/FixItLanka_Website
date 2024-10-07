<?php
// SupervisorDashboard.php

// Start the session to access session variables
session_start();

// Include the database connection file
include("Database.php");

// Check if the supervisor user is logged in
if (!isset($_SESSION["UserData"][0])) {
    // Redirect to login page or display an error
    header("Location: login.php");
    exit();
}

// Get supervisor's category and region from the session
$categoryID = $_SESSION["UserData"][1]; // Category ID
$regionID = $_SESSION["UserData"][8]; // Region ID

// Initialize an array to hold posts
$posts = array();

// Fetch posts based on supervisor's category and region
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
        c.Category_Name,
        r.Region
    FROM 
        post p
    JOIN 
        useraccount u ON p.UID = u.UID
    JOIN 
        category c ON p.CategoryID = c.Category_ID
    JOIN 
        region r ON p.RegionID = r.RegionID
    WHERE 
        p.CategoryID = ? AND p.RegionID = ? AND p.Status = 'Pending' AND p.Visibility != 'Hide'
    ORDER BY 
        p.Created_at DESC
";

// Prepare and execute the query
if ($stmt = $conn->prepare($sqlPosts)) {
    $stmt->bind_param("ii", $categoryID, $regionID);
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
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="css/supervisorDashboard.css">
</head>
<body>

    <!-- Sign-out button at top-right -->
    <div class="signout-container">
        <a href="Signout.php" class="signout-btn">Sign Out</a>
    </div>

    <main class="main-content">
        <header class="page-header">
            <h1>Supervisor Dashboard</h1>
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
                    <p class="no-data">No posts pending moderation in your category and region.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-item" id="post-<?php echo htmlspecialchars($post['PID']); ?>">
                            
                            <!-- Post Image -->
                            <div class="row">
                                <div class="label">Post Image:</div>
                                <div class="value">
                                    <?php if (!empty($post['Image'])): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($post['Image']); ?>" alt="Post Image" class="post-img">
                                    <?php else: ?>
                                        <img src="pics/FIXITLANKALOGO.jpg" alt="Default Image" class="post-img" style="width: 150px;">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- User Information -->
                            <div class="row">
                                <div class="label">User:</div>
                                <div class="value"><?php echo htmlspecialchars($post['PostCreatorUsername']); ?> (<?php echo htmlspecialchars($post['Email']); ?>)</div>
                            </div>

                            <!-- Description -->
                            <div class="row">
                                <div class="label">Description:</div>
                                <div class="value"><?php echo nl2br(htmlspecialchars($post['Description'])); ?></div>
                            </div>

                            <!-- Category -->
                            <div class="row">
                                <div class="label">Category:</div>
                                <div class="value"><?php echo htmlspecialchars($post['Category_Name']); ?></div>
                            </div>

                            <!-- Region -->
                            <div class="row">
                                <div class="label">Region:</div>
                                <div class="value"><?php echo htmlspecialchars($post['Region']); ?></div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row action-buttons">
                                <form onsubmit="updateStatus(event, <?php echo htmlspecialchars($post['PID']); ?>)">
                                    <label for="status-select-<?php echo htmlspecialchars($post['PID']); ?>">Update Status:</label>
                                    <select id="status-select-<?php echo htmlspecialchars($post['PID']); ?>" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="Approved">Approve</option>
                                        <option value="Rejected">Reject</option>
                                        <option value="Needs Revision">Needs Revision</option>
                                    </select>
                                    <input type="text" id="status-message-<?php echo htmlspecialchars($post['PID']); ?>" placeholder="Enter a status message" required>
                                    <button type="submit" class="submit-button">Submit</button>
                                </form>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

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
