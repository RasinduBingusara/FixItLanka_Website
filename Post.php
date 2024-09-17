<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/post.css">
  <title>Post Component</title>
</head>

<body>
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
</body>

</html>