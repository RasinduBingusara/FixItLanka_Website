<?php
session_start();
include("Database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["password"])) {

  $sqlVal = "SELECT UID, Category_ID, RegionID, Username, Email, Password, ContactNumber, UserType, ProfilePicture, Acc_Status FROM useraccount WHERE Email = '" . $_POST["email"] . "'";

  $result = $conn->query($sqlVal);

  if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

      if (!password_verify($_POST["password"], $row["Password"])) {

        echo '<form id="errorForm" action="Login.php" style="display:inline;">';
        echo '<input type="hidden" name="errorMsg" value="Incorrect Username or Password">';
        echo '</form>';
?>
        <script>
          document.getElementById("errorForm").submit();
        </script>

      <?php

      } else if ($row["Acc_Status"] != "Active") {

        echo '<form id="errorForm" action="Login.php" style="display:inline;">';
        echo '<input type="hidden" name="errorMsg" value="Account has been suspened">';
        echo '</form>';
      ?>
        <script>
          document.getElementById("errorForm").submit();
        </script>
<?php
      } else {

        $_SESSION["UserData"] = array($row["UID"],  $row["Category_ID"], $row["Username"], $row["Email"], $row["ContactNumber"],  $row["Acc_Status"], $row["UserType"], base64_encode($row["ProfilePicture"]), $row["RegionID"]);
        $_SESSION["ProfilePicture"] = base64_encode($row["ProfilePicture"]);

        if($row["UserType"] == "Admin"){
          echo '<script>
        setTimeout(function() {
          window.location.href = "Admin/AdminPanel.php";
          }, 1000);
          </script>';
        }
        else if($row["UserType"] == "Moderator"){
          echo '<script>
        setTimeout(function() {
          window.location.href = "Moderator/ModeratorPanel.php";
          }, 1000);
          </script>';
        }
        else if($row["UserType"] == "Supervisor"){
          echo '<script>
        setTimeout(function() {
          window.location.href = "Supervisor/SupervisorPanel.php";
          }, 1000);
          </script>';
        }
        else{
          echo '<script>
          setTimeout(function() {
            window.location.href = "Home.php";
            }, 1000);
            </script>';
        }
       
      }
    }
  }
  else{
    echo '<form id="errorForm" action="Login.php" style="display:inline;">';
        echo '<input type="hidden" name="errorMsg" value="Incorrect Username or Password">';
        echo '</form>';
?>
        <script>
          document.getElementById("errorForm").submit();
        </script>

      <?php
  }
  
}
?>