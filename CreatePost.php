<?php
include('NavigationBar.php');
include("Database.php");

// Initialize variables
$error = '';
$success = '';

// Fetch categories from the database
$sqlCategory = "SELECT Category_ID, Category_Name FROM category";
$resultCategory = $conn->query($sqlCategory);

// Initialize form variables
$category = isset($_POST['category']) ? $_POST['category'] : '';
$complaint = isset($_POST['complaint']) ? $_POST['complaint'] : '';
$region = isset($_POST['region']) ? $_POST['region'] : '';
$visibility = isset($_POST['visibility']) ? $_POST['visibility'] : '';
$isAnonymous = isset($_POST['anonymous']) ? 1 : 0;
$description = isset($_POST['description']) ? $_POST['description'] : '';
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

// Fetch complaints and regions based on the selected category
if (!empty($category)) {
    $sqlComplaint = "SELECT ComplaintID, Complaint FROM complainttype WHERE CID = " . intval($category);
    $resultComplaint = $conn->query($sqlComplaint);

    $sqlRegion = "SELECT RegionID, Region FROM region WHERE CID = " . intval($category);
    $resultRegion = $conn->query($sqlRegion);
} else {
    $resultComplaint = false;
    $resultRegion = false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form is submitted to create a post
    if (isset($_POST['createPost'])) {
        // Check if user is logged in
        if (isset($_SESSION["UserData"])) {
            $uid = $_SESSION["UserData"][0];
        } else {
            $error = "User is not logged in.";
        }

        // Check for required fields
        if (empty($region) || empty($category) || empty($complaint) || empty($visibility) || empty($description) || empty($latitude) || empty($longitude)) {
            $error = "All fields are required!";
        } else {
            // Initialize $imageBlob as NULL
            $imageBlob = NULL;

            // Check if an image is uploaded
            // if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
            //     // Process image upload
            //     $Image = $_FILES['post_image'];

            //     // Ensure the uploaded file is a valid image
            //     $fileType = pathinfo($Image['name'], PATHINFO_EXTENSION);
            //     $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

            //     if (in_array(strtolower($fileType), $allowTypes)) {
            //         // Read the image file and store it as a blob
            //         $imageBlob = file_get_contents($Image['tmp_name']);
            //     } else {
            //         $error = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
            //     }
            // }

            if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
                // Process image upload
                $Image = $_FILES['post_image'];
                $fileType = pathinfo($Image['name'], PATHINFO_EXTENSION);
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
            
                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Read the image file and store it as a blob
                    $imageBlob = file_get_contents($Image['tmp_name']);
            
                    // Prepare and execute the database insert statement
                    $stmt = $conn->prepare("INSERT INTO post (UID, RegionID, CategoryID, ComplaintID, Description, Visibility, Is_Anonymouse, Latitude, Longitude, Image, Created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->bind_param("iiiissiddb", $uid, $region, $category, $complaint, $description, $visibility, $isAnonymous, $latitude, $longitude, $imageBlob);
            
                    if ($stmt->execute()) {
                        $success = "Post created successfully!";
                        $category = $complaint = $region = $visibility = $description = '';
                    $isAnonymous = 0;
                    } else {
                        $error = "Error: " . $stmt->error;  // Log error
                    }
                } else {
                    $error = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
                }
            }
            

            // Proceed if there are no errors
            // if (empty($error)) {
            //     // Prepare and execute the database insert statement
            //     $stmt = $conn->prepare("INSERT INTO post (UID, RegionID, CategoryID, ComplaintID, Description, Visibility, Is_Anonymouse, Latitude, Longitude, Image, Created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
            //     // Ensure $imageBlob is correctly passed
            //     $stmt->bind_param("iiiissiddb", $uid, $region, $category, $complaint, $description, $visibility, $isAnonymous, $latitude, $longitude, $imageBlob);

            //     if ($stmt->execute()) {
            //         $success = "Post created successfully!";
            //         // Reset form variables after successful submission
            //         $category = $complaint = $region = $visibility = $description = '';
            //         $isAnonymous = 0;
            //     } else {
            //         $error = "Error: " . $stmt->error;
            //     }
            // }
        }
    }
}
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
        <div class="main-content">
            <div class="create-post-container">
                <h1>Create Post Page</h1>

                <!-- Error / Success Messages -->
                <?php if (!empty($error)) { echo '<p class="error">' . $error . '</p>'; } ?>
                <?php if (!empty($success)) { echo '<p class="success">' . $success . '</p>'; } ?>

                <!-- Post Form -->
                <form action="CreatePost.php" method="post" enctype="multipart/form-data" id="createPostForm">
                    <div class="create-post-content">
                        <div class="image-section">
                            <label for="post_image">Post Image (Optional)</label>
                            <label for="post_image" id="cameraButton" class="submit-button">Open Camera</label>
                            <input type="file" name="post_image" id="post_image" accept="image/*" style="display:none;">
                            <div id="imagePreviews"></div>
                        </div>
                        <div class="right-panel">
                            <div class="dropdown">
                                <label for="category">Category</label>
                                <select name="category" id="category" onchange="this.form.submit()" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    if ($resultCategory->num_rows > 0) {
                                        while ($rowCategory = $resultCategory->fetch_assoc()) {
                                            $selected = ($rowCategory["Category_ID"] == $category) ? 'selected' : '';
                                            echo '<option value="' . $rowCategory['Category_ID'] . '" ' . $selected . '>' . htmlspecialchars($rowCategory['Category_Name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="dropdown">
                                <label for="complaint">Complaint Issue</label>
                                <select name="complaint" id="complaint" required>
                                    <option value="">Select Complaint</option>
                                    <?php
                                    if ($resultComplaint && $resultComplaint->num_rows > 0) {
                                        while ($rowComplaint = $resultComplaint->fetch_assoc()) {
                                            $selected = ($rowComplaint["ComplaintID"] == $complaint) ? 'selected' : '';
                                            echo '<option value="' . $rowComplaint['ComplaintID'] . '" ' . $selected . '>' . htmlspecialchars($rowComplaint['Complaint']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="dropdown">
                                <label for="region">Region</label>
                                <select name="region" id="region" required>
                                    <option value="">Select Region</option>
                                    <?php
                                    if ($resultRegion && $resultRegion->num_rows > 0) {
                                        while ($rowRegion = $resultRegion->fetch_assoc()) {
                                            $selected = ($rowRegion["RegionID"] == $region) ? 'selected' : '';
                                            echo '<option value="' . $rowRegion['RegionID'] . '" ' . $selected . '>' . htmlspecialchars($rowRegion['Region']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="dropdown">
                                <label for="visibility">Visibility</label>
                                <select name="visibility" id="visibility" required>
                                    <option value="">Select Visibility</option>
                                    <option value="Public" <?php if ($visibility == "Public") echo "selected"; ?>>Public</option>
                                    <option value="Private" <?php if ($visibility == "Private") echo "selected"; ?>>Private</option>
                                </select>
                            </div>

                            <div class="toggle">
                                <label for="anonymous">Anonymous</label>
                                <input type="checkbox" name="anonymous" id="anonymous" <?php if ($isAnonymous) echo 'checked'; ?>>
                            </div>

                            <label for="description">Description</label>
                            <div class="editor-container">
                                <textarea name="description" id="description" placeholder="Enter your post description" required><?php echo htmlspecialchars($description); ?></textarea>
                            </div>

                            <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($latitude); ?>">
                            <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($longitude); ?>">
                            <input type="hidden" id="isMobile" name="isMobile" value="false">

                            <button type="submit" class="submit-button" name="createPost">Create Post</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function isMobileDevice() {
            return /Mobi|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i.test(navigator.userAgent);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const cameraButton = document.getElementById('cameraButton');
            const postImageInput = document.getElementById('post_image');
            const isMobileInput = document.getElementById('isMobile');

            if (isMobileDevice()) {
                cameraButton.style.display = 'inline-block';
                postImageInput.disabled = false; // Enable the file input
                postImageInput.setAttribute('capture', 'environment');
                isMobileInput.value = 'true';
            } else {
                cameraButton.style.display = 'none';
                postImageInput.disabled = true; // Disable the file input
                postImageInput.removeAttribute('capture');
                isMobileInput.value = 'false';
            }
        });

        document.getElementById('cameraButton').addEventListener('click', function(event) {
            event.preventDefault();
            if (isMobileDevice()) {
                document.getElementById('post_image').click();
            }
        });

        document.getElementById('post_image').addEventListener('change', function(event) {
            if (isMobileDevice()) {
                const imagePreviews = document.getElementById('imagePreviews');
                imagePreviews.innerHTML = ''; // Clear previous images
                const file = event.target.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100px'; // Set image preview size
                        img.style.margin = '10px';
                        imagePreviews.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        window.onload = function() {
            if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                    }, function(error) {
                        alert('Error fetching GPS location: ' + error.message);
                    });
                } else {
                    alert('Geolocation is not supported by your browser.');
                }
            }
        };
    </script>
</body>

</html>
