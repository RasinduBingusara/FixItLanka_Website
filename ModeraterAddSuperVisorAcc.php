<?php
// adminCreateSupervisorAcc.php

// Start the session to access session variables
session_start();

// Include the database connection file
include("Database.php");

// Initialize variables to store form data and errors
$name = $email = $password = $confirm_password = $region = $contact = "";
$errors = array();
$success = "";

// Fetch regions from the database to populate the dropdown
$regions = array();
$sqlRegions = "SELECT `RegionID`, `Region` FROM `region` ORDER BY `Region` ASC";

if ($stmtRegions = $conn->prepare($sqlRegions)) {
    $stmtRegions->execute();
    $resultRegions = $stmtRegions->get_result();

    if ($resultRegions && $resultRegions->num_rows > 0) {
        while ($row = $resultRegions->fetch_assoc()) {
            $regions[] = $row;
        }
    }
    $stmtRegions->close();
} else {
    // Handle query preparation error
    $errors[] = "Error fetching regions: " . $conn->error;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $region = isset($_POST["region"]) ? intval($_POST["region"]) : 0;
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
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (empty($confirm_password)) {
        $errors[] = "Confirm Password is required.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($region)) {
        $errors[] = "Region is required.";
    } else {
        // Verify that the selected Region_ID exists in the region table
        $sqlVerifyRegion = "SELECT `RegionID` FROM `region` WHERE `RegionID` = ?";
        if ($stmtVerify = $conn->prepare($sqlVerifyRegion)) {
            $stmtVerify->bind_param("i", $region);
            $stmtVerify->execute();
            $stmtVerify->store_result();
            if ($stmtVerify->num_rows == 0) {
                $errors[] = "Selected region does not exist.";
            }
            $stmtVerify->close();
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }

    if (empty($contact)) {
        $errors[] = "Contact Number is required.";
    } elseif (!preg_match("/^\+?[0-9]{7,15}$/", $contact)) {
        $errors[] = "Invalid contact number format. It should contain 7 to 15 digits and may start with '+'.";
    }

    // If no errors, proceed to insert the supervisor account
    if (empty($errors)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $categoryID = $_SESSION["UserData"][1];
        // Prepare the SQL statement
        $sqlInsert = "INSERT INTO `useraccount` 
            (`Category_ID`, `RegionID`, `Username`, `Password`, `Email`, `ContactNumber`, `Acc_Status`, `UserType`, `Created_at`) 
            VALUES (?, ?, ?, ?, ?, ?, 'Active', 'Supervisor', NOW())";

        if ($stmt = $conn->prepare($sqlInsert)) {
            // Bind parameters: i = integer, s = string
            $stmt->bind_param("iissss", $categoryID, $region, $name, $hashed_password, $email, $contact);

            // Execute the statement
            if ($stmt->execute()) {
                $success = "Supervisor account created successfully.";
                // Optionally, clear form inputs
                $name = $email = $password = $confirm_password = $region = $contact = "";
            } else {
                // Handle execution errors
                if ($conn->errno == 1062) { // Duplicate entry
                    $errors[] = "An account with this email already exists.";
                } else {
                    $errors[] = "Error: " . $conn->error;
                }
            }

            // Close the statement
            $stmt->close();
        } else {
            // Handle preparation errors
            $errors[] = "Error preparing statement: " . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supervisor Account</title>
    <link rel="stylesheet" href="css/adminCreateSupervisorAcc.css">
    <!-- Optional: Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnH1zpsaqe7Nj3U5ZL9vFSJ0YB8HlvBl4ipnJ8FgVsz8w+/X7z1+Nl7OY2r/kH8G4nD4rJ4Fw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <!-- Include Moderator Navigation Bar -->
    <?php include 'ModeratorNavBar.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <h1>Add Supervisor Account</h1>
        </header>

        <!-- Display Success Message -->
        <?php if (!empty($success)): ?>
            <div class="alert success">
                <?php echo htmlspecialchars($success); ?>
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

        <form class="account-form" action="" method="POST">
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
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:<span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="region">Region:<span class="required">*</span></label>
                <select name="region" id="region" required>
                    <option value="">-- Select Region --</option>
                    <?php
                        foreach ($regions as $reg) {
                            // Preserve the selected region after form submission
                            $selected = ($reg['RegionID'] == $region) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($reg['RegionID']) . '" ' . $selected . '>' . htmlspecialchars($reg['Region']) . '</option>';
                        }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="contact">Contact No.:<span class="required">*</span></label>
                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required pattern="^\+?[0-9]{7,15}$" title="Enter a valid contact number (7-15 digits, may start with '+')">
            </div>
            
            <button type="submit" class="add-button">Add</button>
        </form>
    </main>


</body>
</html>
