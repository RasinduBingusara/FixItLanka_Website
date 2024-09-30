<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Post Component</title>
  <style>
    /* General Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Helvetica Neue', Arial, sans-serif;
}

body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f0f2f5;
}

/* Post Card Container */
.post-card {
  width: 420px;
  background-color: #ffffff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
  overflow: hidden;
}

.post-card:hover {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* Post Header */
.post-header {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e0e0e0;
}

.profile-img {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  margin-right: 12px;
  border: 2px solid #e0e0e0;
}

.user-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex: 1;
}

.user-name {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.post-options {
  font-size: 20px;
  cursor: pointer;
  color: #999;
  transition: color 0.3s;
}

.post-options:hover {
  color: #666;
}

/* Post Content */
.post-content {
  margin-bottom: 15px;
}

.post-text {
  font-size: 14px;
  color: #444;
  line-height: 1.5;
  margin-bottom: 15px;
}

.see-more {
  color: #1a73e8;
  cursor: pointer;
  font-weight: 500;
  transition: color 0.3s;
}

.see-more:hover {
  color: #1558b0;
}

/* Image Carousel */
.image-carousel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

.carousel-btn {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #999;
  transition: color 0.3s, transform 0.3s;
}

.carousel-btn:hover {
  color: #333;
  transform: scale(1.1);
}

.image-container {
  flex: 1;
  height: 220px;
  background-color: #e9ecef;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin: 0 8px;
}

.image-container img {
  max-width: 100%;
  max-height: 100%;
  border-radius: 8px;
}

/* Post Footer */
.post-footer {
  display: flex;
  justify-content: space-around;
  align-items: center;
  margin-top: 10px;
}

/* Footer Buttons */
.footer-btn {
  display: flex;
  align-items: center;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
  font-size: 14px;
  transition: color 0.3s, background-color 0.3s;
  padding: 5px 10px;
  border-radius: 5px;
}

.footer-btn:hover {
  color: #1a73e8;
  background-color: #f0f4ff;
}

.btn-icon {
  width: 16px;
  height: 16px;
  fill: currentColor;
}

.btn-text {
  font-size: 14px;
  color: #777;
}

/* Liked/Unliked States */
.liked {
  color: #1a73e8;
}

.unliked {
  color: #e63946;
}
  </style>

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
      <button class="footer-btn" id="like-btn">
        <img src="pics/like.jpg" alt="Like" class="btn-icon">
        <span class="btn-text">1.5k</span>
      </button>
      <button class="footer-btn" id="unlike-btn">
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

  <script>
    const likeButton = document.getElementById('like-btn');
    const unlikeButton = document.getElementById('unlike-btn');

    likeButton.addEventListener('click', () => {
      if (!likeButton.classList.contains('liked')) {
        likeButton.classList.add('liked');
        unlikeButton.classList.remove('unliked');
      } else {
        likeButton.classList.remove('liked');
      }
    });

    unlikeButton.addEventListener('click', () => {
      if (!unlikeButton.classList.contains('unliked')) {
        unlikeButton.classList.add('unliked');
        likeButton.classList.remove('liked');
      } else {
        unlikeButton.classList.remove('unliked');
      }
    });
  </script>
</body>

</html>