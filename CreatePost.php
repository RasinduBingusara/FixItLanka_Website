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
$category = $_POST['category'] ?? '';
$complaint = $_POST['complaint'] ?? '';
$region = $_POST['region'] ?? '';
$visibility = $_POST['visibility'] ?? '';
$isAnonymous = isset($_POST['anonymous']) ? 1 : 0;
$description = $_POST['description'] ?? '';
$latitude = $_POST['latitude'] ?? '';
$longitude = $_POST['longitude'] ?? '';

// Fetch complaints and regions based on the selected category
if (!empty($category)) {
    $sqlComplaint = "SELECT ComplaintID, Complaint FROM complainttype WHERE CID = ?";
    $stmtComplaint = $conn->prepare($sqlComplaint);
    $stmtComplaint->bind_param("i", $category);
    $stmtComplaint->execute();
    $resultComplaint = $stmtComplaint->get_result();

    $sqlRegion = "SELECT RegionID, Region FROM region WHERE CID = ?";
    $stmtRegion = $conn->prepare($sqlRegion);
    $stmtRegion->bind_param("i", $category);
    $stmtRegion->execute();
    $resultRegion = $stmtRegion->get_result();
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
            // Define maximum file size (e.g., 10MB)
            $maxFileSize = 100 * 1024 * 1024; // 10MB in bytes

            // Initialize $imageBlob as NULL
            $imageBlob = NULL;

            // Check if an image is uploaded
            if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] != UPLOAD_ERR_NO_FILE) {
                // Handle different upload errors
                if ($_FILES['post_image']['error'] !== UPLOAD_ERR_OK) {
                    switch ($_FILES['post_image']['error']) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $error = "The uploaded file exceeds the maximum allowed size of 10MB.";
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $error = "The file was only partially uploaded.";
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $error = "No file was uploaded.";
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $error = "Missing a temporary folder.";
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $error = "Failed to write file to disk.";
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $error = "A PHP extension stopped the file upload.";
                            break;
                        default:
                            $error = "An unknown error occurred during file upload.";
                            break;
                    }
                } else {
                    // Validate file size
                    if ($_FILES['post_image']['size'] > $maxFileSize) {
                        $error = "The uploaded file exceeds the maximum allowed size of 100MB.";
                    } else {
                        // Validate file type
                        $allowedMimeTypes = [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/heic',
                            'image/heif',
                            'image/webp'
                        ];
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $_FILES['post_image']['tmp_name']);
                        finfo_close($finfo);

                        if (!in_array($mimeType, $allowedMimeTypes)) {
                            $error = "Only JPG, PNG, GIF, HEIC, HEIF, and WEBP files are allowed.";
                        } else {
                            // Optional: Resize image to reduce size (requires GD or Imagick)
                            // Uncomment the following lines if you want to resize images
                            /*
                            $imageResource = null;
                            switch ($mimeType) {
                                case 'image/jpeg':
                                    $imageResource = imagecreatefromjpeg($_FILES['post_image']['tmp_name']);
                                    break;
                                case 'image/png':
                                    $imageResource = imagecreatefrompng($_FILES['post_image']['tmp_name']);
                                    break;
                                case 'image/gif':
                                    $imageResource = imagecreatefromgif($_FILES['post_image']['tmp_name']);
                                    break;
                                case 'image/webp':
                                    $imageResource = imagecreatefromwebp($_FILES['post_image']['tmp_name']);
                                    break;
                                // HEIC/HEIF support may require additional libraries
                                default:
                                    $imageResource = null;
                            }

                            if ($imageResource) {
                                // Define desired width and height
                                $desiredWidth = 800;
                                $desiredHeight = 600;

                                // Get current dimensions
                                $width = imagesx($imageResource);
                                $height = imagesy($imageResource);

                                // Calculate new dimensions while maintaining aspect ratio
                                $aspectRatio = $width / $height;
                                if ($desiredWidth / $desiredHeight > $aspectRatio) {
                                    $desiredWidth = $desiredHeight * $aspectRatio;
                                } else {
                                    $desiredHeight = $desiredWidth / $aspectRatio;
                                }

                                // Create a new resized image
                                $resizedImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
                                imagecopyresampled($resizedImage, $imageResource, 0, 0, 0, 0, $desiredWidth, $desiredHeight, $width, $height);

                                // Save the resized image to a temporary location
                                $tempPath = tempnam(sys_get_temp_dir(), 'upload_');
                                imagejpeg($resizedImage, $tempPath, 85); // Adjust quality as needed

                                // Update the file path and size
                                $_FILES['post_image']['tmp_name'] = $tempPath;
                                $_FILES['post_image']['size'] = filesize($tempPath);

                                // Clean up
                                imagedestroy($imageResource);
                                imagedestroy($resizedImage);
                            }
                            */

                            // Read the image content
                            $fileTmpPath = $_FILES['post_image']['tmp_name'];
                            $imageBlob = file_get_contents($fileTmpPath);
                        }
                    }
                }
            }

            // Proceed if there are no errors
            if (empty($error)) {
                // Prepare and execute the database insert statement using prepared statements
                $sql = "INSERT INTO post (UID, RegionID, CategoryID, ComplaintID, Description, Visibility, Is_Anonymouse, Latitude, Longitude, Image, Created_at, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending')";

                if ($stmt = $conn->prepare($sql)) {
                    // Bind parameters
                    // 'i' - integer, 's' - string, 'd' - double, 'b' - blob
                    $stmt->bind_param(
                        "iiisssddbs",
                        $uid,
                        $region,
                        $category,
                        $complaint,
                        $description,
                        $visibility,
                        $isAnonymous,
                        $latitude,
                        $longitude,
                        $imageBlob // Note: For large blobs, you may need to use send_long_data
                    );

                    // Handle BLOB data
                    if ($imageBlob !== NULL) {
                        $stmt->send_long_data(9, $imageBlob); // 0-based index
                    }

                    // Execute the statement
                    if ($stmt->execute()) {
                        $success = "Post created successfully!";
                        // Reset form variables after successful submission
                        $category = $complaint = $region = $visibility = $description = '';
                        $isAnonymous = 0;
                    } else {
                        // Log the error and set a user-friendly message
                        error_log("Database Insert Error: " . $stmt->error);
                        $error = "There was an error creating your post. Please try again.";
                    }

                    // Close the statement
                    $stmt->close();
                } else {
                    // Log the error and set a user-friendly message
                    error_log("Database Prepare Error: " . $conn->error);
                    $error = "There was an error preparing your request. Please try again.";
                }
            }
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
    <style>
        /* Additional CSS styling if needed */
    </style>
</head>

<body>
    <div class="home-page-container">
        <div class="main-content">
            <div class="create-post-container">
                <h1>Create Post Page</h1>

                <!-- Error / Success Messages -->
                <?php if (!empty($error)) {
                    echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                } ?>
                <?php if (!empty($success)) {
                    echo '<p class="success">' . htmlspecialchars($success) . '</p>';
                } ?>

                <!-- Post Form -->
                <form action="CreatePost.php" method="post" enctype="multipart/form-data" id="createPostForm">
                    <!-- Optional: Specify maximum file size to the browser -->
                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" /> <!-- 10MB in bytes -->

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
                                        $resultCategory->data_seek(0);
                                        while ($rowCategory = $resultCategory->fetch_assoc()) {
                                            $selected = ($rowCategory["Category_ID"] == $category) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($rowCategory['Category_ID']) . '" ' . $selected . '>' . htmlspecialchars($rowCategory['Category_Name']) . '</option>';
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
                                        $resultComplaint->data_seek(0);
                                        while ($rowComplaint = $resultComplaint->fetch_assoc()) {
                                            $selected = ($rowComplaint["ComplaintID"] == $complaint) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($rowComplaint['ComplaintID']) . '" ' . $selected . '>' . htmlspecialchars($rowComplaint['Complaint']) . '</option>';
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
                                        $resultRegion->data_seek(0);
                                        while ($rowRegion = $resultRegion->fetch_assoc()) {
                                            $selected = ($rowRegion["RegionID"] == $region) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($rowRegion['RegionID']) . '" ' . $selected . '>' . htmlspecialchars($rowRegion['Region']) . '</option>';
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
