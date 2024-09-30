<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post Page</title>
    <link rel="stylesheet" href="css/CreatePost.css">
</head>

<body>

    <!-- Include the navigation bar -->
    <?php include 'NavigationBar.php'; ?>

    <div class="home-page-container">
        <!-- Main Content -->
        <div class="main-content">
            <div class="create-post-container">
                <h1>Create Post Page</h1>
                <div class="create-post-content">
                    <div class="image-section">
                        <div class="image-container">
                            Image
                        </div>
                    </div>
                    <div class="right-panel">
                        <div class="dropdown">
                            <label for="area">Area</label>
                            <select id="area">
                                <option>Select Area</option>
                            </select>
                        </div>
                        <div class="dropdown">
                            <label for="category">Category</label>
                            <select id="category">
                                <option>Select Category</option>
                            </select>
                        </div>
                        <div class="dropdown">
                            <label for="visibility">Visibility</label>
                            <select id="visibility">
                                <option>Select Visibility</option>
                            </select>
                        </div>
                        <div class="toggle">
                            <label for="anonymous">Anonymous</label>
                            <input type="checkbox" id="anonymous">
                        </div>
                        <label for="description">Description</label>
                        <div class="editor-container">
                            <textarea id="description" placeholder="Rich text editor."></textarea>
                            <div class="editor-toolbar">
                                <button>ADD Image</button>
                                <button>ADD ➔</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
