<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Dashboard Page</title>
    <link rel="stylesheet" href="css/superVisorPanel.css">
</head>
<body>

    <!-- Include SupervisorNavBar -->
    <?php include 'ModeratorNavBar.php'; ?>

    <div class="header">Moderator Dashboard Page</div>

    <div class="panel-container">
        <!-- Left Side with Posts and Info -->
        <div class="posts-section">
            <!-- Post Item 1 -->
            <div class="post-item">
                <div class="post-image">
                    <div class="post-label">Post</div>
                </div>
                <div class="info-section">
                    <div class="user-info">
                        <span class="icon">👤</span>
                        <span>User account</span>
                    </div>
                    <div class="description">
                        <span class="icon">≡</span>
                        <span>Description</span>
                    </div>
                    <div class="status">
                        <label for="status-select">Status</label>
                        <select id="status-select">
                            <option>Dropdown text</option>
                            <option>Option 1</option>
                            <option>Option 2</option>
                        </select>
                        <a href="" id="done" class="circle-btn">Done</a>  
                    </div>
                </div>
            </div>

            <!-- Duplicate post items for demonstration -->
            <div class="post-item">
                <div class="post-image">
                    <div class="post-label">Post</div>
                </div>
                <div class="info-section">
                    <div class="user-info">
                        <span class="icon">👤</span>
                        <span>User account</span>
                    </div>
                    <div class="description">
                        <span class="icon">≡</span>
                        <span>Description</span>
                    </div>
                    <div class="status">
                        <label for="status-select">Status</label>
                        <select id="status-select">
                            <option>Dropdown text</option>
                            <option>Option 1</option>
                            <option>Option 2</option>
                        </select>
                        <a href="" id="done" class="circle-btn">Done</a> 
                    </div>
                </div>
            </div>

            <div class="post-item">
                <div class="post-image">
                    <div class="post-label">Post</div>
                </div>
                <div class="info-section">
                    <div class="user-info">
                        <span class="icon">👤</span>
                        <span>User account</span>
                    </div>
                    <div class="description">
                        <span class="icon">≡</span>
                        <span>Description</span>
                    </div>
                    <div class="status">
                        <label for="status-select">Status</label>
                        <select id="status-select">
                            <option>Dropdown text</option>
                            <option>Option 1</option>
                            <option>Option 2</option>
                        </select>
                        <a href="" id="done" class="circle-btn">Done</a> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side with Map -->
        <!-- <div class="map-section">
            <div class="map-label">Map</div>
        </div> -->
    </div>

</body>
</html>
