<?php
include("Database.php");

// Initialize an array to hold posts and their images
$posts = array();

// Fetch all posts from the 'post' table
$sqlPosts = "SELECT `PID`, `UID`, `Description`, `Category`, `Longitude`, `Latitude`, `Status`, `Status_Message`, `Is_Anonymouse`, `Visibility`, `Created_at` FROM `post`";
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
            // Assuming images are stored as file paths or URLs
            $images[] = $rowImage['Image'];
        }
        $stmtImages->close();

        // Add the post data and images to the posts array
        $posts[] = array(
            'PID' => $PID,
            'UID' => $rowPost['UID'],
            'Description' => $rowPost['Description'],
            'Category' => $rowPost['Category'],
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

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/home.css">
  <title>Home Page</title>
</head>
<script src="script/MapAPI.js"></script>
<script>
    async function initMap() {
    // The location of Uluru
    const position = {
      lat: 6.885497678560704,
      lng: 79.86034329536008
    };
    // Request needed libraries.
    //@ts-ignore
    const {
      Map
    } = await google.maps.importLibrary("maps");
    const {
      AdvancedMarkerElement
    } = await google.maps.importLibrary("marker");

    // The map, centered at Uluru
    map = new Map(document.getElementById("map"), {
      zoom: 10,
      center: position,
      mapId: "DEMO_MAP_ID",
    });

    new AdvancedMarkerElement({
      map: map,
      position: position,
      title: "Post Location",
    });
  }

  initMap();
  </script>

<body>

  <!-- Fixed Navbar with Hamburger Icon and Map Toggle -->
  <nav class="navbar">
    <div class="logo">FIXITLANKA</div>
    <div class="navbar-buttons">
      <button class="hamburger">&#9776;</button>
      <button class="map-toggle-btn" id="mapToggle">🗺️ Map</button>
    </div>
    <div class="search-container">
      <input type="text" placeholder="Search" class="search-bar">
      <button class="search-button">🔍</button>
    </div>
    <div class="profile">
      <img src="pics/defaultProfile.png" alt="Profile Icon" class="profile-icon">
      <span class="profile-name">Username</span>
    </div>
  </nav>

  <div class="home-page-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
      <nav class="nav-menu">
        <div class="top-nav">
          <button class="nav-button">
            <span class="icon">📌</span> Map
          </button>
          <button class="nav-button" onclick="window.location.href='createpost.php';">
            <span class="icon">📝</span> Post
          </button>
          <button class="nav-button">
            <span class="icon">🖥️</span> Post View
          </button>
        </div>
        <div class="bottom-nav">
          <button class="nav-button">
            <span class="icon">📜</span> Terms and Policies
          </button>
          <button class="nav-button">
            <span class="icon">⚙️</span> Settings
          </button>
          <button class="nav-button">
            <span class="icon">🚪</span> Sign Out
          </button>
        </div>
      </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <div class="content">
        <!-- Map Section -->
        <div class="map" id="map"></div>
        <!-- Posts Container -->
        <div class="posts-container">
          
        <?php
                    // Loop through the posts and display them
                    foreach ($posts as $post) {
                        // Determine the user's name
                        // For anonymous posts, display 'Anonymous'
                        $userName = 'Anonymous';
                        if (!$post['Is_Anonymouse']) {
                            // Fetch the user's name from the 'user' table
                            // Assuming you have a 'user' table with 'UID' and 'User_Name' columns
                            include("Database.php"); // Reopen the connection
                            $stmtUser = $conn->prepare("SELECT `Username` FROM `useraccount` WHERE `UID` = ?");
                            $stmtUser->bind_param("i", $post['UID']);
                            $stmtUser->execute();
                            $resultUser = $stmtUser->get_result();
                            if ($resultUser->num_rows > 0) {
                                $rowUser = $resultUser->fetch_assoc();
                                $userName = htmlspecialchars($rowUser['Username']);
                            }
                            $stmtUser->close();
                            $conn->close(); // Close the connection
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
                        echo '        <p class="post-text">';
                        echo '            ' . htmlspecialchars($post['Description']);
                        echo '        </p>';

                        // Display images if any
                        if (!empty($post['Images'])) {
                            echo '<div class="image-carousel">';
                            echo '    <button class="carousel-btn left-btn">◀</button>';
                            echo '    <div class="image-container">';
                            // For simplicity, display the first image
                            echo '        <img src="data:image/jpeg;base64,' . base64_encode ($post['Images'][0]) . '" alt="Post Image">';
                            echo '    </div>';
                            echo '    <button class="carousel-btn right-btn">▶</button>';
                            echo '</div>';
                        }

                        echo '    </div>';
                        echo '    <div class="post-footer">';
                        echo '        <button class="footer-btn">';
                        echo '            <img src="pics/like.jpg" alt="Like" class="btn-icon">';
                        echo '            <span class="btn-text">1.5k</span>';
                        echo '        </button>';
                        echo '        <button class="footer-btn">';
                        echo '            <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">';
                        echo '            <span class="btn-text">520</span>';
                        echo '        </button>';
                        echo '        <button class="footer-btn">';
                        echo '            <img src="pics/comment.jpg" alt="Comment" class="btn-icon">';
                        echo '            <span class="btn-text">126</span>';
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
    </main>
  </div>

  <script>
    // Toggle sidebar visibility on mobile
    document.querySelector('.hamburger').addEventListener('click', function () {
      document.getElementById('sidebar').classList.toggle('active');
    });

    // Toggle map visibility on mobile
    document.getElementById('mapToggle').addEventListener('click', function () {
      document.getElementById('map').classList.toggle('visible');
    });
  </script>

</body>

</html>
