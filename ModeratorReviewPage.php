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
  <!-- Optional: Include Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnH1zpsaqe7Nj3U5ZL9vFSJ0YB8HlvBl4ipnJ8FgVsz8w+/X7z1+Nl7OY2r/kH8G4nD4rJ4Fw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                  <img src="data:image/jpeg;base64,<?php echo base64_encode($post['Image']); ?>" alt="Post Image" class="post-img">
                <?php else: ?>
                  <img src="pics/FIXITLANKALOGO.jpg" alt="Default Image" class="post-img">
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

  <!-- JavaScript for handling status form submissions and responsive navigation -->
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

    // Responsive Navigation Toggle
    document.addEventListener('DOMContentLoaded', function() {
      const hamburger = document.getElementById('hamburger');
      const sidebar = document.getElementById('sidebar');

      if (hamburger) {
        hamburger.addEventListener('click', function() {
          sidebar.classList.toggle('open');
        });
      }
    });
  </script>

</body>

</html>