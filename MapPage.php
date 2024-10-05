<?php
include("Database.php");

// Fetch categories from the database
$categoryResult = $conn->query("SELECT Category_ID AS CategoryID, Category_Name AS CategoryName FROM category ORDER BY Category_Name ASC");

// Initialize variables
$locations = array();
$categoryID = isset($_POST["categoryDropdown"]) ? $_POST["categoryDropdown"] : '';
$regionID = isset($_POST["regionDropdown"]) ? $_POST["regionDropdown"] : '';
$complaintID = isset($_POST["complaintDropdown"]) ? $_POST["complaintDropdown"] : '';
$searchQuery = isset($_POST["searchInput"]) ? $_POST["searchInput"] : '';
$startDate = isset($_POST["startDate"]) ? $_POST["startDate"] : '';
$endDate = isset($_POST["endDate"]) ? $_POST["endDate"] : '';

// Fetch locations based on filters
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Build the SQL query with filters
    $sql = "SELECT post.Longitude, post.Latitude, post.Description
            FROM post
            INNER JOIN category ON post.CategoryID = category.Category_ID
            INNER JOIN region ON post.RegionID = region.RegionID
            INNER JOIN complainttype ON post.ComplaintID = complainttype.ComplaintID
            WHERE 1=1";
    
    $params = array();
    $types = "";

    if (!empty($categoryID)) {
        $sql .= " AND category.Category_ID = ?";
        $params[] = $categoryID;
        $types .= "i";
    }

    if (!empty($regionID)) {
        $sql .= " AND region.RegionID = ?";
        $params[] = $regionID;
        $types .= "i";
    }

    if (!empty($complaintID)) {
        $sql .= " AND complainttype.ComplaintID = ?";
        $params[] = $complaintID;
        $types .= "i";
    }

    if (!empty($searchQuery)) {
        $sql .= " AND post.Description LIKE ?";
        $params[] = '%' . $searchQuery . '%';
        $types .= "s";
    }

    if (!empty($startDate)) {
        $sql .= " AND post.Created_at >= ?";
        $params[] = $startDate;
        $types .= "s";
    }

    if (!empty($endDate)) {
        $sql .= " AND post.Created_at <= ?";
        $params[] = $endDate;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);

    if ($stmt && $types) {
        $stmt->bind_param($types, ...$params);
    }

    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($longitude, $latitude, $description);

        while ($stmt->fetch()) {
            $locations[] = array(
                'latitude'    => $latitude,
                'longitude'   => $longitude,
                'description' => $description
            );
        }
        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $conn->error);
    }
} else {
    // Fetch all locations if no filters are applied
    $stmt = $conn->prepare("SELECT Longitude, Latitude, Description FROM post");
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($longitude, $latitude, $description);

        while ($stmt->fetch()) {
            $locations[] = array(
                'latitude'    => $latitude,
                'longitude'   => $longitude,
                'description' => $description
            );
        }
        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $conn->error);
    }
}

// Encode the locations array as JSON for use in JavaScript
$locations_json = json_encode($locations);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Map Page</title>
  <link rel="stylesheet" href="css/MapPage.css">
  <!-- Include jQuery for AJAX calls -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Replace 'YOUR_API_KEY' with your actual Google Maps API key -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_B0Ud1mIL6Ln66nSCnITXRMDV1c3bssc"></script>
</head>

<body>

  <!-- Include the navigation bar -->
  <?php include 'NavigationBar.php'; ?>

  <div class="map-page-container">
    <!-- Main Content Area -->
    <main class="main-content">
      <div class="content">
        <!-- Search Bar and Dropdown Section -->
        <div class="search-bar-container">
          <form id="filterForm" action="" method="post">
            <!-- Search Input -->
            <input type="text" placeholder="Search" class="search-bar" id="searchInput" name="searchInput" value="<?php echo isset($_POST['searchInput']) ? htmlspecialchars($_POST['searchInput']) : ''; ?>">
            <button type="submit" class="search-button" id="searchButton">&#128269;</button>

            <!-- Category Dropdown -->
            <select class="dropdown" id="categoryDropdown" name="categoryDropdown">
              <option value="">Select Category</option>
              <?php
              if ($categoryResult->num_rows > 0) {
                  while ($row = $categoryResult->fetch_assoc()) {
                      $categoryName = htmlspecialchars($row['CategoryName'], ENT_QUOTES);
                      $categoryId = $row['CategoryID'];
                      echo '<option value="' . $categoryId . '"' . ((isset($_POST['categoryDropdown']) && $_POST['categoryDropdown'] == $categoryId) ? ' selected' : '') . '>' . $categoryName . '</option>';
                  }
              }
              ?>
            </select>

            <!-- Region Dropdown -->
            <select class="dropdown" id="regionDropdown" name="regionDropdown">
              <option value="">Select Region</option>
              <!-- Regions will be populated based on selected category -->
            </select>

            <!-- Complaint Type Dropdown -->
            <select class="dropdown" id="complaintDropdown" name="complaintDropdown">
              <option value="">Select Complaint Type</option>
              <!-- Complaint types will be populated based on selected category -->
            </select>

            <!-- Date Range Filters -->
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate" value="<?php echo isset($_POST['startDate']) ? htmlspecialchars($_POST['startDate']) : ''; ?>">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate" value="<?php echo isset($_POST['endDate']) ? htmlspecialchars($_POST['endDate']) : ''; ?>">

          </form>
        </div>

        <!-- Map Section -->
        <div class="map-container">
          <div id="map" style="height: 500px; width: 100%;"></div>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Parse the locations JSON data
    const locations = <?php echo $locations_json; ?>;

    function initMap() {
      // Default location if no data is available
      const defaultLocation = {
        lat: 7.8731,
        lng: 80.7718
      }; // Example: Center of Sri Lanka

      // Set map center
      const mapCenter = locations.length > 0 ? {
        lat: parseFloat(locations[0].latitude),
        lng: parseFloat(locations[0].longitude)
      } : defaultLocation;

      // Create the map
      const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 8,
        center: mapCenter,
      });

      // Add markers to the map
      locations.forEach((location) => {
        const marker = new google.maps.Marker({
          position: {
            lat: parseFloat(location.latitude),
            lng: parseFloat(location.longitude)
          },
          map: map,
          title: location.description,
        });

        // Add info window to each marker
        const infoWindow = new google.maps.InfoWindow({
          content: `<p>${location.description}</p>`
        });

        marker.addListener('click', function() {
          infoWindow.open(map, marker);
        });
      });
    }

    // Function to fetch regions based on selected category
    function loadRegions(categoryID, selectedRegionID = '') {
      if (categoryID) {
        $.ajax({
          url: 'fetch_regions.php',
          type: 'POST',
          data: { categoryID: categoryID, selectedRegionID: selectedRegionID },
          success: function(data) {
            $('#regionDropdown').html(data);
          },
          error: function() {
            console.error('Error fetching regions.');
          }
        });
      } else {
        $('#regionDropdown').html('<option value="">Select Region</option>');
      }
    }

    // Function to fetch complaint types based on selected category
    function loadComplaints(categoryID, selectedComplaintID = '') {
      if (categoryID) {
        $.ajax({
          url: 'fetch_complaints.php',
          type: 'POST',
          data: { categoryID: categoryID, selectedComplaintID: selectedComplaintID },
          success: function(data) {
            $('#complaintDropdown').html(data);
          },
          error: function() {
            console.error('Error fetching complaint types.');
          }
        });
      } else {
        $('#complaintDropdown').html('<option value="">Select Complaint Type</option>');
      }
    }

    // Event listener for category dropdown change
    $('#categoryDropdown').on('change', function() {
      const categoryID = $(this).val();
      loadRegions(categoryID);
      loadComplaints(categoryID);
      // Reset region and complaint dropdowns and submit form
      $('#filterForm').submit();
    });

    // Event listener for region dropdown change
    $('#regionDropdown').on('change', function() {
      $('#filterForm').submit();
    });

    // Event listener for complaint dropdown change
    $('#complaintDropdown').on('change', function() {
      $('#filterForm').submit();
    });

    // Event listener for date inputs change
    $('#startDate, #endDate').on('change', function() {
      $('#filterForm').submit();
    });

    // Initialize the map when the window loads
    window.onload = function() {
      initMap();

      // Load regions and complaints if a category is already selected (e.g., after form submission)
      const selectedCategoryID = $('#categoryDropdown').val();
      const selectedRegionID = '<?php echo isset($regionID) ? $regionID : ''; ?>';
      const selectedComplaintID = '<?php echo isset($complaintID) ? $complaintID : ''; ?>';

      if (selectedCategoryID) {
        loadRegions(selectedCategoryID, selectedRegionID);
        loadComplaints(selectedCategoryID, selectedComplaintID);
      }
    };
  </script>

</body>

</html>
