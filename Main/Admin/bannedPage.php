<?php
include("../Database.php");
include('AdminNavigationBar.php');

if (!isset($_SESSION["UserData"][0])) {
    header("Location: login.php");
    exit();
}

$bannedUsers = array();
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
    <link rel="stylesheet" href="../../CSS/bannedPage.css">
</head>
<body>

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
    </script>

</body>
</html>
