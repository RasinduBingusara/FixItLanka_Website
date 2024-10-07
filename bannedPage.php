<?php
// bannedPage.php

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

// Initialize an array to hold banned users
$bannedUsers = array();

// Fetch banned users from the useraccount table
$sqlBannedUsers = "
    SELECT 
        UID, 
        Username, 
        Email, 
        ContactNumber 
    FROM 
        useraccount 
    WHERE 
        Acc_Status = 'Banned'
    ORDER BY 
        Username ASC
";

if ($stmt = $conn->prepare($sqlBannedUsers)) {
    $stmt->execute();
    $resultBannedUsers = $stmt->get_result();

    if ($resultBannedUsers && $resultBannedUsers->num_rows > 0) {
        while ($user = $resultBannedUsers->fetch_assoc()) {
            $bannedUsers[] = $user;
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
    <title>Banned Users</title>
    <link rel="stylesheet" href="css/bannedPage.css">
    <!-- Optional: Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnH1zpsaqe7Nj3U5ZL9vFSJ0YB8HlvBl4ipnJ8FgVsz8w+/X7z1+Nl7OY2r/kH8G4nD4rJ4Fw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <!-- Include Admin Navigation Bar -->
    <?php include 'AdminNavigationBar.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <h1>Banned Users</h1>
        </header>

        <!-- Display Success Message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert success">
                User has been unbanned successfully.
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="banned-container">
            <?php if (empty($bannedUsers)): ?>
                <p class="no-data">No banned users found.</p>
            <?php else: ?>
                <?php foreach ($bannedUsers as $user): ?>
                    <div class="banned-item" id="user-<?php echo htmlspecialchars($user['UID']); ?>">
                        <div class="user-info">
                            <span class="icon"><i class="fas fa-user"></i></span>
                            <div class="details">
                                <span class="username"><?php echo htmlspecialchars($user['Username']); ?></span>
                                <span class="email"><?php echo htmlspecialchars($user['Email']); ?></span>
                            </div>
                        </div>
                        <button class="btn unban-btn" onclick="unbanUser(<?php echo htmlspecialchars($user['UID']); ?>)">
                            <i class="fas fa-unlock"></i> Unban User
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- JavaScript for handling Unban actions -->
    <script>
        function unbanUser(uid) {
            if (confirm("Are you sure you want to unban this user?")) {
                // Send AJAX request to unban_user.php
                fetch('unban_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ UID: uid }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User has been unbanned successfully.');
                        // Remove the user from the DOM
                        const userDiv = document.getElementById('user-' + uid);
                        if (userDiv) {
                            userDiv.remove();
                        }
                    } else {
                        alert('Error unbanning user: ' + data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('An error occurred while unbanning the user.');
                });
            }
        }

        // Responsive Navigation Toggle (if needed)
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.getElementById('hamburger');
            const navLinks = document.querySelector('.nav-links');

            if (hamburger) {
                hamburger.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
            }
        });
    </script>

</body>
</html>
