<?php
include("Database.php");

// Initialize an array to hold posts
$posts = array();

// Initialize filter variables
$categoryFilter = isset($_POST["CategoryList"]) ? $_POST["CategoryList"] : "";
$regionFilter = isset($_POST["RegionList"]) ? $_POST["RegionList"] : "";
$complaintFilter = isset($_POST["ComplaintList"]) ? $_POST["ComplaintList"] : "";
$startDate = isset($_POST["startDate"]) ? $_POST["startDate"] : "";
$endDate = isset($_POST["endDate"]) ? $_POST["endDate"] : "";
$searchQuery = isset($_POST["searchInput"]) ? $_POST["searchInput"] : "";

// Fetch categories from the database
$sqlCategory = "SELECT Category_ID AS CategoryID, Category_Name AS CategoryName FROM category ORDER BY Category_Name ASC";
$resultCategory = $conn->query($sqlCategory);

// Fetch regions based on CategoryID if selected
$regions = array();
if (!empty($categoryFilter)) {
    $stmtRegions = $conn->prepare("SELECT RegionID, Region FROM region WHERE CID = ? ORDER BY Region ASC");
    $stmtRegions->bind_param("i", $categoryFilter);
    $stmtRegions->execute();
    $resultRegions = $stmtRegions->get_result();
    if ($resultRegions && $resultRegions->num_rows > 0) {
        while ($rowRegion = $resultRegions->fetch_assoc()) {
            $regions[] = $rowRegion;
        }
    }
    $stmtRegions->close();
}

// Fetch complaint types based on CategoryID if selected
$complaints = array();
if (!empty($categoryFilter)) {
    $stmtComplaints = $conn->prepare("SELECT ComplaintID, Complaint FROM complainttype WHERE CID = ? ORDER BY Complaint ASC");
    $stmtComplaints->bind_param("i", $categoryFilter);
    $stmtComplaints->execute();
    $resultComplaints = $stmtComplaints->get_result();
    if ($resultComplaints && $resultComplaints->num_rows > 0) {
        while ($rowComplaint = $resultComplaints->fetch_assoc()) {
            $complaints[] = $rowComplaint;
        }
    }
    $stmtComplaints->close();
}

// Build the SQL query with filters
$sqlPosts = "SELECT p.PID, p.UID, p.Description, p.Longitude, p.Latitude, p.Status, p.Status_Message, p.Is_Anonymouse, p.Visibility, p.Created_at, p.Image, p.CategoryID, p.RegionID, p.ComplaintID, u.ProfilePicture, r.Region 
             FROM post p JOIN useraccount u ON p.UID = u.UID 
             JOIN region r ON p.RegionID = r.RegionID
             WHERE Visibility = 'Public'";

$params = array();
$types = "";

// Category Filter
if (!empty($categoryFilter)) {
    $sqlPosts .= " AND p.CategoryID = ?";
    $params[] = $categoryFilter;
    $types .= "i";
}

// Region Filter
if (!empty($regionFilter)) {
    $sqlPosts .= " AND p.RegionID = ?";
    $params[] = $regionFilter;
    $types .= "i";
}

// Complaint Type Filter
if (!empty($complaintFilter)) {
    $sqlPosts .= " AND p.ComplaintID = ?";
    $params[] = $complaintFilter;
    $types .= "i";
}

// Date Range Filter
if (!empty($startDate)) {
    $sqlPosts .= " AND DATE(p.Created_at) >= ?";
    $params[] = $startDate;
    $types .= "s";
}

if (!empty($endDate)) {
    $sqlPosts .= " AND DATE(p.Created_at) <= ?";
    $params[] = $endDate;
    $types .= "s";
}

// Search Query
if (!empty($searchQuery)) {
    $sqlPosts .= " AND p.Description LIKE ?";
    $params[] = '%' . $searchQuery . '%';
    $types .= "s";
}

$stmtPosts = $conn->prepare($sqlPosts);

if ($stmtPosts && !empty($types)) {
    $stmtPosts->bind_param($types, ...$params);
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
                'Region' => $rowPost['Region'],
                'ComplaintID' => $rowPost['ComplaintID'],
                'ProfilePicture' => $rowPost['ProfilePicture']
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
    <!-- Head content -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <link rel="stylesheet" href="../CSS/AllPost.css">
    <!-- Load Google Maps API (replace YOUR_API_KEY with your actual key) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_B0Ud1mIL6Ln66nSCnITXRMDV1c3bssc"></script>
    <!-- Include jQuery for AJAX calls -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        <!-- Search Input -->
                        <input type="text" placeholder="Search" class="search-bar" id="searchInput" name="searchInput" value="<?php echo isset($searchQuery) ? htmlspecialchars($searchQuery) : ''; ?>">

                        <!-- Category Dropdown -->
                        <select name="CategoryList" id="CategoryList" onchange="categoryChanged()">
                            <option value="">All Categories</option>
                            <?php
                            if ($resultCategory && $resultCategory->num_rows > 0) {
                                while ($rowCat = $resultCategory->fetch_assoc()) {
                                    $selected = ($rowCat["CategoryID"] == $categoryFilter) ? 'selected' : '';
                                    echo "<option value='" . $rowCat["CategoryID"] . "' $selected>" . htmlspecialchars($rowCat["CategoryName"]) . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <!-- Region Dropdown -->
                        <select name="RegionList" id="RegionList">
                            <option value="">All Regions</option>
                            <?php
                            if (!empty($categoryFilter) && !empty($regions)) {
                                foreach ($regions as $rowRegion) {
                                    $selected = ($rowRegion["RegionID"] == $regionFilter) ? 'selected' : '';
                                    echo "<option value='" . $rowRegion["RegionID"] . "' $selected>" . htmlspecialchars($rowRegion["Region"]) . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <!-- Complaint Type Dropdown -->
                        <select name="ComplaintList" id="ComplaintList">
                            <option value="">All Complaint Types</option>
                            <?php
                            if (!empty($categoryFilter) && !empty($complaints)) {
                                foreach ($complaints as $rowComplaint) {
                                    $selected = ($rowComplaint["ComplaintID"] == $complaintFilter) ? 'selected' : '';
                                    echo "<option value='" . $rowComplaint["ComplaintID"] . "' $selected>" . htmlspecialchars($rowComplaint["Complaint"]) . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <!-- Date Range Filters -->
                        <label for="startDate">From:</label>
                        <input type="date" id="startDate" name="startDate" value="<?php echo isset($startDate) ? htmlspecialchars($startDate) : ''; ?>">

                        <label for="endDate">To:</label>
                        <input type="date" id="endDate" name="endDate" value="<?php echo isset($endDate) ? htmlspecialchars($endDate) : ''; ?>">

                        <!-- Submit Button -->
                        <input type="submit" class="search-button" value="Search">
                    </form>
                </div>

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
                        $postProfileIcon = $post['ProfilePicture'];
                        $postStatus = $post['Status'];
                        $postStatusMsg = $post['Status_Message'];
                        $postRegion = $post["Region"];
                        $PID = $post["PID"];
                        $UID = $_SESSION["UserData"][0]; // Assuming user is logged in and UID is stored in session
                        $PostUID = $post["UID"];
                        $IsAnonymouse = $post["Is_Anonymouse"];
                ?>
                        <!-- Post and Map Layout -->
                        <div class="post-content">
                            <div class="post-section">
                                <!-- Display the post card -->
                                <?php
                                    include("Post.php");
                                ?>
                            </div>

                            <?php if (!empty($post['Image'])) { ?>
                                <!-- Map container -->
                                <div id="map_<?php echo $post['PID']; ?>" style="height: 400px; width: 100%;"></div>
                            <?php } ?>
                        </div>

                        <?php if (!empty($post['Image'])) { ?>
                            <script>
                                function initMap_<?php echo $post["PID"]; ?>() {
                                    const position = {
                                        lat: <?php echo $post['Latitude']; ?>,
                                        lng: <?php echo $post['Longitude']; ?>
                                    };
                                    const map = new google.maps.Map(document.getElementById('map_<?php echo $post['PID']; ?>'), {
                                        zoom: 15,
                                        center: position
                                    });
                                    const marker = new google.maps.Marker({
                                        position: position,
                                        map: map,
                                        title: "Post Location"
                                    });

                                    // Optional: Add InfoWindow if needed
                                    const infoWindow = new google.maps.InfoWindow({
                                        content: `<p><?php echo $postDescription; ?></p>`
                                    });

                                    marker.addListener('click', function() {
                                        infoWindow.open(map, marker);
                                    });
                                }
                            </script>
                        <?php } ?>

                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        function initAllMaps() {
            <?php
            foreach ($posts as $post) {
                if (!empty($post['Image'])) {
                    echo "initMap_" . $post['PID'] . "();";
                }
            }
            ?>
        }

        function categoryChanged() {
            const categoryID = document.getElementById('CategoryList').value;
            loadRegions(categoryID);
            loadComplaints(categoryID);
        }

        // Function to fetch regions based on selected category
        function loadRegions(categoryID) {
            if (categoryID) {
                $.ajax({
                    url: 'fetch_regions.php',
                    type: 'POST',
                    data: { categoryID: categoryID },
                    success: function(data) {
                        $('#RegionList').html(data);
                    },
                    error: function() {
                        console.error('Error fetching regions.');
                    }
                });
            } else {
                $('#RegionList').html('<option value="">All Regions</option>');
            }
        }

        // Function to fetch complaint types based on selected category
        function loadComplaints(categoryID) {
            if (categoryID) {
                $.ajax({
                    url: 'fetch_complaints.php',
                    type: 'POST',
                    data: { categoryID: categoryID },
                    success: function(data) {
                        $('#ComplaintList').html(data);
                    },
                    error: function() {
                        console.error('Error fetching complaint types.');
                    }
                });
            } else {
                $('#ComplaintList').html('<option value="">All Complaint Types</option>');
            }
        }

        $(document).ready(function() {
            // If a category is already selected, load regions and complaints
            const selectedCategoryID = $('#CategoryList').val();
            const selectedRegionID = '<?php echo isset($regionFilter) ? $regionFilter : ''; ?>';
            const selectedComplaintID = '<?php echo isset($complaintFilter) ? $complaintFilter : ''; ?>';

            if (selectedCategoryID) {
                $.ajax({
                    url: 'fetch_regions.php',
                    type: 'POST',
                    data: { 
                        categoryID: selectedCategoryID,
                        selectedRegionID: selectedRegionID 
                    },
                    success: function(data) {
                        $('#RegionList').html(data);
                    },
                    error: function() {
                        console.error('Error fetching regions.');
                    }
                });

                $.ajax({
                    url: 'fetch_complaints.php',
                    type: 'POST',
                    data: { 
                        categoryID: selectedCategoryID,
                        selectedComplaintID: selectedComplaintID 
                    },
                    success: function(data) {
                        $('#ComplaintList').html(data);
                    },
                    error: function() {
                        console.error('Error fetching complaint types.');
                    }
                });
            }
        });
    </script>

</body>

</html>
