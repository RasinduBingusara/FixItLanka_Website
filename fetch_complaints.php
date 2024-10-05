<?php
include("Database.php");

if (isset($_POST['categoryID']) && !empty($_POST['categoryID'])) {
    $categoryID = $_POST['categoryID'];
    $selectedComplaintID = isset($_POST['selectedComplaintID']) ? $_POST['selectedComplaintID'] : '';

    // Fetch complaint types based on CategoryID
    $stmt = $conn->prepare("SELECT ComplaintID, Complaint FROM complainttype WHERE CID = ? ORDER BY Complaint ASC");
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate the complaint type options
    if ($result->num_rows > 0) {
        echo '<option value="">Select Complaint Type</option>';
        while ($row = $result->fetch_assoc()) {
            $complaintName = htmlspecialchars($row['Complaint'], ENT_QUOTES);
            $complaintID = $row['ComplaintID'];
            // Check if the complaint type was previously selected
            $selected = ($selectedComplaintID == $complaintID) ? ' selected' : '';
            echo '<option value="' . $complaintID . '"' . $selected . '>' . $complaintName . '</option>';
        }
    } else {
        echo '<option value="">No complaint types available</option>';
    }

    $stmt->close();
} else {
    echo '<option value="">Select Complaint Type</option>';
}

$conn->close();
?>
