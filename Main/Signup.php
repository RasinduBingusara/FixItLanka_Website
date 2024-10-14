<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="../CSS/Signup.css">
</head>

<body>

    <form id="errorForm" action="Signup.html" style="display:inline;">
        <input type="hidden" name="errorMsg" id="errorMsg">
    </form>

    <div class="container">
        <h2>Registration Form</h2>

        <form method="post" action="SignUpValidate.php" onsubmit="return validateForm()">
            <div class="form-group">
                <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                <label for="firstName" class="label">First Name</label>
            </div>
            <div class="form-group">
                <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                <label for="lastName" class="label">Last Name</label>
            </div>
            <div class="form-group"> 
                <input type="email" id="email" name="email" placeholder="Email" required>
                <label for="email" class="label">Email</label>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <label for="password" class="label">Password</label>
            </div>
            <div class="form-group">
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                <label for="confirmPassword" class="label">Confirm Password</label>
            </div>
            <div class="form-group">
                <input type="tel" id="contactNumber" name="contactNumber" placeholder="Contact Number" required>
                <label for="contactNumber" class="label">Contact Number</label>
            </div>
            <div class="error-message" id="error-container">
                <label for="error" id="error-message">
                    <?php 
                    echo isset($_GET["errorMsg"]) ? htmlspecialchars($_GET["errorMsg"]) : ''; 
                    ?>
                </label>
            </div>
            <div class="link">
                <span><a href="Login.php"> Already have an account? Login</a></span>
            </div>
            <button type="submit" class="btn">SIGN UP</button>
        </form>
    </div>

    <script>
        function validateForm() {
            let firstName = document.getElementById("firstName").value;
            let lastName = document.getElementById("lastName").value;
            let email = document.getElementById("email").value;
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirmPassword").value;
            let contactNumber = document.getElementById("contactNumber").value;

            const emailReg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            const phoneReg = /^(7\d{8}|07\d{8}|\+947\d{8})$/;
            const passwordReg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (firstName.length < 5 || firstName.length > 15) {
                showAlert("First Name must have 5-15 characters");
                return false;
            } else if (lastName.length < 5 || lastName.length > 15) {
                showAlert("Last Name must have 5-15 characters");
                return false;
            } else if (!emailReg.test(email)) {
                showAlert("Invalid Email Address");
                return false;
            } else if (!passwordReg.test(password)) {
                showAlert(`Password must meet the following criteria:
                - At least 8 characters long
                - Contains at least one uppercase letter
                - Contains at least one lowercase letter
                - Contains at least one digit
                - Contains at least one special character (e.g., !@#$%^&*)`);
                return false;
            } else if (password !== confirmPassword) {
                showAlert("Password and Confirm Password must be the same");
                return false;
            } else if (!phoneReg.test(contactNumber)) {
                showAlert("Invalid Phone Number");
                return false;
            } else {
                return true;
            }
        }

        function showAlert(message) {
            document.getElementById("errorMsg").value = message;
            document.getElementById("error-message").textContent = message;
            checkErrorMessage();
        }

        // JavaScript to show label when input is filled
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function () {
                const label = this.nextElementSibling;
                if (this.value.trim() !== '') {
                    label.classList.add('show');
                } else {
                    label.classList.remove('show');
                }
            });

            // JavaScript to handle placeholder disappearance on focus
            input.addEventListener('focus', function () {
                this.dataset.placeholder = this.placeholder; // Store the current placeholder
                this.placeholder = ''; // Clear the placeholder
            });

            input.addEventListener('blur', function () {
                this.placeholder = this.dataset.placeholder; // Restore the placeholder if empty
            });
        });

        // JavaScript to display the error message container only if it contains text
        const errorMessage = document.getElementById('error-message');
        const errorContainer = document.getElementById('error-container');

        // Check if error message has text and display the container if true
        function checkErrorMessage() {
            if (errorMessage.textContent.trim() !== '') {
                errorContainer.style.display = 'flex';
            } else {
                errorContainer.style.display = 'none';
            }
        }

        // Initial check and setup a mutation observer to watch for changes in the error message
        checkErrorMessage();

        // Mutation Observer to detect changes in the error message text
        const observer = new MutationObserver(checkErrorMessage);
        observer.observe(errorMessage, { childList: true, subtree: true });
    </script>
</body>

</html>
