<style>
  /* Sidebar Styles */
  .sidebar {
      width: 175px;
      background-color: #f7f8fa;
      padding: 20px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      transition: transform 0.3s ease;
  }

  .sidebar h2 {
      font-size: 20px;
      color: #333;
      margin-bottom: 20px;
  }

  .menu {
      list-style: none;
      padding: 0;
      margin: 0;
  }

  .menu-item {
      padding: 10px 15px;
      color: #555;
      cursor: pointer;
      border-radius: 4px;
      margin-bottom: 10px;
      transition: background-color 0.3s;
      display: flex;
      align-items: center;
  }

  .circle-btn {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: black;
      text-decoration: none;
  }

  .menu-item i {
      margin-right: 10px;
  }

  .menu-item:hover,
  .menu-item.active {
      background-color: #e0e0e0;
  }

  .menu.bottom {
      margin-top: auto;
  }

  /* Top Navigation Styles */
  .top-nav {
      display: none; /* Hidden by default on desktop */
      justify-content: space-between;
      align-items: center;
      background-color: #f7f8fa;
      padding: 15px;
      border-bottom: 1px solid #e0e0e0;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
  }

  .top-nav h1 {
      margin: 0;
      font-size: 24px;
      color: #333;
  }

  /* Hamburger Icon */
  .hamburger {
      font-size: 30px;
      cursor: pointer;
      z-index: 999;
  }

  /* Adjust main content to avoid overlap with the sidebar */
  .content {
      padding-top: 60px;
      padding-left: 220px; /* Adjusted for sidebar width */
  }

  /* Media query for mobile view */
  @media screen and (max-width: 768px) {
      .top-nav {
          display: flex; /* Show top nav with hamburger on mobile */
      }

      .sidebar {
          transform: translateX(-100%); /* Hidden sidebar by default */
      }

      .sidebar.open {
          transform: translateX(0); /* Show sidebar when open */
      }

      .content {
          padding-left: 0; /* Adjust content padding on mobile */
      }
  }
</style>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <br> <br> <br>
    <h2>Moderator Panel</h2>
    <ul class="menu">
        <li class="menu-item">
            <a href="ModeratorPanel.php" class="circle-btn">DashBoard</a> 
        </li>
        <li class="menu-item">
            <a href="ModeraterAddSuperVisorAcc.php" class="circle-btn">Add Supervisor</a>  
        </li>
       
    </ul>
    <ul class="menu bottom">
        <li class="menu-item">
            <i class="fas fa-cog"></i> Settings
        </li>
        <li class="menu-item">
            <i class="fas fa-sign-out-alt"></i> Sign Out
        </li>
    </ul>
</aside>

<!-- Top Navigation with Hamburger Icon (Only Visible on Mobile) -->
<div class="top-nav">
    <div class="hamburger" id="hamburger">&#9776;</div>
</div>

<!-- Script to toggle sidebar visibility -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
      var hamburger = document.getElementById('hamburger');
      var sidebar = document.getElementById('sidebar');

      // Toggle sidebar visibility on hamburger click
      hamburger.addEventListener('click', function() {
          sidebar.classList.toggle('open');
      });
  });
</script>
