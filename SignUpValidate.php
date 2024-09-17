<?php
include("Database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["contactNumber"])) {

  $sqlVal = "SELECT UID FROM useraccount WHERE Email = '" . $_POST["email"] . "'";
  $result = $conn->query($sqlVal);

  if ($result->num_rows > 0) {
    echo '<form id="errorForm" action="Signup.php" style="display:inline;">';
    echo '<input type="hidden" name="errorMsg" value="Email Address Already Exist">';
    echo '</form>';
?>
    <script>
      document.getElementById("errorForm").submit();
    </script>
    <?php
  } else {

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $currentTimestamp = time();
    $currentDateTime = date('Y-m-d H:i:s', $currentTimestamp);
    $username = $_POST["firstName"] . $_POST["lastName"];

    $sql = "INSERT INTO useraccount(Category_ID, Area_ID, Username, Password, Email, ContactNumber, DOB, ProfilePicture, UserType, Acc_Status, Created_At) VALUES (1,1,'" . $username . "','" . $password . "','" . $_POST["email"] . "','" . $_POST["contactNumber"] . "','2024-09-11','','User','Active','" . $currentDateTime . "')";

    if (mysqli_query($conn, $sql)) {

      echo "<script>
        setTimeout(function() {
          window.location.href = 'login.php';
        }, 1000);
      </script>";
    } else {
      echo '<form id="errorForm" action="Signup.php" style="display:inline;">';
    echo '<input type="hidden" name="errorMsg" value=""Error Creating Account">';
    echo '</form>';
    ?>
      <script>
        document.getElementById("errorForm").submit();
      </script>
<?php
    }
  }
}
