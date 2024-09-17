<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <link rel="stylesheet" href="css/home.css">
</head>

<body>

  <!-- Fixed Navbar with Hamburger Icon -->
  <nav class="navbar">
    <div class="logo">FIXITLANKA</div>
    <div class="search-container">
      <input type="text" placeholder="Search" class="search-bar">
      <button class="search-button">🔍</button>
    </div>
    <label for="menu-toggle" class="hamburger">&#9776;</label>
    <div class="profile">
      <img src="pics/defaultProfile.png" alt="Profile Icon" class="profile-icon">
      <span class="profile-name">Username</span>
    </div>
  </nav>

  <div class="home-page-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
      <nav class="nav-menu">
        <div class="top-nav">
          <button class="nav-button">
            <span class="icon">📌</span> Map
          </button>
          <button class="nav-button">
            <span class="icon">📝</span> Post
          </button>
          <button class="nav-button">
            <span class="icon">🖥️</span> Post View
          </button>
        </div>
        <div class="bottom-nav">
          <button class="nav-button">
            <span class="icon">📜</span> Terms and Policies
          </button>
          <button class="nav-button">
            <span class="icon">⚙️</span> Settings
          </button>
          <button class="nav-button">
            <span class="icon">🚪</span> Sign Out
          </button>
        </div>
      </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <div class="content">
        <div class="posts-container">
          <div class="post">Post 1</div>
          <div class="post">Post 2</div>
          <div class="post">Post 3</div>
          <div class="post">Post 4</div>
          <div class="post">Post 5</div>
          <div class="post">Post 6</div>
          <div class="post">Post 7</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
          <div class="post">Post 8</div>
        </div>
        <div class="map">Map</div>
      </div>
    </main>
  </div>

  <script>
    // JavaScript to toggle sidebar visibility on mobile
    document.querySelector('.hamburger').addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('active');
    });
  </script>

</body>

</html>