<?php
include('NavigationBar.php');
include('Database.php');

// Initialize variables
$Username = "";
$Email = "";
$ContactNumber = "";
$otherUID = null;
$editDetailsAccess = false;
$IsCurrentUser = true;
$CurrentProfileUserID = "";
$view = 'my_posts'; // Default view

// Determine the profile to display
if (isset($_GET["otherUID"])) {
    $otherUID = intval($_GET["otherUID"]);
    $IsCurrentUser = false;
    $CurrentProfileUserID = $otherUID;

    // Fetch other user's details
    $stmt = $conn->prepare("SELECT Username, Email, ContactNumber, ProfilePicture FROM useraccount WHERE UID = ?");
    $stmt->bind_param("i", $otherUID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $Username = htmlspecialchars($row["Username"]);
        $Email = htmlspecialchars($row["Email"]);
        $ContactNumber = htmlspecialchars($row["ContactNumber"]);
    }
    $stmt->close();
} else {
    $CurrentProfileUserID = $_SESSION['UserData'][0];
    if (isset($_GET["editProfileBtn"])) {
        $editDetailsAccess = true;
    }
    if (isset($_POST["updateProfileBtn"])) {
        if (isset($_POST["username"]) && isset($_POST["ContactNumber"])) {
            $username = trim($_POST["username"]);
            $contactNumber = trim($_POST["ContactNumber"]);
            $uid = $_SESSION['UserData'][0];

            if (!empty($username) && !empty($contactNumber) && !empty($uid)) {
                $stmt = $conn->prepare("UPDATE useraccount SET Username = ?, ContactNumber = ? WHERE UID = ?");
                if ($stmt) {
                    $stmt->bind_param("ssi", $username, $contactNumber, $uid);
                    if ($stmt->execute()) {
                        $editDetailsAccess = false;
                        $_SESSION['UserData'][2] = $username;
                        $_SESSION['UserData'][4] = $contactNumber;
                        echo "<script>alert('Profile updated successfully.');</script>";
                    } else {
                        error_log("Error executing query: " . $stmt->error);
                        echo "<script>alert('An error occurred while updating your profile.');</script>";
                    }
                    $stmt->close();
                } else {
                    error_log("Error preparing statement: " . $conn->error);
                    echo "<script>alert('An error occurred while updating your profile.');</script>";
                }
            } else {
                echo "<script>alert('Please fill in all required fields.');</script>";
            }
        }
    }

    if (isset($_SESSION["UserData"])) {
        $Username = htmlspecialchars($_SESSION["UserData"][2]);
        $Email = htmlspecialchars($_SESSION["UserData"][3]);
        $ContactNumber = htmlspecialchars($_SESSION["UserData"][4]);
    }
}

// Determine which view to display
if (isset($_GET['view'])) {
    if ($_GET['view'] === 'shared_posts') {
        $view = 'shared_posts';
    }
}

// Fetch posts based on the selected view
$posts = array();

if ($view === 'my_posts') {
    // Fetch created posts
    $sqlPosts = "SELECT PID, UID, Description, Longitude, Latitude, Status, Status_Message, Is_Anonymouse, Visibility, Created_at, Image, CategoryID, RegionID, ComplaintID 
                 FROM post 
                 WHERE Visibility = 'Public' AND UID = ?";
    $stmtPosts = $conn->prepare($sqlPosts);
    if ($stmtPosts) {
        $stmtPosts->bind_param("i", $CurrentProfileUserID);
        $stmtPosts->execute();
        $resultPosts = $stmtPosts->get_result();

        if ($resultPosts && $resultPosts->num_rows > 0) {
            while ($rowPost = $resultPosts->fetch_assoc()) {
                $posts[] = array(
                    'PID' => $rowPost['PID'],
                    'UID' => $rowPost['UID'],
                    'Description' => htmlspecialchars($rowPost['Description']),
                    'Longitude' => $rowPost['Longitude'],
                    'Latitude' => $rowPost['Latitude'],
                    'Status' => $rowPost['Status'],
                    'Status_Message' => htmlspecialchars($rowPost['Status_Message']),
                    'Is_Anonymouse' => $rowPost['Is_Anonymouse'],
                    'Visibility' => $rowPost['Visibility'],
                    'Created_at' => $rowPost['Created_at'],
                    'Image' => $rowPost['Image'],
                    'CategoryID' => $rowPost['CategoryID'],
                    'RegionID' => $rowPost['RegionID'],
                    'ComplaintID' => $rowPost['ComplaintID']
                );
            }
        }
        $stmtPosts->close();
    } else {
        error_log("Error preparing statement: " . $conn->error);
    }
} elseif ($view === 'shared_posts') {
    // Fetch shared posts
    // Assuming there's a 'shared_posts' table with fields: UID (user who shared), PID (post ID)
    $sqlSharedPosts = "SELECT p.PID, p.UID, p.Description, p.Longitude, p.Latitude, p.Status, p.Status_Message, p.Is_Anonymouse, p.Visibility, p.Created_at, p.Image, p.CategoryID, p.RegionID, p.ComplaintID 
                      FROM shared_post sp
                      INNER JOIN post p ON sp.PID = p.PID
                      WHERE sp.UID = ? AND p.Visibility = 'Public'";
    $stmtSharedPosts = $conn->prepare($sqlSharedPosts);
    if ($stmtSharedPosts) {
        $stmtSharedPosts->bind_param("i", $CurrentProfileUserID);
        $stmtSharedPosts->execute();
        $resultSharedPosts = $stmtSharedPosts->get_result();

        if ($resultSharedPosts && $resultSharedPosts->num_rows > 0) {
            while ($rowSharedPost = $resultSharedPosts->fetch_assoc()) {
                $posts[] = array(
                    'PID' => $rowSharedPost['PID'],
                    'UID' => $rowSharedPost['UID'],
                    'Description' => htmlspecialchars($rowSharedPost['Description']),
                    'Longitude' => $rowSharedPost['Longitude'],
                    'Latitude' => $rowSharedPost['Latitude'],
                    'Status' => $rowSharedPost['Status'],
                    'Status_Message' => htmlspecialchars($rowSharedPost['Status_Message']),
                    'Is_Anonymouse' => $rowSharedPost['Is_Anonymouse'],
                    'Visibility' => $rowSharedPost['Visibility'],
                    'Created_at' => $rowSharedPost['Created_at'],
                    'Image' => $rowSharedPost['Image'],
                    'CategoryID' => $rowSharedPost['CategoryID'],
                    'RegionID' => $rowSharedPost['RegionID'],
                    'ComplaintID' => $rowSharedPost['ComplaintID']
                );
            }
        }
        $stmtSharedPosts->close();
    } else {
        error_log("Error preparing statement: " . $conn->error);
    }
}

$linkCode
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <title>Profile Page</title>
</head>

<body>

    <!-- Main Content Area -->
    <div class="profile-container">
        <main class="profile-content">
            <header class="profile-header">
                <h1>Profile</h1>
                <?php if (!$editDetailsAccess && $IsCurrentUser): ?>
                    <form action="" method="get">
                        <input type="submit" class="edit-profile-btn" id="editProfileBtn" name="editProfileBtn" value="Edit Profile">
                    </form>
                <?php endif; ?>
            </header>

            <?php if ($editDetailsAccess): ?>
                <form action="" method="post">
                    <div class="profile-info">
                        <div class="form-group">
                            <label for="username">Name:</label>
                            <input type="text" id="username" name="username" value="<?php echo $Username; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <span id="email"><?php echo $Email; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact No.:</label>
                            <input type="text" id="ContactNumber" name="ContactNumber" value="<?php echo $ContactNumber; ?>" required>
                        </div>
                        <input type="submit" id="updateProfileBtn" name="updateProfileBtn" value="Update">
                    </div>
                </form>
            <?php else: ?>
                <div class="profile-info">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <span id="name"><?php echo $Username; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <span id="email"><?php echo $Email; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact No.:</label>
                        <span id="contact"><?php echo $ContactNumber; ?></span>
                    </div>
                </div>

                <!-- Toggle Buttons for Posts -->
                <?php
                // Determine if viewing another user's profile
                if (isset($_GET["otherUID"])) {
                    $linkCode = '?otherUID=' . urlencode($CurrentProfileUserID);
                } else {
                    $linkCode = ''; // No otherUID parameter for current user
                }

                // Retrieve the 'view' parameter from the URL, default to 'my_posts'
                $view = isset($_GET['view']) ? $_GET['view'] : 'my_posts';
                ?>
                <div class="posts-toggle">
                    <a href="<?php echo $linkCode ? $linkCode . '&view=my_posts' : '?view=my_posts'; ?>"
                        class="toggle-btn <?php echo ($view === 'my_posts') ? 'active' : ''; ?>">
                        My Posts
                    </a>
                    <a href="<?php echo $linkCode ? $linkCode . '&view=shared_posts' : '?view=shared_posts'; ?>"
                        class="toggle-btn <?php echo ($view === 'shared_posts') ? 'active' : ''; ?>">
                        Shared Posts
                    </a>
                </div>


                <!-- Posts Section -->
                <section class="posts-section">
                    <?php
                    if (empty($posts)) {
                        echo "<p>No posts found.</p>";
                    } else {
                        foreach ($posts as $post) {
                            // Initialize variables for each post
                            $userName = 'Anonymous';
                            $totalUpVotes = '0';
                            $totalDownVotes = '0';
                            $totalComments = '0';

                            // Fetch username if not anonymous
                            if (!$post['Is_Anonymouse']) {
                                $stmtUser = $conn->prepare("SELECT Username FROM useraccount WHERE UID = ?");
                                $stmtUser->bind_param("i", $post['UID']);
                                $stmtUser->execute();
                                $resultUser = $stmtUser->get_result();
                                if ($resultUser && $resultUser->num_rows > 0) {
                                    $rowUser = $resultUser->fetch_assoc();
                                    $userName = htmlspecialchars($rowUser['Username']);
                                }
                                $stmtUser->close();
                            }

                            // Fetch votes
                            $stmtVotes = $conn->prepare("SELECT 
                                SUM(CASE WHEN Vote_direction = 1 THEN 1 ELSE 0 END) AS totalUpVotes,
                                SUM(CASE WHEN Vote_direction = -1 THEN 1 ELSE 0 END) AS totalDownVotes
                                FROM vote WHERE PID = ?");
                            $stmtVotes->bind_param("i", $post['PID']);
                            $stmtVotes->execute();
                            $resultVotes = $stmtVotes->get_result();
                            if ($resultVotes && $resultVotes->num_rows > 0) {
                                $rowVotes = $resultVotes->fetch_assoc();
                                $totalUpVotes = htmlspecialchars($rowVotes['totalUpVotes'] ?? '0');
                                $totalDownVotes = htmlspecialchars($rowVotes['totalDownVotes'] ?? '0');
                            }
                            $stmtVotes->close();

                            // Fetch comments count
                            $stmtComments = $conn->prepare("SELECT COUNT(*) AS totalComments FROM comment WHERE PID = ?");
                            $stmtComments->bind_param("i", $post['PID']);
                            $stmtComments->execute();
                            $resultComments = $stmtComments->get_result();
                            if ($resultComments && $resultComments->num_rows > 0) {
                                $rowComments = $resultComments->fetch_assoc();
                                $totalComments = htmlspecialchars($rowComments['totalComments']);
                            }
                            $stmtComments->close();

                            // Assign variables for display
                            $postUsername = $userName;
                            $postDescription = htmlspecialchars($post['Description']);
                            $postImage = $post['Image'];
                            $PID = $post["PID"];
                            $UID = $_SESSION["UserData"][0];
                            $PostUID = $post["UID"];
                    ?>
                            <div class="post">
                                <?php
                                // Include the Post.php component
                                // Ensure that Post.php uses the variables defined above to display the post
                                include("Post.php");
                                ?>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </section>
            <?php endif; ?>

        </main>
    </div>
</body>

</html>