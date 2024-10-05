<link rel="stylesheet" href="css/AllPost.css">
<style>
  /* Modal Overlay */
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  /* Modal Content */
  .modal-content {
    background-color: #fff;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
  }

  /* Close Button */
  .close-button {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 24px;
    color: #333;
    cursor: pointer;
  }

  /* Modal Header */
  .modal-content h2 {
    margin: 0;
    padding: 15px 20px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 20px;
    color: #333;
  }

  /* Comments Section */
  .comments-section {
    flex: 1;
    padding: 15px 20px;
    overflow-y: auto;
  }

  /* Individual Comment */
  .comment {
    display: flex;
    margin-bottom: 15px;
  }

  .comment:last-child {
    margin-bottom: 0;
  }

  /* Comment User Avatar */
  .comment .avatar {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background-color: #ccc;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 15px;
  }

  .comment .avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Comment Content */
  .comment .comment-content {
    flex: 1;
  }

  .comment .comment-content .comment-author {
    font-weight: bold;
    color: #007BFF;
    margin-bottom: 5px;
  }

  .comment .comment-content .comment-text {
    font-size: 14px;
    color: #555;
  }

  /* New Comment Input */
  .new-comment-container {
    border-top: 1px solid #e0e0e0;
    padding: 15px 20px;
  }

  .new-comment-container textarea {
    width: 100%;
    height: 80px;
    padding: 10px;
    font-size: 14px;
    resize: none;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  .new-comment-container button {
    margin-top: 10px;
    padding: 10px 20px;
    background-color: #007BFF;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .new-comment-container button:hover {
    background-color: #0056b3;
  }

  /* Scrollbar Styling */
  .comments-section::-webkit-scrollbar {
    width: 8px;
  }

  .comments-section::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .comments-section::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .comments-section::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .footer-btn.voted {
    background-color: #e0e0e0;
    /* Change to your preferred highlight color */
  }
</style>
<?php
// $postUsername;
// $postDescription;
// $postImage;
// $totalUpVotes;
// $totalDownVotes;
// $totalComments;
// $PID;
// $UID;
?>
<div class="post-card" data-pid="<?php echo $PID; ?>">
  <div class="post-header">
    <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">
    <div class="user-info">
      <span class="user-name"><?php echo $postUsername; ?></span>
      <span class="post-options">•••</span>
    </div>
  </div>
  <div class="post-incontent">
    <p class="post-text"><?php echo $postDescription; ?></p>

    <?php
    // Display image if it exists
    if (!empty($postImage)) {
      echo '<div class="image-container">';
      echo '<img src="' . htmlspecialchars($postImage) . '" alt="Post Image">';
      echo '</div>';
    }
    ?>
  </div>
  <div class="post-footer">
    <button class="footer-btn upvote-button" onclick="updateVotes(1,<?php echo $PID ?>,<?php echo $UID ?>)">
      <img src="pics/like.jpg" alt="Like" class="btn-icon">
      <span class="btn-text upvote-count"><?php echo $totalUpVotes; ?></span>
    </button>
    <button class="footer-btn downvote-button" onclick="updateVotes(0,<?php echo $PID ?>,<?php echo $UID ?>)">
      <img src="pics/dislike.jpg" alt="Dislike" class="btn-icon">
      <span class="btn-text downvote-count"><?php echo $totalDownVotes; ?></span>
    </button>
    <button class="footer-btn" onclick="popUpCommentPanel(<?php echo $PID ?>,<?php echo $UID ?>)">
      <img src="pics/comment.jpg" alt="Comment" class="btn-icon">
      <span class="btn-text"><?php echo $totalComments; ?></span>
    </button>
    <button class="footer-btn">
      <img src="pics/share.jpg" alt="Share" class="btn-icon">
      <span class="btn-text">Share</span>
    </button>
  </div>

</div>

<script>
  function updateVotes(value, PID, UID) {
    if (!PID || !UID) {
      alert('You must be logged in to vote.');
      return;
    }

    // Prepare the data to send
    const data = {
      UID: UID,
      PID: PID,
      Vote_direction: value, // 1 for upvote, -1 for downvote
    };

    // Send an AJAX POST request to update the vote
    fetch('update_vote.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          // Update the vote counts on the page
          const postCard = document.querySelector(`.post-card[data-pid="${PID}"]`);
          postCard.querySelector('.upvote-count').textContent = result.totalUpVotes;
          postCard.querySelector('.downvote-count').textContent = result.totalDownVotes;

          // Update the UI to reflect the current vote
          currentVote[PID] = result.userVote; // Store the vote per post
          updateVoteButtons(PID);
        } else {
          alert('Error updating vote: ' + result.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }

  let currentVote = {}; // Keep track of votes per post

  function updateVoteButtons(PID) {
    const postCard = document.querySelector(`.post-card[data-pid="${PID}"]`);
    const upvoteButton = postCard.querySelector('.upvote-button');
    const downvoteButton = postCard.querySelector('.downvote-button');

    // Reset button styles
    upvoteButton.classList.remove('voted');
    downvoteButton.classList.remove('voted');

    if (currentVote[PID] === 1) {
      upvoteButton.classList.add('voted');
    } else if (currentVote[PID] === -1) {
      downvoteButton.classList.add('voted');
    }
  }


  function popUpCommentPanel(PostID, UserID) {
    // Replace with your actual PID and UID
    const PID = PostID; // Insert the Post ID here
    const UID = UserID; // Insert the User ID here

    if (PID === '' || UID === '') {
      alert('Post ID and User ID must be provided.');
      return;
    }

    // Create the modal element
    const modal = document.createElement('div');
    modal.classList.add('modal');

    modal.innerHTML = `
    <div class="modal-content">
  <span class="close-button">&times;</span>
  <h2>Comments</h2>
  <div class="comments-section" id="commentsSection">
    <!-- Comments will be dynamically loaded here -->
    <!-- Example of a comment -->
    <!--
    <div class="comment">
      <div class="avatar">
        <img src="path_to_avatar_image" alt="User Avatar">
      </div>
      <div class="comment-content">
        <p class="comment-author">Username</p>
        <p class="comment-text">This is a comment text.</p>
      </div>
    </div>
    -->
  </div>
  <div class="new-comment-container">
    <textarea id="newComment" placeholder="Write your comment..."></textarea>
    <button id="submitComment">Submit</button>
  </div>
</div>
  `;

    document.body.appendChild(modal);

    // Load existing comments
    loadComments(PID);

    // Event listeners
    modal.querySelector('.close-button').addEventListener('click', function() {
      document.body.removeChild(modal);
    });

    modal.querySelector('#submitComment').addEventListener('click', function() {
      const commentText = document.getElementById('newComment').value.trim();
      if (commentText !== '') {
        submitComment(PID, UID, commentText);
      }
    });
  }

  function loadComments(PID) {
    fetch('fetch_comments.php?PID=' + encodeURIComponent(PID))
      .then((response) => response.json())
      .then((comments) => {
        const commentsSection = document.getElementById('commentsSection');
        commentsSection.innerHTML = '';
        comments.forEach((comment) => {
          const commentDiv = document.createElement('div');
          commentDiv.classList.add('comment');
          commentDiv.innerHTML = `
          <a href="#"><strong>${comment.username}</strong></a>
          <p style="padding-left: 20px;">${comment.text}</p>
        `;
          commentsSection.appendChild(commentDiv);
        });
      })
      .catch((error) => {
        console.error('Error loading comments:', error);
      });
  }

  function submitComment(PID, UID, commentText) {
    const data = {
      UID: UID,
      PID: PID,
      Comment_text: commentText,
    };

    fetch('submit_comment.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          // Reload comments
          loadComments(PID);
          // Clear the textarea
          document.getElementById('newComment').value = '';
        } else {
          alert('Error submitting comment: ' + result.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }
</script>