<?php
session_start();
if (!isset($_SESSION['UserData'])) {
    // Redirect to login page
    header("Location: Login.php");
    exit();
}
$Username = $_SESSION["UserData"][2];
?>

<style>
    /* General Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
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
        cursor: pointer;
    }

    .navbar-buttons {
        display: flex;
        gap: 10px;
    }

    .hamburger {
        display: none;
        font-size: 28px;
        cursor: pointer;
        color: white;
        background: none;
        border: none;
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
        cursor: pointer;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background-color: #1d3557;
        display: flex;
        flex-direction: column;
        padding: 20px;
        position: fixed;
        left: 0;
        top: 65px;
        height: calc(100vh - 65px);
        color: white;
        box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        overflow-y: auto;
        z-index: 200;
    }

    .nav-menu {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
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
        padding: 10px;
        border-radius: 10px;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.3s ease;
    }

    .nav-button:hover {
        background-color: #457b9d;
    }

    .icon {
        font-size: 20px;
    }

    /* Hide sidebar only on mobile */
    @media (max-width: 768px) {
        .hamburger {
            display: inline;
        }

        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }
    }
</style>

<!-- navigation.html -->
<nav class="navbar">
    <div class="logo" onclick="window.location.href='Home.php';">FIXITLANKA</div>
    <div class="navbar-buttons">
        <button class="hamburger" id="hamburger">&#9776;</button>
    </div>
    <div class="profile">
        <img src="pics/defaultProfile.png" alt="Profile Icon" class="profile-icon">
        <span class="profile-name" onclick="window.location.href='Profile.php';"><?php echo$Username ?></span>
    </div>
</nav>

<!-- Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
    <nav class="nav-menu">
        <div class="top-nav">
            <button class="nav-button" onclick="window.location.href='MapPage.php';">
                <span class="icon">📌</span> Map
            </button>
            <button class="nav-button" onclick="window.location.href='CreatePost.php';">
                <span class="icon">📝</span> Post
            </button>
            <button class="nav-button" onclick="window.location.href='AllPost.php';">
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
            <button class="nav-button" onclick="window.location.href='Signout.php';">
                <span class="icon">🚪</span> Sign Out
            </button>
        </div>
    </nav>
</aside>

<script>
    // Toggle sidebar visibility on mobile
    document.getElementById('hamburger').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>