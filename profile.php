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
            <div class="profile-info">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <span id="name">John Doe</span> <!-- Replace with dynamic content -->
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <span id="email">john.doe@example.com</span> <!-- Replace with dynamic content -->
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <span id="dob">1990-01-01</span> <!-- Replace with dynamic content -->
                </div>
                <div class="form-group">
                    <label for="location">Location:</label>
                    <span id="location">New York, USA</span> <!-- Replace with dynamic content -->
                </div>
                <div class="form-group">
                    <label for="contact">Contact No.:</label>
                    <span id="contact">+1 123-456-7890</span> <!-- Replace with dynamic content -->
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
        </main>
    </div>

    <!-- JavaScript to toggle any functionality -->
    <script>
        // Add any necessary JavaScript here
    </script>
</body>
</html>
