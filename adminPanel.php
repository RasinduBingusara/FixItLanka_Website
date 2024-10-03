<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/adminPanel.css">
    <title>Admin Panel</title>
</head>
<body>
    <!-- Include AdminNavigationBar -->
    <?php include 'AdminNavigationBar.php'; ?>

    <!-- Main Content Area -->
    <div class="admin-panel">
        <main class="content">
            <div class="header">
                <div class="hamburger" id="hamburger">&#9776;</div>
                <h1>Admin Panel</h1>
            </div>

            <div class="charts">
                <div class="chart line-chart">
                    <!-- Line chart placeholder -->
                </div>
                <div class="chart donut-chart">
                    <!-- Donut chart placeholder -->
                </div>
                <div class="chart bar-chart">
                    <!-- Bar chart placeholder -->
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar visibility when hamburger is clicked
        document.getElementById('hamburger').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        });
    </script>
</body>
</html>
