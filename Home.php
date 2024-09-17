<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <link rel="stylesheet" href="css/home.css">
  <link rel="stylesheet" href="css/post.css">
</head>

<style>
  #map {
    height: 80%;
  }
</style>

<script>
  (g => {
    var h, a, k, p = "The Google Maps JavaScript API",
      c = "google",
      l = "importLibrary",
      q = "__ib__",
      m = document,
      b = window;
    b = b[c] || (b[c] = {});
    var d = b.maps || (b.maps = {}),
      r = new Set,
      e = new URLSearchParams,
      u = () => h || (h = new Promise(async (f, n) => {
        await (a = m.createElement("script"));
        e.set("libraries", [...r] + "");
        for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
        e.set("callback", c + ".maps." + q);
        a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
        d[q] = f;
        a.onerror = () => h = n(Error(p + " could not load."));
        a.nonce = m.querySelector("script[nonce]")?.nonce || "";
        m.head.append(a)
      }));
    d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
  })({
    key: "AIzaSyB_B0Ud1mIL6Ln66nSCnITXRMDV1c3bssc",
    v: "weekly",
    // Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
    // Add other bootstrap parameters as needed, using camel case.
  });
</script>

<script>
  let map;
  let gpsLocation = null;

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, showError);
  } else {
    console.log("Geolocation is not supported by this browser.");
  }
  async function showPosition(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;
    const accuracy = position.coords.accuracy;

    const {
      Map
    } = await google.maps.importLibrary("maps");
    const {
      AdvancedMarkerElement
    } = await google.maps.importLibrary("marker");
    gpsLocation = {
      lat: latitude,
      lng: longitude
    }
    new AdvancedMarkerElement({
      map: map,
      position: gpsLocation,
      title: "GPS Location",
    });
  }

  function showError(error) {
    switch (error.code) {
      case error.PERMISSION_DENIED:
        console.log("User denied the request for Geolocation.");
        break;
      case error.POSITION_UNAVAILABLE:
        console.log("Location information is unavailable.");
        break;
      case error.TIMEOUT:
        console.log("The request to get user location timed out.");
        break;
      case error.UNKNOWN_ERROR:
        console.log("An unknown error occurred.");
        break;
    }
  }


  async function initMap() {
    // The location of Uluru
    const position = {
      lat: 6.885497678560704,
      lng: 79.86034329536008
    };
    // Request needed libraries.
    //@ts-ignore
    const {
      Map
    } = await google.maps.importLibrary("maps");
    const {
      AdvancedMarkerElement
    } = await google.maps.importLibrary("marker");

    // The map, centered at Uluru
    map = new Map(document.getElementById("map"), {
      zoom: 10,
      center: position,
      mapId: "DEMO_MAP_ID",
    });

    new AdvancedMarkerElement({
      map: map,
      position: position,
      title: "Post Location",
    });
  }

  initMap();
</script>

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

          <div class="post">
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

          <div class="post">
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

          <div class="post">
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

          <div class="post">
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
        <div class="map" id="map">Map</div>
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