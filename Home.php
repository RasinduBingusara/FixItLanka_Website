<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <style>
    /* General Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      background-color: #f4f4f4;
      color: #333;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #1d3557;
      color: white;
      padding: 15px 20px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .logo {
      font-size: 22px;
      font-weight: bold;
    }

    .navbar-buttons {
      display: flex;
      gap: 10px;
    }

    .hamburger,
    .map-toggle-btn {
      display: none;
      font-size: 28px;
      cursor: pointer;
      color: white;
      background: none;
      border: none;
    }

    .search-container {
      display: flex;
      align-items: center;
      background-color: white;
      border-radius: 20px;
      padding: 5px 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .search-bar {
      border: none;
      outline: none;
      padding: 8px;
      border-radius: 15px;
      font-size: 14px;
    }

    .search-button {
      background-color: #457b9d;
      color: white;
      border: none;
      padding: 8px 12px;
      margin-left: 5px;
      border-radius: 15px;
      cursor: pointer;
    }

    .profile {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .profile-icon {
      width: 30px;
      height: 30px;
      border-radius: 50%;
    }

    .profile-name {
      font-size: 14px;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #1d3557;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px;
      position: fixed;
      left: 0;
      top: 65px;
      height: calc(100vh - 65px);
      transition: transform 0.3s ease;
      overflow-y: auto;
      color: #ffffff;
      box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.1);
      border-radius: 0 12px 12px 0;
    }

    /* By default, no .inactive class here */
    .nav-menu {
      display: flex;
      flex-direction: column;
      height: 100%;
      justify-content: space-between;
    }

    .top-nav {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .bottom-nav {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .nav-button {
      background-color: transparent;
      color: white;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px;
      border-radius: 10px;
      transition: background-color 0.3s ease;
    }

    .nav-button:hover {
      background-color: #457b9d;
    }

    /* Main Content */
    .home-page-container {
      display: flex;
      flex: 1;
      padding-top: 70px;
    }

    .main-content {
      flex: 1;
      padding: 20px;
      margin-left: 270px;
      background-color: #f4f4f4;
    }

    .content {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .posts-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
      overflow-y: auto;
    }

    .post-card {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .post-header {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .profile-img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .user-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex: 1;
    }

    .user-name {
      font-size: 16px;
      font-weight: bold;
    }

    .post-options {
      font-size: 20px;
      cursor: pointer;
      color: #999;
    }

    .post-content {
      margin-bottom: 10px;
    }

    .post-text {
      font-size: 14px;
      color: #444;
      margin-bottom: 10px;
    }

    .see-more {
      color: #1a73e8;
      cursor: pointer;
      font-weight: bold;
    }

    .image-carousel {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .carousel-btn {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      color: #999;
    }

    .image-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .image-container img {
      max-width: 100%;
      border-radius: 8px;
    }

    .post-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 10px;
    }

    .footer-btn {
      background: none;
      border: none;
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .btn-icon {
      width: 20px;
      height: 20px;
      margin-right: 5px;
    }

    .btn-text {
      font-size: 14px;
    }

    /* Map */
    .map {
      display: none;
      width: 100%;
      height: 300px;
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .map.visible {
      display: block;
    }

    /* Responsive Design */
    @media (min-width: 768px) {

      .hamburger,
      .map-toggle-btn {
        display: none;
      }

      .content {
        flex-direction: row;
      }

      .posts-container {
        flex: 1;
      }

      .map {
        display: block;
        width: 500px;
        height: calc(100vh - 150px);
        position: sticky;
        top: 90px;
      }

      .sidebar {
        transform: translateX(0);
      }
    }

    @media (max-width: 768px) {

      .hamburger,
      .map-toggle-btn {
        display: inline;
      }

      .sidebar {
        transform: translateX(-100%);
        position: fixed;
        z-index: 999;
        width: 250px;
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
      }

      .map {
        position: fixed;
        width: 100%;
        height: 300px;
      }
    }
  </style>
</head>

<script src="script/MapAPI.js"></script>
<script>
  let map;

  async function initMap() {
    const {
      Map
    } = await google.maps.importLibrary("maps");

    map = new Map(document.getElementById("map"), {
      center: {
        lat: -34.397,
        lng: 150.644
      },
      zoom: 8,
    });
  }

  initMap();
</script>

<body>

  <!-- Fixed Navbar with Hamburger Icon and Map Toggle -->
  <nav class="navbar">
    <div class="logo">FIXITLANKA</div>
    <div class="navbar-buttons">
      <button class="hamburger">&#9776;</button>
      <button class="map-toggle-btn" id="mapToggle">🗺️ Map</button>
    </div>
    <div class="search-container">
      <input type="text" placeholder="Search" class="search-bar">
      <button class="search-button">🔍</button>
    </div>
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
        <div class="map" id="map">Map</div>
        <div class="posts-container">

          <div class="post-card">
            <div class="post-header">
              <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">
              <div class="user-info">
                <span class="user-name">John Doe</span>
                <span class="post-options">•••</span>
              </div>
            </div>
            <div class="post-content">
              <p class="post-text">
                Most fonts have a particular weight which corresponds to one of the numbers in common weight name mapping. However, some fonts, called variable fonts, can support a range of weights with a more or less fine <span class="see-more">See More...</span>
              </p>
              <div class="image-carousel">
                <button class="carousel-btn left-btn">◀</button>
                <div class="image-container">
                  <img src="pics/ali.jpg" alt="Post Image">
                </div>
                <button class="carousel-btn right-btn">▶</button>
              </div>
            </div>
            <div class="post-footer">
              <button class="footer-btn">
                <img src="pics/like.jpg" alt="Like" class="btn-icon">
                <span class="btn-text">1.5k</span>
              </button>
              <button class="footer-btn">
                <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">
                <span class="btn-text">520</span>
              </button>
              <button class="footer-btn">
                <img src="pics/comment.jpg" alt="Comment" class="btn-icon">
                <span class="btn-text">126</span>
              </button>
              <button class="footer-btn">
                <img src="pics/share.jpg" alt="Share" class="btn-icon">
                <span class="btn-text">86</span>
              </button>
            </div>
          </div>

          <div class="post-card">
            <div class="post-header">
              <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">
              <div class="user-info">
                <span class="user-name">John Doe</span>
                <span class="post-options">•••</span>
              </div>
            </div>
            <div class="post-content">
              <p class="post-text">
                Most fonts have a particular weight which corresponds to one of the numbers in common weight name mapping. However, some fonts, called variable fonts, can support a range of weights with a more or less fine <span class="see-more">See More...</span>
              </p>
              <div class="image-carousel">
                <button class="carousel-btn left-btn">◀</button>
                <div class="image-container">
                  <img src="pics/ali.jpg" alt="Post Image">
                </div>
                <button class="carousel-btn right-btn">▶</button>
              </div>
            </div>
            <div class="post-footer">
              <button class="footer-btn">
                <img src="pics/like.jpg" alt="Like" class="btn-icon">
                <span class="btn-text">1.5k</span>
              </button>
              <button class="footer-btn">
                <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">
                <span class="btn-text">520</span>
              </button>
              <button class="footer-btn">
                <img src="pics/comment.jpg" alt="Comment" class="btn-icon">
                <span class="btn-text">126</span>
              </button>
              <button class="footer-btn">
                <img src="pics/share.jpg" alt="Share" class="btn-icon">
                <span class="btn-text">86</span>
              </button>
            </div>
          </div>

          <div class="post-card">
            <div class="post-header">
              <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">
              <div class="user-info">
                <span class="user-name">John Doe</span>
                <span class="post-options">•••</span>
              </div>
            </div>
            <div class="post-content">
              <p class="post-text">
                Most fonts have a particular weight which corresponds to one of the numbers in common weight name mapping. However, some fonts, called variable fonts, can support a range of weights with a more or less fine <span class="see-more">See More...</span>
              </p>
              <div class="image-carousel">
                <button class="carousel-btn left-btn">◀</button>
                <div class="image-container">
                  <img src="pics/ali.jpg" alt="Post Image">
                </div>
                <button class="carousel-btn right-btn">▶</button>
              </div>
            </div>
            <div class="post-footer">
              <button class="footer-btn">
                <img src="pics/like.jpg" alt="Like" class="btn-icon">
                <span class="btn-text">1.5k</span>
              </button>
              <button class="footer-btn">
                <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">
                <span class="btn-text">520</span>
              </button>
              <button class="footer-btn">
                <img src="pics/comment.jpg" alt="Comment" class="btn-icon">
                <span class="btn-text">126</span>
              </button>
              <button class="footer-btn">
                <img src="pics/share.jpg" alt="Share" class="btn-icon">
                <span class="btn-text">86</span>
              </button>
            </div>
          </div>

          <div class="post-card">
            <div class="post-header">
              <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">
              <div class="user-info">
                <span class="user-name">John Doe</span>
                <span class="post-options">•••</span>
              </div>
            </div>
            <div class="post-content">
              <p class="post-text">
                Most fonts have a particular weight which corresponds to one of the numbers in common weight name mapping. However, some fonts, called variable fonts, can support a range of weights with a more or less fine <span class="see-more">See More...</span>
              </p>
              <div class="image-carousel">
                <button class="carousel-btn left-btn">◀</button>
                <div class="image-container">
                  <img src="pics/ali.jpg" alt="Post Image">
                </div>
                <button class="carousel-btn right-btn">▶</button>
              </div>
            </div>
            <div class="post-footer">
              <button class="footer-btn">
                <img src="pics/like.jpg" alt="Like" class="btn-icon">
                <span class="btn-text">1.5k</span>
              </button>
              <button class="footer-btn">
                <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">
                <span class="btn-text">520</span>
              </button>
              <button class="footer-btn">
                <img src="pics/comment.jpg" alt="Comment" class="btn-icon">
                <span class="btn-text">126</span>
              </button>
              <button class="footer-btn">
                <img src="pics/share.jpg" alt="Share" class="btn-icon">
                <span class="btn-text">86</span>
              </button>
            </div>
          </div>

          <div class="post-card">
            <div class="post-header">
              <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">
              <div class="user-info">
                <span class="user-name">John Doe</span>
                <span class="post-options">•••</span>
              </div>
            </div>
            <div class="post-content">
              <p class="post-text">
                Most fonts have a particular weight which corresponds to one of the numbers in common weight name mapping. However, some fonts, called variable fonts, can support a range of weights with a more or less fine <span class="see-more">See More...</span>
              </p>
              <div class="image-carousel">
                <button class="carousel-btn left-btn">◀</button>
                <div class="image-container">
                  <img src="pics/ali.jpg" alt="Post Image">
                </div>
                <button class="carousel-btn right-btn">▶</button>
              </div>
            </div>
            <div class="post-footer">
              <button class="footer-btn">
                <img src="pics/like.jpg" alt="Like" class="btn-icon">
                <span class="btn-text">1.5k</span>
              </button>
              <button class="footer-btn">
                <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">
                <span class="btn-text">520</span>
              </button>
              <button class="footer-btn">
                <img src="pics/comment.jpg" alt="Comment" class="btn-icon">
                <span class="btn-text">126</span>
              </button>
              <button class="footer-btn">
                <img src="pics/share.jpg" alt="Share" class="btn-icon">
                <span class="btn-text">86</span>
              </button>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>

  <script>
    // JavaScript to toggle sidebar visibility on mobile
    document.querySelector('.hamburger').addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('active');
    });

    // JavaScript to toggle map visibility on mobile
    document.getElementById('mapToggle').addEventListener('click', function() {
      document.getElementById('map').classList.toggle('visible');
    });
  </script>

</body>



</html>