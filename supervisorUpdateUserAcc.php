<?php
// AdminAccountManage.php

// Start the session to access session variables
session_start();

// Include the database connection file
include("Database.php");

// Initialize an array to hold moderators
$moderators = array();

// Fetch moderators from the useraccount table joined with category table
$sqlModerators = "
    SELECT 
        u.UID,
        u.Username,
        u.Email,
        u.ContactNumber,
        c.Category_Name
    FROM 
        useraccount u
    JOIN 
        category c ON u.Category_ID = c.Category_ID
    WHERE 
        u.UserType = 'Moderator'
    ORDER BY 
        u.Username ASC
";

// Prepare and execute the query
if ($stmt = $conn->prepare($sqlModerators)) {
    $stmt->execute();
    $resultModerators = $stmt->get_result();

    if ($resultModerators && $resultModerators->num_rows > 0) {
        while ($moderator = $resultModerators->fetch_assoc()) {
            $moderators[] = $moderator;
        }
    }
    $stmt->close();
} else {
    // Handle query preparation error
    $error = "Error fetching moderators: " . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Manage</title> 
    <link rel="stylesheet" href="css/supervisorUpdateUserAcc.css">
    <!-- Optional: Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnH1zpsaqe7Nj3U5ZL9vFSJ0YB8HlvBl4ipnJ8FgVsz8w+/X7z1+Nl7OY2r/kH8G4nD4rJ4Fw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <!-- Include AdminNavigationBar -->
    <?php include 'ModeratorNavBar.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <h1>Manage Moderator Accounts</h1>
        </header>

        <!-- Display Success Message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert success">
                Moderator account updated successfully.
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (isset($error)): ?>
            <div class="alert error">
                <ul>
                    <li><?php echo htmlspecialchars($error); ?></li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Category</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($moderators)): ?>
                        <?php foreach ($moderators as $moderator): ?>
                            <tr id="row-<?php echo htmlspecialchars($moderator['UID']); ?>">
                                <td class="name"><?php echo htmlspecialchars($moderator['Username']); ?></td>
                                <td class="email"><?php echo htmlspecialchars($moderator['Email']); ?></td>
                                <td class="category"><?php echo htmlspecialchars($moderator['Category_Name']); ?></td>
                                <td class="contact"><?php echo htmlspecialchars($moderator['ContactNumber']); ?></td>
                                <td class="actions">
                                    <button class="btn edit-btn" onclick="editRow(<?php echo htmlspecialchars($moderator['UID']); ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn delete-btn" onclick="deleteRow(<?php echo htmlspecialchars($moderator['UID']); ?>)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No moderators found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- JavaScript for handling Edit and Delete actions -->
    <script>
        let currentEditingRow = null;

        function editRow(uid) {
            // Prevent multiple rows being edited at the same time
            if (currentEditingRow !== null) {
                alert("Please finish editing the current row before editing another.");
                return;
            }

            currentEditingRow = uid;

            // Get the table row
            const row = document.getElementById(`row-${uid}`);

            // Get current data
            const nameCell = row.querySelector('.name');
            const emailCell = row.querySelector('.email');
            const contactCell = row.querySelector('.contact');

            const currentName = nameCell.textContent;
            const currentEmail = emailCell.textContent;
            const currentContact = contactCell.textContent;

            // Replace text with input fields
            nameCell.innerHTML = `<input type="text" class="edit-input" id="name-${uid}" value="${currentName}">`;
            emailCell.innerHTML = `<input type="email" class="edit-input" id="email-${uid}" value="${currentEmail}">`;
            contactCell.innerHTML = `<input type="text" class="edit-input" id="contact-${uid}" value="${currentContact}" pattern="^\\+?[0-9]{7,15}$" title="Enter a valid contact number (7-15 digits, may start with '+')">`;

            // Replace Edit and Delete buttons with Update and Cancel buttons
            const actionsCell = row.querySelector('.actions');
            actionsCell.innerHTML = `
                <button class="btn update-btn" onclick="updateRow(${uid})">
                    <i class="fas fa-save"></i> Update
                </button>
                <button class="btn cancel-btn" onclick="cancelEdit(${uid})">
                    <i class="fas fa-times-circle"></i> Cancel
                </button>
            `;

            // Move the edited row to the top
            const tbody = row.parentElement;
            tbody.insertBefore(row, tbody.firstChild);
        }

        function cancelEdit(uid) {
            const row = document.getElementById(`row-${uid}`);

            // Get input values
            const nameInput = document.getElementById(`name-${uid}`);
            const emailInput = document.getElementById(`email-${uid}`);
            const contactInput = document.getElementById(`contact-${uid}`);

            const originalName = nameInput.defaultValue;
            const originalEmail = emailInput.defaultValue;
            const originalContact = contactInput.defaultValue;

            // Restore original data
            row.querySelector('.name').textContent = originalName;
            row.querySelector('.email').textContent = originalEmail;
            row.querySelector('.contact').textContent = originalContact;

            // Restore Edit and Delete buttons
            row.querySelector('.actions').innerHTML = `
                <button class="btn edit-btn" onclick="editRow(${uid})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn delete-btn" onclick="deleteRow(${uid})">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            `;

            currentEditingRow = null;
        }

        function updateRow(uid) {
            // Get input values
            const nameInput = document.getElementById(`name-${uid}`);
            const emailInput = document.getElementById(`email-${uid}`);
            const contactInput = document.getElementById(`contact-${uid}`);

            const updatedName = nameInput.value.trim();
            const updatedEmail = emailInput.value.trim();
            const updatedContact = contactInput.value.trim();

            // Basic validation
            if (updatedName === "" || updatedEmail === "" || updatedContact === "") {
                alert("All fields are required.");
                return;
            }

            // Email format validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(updatedEmail)) {
                alert("Please enter a valid email address.");
                return;
            }

            // Contact number validation
            const contactPattern = /^\+?[0-9]{7,15}$/;
            if (!contactPattern.test(updatedContact)) {
                alert("Please enter a valid contact number (7-15 digits, may start with '+').");
                return;
            }

            // Send AJAX request to update the moderator
            fetch('update_moderator.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    UID: uid,
                    Username: updatedName,
                    Email: updatedEmail,
                    ContactNumber: updatedContact
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Moderator account updated successfully.');

                    // Update the table with new data
                    const row = document.getElementById(`row-${uid}`);
                    row.querySelector('.name').textContent = updatedName;
                    row.querySelector('.email').textContent = updatedEmail;
                    row.querySelector('.contact').textContent = updatedContact;

                    // Restore Edit and Delete buttons
                    row.querySelector('.actions').innerHTML = `
                        <button class="btn edit-btn" onclick="editRow(${uid})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn delete-btn" onclick="deleteRow(${uid})">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    `;

                    // Move the updated row back to its original position (alphabetical order)
                    const tbody = row.parentElement;
                    const allRows = Array.from(tbody.querySelectorAll('tr')).slice(1); // Exclude header

                    // Find the correct position based on Username
                    const index = allRows.findIndex(r => {
                        const name = r.querySelector('.name').textContent.toLowerCase();
                        return name > updatedName.toLowerCase();
                    });

                    if (index === -1) {
                        tbody.appendChild(row);
                    } else {
                        tbody.insertBefore(row, allRows[index]);
                    }

                    currentEditingRow = null;
                } else {
                    alert('Error updating moderator: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred while updating the moderator account.');
            });
        }

        function deleteRow(uid) {
            if (!confirm("Are you sure you want to delete this moderator account?")) {
                return;
            }

            // Send AJAX request to delete the moderator
            fetch('delete_moderator.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    UID: uid
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Moderator account deleted successfully.');

                    // Remove the row from the table
                    const row = document.getElementById(`row-${uid}`);
                    if (row) {
                        row.remove();
                    }
                } else {
                    alert('Error deleting moderator: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred while deleting the moderator account.');
            });
        }
    </script>

</body>
</html>
