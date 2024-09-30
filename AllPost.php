<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <link rel="stylesheet" href="css/AllPost.css">
</head>

<body>

    <!-- Include the navigation bar -->
    <?php include 'NavigationBar.php'; ?>

    <div class="home-page-container">
        <!-- Main Content -->
        <div class="main-content">
            <div class="post-container">
                <h1>All Posts</h1>

                <!-- Filters Section -->
                <div class="filters">
                    <select>
                        <option>Category</option>
                        <option>Category 1</option>
                        <option>Category 2</option>
                    </select>
                    <select>
                        <option>Location</option>
                        <option>Location 1</option>
                        <option>Location 2</option>
                    </select>
                    <button class="search-button">Search</button>
                </div>

                <!-- Post and Map Layout -->
                <div class="post-content">
                    <div class="post-section">
                        <div class="image-container">
                            Post Image
                        </div>
                    </div>
                    <div class="map-section">
                        <div class="map-container">
                            Map
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>
