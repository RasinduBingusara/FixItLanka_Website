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
    <!-- Include NavigationBar -->
    <?php include 'NavigationBar.php'; ?>

    <!-- Main Content Area -->
    <div class="profile-container">
        <main class="profile-content">
            <header class="profile-header">
                <h1>Profile</h1>
                <button class="edit-profile-btn">Edit Profile</button>
            </header>
            <form class="profile-form">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob">
                </div>
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" placeholder="Enter your location">
                </div>
                <div class="form-group">
                    <label for="contact">Contact No.:</label>
                    <input type="text" id="contact" name="contact" placeholder="Enter your contact number">
                </div>
            </form>
            <section class="shared-posts">
                <h2>Shared Posts</h2>
                <div class="post">
                    <img src="placeholder.png" alt="Shared Post">
                </div>
                <div class="post">
                    <img src="placeholder.png" alt="Shared Post">
                </div>
            </section>
        </main>
    </div>

    <!-- JavaScript to toggle any functionality -->
    <script>
        // Add any necessary JavaScript here
    </script>
</body>
</html>
