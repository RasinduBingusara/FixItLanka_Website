<?php
session_start();
if (!isset($_SESSION["UserData"][0])) {
    // Redirect to login page or display an error
    header("Location: ../Login.php");
    exit();
}

include("../Database.php");

$categoryID = $_SESSION["UserData"][1];
$regionID = $_SESSION["UserData"][8];

$posts = array();

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
        r.Region,
        p.Latitude,
        p.Longitude
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
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="../../CSS/supervisorDashboard.css">

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_B0Ud1mIL6Ln66nSCnITXRMDV1c3bssc"></script>

    <!-- Font Awesome for Icons (Optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="signout-container">
        <a href="../Signout.php" class="signout-btn">Sign Out</a>
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
                            <div class="post-image">
                                <?php if (!empty($post['Image'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($post['Image']); ?>" alt="Post Image" class="post-image">
                                <?php else: ?>
                                    <img src="../../IMG/FIXITLANKALOGO.jpg" alt="Default Image" class="post-img">
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

                                <!-- Region Information -->
                                <div class="region-info">
                                    <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <span>Region: <?php echo htmlspecialchars($post['Region']); ?></span>
                                </div>

                                <!-- Status Message -->
                                <?php if (!empty($post['Status_Message'])): ?>
                                    <div class="status-message">
                                        <span class="icon"><i class="fas fa-info-circle"></i></span>
                                        <span>Message: <?php echo htmlspecialchars($post['Status_Message']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Google Map -->
                                <?php if (!empty($post['Latitude']) && !empty($post['Longitude'])): ?>
                                    <div id="map-<?php echo htmlspecialchars($post['PID']); ?>" class="map-container"></div>
                                <?php endif; ?>
                            </div>

                            <!-- Action Form -->
                            <div class="action-form">
                                <form class="status-form" onsubmit="submitStatus(event, <?php echo htmlspecialchars($post['PID']); ?>)">
                                    <div class="form-group">
                                        <label for="status-<?php echo htmlspecialchars($post['PID']); ?>">Status:</label>
                                        <select id="status-<?php echo htmlspecialchars($post['PID']); ?>" name="status" required>
                                            <option value="">-- Select Status --</option>
                                            <option value="Approved">Approve</option>
                                            <option value="Rejected">Reject</option>
                                            <option value="Needs Revision">Needs Revision</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="status-message-<?php echo htmlspecialchars($post['PID']); ?>">Status Message:</label>
                                        <input type="text" id="status-message-<?php echo htmlspecialchars($post['PID']); ?>" name="status_message" placeholder="Enter status message" required>
                                    </div>
                                    <button type="submit" class="submit-button">Submit</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- JavaScript for handling status form submissions and initializing Google Maps -->
    <script>
        // Function to handle status form submission
        function submitStatus(event, pid) {
            event.preventDefault(); // Prevent the form from submitting normally

            const statusSelect = document.getElementById(`status-${pid}`);
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

            // Confirm the action
            if (!confirm(`Are you sure you want to mark this post as "${status}"?`)) {
                return;
            }

            // Send AJAX request to update_status.php
            fetch('../Moderator/update_status.php', {
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

        // Function to initialize all maps
        function initMaps() {
            <?php foreach ($posts as $post): ?>
                <?php if (!empty($post['Latitude']) && !empty($post['Longitude'])): ?>
                    (function() {
                        const mapOptions = {
                            center: { lat: parseFloat('<?php echo htmlspecialchars($post['Latitude']); ?>'), lng: parseFloat('<?php echo htmlspecialchars($post['Longitude']); ?>') },
                            zoom: 15,
                        };
                        const mapElement = document.getElementById('map-<?php echo htmlspecialchars($post['PID']); ?>');
                        if (mapElement) {
                            const map = new google.maps.Map(mapElement, mapOptions);
                            new google.maps.Marker({
                                position: { lat: parseFloat('<?php echo htmlspecialchars($post['Latitude']); ?>'), lng: parseFloat('<?php echo htmlspecialchars($post['Longitude']); ?>') },
                                map: map,
                            });
                        }
                    })();
                <?php endif; ?>
            <?php endforeach; ?>
        }

        // Initialize maps when the window loads
        window.onload = initMaps;
    </script>

</body>

</html>
