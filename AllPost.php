<?php
include("Database.php");

// Initialize an array to hold posts and their images
$posts = array();

$sqlPosts = "SELECT `PID`, `UID`, `Description`, `Longitude`, `Latitude`, `Status`, `Status_Message`, `Is_Anonymouse`, `Visibility`, `Created_at` FROM `post` WHERE Visibility = 'Public'";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["CategoryList"]) && isset($_POST["LocationList"])) {
        if ($_POST["CategoryList"] != "") {
            $sqlPosts .= " AND CategoryID = " . $_POST["CategoryList"];
        }
        if ($_POST["LocationList"] != "") {
            $sqlPosts .= " AND AreaID = " . $_POST["LocationList"];
        }
    }
}
$resultPosts = $conn->query($sqlPosts);

// Check if there are any posts
if ($resultPosts->num_rows > 0) {
    while ($rowPost = $resultPosts->fetch_assoc()) {
        $PID = $rowPost['PID'];

        // Fetch images associated with this post
        $stmtImages = $conn->prepare("SELECT `Image` FROM `post_image` WHERE `PID` = ?");
        $stmtImages->bind_param("i", $PID);
        $stmtImages->execute();
        $resultImages = $stmtImages->get_result();

        $images = array();
        while ($rowImage = $resultImages->fetch_assoc()) {
            $images[] = $rowImage['Image'];
        }
        $stmtImages->close();

        // Add the post data and images to the posts array
        $posts[] = array(
            'PID' => $PID,
            'UID' => $rowPost['UID'],
            'Description' => $rowPost['Description'],
            'Longitude' => $rowPost['Longitude'],
            'Latitude' => $rowPost['Latitude'],
            'Status' => $rowPost['Status'],
            'Status_Message' => $rowPost['Status_Message'],
            'Is_Anonymouse' => $rowPost['Is_Anonymouse'],
            'Visibility' => $rowPost['Visibility'],
            'Created_at' => $rowPost['Created_at'],
            'Images' => $images
        );
    }
} else {
    echo "No posts found.";
}

$sqlCategory = "SELECT Category_ID, Category_Name AS CategoryName FROM category";
$resultCategory = $conn->query($sqlCategory);

$sqLocation = "SELECT Area_ID AS AreaID, City AS Location from area ORDER BY City ASC";
$resultLocation = $conn->query($sqLocation);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <link rel="stylesheet" href="css/AllPost.css">
    <!-- Load Google Maps API (replace YOUR_API_KEY with your actual key) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_B0Ud1mIL6Ln66nSCnITXRMDV1c3bssc"></script>
</head>

<body onload="initAllMaps()">

    <!-- Include the navigation bar -->
    <?php include 'NavigationBar.php'; ?>

    <div class="home-page-container">
        <!-- Main Content -->
        <div class="main-content">
            <div class="post-container">
                <h1>All Posts</h1>

                <!-- Filters Section -->
                <div class="filters">
                    <form action="" method="post">
                        <select name="CategoryList">
                            <option value="">All Category</option>
                            <?php
                            if ($resultCategory->num_rows > 0) {
                                while ($rowCat = $resultCategory->fetch_assoc()) {
                                    echo "<option value='" . $rowCat["Category_ID"] . "' " . (($rowCat["Category_ID"] == $_POST["CategoryList"]) ? 'selected' : '') . ">" . $rowCat["CategoryName"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <select name="LocationList">
                            <option value="">All Location</option>
                            <?php
                            if ($resultLocation->num_rows > 0) {
                                while ($rowLoc = $resultLocation->fetch_assoc()) {
                                    echo "<option value='" . $rowLoc["AreaID"] . "' " . (($rowLoc["AreaID"] == $_POST["LocationList"]) ? 'selected' : '') . ">" . $rowLoc["Location"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <input type="submit" class="search-button" value="Search">
                    </form>
                </div>

                <?php
                foreach ($posts as $post) {
                    $userName = 'Anonymous';
                    $totalUpVotes = '0';
                    $totalDownVotes = '0';
                    $totalComments = '0';

                    if (!$post['Is_Anonymouse']) {
                        // Fetch username and votes/comments
                        $stmtUser = $conn->prepare("SELECT `Username` FROM `useraccount` WHERE `UID` = ?");
                        $stmtUser->bind_param("i", $post['UID']);
                        $stmtUser->execute();
                        $resultUser = $stmtUser->get_result();
                        if ($resultUser->num_rows > 0) {
                            $rowUser = $resultUser->fetch_assoc();
                            $userName = htmlspecialchars($rowUser['Username']);
                        }
                        $stmtUser->close();

                        // Fetch votes and comments
                        $stmtUpVotes = $conn->prepare("SELECT COUNT(*) AS totalVotes FROM vote WHERE PID = ? AND Vote_direction = 1;");
                        $stmtUpVotes->bind_param("i", $post['PID']);
                        $stmtUpVotes->execute();
                        $resultUpVotes = $stmtUpVotes->get_result();
                        if ($resultUpVotes->num_rows > 0) {
                            $rowvote = $resultUpVotes->fetch_assoc();
                            $totalUpVotes = htmlspecialchars($rowvote['totalVotes']);
                        }
                        $stmtUpVotes->close();

                        $stmtDownVotes = $conn->prepare("SELECT COUNT(*) AS totalVotes FROM vote WHERE PID = ? AND Vote_direction = 0;");
                        $stmtDownVotes->bind_param("i", $post['PID']);
                        $stmtDownVotes->execute();
                        $resultDownVotes = $stmtDownVotes->get_result();
                        if ($resultDownVotes->num_rows > 0) {
                            $rowvote = $resultDownVotes->fetch_assoc();
                            $totalDownVotes = htmlspecialchars($rowvote['totalVotes']);
                        }
                        $stmtDownVotes->close();

                        $stmtComments = $conn->prepare("SELECT COUNT(*) AS totalComments FROM comment WHERE PID = ?;");
                        $stmtComments->bind_param("i", $post['PID']);
                        $stmtComments->execute();
                        $resultComments = $stmtComments->get_result();
                        if ($resultComments->num_rows > 0) {
                            $rowcom = $resultComments->fetch_assoc();
                            $totalComments = htmlspecialchars($rowcom['totalComments']);
                        }
                        $stmtComments->close();
                    }
                ?>
                    <!-- Post and Map Layout -->
                    <div class="post-content">

                        <div class="post-section">
                            <?php
                            // Display the post card
                            echo '<div class="post-card">';
                            echo '    <div class="post-header">';
                            echo '        <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">';
                            echo '        <div class="user-info">';
                            echo '            <span class="user-name">' . $userName . '</span>';
                            echo '            <span class="post-options">•••</span>';
                            echo '        </div>';
                            echo '    </div>';
                            echo '    <div class="post-incontent">';
                            echo '        <p class="post-text">' . htmlspecialchars($post['Description']) . '</p>';

                            // Display images if any
                            if (!empty($post['Images'])) {
                                echo '<div class="image-carousel">';
                                echo '    <button class="carousel-btn left-btn">◀</button>';
                                echo '    <div class="image-container">';
                                // For simplicity, display the first image
                                echo '        <img src="data:image/jpeg;base64,' . base64_encode($post['Images'][0]) . '" alt="Post Image">';
                                echo '    </div>';
                                echo '    <button class="carousel-btn right-btn">▶</button>';
                                echo '</div>';
                            }

                            echo '    </div>';
                            echo '    <div class="post-footer">';
                            echo '        <button class="footer-btn">';
                            echo '            <img src="pics/like.jpg" alt="Like" class="btn-icon">';
                            echo '            <span class="btn-text">' . $totalUpVotes . '</span>';
                            echo '        </button>';
                            echo '        <button class="footer-btn">';
                            echo '            <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">';
                            echo '            <span class="btn-text">' . $totalDownVotes . '</span>';
                            echo '        </button>';
                            echo '        <button class="footer-btn">';
                            echo '            <img src="pics/comment.jpg" alt="Comment" class="btn-icon">';
                            echo '            <span class="btn-text">' . $totalComments . '</span>';
                            echo '        </button>';
                            echo '        <button class="footer-btn">';
                            echo '            <img src="pics/share.jpg" alt="Share" class="btn-icon">';
                            echo '            <span class="btn-text">86</span>';
                            echo '        </button>';
                            echo '    </div>';
                            echo '</div>';

                            $Longitude = $post["Longitude"];
                            $Latitude = $post["Latitude"];
                            $mapId = 'map_' . $post["PID"];
                            ?>
                        </div>

                        <!-- Map container -->
                        <div id="<?php echo $mapId; ?>" style="height: 400px; width: 600px;"></div>

                    </div>

                    <script>
                        function initMap_<?php echo $post["PID"]; ?>() {
                            const position = { lat: <?php echo $Latitude; ?>, lng: <?php echo $Longitude; ?> };
                            const map = new google.maps.Map(document.getElementById('<?php echo $mapId; ?>'), {
                                zoom: 10,
                                center: position
                            });
                            const marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: "Post Location"
                            });
                        }
                    </script>

                <?php
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        function initAllMaps() {
            <?php
            foreach ($posts as $post) {
                echo "initMap_" . $post['PID'] . "();";
            }
            ?>
        }
    </script>

</body>

</html>
