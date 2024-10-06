<?php
include('NavigationBar.php');
include("Database.php");

// Initialize an array to hold posts
$posts = array();

// Fetch today's highest voted posts
$sqlPosts = "
SELECT p.`PID`, p.`UID`, p.`Description`, p.`Longitude`, p.`Latitude`, p.`Status`, 
       p.`Status_Message`, p.`Is_Anonymouse`, p.`Visibility`, p.`Created_at`, 
       p.`Image`, 
       COALESCE(v.upVotes, 0) as totalUpVotes,
       COALESCE(v.downVotes, 0) as totalDownVotes
FROM `post` p
LEFT JOIN (
    SELECT `PID`, 
           SUM(CASE WHEN `Vote_direction` = 1 THEN 1 ELSE 0 END) as upVotes,
           SUM(CASE WHEN `Vote_direction` = 0 THEN 1 ELSE 0 END) as downVotes
    FROM `vote` 
    WHERE DATE(`Created_at`) = CURDATE() 
    GROUP BY `PID`
) v ON p.`PID` = v.`PID`
WHERE p.Visibility = 'Public'
ORDER BY totalUpVotes DESC
LIMIT 10";

$resultPosts = $conn->query($sqlPosts);

// Check if there are any posts
if ($resultPosts->num_rows > 0) {
    while ($rowPost = $resultPosts->fetch_assoc()) {
        // Add the post data to the posts array
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
            'totalUpVotes' => $rowPost['totalUpVotes'],
            'totalDownVotes' => $rowPost['totalDownVotes']
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_B0Ud1mIL6Ln66nSCnITXRMDV1c3bssc"></script>
    <script>
        let postsData = <?php echo json_encode($posts); ?>; // Pass PHP array to JavaScript

        function initMap() {
            const position = { lat: 6.885497678560704, lng: 79.86034329536008 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: position,
            });

            // Loop through postsData and create markers
            postsData.forEach(post => {
                const markerPosition = { lat: parseFloat(post.Latitude), lng: parseFloat(post.Longitude) };
                const marker = new google.maps.Marker({
                    position: markerPosition,
                    map: map,
                    title: post.Description, // Title can be changed as needed
                });

                // Info Window for each marker
                const infoWindow = new google.maps.InfoWindow({
                    content: `<h4>${post.Description}</h4>
                              <p>Votes: ${post.totalUpVotes} Up / ${post.totalDownVotes} Down</p>`
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
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

        /* Main content area for map and posts */
        .main-content-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            margin-top: 50px;
            margin-bottom: 20px;
            margin-left: 300px;
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
            padding: 10px; /* Padding around the posts */
        }

        /* Mobile view - center posts and map */
        @media (max-width: 768px) {

            .main-content-container{
                margin-left: 0;
            }
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
                z-index: 100;
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
                    <h1>Today's Top 10 Highest Voted Posts</h1>
                    <?php
                    foreach ($posts as $post) {
                        $userName = 'Anonymous';
                        $totalUpVotes = '0';
                        $totalDownVotes = '0';
                        $totalComments = '0';

                        if (!$post['Is_Anonymouse']) {
                            // Fetch username
                            $stmtUser = $conn->prepare("SELECT `Username` FROM `useraccount` WHERE `UID` = ?");
                            $stmtUser->bind_param("i", $post['UID']);
                            $stmtUser->execute();
                            $resultUser = $stmtUser->get_result();
                            if ($resultUser->num_rows > 0) {
                                $rowUser = $resultUser->fetch_assoc();
                                $userName = htmlspecialchars($rowUser['Username']);
                            }
                            $stmtUser->close();

                            // Fetch votes
                            $stmtVotes = $conn->prepare("SELECT 
                                SUM(CASE WHEN Vote_direction = 1 THEN 1 ELSE 0 END) AS totalUpVotes,
                                SUM(CASE WHEN Vote_direction = 0 THEN 1 ELSE 0 END) AS totalDownVotes
                                FROM vote WHERE PID = ?");
                            $stmtVotes->bind_param("i", $post['PID']);
                            $stmtVotes->execute();
                            $resultVotes = $stmtVotes->get_result();
                            if ($resultVotes->num_rows > 0) {
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
                            if ($resultComments->num_rows > 0) {
                                $rowComments = $resultComments->fetch_assoc();
                                $totalComments = htmlspecialchars($rowComments['totalComments']);
                            }
                            $stmtComments->close();
                        }

                        $postUsername = $userName;
                        $postDescription = $post["Description"];
                        $postImage = $post["Image"];
                        $PID = $post["PID"];
                        $UID = $post["UID"];
                        $PostUID = $post["UID"];
                        include("Post.php");
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
