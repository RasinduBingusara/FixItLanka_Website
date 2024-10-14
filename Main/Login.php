<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to FixItLanka</title>
    <link rel="stylesheet" href="../CSS/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Login to FixItLanka</h2>
        </div>

        <div class="divider">
            <span></span>
        </div>

        <form action="LoginValidate.php" method="post">
            <div class="input-group">
                <input type="text" id="email" name="email" placeholder="Email address" required>
                <label for="identifier" class="label">Email</label>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <label for="password" class="label">Password</label>
            </div>
            <div class="error-message" id="error-container">
                <label for="error" id="error-message"><?php echo isset($_GET["errorMsg"])? $_GET["errorMsg"]:''; ?></label>
            </div>
            <button type="submit" class="btn primary-btn">SIGN IN</button>
        </form>


        <div class="sign-up">
            <a href="Signup.php"> Don't have an account? Sign up</a>
        </div>
    </div>

    <script>
        // JavaScript to handle label and placeholder behavior
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const label = this.nextElementSibling;
                if (this.value.trim() !== '') {
                    label.classList.add('show');
                } else {
                    label.classList.remove('show');
                }
            });

            // Handle placeholder disappearance on focus
            input.addEventListener('focus', function() {
                this.dataset.placeholder = this.placeholder; // Store the current placeholder
                this.placeholder = ''; // Clear the placeholder
            });

            input.addEventListener('blur', function() {
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
        observer.observe(errorMessage, {
            childList: true,
            subtree: true
        });
    </script>
</body>

</html>