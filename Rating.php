<?php 
include("./config/db.php"); 
include("./includes/topbar.php");  
include("./includes/sidebar.php");?>
<br>
<br>
<br>
<br>
<br>
<br>
<style>
/* Navigation Styles */
.page-nav {
  margin: 30px 0;
  padding-bottom: 15px;
  text-align: center;
  border-bottom: 3px solid #e0123f;
}

.page-nav ul {
  display: inline-flex;
  gap: 25px;
  list-style: none;
  margin: 0;
  padding: 0;
  justify-content: center;
  align-items: center;
}

.page-nav ul li a {
  display: inline-block;
  padding: 12px 25px;
  border-radius: 12px;
  text-decoration: none;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  color: #333;
  font-size: 22px;
  font-weight: 600;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.page-nav ul li a:hover {
  background: linear-gradient(135deg, #e0123f 0%, #c81038 100%);
  color: #fff;
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(224, 18, 63, 0.3);
  border-color: #e0123f;
}

/* Main Navigation Links */
.nav-links {
  text-align: center;
  margin: 30px 0;
  padding: 25px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 15px;
  margin: 20px 50px;
}

.nav-links a {
  display: inline-block;
  padding: 15px 35px;
  margin: 0 20px;
  border-radius: 30px;
  text-decoration: none;
  background: rgba(255,255,255,0.9);
  color: #333;
  font-weight: 700;
  font-size: 18px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.nav-links a:hover {
  background: #e0123f;
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(224, 18, 63, 0.4);
  border-color: white;
}

/* Ratings Section */
.ratings-section {
  padding: 40px 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.ratings-section h2 {
  text-align: center;
  color: #e0123f;
  font-size: 3.5rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin: 40px 0 20px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.ratings-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 30px;
  margin: 40px 0;
  padding: 20px;
}

/* Rating Card */
.rating-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  transition: all 0.4s ease;
  border: 1px solid #e9ecef;
  position: relative;
  overflow: hidden;
}

.rating-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, #e0123f, #667eea, #764ba2);
}

.rating-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.rating-header {
  display: flex;
  justify-content: between;
  align-items: center;
  margin-bottom: 20px;
  gap: 15px;
}

.artist-avatar {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e0123f;
}

.artist-info {
  flex: 1;
}

.artist-name {
  font-size: 1.4rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 5px;
}

.artist-genre {
  color: #666;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Star Ratings */
.star-rating {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 15px 0;
}

.stars {
  display: flex;
  gap: 3px;
}

.star {
  font-size: 1.4rem;
  color: #ddd;
  transition: color 0.3s ease;
}

.star.filled {
  color: #ffc107;
}

.star.half {
  background: linear-gradient(90deg, #ffc107 50%, #ddd 50%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.rating-value {
  font-size: 1.3rem;
  font-weight: 700;
  color: #333;
  background: linear-gradient(135deg, #e0123f, #667eea);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Review Content */
.review-content {
  margin: 20px 0;
}

.review-text {
  color: #555;
  line-height: 1.6;
  font-size: 1rem;
  margin-bottom: 15px;
  font-style: italic;
}

.reviewer-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px solid #e9ecef;
}

.reviewer-name {
  font-weight: 600;
  color: #333;
}

.review-date {
  color: #666;
  font-size: 0.9rem;
}

/* Rating Stats */
.rating-stats {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 15px;
  padding: 30px;
  margin: 40px 0;
  color: white;
  text-align: center;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.stat-item {
  background: rgba(255,255,255,0.1);
  padding: 20px;
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.9;
}

/* Add Review Form */
.add-review-section {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 20px;
  padding: 40px;
  margin: 50px 0;
  border: 2px dashed #e0123f;
}

.add-review-section h3 {
  text-align: center;
  color: #e0123f;
  font-size: 2rem;
  margin-bottom: 30px;
  font-weight: 700;
}

.review-form {
  max-width: 600px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 25px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #333;
  font-weight: 600;
  font-size: 1rem;
}

.form-group select,
.form-group input,
.form-group textarea {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid #e9ecef;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
  border-color: #e0123f;
  box-shadow: 0 0 0 4px rgba(224, 18, 63, 0.1);
  outline: none;
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
}

.star-rating-input {
  display: flex;
  gap: 5px;
  justify-content: center;
  margin: 15px 0;
}

.star-input {
  font-size: 2rem;
  cursor: pointer;
  color: #ddd;
  transition: color 0.3s ease;
}

.star-input:hover,
.star-input.active {
  color: #ffc107;
}

.submit-btn {
  width: 100%;
  padding: 18px;
  background: linear-gradient(135deg, #e0123f 0%, #c81038 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.submit-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(224, 18, 63, 0.4);
}

/* No Ratings Message */
.no-ratings {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.no-ratings h3 {
  font-size: 2rem;
  margin-bottom: 15px;
  color: #e0123f;
}

.no-ratings p {
  font-size: 1.1rem;
  margin-bottom: 25px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-nav ul {
    flex-direction: column;
    gap: 15px;
  }
  
  .page-nav ul li a {
    width: 250px;
    text-align: center;
  }
  
  .nav-links {
    margin: 20px;
  }
  
  .nav-links a {
    display: block;
    margin: 10px auto;
    max-width: 250px;
  }
  
  .ratings-container {
    grid-template-columns: 1fr;
    margin: 20px;
    padding: 10px;
  }
  
  .ratings-section h2 {
    font-size: 2.5rem;
  }
  
  .rating-header {
    flex-direction: column;
    text-align: center;
  }
  
  .add-review-section {
    padding: 30px 20px;
    margin: 30px 20px;
  }
}

@media (max-width: 480px) {
  .ratings-container {
    margin: 10px;
    padding: 5px;
  }
  
  .ratings-section h2 {
    font-size: 2rem;
  }
  
  .page-nav ul li a {
    font-size: 18px;
    padding: 10px 20px;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="nav-links">
  <a href="Artistprofile.php">🎭 Profile</a>
  <a href="Profile_artist.php">👤 Artist Profile</a>
</div>
<br>
<br>
<br>
<nav class="page-nav">
  <ul>
    <li><a href="contract.php">📝 Contract</a></li>
    <li><a href="endorsements.php">⭐ Endorsements</a></li>
    <li><a href="assets.php">📁 Assets</a></li>
    <li><a href="ratings.php">🏆 Ratings</a></li>
  </ul>
</nav>
<br>
<br>
<br>

<section class="ratings-section">
  <h2>⭐ Artist Ratings & Reviews</h2>

  <?php
  // Handle form submission for new reviews
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
      $artist_id = $_POST['artist_id'];
      $rating = $_POST['rating'];
      $review_text = $conn->real_escape_string($_POST['review_text']);
      $reviewer_name = $conn->real_escape_string($_POST['reviewer_name']);
      
      $sql = "INSERT INTO artist_ratings (artist_id, rating, review_text, reviewer_name, created_at) 
              VALUES ('$artist_id', '$rating', '$review_text', '$reviewer_name', NOW())";
      
      if ($conn->query($sql)) {
          echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin: 20px 0; text-align: center; font-weight: 600;'>
                   Thank you for your review!
                </div>";
      } else {
          echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin: 20px 0; text-align: center; font-weight: 600;'>
                   Error submitting review. Please try again.
                </div>";
      }
  }

  // Get overall rating stats
  $stats_result = $conn->query("
      SELECT 
          COUNT(*) as total_reviews,
          AVG(rating) as avg_rating,
          COUNT(DISTINCT artist_id) as rated_artists
      FROM artist_ratings
  ");
  $stats = $stats_result->fetch_assoc();
  ?>

  <!-- Rating Statistics -->
  <div class="rating-stats">
    <h3 style="color: white; margin-bottom: 20px;">📊 Rating Statistics</h3>
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-number"><?php echo $stats['total_reviews'] ?? 0; ?></div>
        <div class="stat-label">Total Reviews</div>
      </div>
      <div class="stat-item">
        <div class="stat-number"><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?></div>
        <div class="stat-label">Average Rating</div>
      </div>
      <div class="stat-item">
        <div class="stat-number"><?php echo $stats['rated_artists'] ?? 0; ?></div>
        <div class="stat-label">Rated Artists</div>
      </div>
    </div>
  </div>

  <!-- Ratings Container -->
  <div class="ratings-container">
    <?php
    // Get all ratings with artist information
    $ratings_result = $conn->query("
        SELECT ar.*, p.name as artist_name, p.genre, p.image_path 
        FROM artist_ratings ar 
        LEFT JOIN profile p ON ar.artist_id = p.artist_id 
        ORDER BY ar.created_at DESC
    ");
    
    if ($ratings_result && $ratings_result->num_rows > 0) {
        while ($rating = $ratings_result->fetch_assoc()) {
            $artist_avatar = !empty($rating['image_path']) 
                ? "./assets/BACKSTAGE_PICTURES/{$rating['image_path']}" 
                : "./assets/BACKSTAGE_PICTURES/default.png";
            
            echo "
            <div class='rating-card'>
                <div class='rating-header'>
                    <img src='$artist_avatar' alt='{$rating['artist_name']}' class='artist-avatar'
                         onerror=\"this.src='./assets/BACKSTAGE_PICTURES/default.png'\">
                    <div class='artist-info'>
                        <div class='artist-name'>" . htmlspecialchars($rating['artist_name'] ?? 'Unknown Artist') . "</div>
                        <div class='artist-genre'>" . htmlspecialchars($rating['genre'] ?? 'Unknown Genre') . "</div>
                    </div>
                </div>
                
                <div class='star-rating'>
                    <div class='stars'>";
            
            // Display star rating
            $full_stars = floor($rating['rating']);
            $has_half_star = ($rating['rating'] - $full_stars) >= 0.5;
            
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $full_stars) {
                    echo "<span class='star filled'>★</span>";
                } elseif ($i == $full_stars + 1 && $has_half_star) {
                    echo "<span class='star half'>★</span>";
                } else {
                    echo "<span class='star'>★</span>";
                }
            }
            
            echo "</div>
                    <div class='rating-value'>{$rating['rating']}/5</div>
                </div>
                
                <div class='review-content'>
                    <p class='review-text'>" . nl2br(htmlspecialchars($rating['review_text'])) . "</p>
                </div>
                
                <div class='reviewer-info'>
                    <span class='reviewer-name'>👤 " . htmlspecialchars($rating['reviewer_name']) . "</span>
                    <span class='review-date'>📅 " . date('M j, Y', strtotime($rating['created_at'])) . "</span>
                </div>
            </div>";
        }
    } else {
        echo "
        <div class='no-ratings' style='grid-column: 1 / -1;'>
            <h3>🌟 No Reviews Yet</h3>
            <p>Be the first to rate and review our artists!</p>
        </div>";
    }
    ?>
  </div>

  <!-- Add Review Form -->
  <div class="add-review-section">
    <h3>💫 Add Your Review</h3>
    <form method="POST" class="review-form">
      <div class="form-group">
        <label for="artist_id">Select Artist:</label>
        <select name="artist_id" id="artist_id" required>
          <option value="">Choose an artist...</option>
          <?php
          $artists_result = $conn->query("SELECT id, name FROM artistprofile");
          while ($artist = $artists_result->fetch_assoc()) {
              echo "<option value='{$artist['id']}'>{$artist['name']}</option>";
          }
          ?>
        </select>
      </div>
      
      <div class="form-group">
        <label>Your Rating:</label>
        <div class="star-rating-input" id="starRating">
          <span class="star-input" data-rating="1">★</span>
          <span class="star-input" data-rating="2">★</span>
          <span class="star-input" data-rating="3">★</span>
          <span class="star-input" data-rating="4">★</span>
          <span class="star-input" data-rating="5">★</span>
        </div>
        <input type="hidden" name="rating" id="ratingValue" value="5" required>
      </div>
      
      <div class="form-group">
        <label for="reviewer_name">Your Name:</label>
        <input type="text" name="reviewer_name" id="reviewer_name" required 
               placeholder="Enter your name">
      </div>
      
      <div class="form-group">
        <label for="review_text">Your Review:</label>
        <textarea name="review_text" id="review_text" required 
                  placeholder="Share your experience with this artist..."></textarea>
      </div>
      
      <button type="submit" name="submit_review" class="submit-btn">
        📝 Submit Review
      </button>
    </form>
  </div>
</section>

<script>
// Star rating functionality
document.addEventListener('DOMContentLoaded', function() {
    const starInputs = document.querySelectorAll('.star-input');
    const ratingValue = document.getElementById('ratingValue');
    
    starInputs.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingValue.value = rating;
            
            // Update star display
            starInputs.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            starInputs.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
        
        star.addEventListener('mouseout', function() {
            const currentRating = ratingValue.value;
            starInputs.forEach((s, index) => {
                if (index < currentRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    // Initialize with 5 stars
    starInputs.forEach((star, index) => {
        if (index < 5) {
            star.classList.add('active');
        }
    });
});
</script>

<?php include("./includes/footer.php"); ?>