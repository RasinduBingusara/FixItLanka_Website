<?php
// get_profile_pic.php
include('Database.php');

if (isset($_GET['uid'])) {
    $uid = intval($_GET['uid']);
    $stmt = $conn->prepare("SELECT ProfilePicture FROM useraccount WHERE UID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->bind_result($profilePicture);
        if ($stmt->fetch()) {
            if ($profilePicture) {
                // Determine the image type (assuming JPEG; adjust as needed)
                header("Content-Type: image/jpeg");
                echo $profilePicture;
            } else {
                // Serve default image
                readfile('../IMG/defaultProfile.png');
            }
        } else {
            // Serve default image if user not found
            readfile('../IMG/defaultProfile.png');
        }
        $stmt->close();
    } else {
        // Serve default image on error
        readfile('../IMG/defaultProfile.png');
    }
} else {
    // Serve default image if UID not provided
    readfile('../IMG/defaultProfile.png');
}
?>
