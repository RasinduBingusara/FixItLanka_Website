<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Map Page</title>
  <link rel="stylesheet" href="css/MapPage.css">
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
          <input type="text" placeholder="Search" class="search-bar">
          <button class="search-button">&#128269;</button>
          <select class="dropdown">
            <option>Sort</option>
            <option>Option 1</option>
            <option>Option 2</option>
          </select>
          <select class="dropdown">
            <option>Area</option>
            <option>Option 1</option>
            <option>Option 2</option>
          </select>
        </div>

        <!-- Map Section -->
        <div class="map-container">
          <div class="map" id="map">Map will load here...</div>
        </div>
      </div>
    </main>
  </div>

  <script>

    // JavaScript to toggle map visibility on mobile
    document.getElementById('mapToggle').addEventListener('click', function () {
      document.getElementById('map').classList.toggle('visible');
    });
  </script>

</body>

</html>
