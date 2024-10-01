<?php
include('NavigationBar.php');
include("Database.php");

// Initialize an array to hold posts and their images
$posts = array();

// Fetch all posts from the 'post' table
$sqlPosts = "SELECT `PID`, `UID`, `Description`, `Longitude`, `Latitude`, `Status`, `Status_Message`, `Is_Anonymouse`, `Visibility`, `Created_at` FROM `post`";
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/Home.css">
    <title>Home Page</title>
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_B0Ud1mIL6Ln66nSCnITXRMDV1c3bssc"></script>
    <script>
        function initMap() {
            const position = { lat: 6.885497678560704, lng: 79.86034329536008 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: position,
            });

            new google.maps.Marker({
                position: position,
                map: map,
                title: "Post Location",
            });
        }

        // Initialize the map when the window loads
        window.onload = function() {
            initMap();
        }

        // Toggle map visibility on mobile
        function toggleMap() {
            const mapContainer = document.getElementById("map-container");
            const mapButton = document.getElementById("map-toggle-btn");
            if (mapContainer.style.display === "none" || mapContainer.style.display === "") {
                mapContainer.style.display = "block";
                mapButton.textContent = "Hide Map";
                initMap();
            } else {
                mapContainer.style.display = "none";
                mapButton.textContent = "Show Map";
            }
        }
    </script>

    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        /* Body */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4;
            color: #333;
            overflow-x: hidden;
        }

        /* Layout container for sidebar, map, and posts */
        .layout-container {
            display: flex;
            height: 100vh;
            justify-content: center;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #1d3557;
            padding: 20px;
            color: white;
            flex-shrink: 0;
        }

        /* Main content area for map and posts */
        .main-content-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            right: 10px;
        }

        /* Split layout for map and posts on desktop mode */
        .map-post-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            height: 100%;
            margin-top: 20px;
        }

        /* Map container */
        .map {
            flex: 1;
            margin-right: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #fff;
            height: 100%;
            overflow: hidden;
        }

        /* Posts container */
        .posts-container {
            flex: 1;
            margin-left: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #fff;
            overflow-y: auto;
        }

        /* Mobile view - center posts and map */
        @media (max-width: 768px) {
            .map-post-container {
                flex-direction: column;
                align-items: center;
            }

            .map,
            .posts-container {
                width: 100%;
                margin: 10px 0;
            }

            /* Map toggle button only visible on mobile */
            .map-toggle-btn {
                display: block;
                position: fixed;
                top: 70px;
                left: 10px;
                z-index: 1001;
                background-color: #1d3557;
                color: white;
                padding: 10px;
                border-radius: 5px;
                cursor: pointer;
            }
        }

        /* Hide map button on desktop */
        @media (min-width: 769px) {
            .map-toggle-btn {
                display: none;
            }
        }

        /* Post card styling */
        .post-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .post-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .post-content {
            margin-bottom: 20px;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
        }

        .footer-btn {
            display: flex;
            align-items: center;
        }

        .footer-btn img {
            width: 24px;
            height: 24px;
            margin-right: 5px;
        }

        /* Image carousel styling */
        .image-carousel {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-container {
            max-width: 100%;
        }

        .image-carousel img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .carousel-btn {
            background-color: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <!-- Fixed Map Toggle Button for Mobile -->
    <button class="map-toggle-btn" id="map-toggle-btn" onclick="toggleMap()">Show Map</button>

    <div class="layout-container">
        <!-- Main Content: Map and Posts -->
        <div class="main-content-container">
            <div class="map-post-container">
                <!-- Map Section -->
                <div class="map" id="map-container">
                    <div id="map" style="width: 100%; height: 100%;"></div>
                </div>

                <!-- Posts Section -->
                <div class="posts-container">
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

                        // Display the post card
                        echo '<div class="post-card">';
                        echo '    <div class="post-header">';
                        echo '        <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">';
                        echo '        <div class="user-info">';
                        echo '            <span class="user-name">' . $userName . '</span>';
                        echo '            <span class="post-options">•••</span>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '    <div class="post-content">';
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
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
