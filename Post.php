<link rel="stylesheet" href="css/AllPost.css">
<style>
  /* Dropdown Menu */
  .dropdown-menu {
    display: none;
    position: absolute;
    background-color: #fff;
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    border-radius: 5px;
    min-width: 150px;
    margin-top: 5px; /* Added space between icon and dropdown */
  }

  .dropdown-menu a {
    display: block;
    padding: 10px;
    color: #333;
    text-decoration: none;
  }

  .dropdown-menu a:hover {
    background-color: #f0f0f0;
  }

  .post-header {
    position: relative; /* Added to position the dropdown menu */
  }

  /* Modal Styles */
  .modal {
    display: none; /* Initially hidden */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

  .modal-content {
    background-color: #fff;
    width: 90%;
    max-width: 400px;
    border-radius: 8px;
    padding: 20px;
  }

  .close-button {
    float: right;
    cursor: pointer;
  }

  .modal-content h2 {
    margin: 0 0 15px;
  }

  .modal-content textarea {
    width: 100%;
    height: 80px;
    resize: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 10px;
  }

  .modal-content button {
    padding: 10px 20px;
    background-color: #007BFF;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .modal-content button:hover {
    background-color: #0056b3;
  }
</style>

<div class="post-card" data-pid="<?php echo $PID; ?>">
  <div class="post-header">
    <img src="pics/defaultProfile.png" alt="Profile" class="profile-img">
    <div class="user-info">
      <span class="user-name"><?php echo $postUsername; ?></span>
      <span class="post-options" onclick="togglePostOptions(event)">•••</span>
      <div class="dropdown-menu" id="postOptionsMenu_<?php echo $PID; ?>">
        <a href="#" onclick="goToUserProfile(<?php echo $PostUID; ?>)">View User Profile</a>
        <a href="#" onclick="showAdminReviewModal(<?php echo $PID; ?>)">Admin Review</a>
      </div>
    </div>
  </div>
  <div class="post-incontent">
    <p class="post-text"><?php echo $postDescription; ?></p>

    <?php
    // Display image if it exists
    if (isset($postImage)) {
      // Convert the Blob to a Base64 encoded string
      $imageData = base64_encode($postImage);
      echo '<div class="image-container">';
      echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Post Image" style="max-width: 100%; height: auto;">'; 
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
    <button class="footer-btn" onclick="sharePost(<?php echo $PID; ?>, <?php echo $UID; ?>)">
      <img src="pics/share.jpg" alt="Share" class="btn-icon">
      <span class="btn-text">Share</span>
    </button>
  </div>
</div>

<!-- Modal for Admin Review -->
<div id="adminReviewModal" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeAdminReviewModal()">&times;</span>
    <h2>Admin Review</h2>
    <textarea id="reportMessage" placeholder="Enter your report message..."></textarea>
    <button id="submitReport" onclick="submitAdminReview()">Submit Review</button>
  </div>
</div>

<script>
  function togglePostOptions(event) {
    event.stopPropagation(); // Prevent event from bubbling up to the document
    const dropdownMenu = document.getElementById('postOptionsMenu_' + event.target.closest('.post-card').dataset.pid);
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block'; // Toggle dropdown visibility
  }

  function goToUserProfile(uid) {
    window.location.href = 'Profile.php?otherUID=' + uid; // Redirect to the user profile page
  }

  function showAdminReviewModal(pid) {
    document.getElementById('adminReviewModal').style.display = 'flex'; // Show the admin review modal
    document.getElementById('submitReport').setAttribute('data-pid', pid); // Store the PID
  }

  function closeAdminReviewModal() {
    document.getElementById('adminReviewModal').style.display = 'none'; // Hide the admin review modal
  }

  function submitAdminReview() {
    const reportMessage = document.getElementById('reportMessage').value.trim();
    const PID = document.getElementById('submitReport').getAttribute('data-pid'); // Get PID from the button data attribute

    if (reportMessage === '') {
      alert('Please enter a report message.');
      return;
    }

    const data = {
      UID: <?php echo $UID; ?>, // Pass the UID directly
      PID: PID,
      Report_Message: reportMessage,
    };

    fetch('submit_admin_report.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          alert('Report submitted successfully!');
          closeAdminReviewModal(); // Close the modal after successful submission
          document.getElementById('reportMessage').value = ''; // Clear the textarea
        } else {
          alert('Error submitting report: ' + result.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }

  // Close the dropdown menu if the user clicks outside of it
  window.onclick = function(event) {
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(dropdown => {
      if (event.target !== dropdown && event.target !== dropdown.previousElementSibling) {
        dropdown.style.display = 'none';
      }
    });
  };

  function updateVotes(value, PID, UID) {
    if (!PID || !UID) {
      alert('You must be logged in to vote.');
      return;
    }

    const data = {
      UID: UID,
      PID: PID,
      Vote_direction: value,
    };

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
          const postCard = document.querySelector(`.post-card[data-pid="${PID}"]`);
          postCard.querySelector('.upvote-count').textContent = result.totalUpVotes;
          postCard.querySelector('.downvote-count').textContent = result.totalDownVotes;

          currentVote[PID] = result.userVote;
          updateVoteButtons(PID);
        } else {
          alert('Error updating vote: ' + result.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }

  let currentVote = {};

  function updateVoteButtons(PID) {
    const postCard = document.querySelector(`.post-card[data-pid="${PID}"]`);
    const upvoteButton = postCard.querySelector('.upvote-button');
    const downvoteButton = postCard.querySelector('.downvote-button');

    upvoteButton.classList.remove('voted');
    downvoteButton.classList.remove('voted');

    if (currentVote[PID] === 1) {
      upvoteButton.classList.add('voted');
    } else if (currentVote[PID] === -1) {
      downvoteButton.classList.add('voted');
    }
  }

  function popUpCommentPanel(PostID, UserID) {
    console.log('Pop-up triggered for Post ID:', PostID, 'User ID:', UserID); // Debug log

    // Check if the PostID and UserID are provided
    if (!PostID || !UserID) {
        alert('Post ID and User ID must be provided.');
        return;
    }

    // Create the modal
    const modal = document.createElement('div');
    modal.classList.add('modal');

    modal.innerHTML = `
      <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Comments</h2>
        <div class="comments-section" id="commentsSection"></div>
        <div class="new-comment-container">
          <textarea id="newComment" placeholder="Write your comment..."></textarea>
          <button id="submitComment">Submit</button>
        </div>
      </div>
    `;

    // Append the modal to the body
    document.body.appendChild(modal);

    // Load existing comments for the specified PostID
    loadComments(PostID);

    // Add event listener to the close button
    modal.querySelector('.close-button').addEventListener('click', function() {
        document.body.removeChild(modal); // Close the modal
    });

    // Add event listener to the submit comment button
    modal.querySelector('#submitComment').addEventListener('click', function() {
        const commentText = document.getElementById('newComment').value.trim();
        if (commentText !== '') {
            submitComment(PostID, UserID, commentText); // Call the function to submit the comment
        }
    });

    // Display the modal
    modal.style.display = 'flex'; // Ensure the modal is displayed
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
          <div style = "display:flex;">
            <a href="#"><strong>${comment.username}</strong></a>
            <p style="padding-left: 20px;">${comment.text}</p>
          </div>
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
          loadComments(PID);
          document.getElementById('newComment').value = '';
        } else {
          alert('Error submitting comment: ' + result.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }

  function sharePost(PID, UID) {
    const data = {
      UID: UID,
      PID: PID,
    };

    // Check if the user has already shared the post
    fetch('check_shared_post.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(result => {
      if (result.shared) {
        alert('You have already shared this post.');
      } else {
        // If not shared, proceed to share
        fetch('insert_shared_post.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            alert('Post shared successfully!');
          } else {
            alert('Error sharing post: ' + result.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  }
</script>
