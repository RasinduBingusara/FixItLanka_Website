<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\ModeraterAddSuperVisorAcc.css">
    <title>Add Supervisor Account</title>
</head>
<body>

    <!-- Include ModeratorNavBar -->
    <?php include 'ModeratorNavBar.php'; ?>

    <main class="content">
        <div class="header">
            <h1>Add Supervisor Account</h1>
        </div>

        <form class="account-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob">
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location">
            </div>
            <div class="form-group">
                <label for="contact">Contact No.:</label>
                <input type="text" id="contact" name="contact">
            </div>
            <button type="submit" class="add-button">Add</button>
        </form>
    </main>

</body>
</html>
