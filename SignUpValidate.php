<?php
include("Database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["username"]) && isset($_POST["phone"]) && isset($_POST["profilepicture"])) {

  $sqlVal = "SELECT UID FROM useraccounts WHERE Email = '" . $_POST["email"] . "'";

  $result = $conn->query($sqlVal);

  if ($result->num_rows > 0) {
    echo "<script>alert('Email Address Already Exist');</script>";
  } else {

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $currentTimestamp = time();
    $currentDateTime = date('Y-m-d H:i:s', $currentTimestamp);

    $sql = "INSERT INTO useraccounts(CID, Username, Password, Email, ContactNumber, DOB, Area, ProfilePicture, UserType, Acc_Status, Created_At) VALUES ('2','" . $_POST["username"] . "','" . $password . "','" . $_POST["email"] . "','" . $_POST["phone"] . "','2024-09-11','Colombo','','User','Active','" . $currentDateTime . "')";

    if (mysqli_query($conn, $sql)) {

      echo "<script>
        setTimeout(function() {
          window.location.href = 'SignUp.php';
        }, 1000);
      </script>";
    } else {
      echo "<script>alert('Error Creating Account');</script>";
    }
  }
}
