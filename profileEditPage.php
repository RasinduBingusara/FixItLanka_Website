<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/profileEditPage.css">
</head>
<body>

    <!-- Include AdminNavigationBar -->
    <?php include 'AdminNavigationBar.php'; ?>

    <div class="form-container">
        <h2>Edit Account</h2>
        <form action="#" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your Password">

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your Password">

            <button type="submit" class="update-button">Update</button>
        </form>
    </div>

</body>
</html>
