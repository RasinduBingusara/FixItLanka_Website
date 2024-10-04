<?php
include('NavigationBar.php');
include('Database.php');
$Username = "";
$Email = "";
$ContactNumber = "";
$DOB = "-";
$Location = "";


$editDetailsAccess = false;
if (isset($_GET["editProfileBtn"])) {
    $editDetailsAccess = true;
}
if (isset($_POST["updateProfileBtn"])) {

    if (isset($_POST["username"]) && isset($_POST["ContactNumber"])) {

        $username = trim($_POST["username"]);
        $contactNumber = trim($_POST["ContactNumber"]);
        $uid = $_SESSION['UserData'][0];

        if (!empty($username) && !empty($contactNumber) && !empty($uid)) {
            $stmt = $conn->prepare("UPDATE useraccount SET Username = ?, ContactNumber = ? WHERE UID = ?");

            if ($stmt) {
                $stmt->bind_param("ssi", $username, $contactNumber, $uid);

                if ($stmt->execute()) {
                    $editDetailsAccess = false;
                    $_SESSION['UserData'][2] = $username;
                    $_SESSION['UserData'][4] = $contactNumber;

                    echo "Profile updated successfully.";
                } else {
                    error_log("Error executing query: " . $stmt->error);
                    echo "An error occurred while updating your profile.";
                }

                $stmt->close();
            } else {
                error_log("Error preparing statement: " . $conn->error);
                echo "An error occurred while updating your profile.";
            }
        } else {
            echo "Please fill in all required fields.";
        }
    }
}

if (isset($_SESSION["UserData"])) {
    $Username = $_SESSION["UserData"][2];
    $Email = $_SESSION["UserData"][3];
    $ContactNumber = $_SESSION["UserData"][4];
    $DOB = $_SESSION["UserData"][5];
    $Location = $_SESSION["UserData"][8];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\profile.css">
    <title>Profile Page</title>
</head>

<body>

    <!-- Main Content Area -->
    <div class="profile-container">
        <main class="profile-content">
            <header class="profile-header">
                <h1>Profile</h1>
                <?php
                if (!$editDetailsAccess) {
                ?>
                    <form action="" method="get">
                        <input type="submit" class="edit-profile-btn" id="editProfileBtn" name="editProfileBtn" value="Edit Profile"></input>
                    </form>
                <?php
                }
                ?>
            </header>
            <?php
            if ($editDetailsAccess) {
            ?>
                <form action="" method="post">
                    <div class="profile-info">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="username" name="username" value="<?php echo $Username ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <span id="email"><?php echo $Email ?></span> <!-- Replace with dynamic content -->
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth:</label>
                            <span id="dob"><?php echo $DOB ?></span> <!-- Replace with dynamic content -->
                        </div>
                        <div class="form-group">
                            <label for="location">Location:</label>
                            <span id="location"><?php echo $Location ?></span> <!-- Replace with dynamic content -->
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact No.:</label>
                            <input type="text" id="ContactNumber" name="ContactNumber" value="<?php echo $ContactNumber ?>">
                        </div>
                        <input type="submit" id="updateProfileBtn" name="updateProfileBtn" value="Update">
                    </div>
                </form>
            <?php
            } else {

            ?>
                <div class="profile-info">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <span id="name"><?php echo $Username ?></span> <!-- Replace with dynamic content -->
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <span id="email"><?php echo $Email ?></span> <!-- Replace with dynamic content -->
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <span id="dob"><?php echo $DOB ?></span> <!-- Replace with dynamic content -->
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <span id="location"><?php echo $Location ?></span> <!-- Replace with dynamic content -->
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact No.:</label>
                        <span id="contact"><?php echo $ContactNumber ?></span> <!-- Replace with dynamic content -->
                    </div>
                </div>

                <section class="shared-posts">
                <h2>Shared Posts</h2>
                <div class="post">
                    <img src="placeholder.png" alt="Shared Post">
                </div>
                <div class="post">
                    <img src="placeholder.png" alt="Shared Post">
                </div>
            </section>
            <?php
            }
            ?>
            
        </main>
    </div>

    <!-- JavaScript to toggle any functionality -->
    <script>
        // Add any necessary JavaScript here
    </script>
</body>

</html>