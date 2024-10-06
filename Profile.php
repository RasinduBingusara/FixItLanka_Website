<?php
include('NavigationBar.php');
include('Database.php');
$Username = "";
$Email = "";
$ContactNumber = "";
$DOB = "-";
$otherUID;
$editDetailsAccess = false;
$IsCurrentUser = true;
$CurrentProfileUserID = "";

if (isset($_GET["otherUID"])) {
    $otherUID = $_GET["otherUID"];
    $IsCurrentUser = false;
    $CurrentProfileUserID = $otherUID;

    $result = $conn->query("SELECT u.Username, u.Email, u.ContactNumber, u.DOB, u.ProfilePicture FROM useraccount WHERE UID = ".$otherUID);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $Username = $row["Username"];
            $Email = $row["Email"];
            $ContactNumber = $row["ContactNumber"];
            $DOB = $row["DOB"];
        }
    }
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

                        echo "Profile updated successfully.";
                    } else {
                        error_log("Error executing query: " . $stmt->error);
                        echo "An error occurred while updating your profile.";
                    }

                    $stmt->close();
                } else {
                    error_log("Error preparing statement: " . $conn->error);
                    echo "An error occurred while updating your profile.";
                }
            } else {
                echo "Please fill in all required fields.";
            }
        }
    }

    if (isset($_SESSION["UserData"])) {
        $Username = $_SESSION["UserData"][2];
        $Email = $_SESSION["UserData"][3];
        $ContactNumber = $_SESSION["UserData"][4];
        $DOB = $_SESSION["UserData"][5];
    }
}

$sqlPosts = "SELECT `PID`, `UID`, `Description`, `Longitude`, `Latitude`, `Status`, `Status_Message`, `Is_Anonymouse`, `Visibility`, `Created_at`, `Image`, `CategoryID`, `RegionID`, `ComplaintID` 
             FROM `post` 
             WHERE Visibility = 'Public' AND UID = ".$CurrentProfileUserID;

$params = array();

$stmtPosts = $conn->prepare($sqlPosts);

if ($stmtPosts && !empty($types)) {
    $stmtPosts->bind_param("s", ...$params);
}

if ($stmtPosts) {
    $stmtPosts->execute();
    $resultPosts = $stmtPosts->get_result();
    
    if ($resultPosts && $resultPosts->num_rows > 0) {
        while ($rowPost = $resultPosts->fetch_assoc()) {
            $posts[] = array(
                'PID' => $rowPost['PID'],
                'UID' => $rowPost['UID'],
                'Description' => $rowPost['Description'],
                'Longitude' => $rowPost['Longitude'],
                'Latitude' => $rowPost['Latitude'],
                'Status' => $rowPost['Status'],
                'Status_Message' => $rowPost['Status_Message'],
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\profile.css">
    <title>Profile Page</title>
</head>


<body>

    <!-- Main Content Area -->
    <div class="profile-container">
        <main class="profile-content">
            <header class="profile-header">
                <h1>Profile</h1>
                <?php
                if (!$editDetailsAccess && $IsCurrentUser) {
                ?>
                    <form action="" method="get">
                        <input type="submit" class="edit-profile-btn" id="editProfileBtn" name="editProfileBtn" value="Edit Profile"></input>
                    </form>
                <?php
                }
                ?>
            </header>
            <?php
            if ($editDetailsAccess) {
            ?>
                <form action="" method="post">
                    <div class="profile-info">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="username" name="username" value="<?php echo $Username ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <span id="email"><?php echo $Email ?></span> <!-- Replace with dynamic content -->
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth:</label>
                            <span id="dob"><?php echo $DOB ?></span> <!-- Replace with dynamic content -->
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact No.:</label>
                            <input type="text" id="ContactNumber" name="ContactNumber" value="<?php echo $ContactNumber ?>">
                        </div>
                        <input type="submit" id="updateProfileBtn" name="updateProfileBtn" value="Update">
                    </div>
                </form>
            <?php
            } else {

            ?>
                <div class="profile-info">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <span id="name"><?php echo $Username ?></span> <!-- Replace with dynamic content -->
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <span id="email"><?php echo $Email ?></span> <!-- Replace with dynamic content -->
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <span id="dob"><?php echo $DOB ?></span> <!-- Replace with dynamic content -->
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact No.:</label>
                        <span id="contact"><?php echo $ContactNumber ?></span> <!-- Replace with dynamic content -->
                    </div>
                </div>

                <section class="shared-posts">
                    <!-- <h2>Shared Posts</h2> -->
                    <?php
                if (empty($posts)) {
                    echo "<p>No posts found.</p>";
                } else {
                    foreach ($posts as $post) {
                        $userName = 'Anonymous';
                        $totalUpVotes = '0';
                        $totalDownVotes = '0';
                        $totalComments = '0';

                        // Fetch username if not anonymous
                        if (!$post['Is_Anonymouse']) {
                            $stmtUser = $conn->prepare("SELECT `Username` FROM `useraccount` WHERE `UID` = ?");
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

                        $postUsername = $userName;
                        $postDescription = htmlspecialchars($post['Description']);
                        $postImage = $post['Image'];
                        $PID = $post["PID"];
                        $UID = $_SESSION["UserData"][0]; // Assuming user is logged in and UID is stored in session
                        $PostUID = $post["UID"];
                ?>
                    <div class="post">
                        <?php
                        //add all required php codes and post should be display according to $CurrentProfileUserID;
                            include("Post.php");
                        ?>
                    </div>
                    <?php
                    }}
                    ?>
                </section>
            <?php
            }
            ?>

        </main>
    </div>
</body>

</html>




