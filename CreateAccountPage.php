<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="css/createAccountPage.css">
</head>
<body>

    <!-- Include AdminNavigationBar -->
    <?php include 'AdminNavigationBar.php'; ?>

    <div class="form-container">
        <h2>Create Account</h2>
        <form action="#" method="POST">
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="admin">Admin</option>
                <option value="user">User</option>
                <option value="moderator">Moderator</option>
            </select>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your name">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email">

            <label for="contact">Contact No:</label>
            <input type="text" id="contact" name="contact" placeholder="Enter your contact number">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password">

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password">

            <button type="submit" class="update-button">Create Account</button>
        </form>
    </div>

</body>
</html>
