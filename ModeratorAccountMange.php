<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Account manage</title> 
    <link rel="stylesheet" href="css/supervisorUpdateUserAcc.css">
</head>
<body>

    <!-- Include SupervisorNavBar -->
    <?php include 'ModeratorNavBar.php'; ?>

    <div class="container">
        <!-- Main Content -->
        <main class="main-content">
            <h1>Update User Account</h1>
            <h3>Users</h3>
            <div class="table-container">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>UID</th>
                            <th>UserName</th>
                            <th>Password</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Name1</td>
                            <td>*******</td>
                            <td>1@gmail.com</td>
                            <td>071 09876</td>
                            <td>
                                <button class="edit-button">Edit</button>
                                <button class="delete-button">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Name2</td>
                            <td>*******</td>
                            <td>2@gmail.com</td>
                            <td>071 09876</td>
                            <td>
                                <button class="edit-button">Edit</button>
                                <button class="delete-button">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Name3</td>
                            <td>*******</td>
                            <td>3@gmail.com</td>
                            <td>071 09876</td>
                            <td>
                                <button class="edit-button">Edit</button>
                                <button class="delete-button">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Name4</td>
                            <td>*******</td>
                            <td>4@gmail.com</td>
                            <td>071 09876</td>
                            <td>
                                <button class="edit-button">Edit</button>
                                <button class="delete-button">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>
