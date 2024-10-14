<?php
include("../Database.php");
include('AdminNavigationBar.php');

// Initialize variables for form fields and messages
$name = $email = $password = $confirm_password = $category = $contact = "";
$errors = array();
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and assign POST variables
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $category = $_POST["category"];
    $contact = trim($_POST["contact"]);

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($confirm_password)) {
        $errors[] = "Confirm Password is required.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($category)) {
        $errors[] = "Category is required.";
    }

    if (empty($contact)) {
        $errors[] = "Contact Number is required.";
    } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $contact)) {
        $errors[] = "Invalid Contact Number format.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $sqlCheckEmail = "SELECT UID FROM useraccount WHERE Email = ?";
        if ($stmtCheck = $conn->prepare($sqlCheckEmail)) {
            $stmtCheck->bind_param("s", $email);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows > 0) {
                $errors[] = "An account with this email already exists.";
            }

            $stmtCheck->close();
        } else {
            $errors[] = "Database error: Unable to prepare statement.";
        }
    }

    // If no errors, proceed to insert the new moderator
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Set default values for other fields
        $acc_status = "Active"; // Assuming default status is Active
        $user_type = "Moderator";
        $profile_picture = "../../IMG/defaultProfile.png"; // Default profile picture
        $created_at = date("Y-m-d H:i:s"); // Current timestamp
        $region = 1;

        // Prepare the INSERT statement
        $sqlInsert = "INSERT INTO useraccount (Category_ID, RegionID, Username, Password, Email, ContactNumber, Acc_Status, UserType, Created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmtInsert = $conn->prepare($sqlInsert)) {
            $stmtInsert->bind_param("iisssssss", $category, $region, $name, $hashed_password, $email, $contact, $acc_status, $user_type, $created_at);

            if ($stmtInsert->execute()) {
                $success_message = "Moderator account created successfully.";
                // Clear form fields
                $name = $email = $password = $confirm_password = $category = $contact = "";
            } else {
                $errors[] = "Database error: Unable to execute query.";
            }

            $stmtInsert->close();
        } else {
            $errors[] = "Database error: Unable to prepare statement.";
        }
    }
}

// Fetch categories for the dropdown
$categories = array();
$sqlCategories = "SELECT `Category_ID`, `Category_Name` FROM `category` ORDER BY `Category_Name` ASC";
if ($resultCategories = $conn->query($sqlCategories)) {
    if ($resultCategories->num_rows > 0) {
        while ($row = $resultCategories->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    $resultCategories->free();
} else {
    $errors[] = "Database error: Unable to fetch categories.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Moderator Account</title>
    <link rel="stylesheet" href="../../CSS/adminCreateModeratorAcc.css">
</head>
<body>

    <main class="main-content">
        <div class="header">
            <h1>Add Moderator Account</h1>
        </div>

        <!-- Display Success Message -->
        <?php if (!empty($success_message)): ?>
            <div class="alert success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="account-form" method="POST" action="adminCreateModeratorAcc.php" novalidate>
            <div class="form-group">
                <label for="name">Name:<span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:<span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:<span class="required">*</span></label>
                <input type="password" id="password" name="password" required>
                <small class="password-hint">Must be at least 8 characters, include letters and numbers.</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:<span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="category">Category:<span class="required">*</span></label>
                <select name="category" id="category" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['Category_ID']); ?>" <?php echo ($cat['Category_ID'] == $category) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['Category_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="contact">Contact Number:<span class="required">*</span></label>
                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                <small class="contact-hint">Include country code (e.g., +1234567890).</small>
            </div>
            <button type="submit" class="add-button">Add Moderator</button>
        </form>
    </main>

    <!-- Optional JavaScript for Enhanced UX -->
    <script>
        // Example: Client-side validation for password match
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.account-form');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            form.addEventListener('submit', function(e) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    confirmPassword.focus();
                }
            });
        });
    </script>

</body>
</html>
