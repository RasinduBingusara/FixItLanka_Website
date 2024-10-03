<?php
include('NavigationBar.php');
include("Database.php");

// Initialize variables
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $area = $_POST['area'];
    $category = $_POST['category'];
    $visibility = $_POST['visibility'];
    $isAnonymous = isset($_POST['anonymous']) ? 1 : 0;
    $description = $_POST['description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $uid = $_SESSION["UserData"][0];

    // Check if required fields are filled
    if (empty($area) || empty($category) || empty($visibility) || empty($description) || empty($latitude) || empty($longitude)) {
        $error = "All fields are required!";
    } elseif (empty($_FILES['post_images']['tmp_name'][0])) {
        // Check if at least one image is uploaded
        $error = "You must attach at least one photo to create a post!";
    } else {
        // Insert the post with GPS data into the database
        $stmt = $conn->prepare("INSERT INTO post (UID, AreaID, CategoryID, Description, Visibility, Is_Anonymouse, latitude, Longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssidd", $uid, $area, $category, $description, $visibility, $isAnonymous, $latitude, $longitude);
        if ($stmt->execute()) {
            $postId = $stmt->insert_id; // Get the last inserted post ID
            $stmt->close();

            // Handle multiple image uploads
            foreach ($_FILES['post_images']['tmp_name'] as $index => $tmpName) {
                $imageData = file_get_contents($tmpName); // Read the image file as binary data
                $imageFileType = strtolower(pathinfo($_FILES['post_images']['name'][$index], PATHINFO_EXTENSION));

                // Allow certain file formats
                $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'heic');
                if (in_array($imageFileType, $allowedTypes)) {
                    // Insert binary data into the database
                    $stmtImage = $conn->prepare("INSERT INTO post_image (PID, Image) VALUES (?, ?)");
                    $stmtImage->bind_param("ib", $postId, $null); // Bind NULL first as we are sending data separately

                    $stmtImage->send_long_data(1, $imageData); // Send the BLOB data
                    $stmtImage->execute();
                    $stmtImage->close();
                } else {
                    $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
                    break;
                }
            }
            if (empty($error)) {
                $success = "Post created successfully with images!";
            }
        } else {
            $error = "Error creating post.";
        }
    }
}

// Fetch areas and categories from the database for the dropdowns
$sqlArea = "SELECT Area_ID, City FROM area ORDER BY City ASC";
$resultArea = $conn->query($sqlArea);

$sqlCategory = "SELECT Category_ID, Category_Name FROM category";
$resultCategory = $conn->query($sqlCategory);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post Page</title>
    <link rel="stylesheet" href="css/CreatePost.css">
</head>

<body>

    <div class="home-page-container">
        <!-- Main Content -->
        <div class="main-content">
            <div class="create-post-container">
                <h1>Create Post Page</h1>

                <!-- Error / Success Messages -->
                <?php if (!empty($error)) {
                    echo '<p class="error">' . $error . '</p>';
                } ?>
                <?php if (!empty($success)) {
                    echo '<p class="success">' . $success . '</p>';
                } ?>

                <!-- Post Form -->
                <form action="CreatePost.php" method="post" enctype="multipart/form-data" id="createPostForm">
                    <div class="create-post-content">
                        <div class="image-section">
                            <label for="post_images">Post Images</label>
                            <input type="file" name="post_images[]" id="post_images" accept="image/*" capture="camera" style="display:none;">
                            <button type="button" id="cameraButton" class="submit-button" onclick="captureImage()">Open Camera</button>
                            <div id="imagePreviews"></div> <!-- Where the captured images will be displayed -->
                        </div>
                        <div class="right-panel">
                            <!-- Area Dropdown -->
                            <div class="dropdown">
                                <label for="area">Area</label>
                                <select name="area" id="area" required>
                                    <option value="">Select Area</option>
                                    <?php
                                    while ($rowArea = $resultArea->fetch_assoc()) {
                                        echo '<option value="' . $rowArea['Area_ID'] . '">' . $rowArea['City'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Category Dropdown -->
                            <div class="dropdown">
                                <label for="category">Category</label>
                                <select name="category" id="category" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    while ($rowCategory = $resultCategory->fetch_assoc()) {
                                        echo '<option value="' . $rowCategory['Category_ID'] . '">' . $rowCategory['Category_Name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Visibility Dropdown -->
                            <div class="dropdown">
                                <label for="visibility">Visibility</label>
                                <select name="visibility" id="visibility" required>
                                    <option value="">Select Visibility</option>
                                    <option value="Public">Public</option>
                                    <option value="Private">Private</option>
                                </select>
                            </div>

                            <!-- Anonymous Toggle -->
                            <div class="toggle">
                                <label for="anonymous">Anonymous</label>
                                <input type="checkbox" name="anonymous" id="anonymous">
                            </div>

                            <!-- Description -->
                            <label for="description">Description</label>
                            <div class="editor-container">
                                <textarea name="description" id="description" placeholder="Enter your post description" required></textarea>
                            </div>

                            <!-- Hidden fields to store latitude and longitude -->
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">

                            <!-- Submit Button -->
                            <button type="submit" class="submit-button">Create Post</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to detect if the user is on a mobile device
        function isMobileDevice() {
            return /Mobi|Android/i.test(navigator.userAgent);
        }

        // Function to open the camera and capture images
        function captureImage() {
            if (isMobileDevice()) {
                document.getElementById('post_images').click(); // Open camera on mobile
            } else {
                alert("User cannot create a post in desktop mode");
            }
        }

        // Preview captured images
        document.getElementById('post_images').addEventListener('change', function(event) {
            const imagePreviews = document.getElementById('imagePreviews');
            imagePreviews.innerHTML = ''; // Clear previous images
            const files = event.target.files;

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px'; // Set image preview size
                    img.style.margin = '10px';
                    imagePreviews.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });

        // Disable form submission if no images are uploaded
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            const files = document.getElementById('post_images').files;
            if (files.length === 0) {
                e.preventDefault();
                alert("You must attach at least one photo to create a post.");
            }
        });

        // Get the GPS location and store it in hidden inputs before form submission
        function getLocationAndSubmit() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('createPostForm').submit();
                }, function(error) {
                    alert('Error fetching GPS location: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        }

        // Attach the function to form submission
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            getLocationAndSubmit(); // Get location and submit the form
        });

        function isIOS() {
            return /iPhone|iPad|iPod/i.test(navigator.userAgent);
        }

        function adjustFileInputAttributes() {
            const fileInput = document.getElementById('post_images');
            if (isIOS()) {
                fileInput.removeAttribute('multiple');
            } else {
                fileInput.setAttribute('multiple', '');
            }
        }

        adjustFileInputAttributes();
    </script>

</body>

</html>