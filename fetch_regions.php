<?php
include("Database.php");

if (isset($_POST['categoryID']) && !empty($_POST['categoryID'])) {
    $categoryID = $_POST['categoryID'];
    $selectedRegionID = isset($_POST['selectedRegionID']) ? $_POST['selectedRegionID'] : '';

    // Fetch regions based on CategoryID
    $stmt = $conn->prepare("SELECT RegionID, Region FROM region WHERE CID = ? ORDER BY Region ASC");
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate the regions options
    if ($result->num_rows > 0) {
        echo '<option value="">Select Region</option>';
        while ($row = $result->fetch_assoc()) {
            $regionName = htmlspecialchars($row['Region'], ENT_QUOTES);
            $regionID = $row['RegionID'];
            // Check if the region was previously selected
            $selected = ($selectedRegionID == $regionID) ? ' selected' : '';
            echo '<option value="' . $regionID . '"' . $selected . '>' . $regionName . '</option>';
        }
    } else {
        echo '<option value="">No regions available</option>';
    }

    $stmt->close();
} else {
    echo '<option value="">Select Region</option>';
}

$conn->close();
?>
