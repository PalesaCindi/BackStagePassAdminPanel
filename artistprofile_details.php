<?php
include("./config/db.php");


$result = $conn->query("SELECT * FROM artist_profiles ORDER BY created_at DESC");
?>

<h2 style="text-align:center; margin-top:30px;">All Artists</h2>

<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:20px; margin:30px;">
<?php
if($result && $result->num_rows > 0) {
    while($artist = $result->fetch_assoc()) {
        $image = !empty($artist['profile_image']) && file_exists("../uploads/artists/".$artist['profile_image'])
                 ? "../uploads/artists/".$artist['profile_image']
                 : "https://via.placeholder.com/150";
        $social_links = json_decode($artist['social_media'], true);
        echo "
        <div style='width:250px; padding:15px; border:1px solid #ddd; border-radius:10px; text-align:center;'>
            <img src='{$image}' style='width:150px; height:150px; border-radius:50%; object-fit:cover;'><br><br>
            <strong>{$artist['artist_name']}</strong><br>
            <em>{$artist['genre']}</em><br>
            <p>Booking Fee: R {$artist['booking_fee']}</p>
            <p>Endorsements: {$artist['endorsement']}</p>
            <p>
                <a href='{$social_links['tiktok']}' target='_blank'>TikTok</a> | 
                <a href='{$social_links['instagram']}' target='_blank'>Instagram</a> | 
                <a href='{$social_links['twitter']}' target='_blank'>Twitter</a>
            </p>
        </div>
        ";
    }
} else {
    echo "<p>No artists found.</p>";
}
?>
</div>

