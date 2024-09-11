<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<script>
  function validateForm() {

    $picID = document.getElementById("profilepicture").value;
    if ($picID == "") {
      showAlert("Choose a profile picture");
      return false;
    }

    
    firstname = document.getElementById("firstname").value;
    lastname = document.getElementById("lastname").value;
    email = document.getElementById("email").value;
    password = document.getElementById("password").value;
    rep_password = document.getElementById("confirm-password").value;
    phoneNumber = document.getElementById("phone").value;
    email_reg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    phone_reg = /^(7\d{8}|07\d{8}|\+947\d{8})$/;;
    password_reg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (15 < firstname.length < 5) {
      showAlert("Username must have 5-15 characters");
      return false;
    }
    if (15 < lastname.length < 5) {
      showAlert("Username must have 5-15 characters");
      return false;
    }
    if (!email_reg.test(email)) {
      showAlert("Invalid Email Address");
      return false;
    }
    if (!password_reg.test(password)) {
      showAlert(`Password must meet the following criteria:
    - At least 8 characters long
    - Contains at least one uppercase letter
    - Contains at least one lowercase letter
    - Contains at least one digit
    - Contains at least one special character (e.g., !@#$%^&*)`);
      return false;
    }
    if (password.length < 8) {
      showAlert("Password must have atleast 8 characters");
      return false;
    }
    if (!(password == rep_password)) {
      showAlert("Password and Confirm password must be same");
      return false;
    }
    if (!phone_reg.test(phoneNumber)) {
      showAlert("Invalid Phone Number");
      return false;
    }
  }

  function setUsername(){
    firstname = document.getElementById("firstname").value;
    lastname = document.getElementById("lastname").value;
    document.getElementById("username").value = firstname+" "+lastname;
    }
</script>

<body>
  <div>
    <h2>Sign Up</h2>
    <form name="loginForm" action="SignUpValidate.php" method="post" onsubmit="return validateForm()">
      <div>
        <label for="profile-picture">Profile Picture</label>
      </div>
      <input type="hidden" name="profilepicture" id="profilepicture">

      <div style="display: flex; gap: 10px;">
        <div>
          <label for="firstname">First Name</label>
          <input type="text" name="firstname" id="firstname" placeholder="Enter first name" required>
        </div>
        <div>
          <label for="lastname">Last Name</label>
          <input type="text" name="lastname" id="lastname" placeholder="Enter last name" required>
        </div>
        <input type="hidden" name="username" id="username" required>
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>
      </div>
      <div>
        <label for="confirm-password">Confirm Password</label>
        <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm password" required>
      </div>
      <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Enter email" required>
      </div>
      <div>
        <label for="phone">Phone Number</label>
        <input type="tel" name="phone" id="phone" placeholder="Enter phone number" required>
      </div>
      <a href="login.php">Already have an account? Log in</a>
      <input type="submit" value="Create Account" name="createAccount" onclick="setUsername()">
    </form>

  </div>
</body>

</html>