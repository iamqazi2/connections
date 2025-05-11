<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "connections";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all posts from the research_posts table, join with user_details table
// First, let's check what columns exist in research_posts table
$check_sql = "DESCRIBE research_posts";
$check_result = $conn->query($check_sql);
$research_columns = [];

if ($check_result && $check_result->num_rows > 0) {
    while($column = $check_result->fetch_assoc()) {
        $research_columns[] = $column['Field'];
        echo "<!-- Found column: " . $column['Field'] . " -->";
    }
}

// Build the SQL query based on available columns
if (in_array('user_id', $research_columns)) {
    $sql = "SELECT o.*, ud.profile_image, ud.name as username FROM research_posts o 
            LEFT JOIN user_details ud ON o.user_id = ud.user_id 
            ORDER BY o.id ASC";
} else if (in_array('created_by', $research_columns)) {
    $sql = "SELECT o.*, ud.profile_image, ud.name as username FROM research_posts o 
            LEFT JOIN user_details ud ON o.created_by = ud.user_id 
            ORDER BY o.id ASC";
} else {
    // Fallback: Still try to join with user_details using a possible user_id match
    $sql = "SELECT o.*, ud.profile_image, ud.name as username FROM research_posts o 
            LEFT JOIN user_details ud ON o.id = ud.user_id 
            ORDER BY o.id ASC";
    echo "<!-- Warning: Could not find user_id or created_by column in research_posts table, attempting fallback join -->";
}

$result = $conn->query($sql);

// Debug query
if (!$result) {
    echo "Error: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Posts</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        .project-card {
            margin: 20px auto;
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #f2f2f2;
        }
        
        .info-tag {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-icon {
            width: 26px;
            height: 26px;
            background-color: #dfe3e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            font-size: 14px;
        }
        
        .info-text {
            color: #f8972b;
            font-size: 16px;
            font-weight: 500;
        }
        
        .created-by {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .author-image {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .author-name {
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }
        
        .project-content {
            padding: 15px 20px;
        }
        
        .project-title {
            color: #1a365d;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .project-avatar {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 15px;
            float: left;
        }
        
        .focus-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 12px 0;
        }
        
        .focus-tag {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .tag-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .tag-text {
            font-size: 14px;
            color: #4299e1;
        }
        
        .time-tag {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
        }
        
        .time-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #a0aec0;
        }
        
        .time-text {
            font-size: 14px;
            color: #a0aec0;
        }
        
        .project-description {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.6;
            margin: 15px 0;
        }
        
        .more-link {
            text-align: right;
            font-size: 14px;
            color: #a0aec0;
            text-decoration: none;
            display: block;
        }
        
        .project-image {
            width: 100%;
            height: auto;
            background-color: #f9f7f2;
            border-top: 1px solid #f2f2f2;
            overflow: hidden;
        }

        .project-image img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
        
        .design-image {
            position: relative;
            background-color: #f9f7f2;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .design-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url('pattern-bg.png');
            background-size: cover;
            opacity: 0.7;
        }
        
        .design-content {
            position: relative;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            width: 60%;
            max-width: 350px;
        }
        
        .freelance-text {
            font-size: 26px;
            color: #4a5568;
            font-weight: 300;
        }
        
        .project-text {
            font-size: 50px;
            font-weight: 800;
            color: #2d3748;
            text-transform: uppercase;
            line-height: 1;
            margin-top: 5px;
        }
        
        .apply-button {
            display: inline-block;
            background-color: #e9da73;
            color: #4a5568;
            font-size: 14px;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 20px;
            margin-top: 15px;
            text-decoration: none;
        }
        
        .notebook-edge {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 35px;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            padding: 20px 0;
        }
        
        .notebook-hole {
            width: 20px;
            height: 20px;
            background-color: #4a5568;
            border-radius: 50%;
            opacity: 0.3;
        }
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-top: 1px solid #f2f2f2;
        }
        
        .like-section {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .like-avatars {
            display: flex;
            align-items: center;
        }
        
        .like-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            margin-right: -8px;
        }
        
        .like-text {
            font-size: 14px;
            color: #4a5568;
        }
        
        .comment-count {
            font-size: 14px;
            color: #4a5568;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            border-top: 1px solid #f2f2f2;
        }
        
        .action-button {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2d3748;
            font-size: 14px;
            text-decoration: none;
            padding: 5px 10px;
        }
        
        .action-button i {
            font-size: 18px;
        }
        
        .more-options {
            font-size: 18px;
            color: #4a5568;
        }

        .apply-top-button {
            display: flex;
            align-items: center;
            background-color: #fff;
            border: 1px solid #2d3748;
            border-radius: 25px;
            padding: 8px 15px;
            text-decoration: none;
            color: #2d3748;
            font-weight: 500;
        }

        .download-icon {
            margin-left: 5px;
            font-size: 16px;
        }
        
        .plus-more {
            background-color: #e2e8f0;
            color: #4a5568;
            font-size: 12px;
            padding: 2px 5px;
            border-radius: 3px;
        }

        /* Apply button style in front of description */
        .apply-btn-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        
        .apply-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            border: 1px solid #023564;
            border-radius: 25px;
            padding: 8px 20px;
            text-decoration: none;
            color: #023564;
            font-weight: 500;
            gap: 8px;
        }
        
        .apply-btn i {
            font-size: 16px;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .design-content {
                width: 80%;
            }
            
            .freelance-text {
                font-size: 20px;
            }
            
            .project-text {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <?php
    // Debug - Output table structure
    echo "<!-- Debug: Checking query result -->";
    if ($result === false) {
        echo "<!-- Query error: " . $conn->error . " -->";
    } else {
        echo "<!-- Query successful. Found " . $result->num_rows . " rows -->";
    }
    
    if ($result && $result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Use created_at for the time calculation
        $time_ago = "01 day ago"; // Default
        
        if (isset($row["created_at"])) {
            try {
                $post_date = new DateTime($row["created_at"]);
                $now = new DateTime();
                $interval = $post_date->diff($now);
                
                if ($interval->d == 0) {
                    if ($interval->h == 0) {
                        if ($interval->i == 0) {
                            $time_ago = "Just now";
                        } else {
                            $time_ago = $interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " ago";
                        }
                    } else {
                        $time_ago = $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ago";
                    }
                } elseif ($interval->d == 1) {
                    $time_ago = "Yesterday";
                } elseif ($interval->d < 7) {
                    $time_ago = $interval->d . " days ago";
                } elseif ($interval->d < 30) {
                    $weeks = floor($interval->d / 7);
                    $time_ago = $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
                } else {
                    $months = floor($interval->d / 30);
                    $time_ago = $months . " month" . ($months > 1 ? "s" : "") . " ago";
                }
            } catch (Exception $e) {
                // Keep default if date parsing fails
            }
        }
        
        // Rest of your code remains the same
        $focus_tags = ["Focus of Interest", "Focus of Interest", "Focus of Interest"];
        if (isset($row["tags"]) && !empty($row["tags"])) {
            $tags_array = explode(",", $row["tags"]);
            if (count($tags_array) > 0) {
                $focus_tags = $tags_array;
            }
        }
?>
    <div class="project-card">
        <div class="card-header">
            <div class="info-tag">
                <div class="info-icon">
                    <i class="fas fa-info"></i>
                </div>
                <div class="info-text"><?php echo htmlspecialchars($row["listing_type"] ?? 'Research Post'); ?></div>
            </div>
            <div class="created-by">
                <span>Created by</span>
                <span class="author-name cursor-pointer"><?php echo htmlspecialchars($row["username"] ?? $row["company"] ?? 'Abdullah Tajammal'); ?></span>
            </div>
        </div>
        
        <div class="project-content">
            <?php
            $media_path = $row["media_path"] ?? null;
            $image_extensions = ['jpg', 'jpeg', 'png', 'svg'];
            
            // Trim the media_path to start from 'research_media'
            if ($media_path) {
                $search_term = 'research_media';
                $pos = strpos($media_path, $search_term);
                if ($pos !== false) {
                    $media_path = substr($media_path, $pos);
                }
                // Ensure the path is correctly formatted for URL usage
                $full_media_url = $media_path;
            } else {
                $full_media_url = 'ai-image.jpg';
            }
            
            $file_extension = $media_path ? strtolower(pathinfo($media_path, PATHINFO_EXTENSION)) : '';
            ?>
            <?php if ($media_path && in_array($file_extension, $image_extensions)): ?>
                <img src="<?php echo htmlspecialchars($full_media_url); ?>" alt="Project" class="project-avatar">
            <?php elseif ($media_path): ?>
                <a href="<?php echo htmlspecialchars($full_media_url); ?>" download class="project-avatar" style="display: inline-block; text-decoration: none; color: #2d3748;">
                    <i class="fas fa-file" style="font-size: 50px;"></i>
                    <span style="font-size: 12px;"><?php echo htmlspecialchars(basename($media_path)); ?></span>
                </a>
            <?php else: ?>
                <img src="ai-image.jpg" alt="Project" class="project-avatar">
            <?php endif; ?>
            <h2 class="project-title"><?php echo htmlspecialchars($row["title"] ?? 'Research Post'); ?></h2>
            
            <div class="focus-tags">
                <?php 
                $tag_colors = ["#4299e1", "#38b2ac", "#4c51bf", "#ed8936"];
                $tag_count = 0;
                foreach($focus_tags as $index => $tag): 
                    if ($tag_count < 3 || (isset($focus_tags[3]) && $tag_count == 3)):
                        $color = $tag_colors[$index % count($tag_colors)];
                ?>
                <div class="focus-tag">
                    <div class="tag-dot" style="background-color: <?php echo $color; ?>"></div>
                    <div class="tag-text"><?php echo htmlspecialchars($tag); ?></div>
                </div>
                <?php 
                    endif;
                    $tag_count++;
                endforeach; 
                
                // If there are more than 3 tags, show a +X more indicator
                if (count($focus_tags) > 4):
                ?>
                <div class="focus-tag">
                    <div class="plus-more">+<?php echo count($focus_tags) - 3; ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="time-tag">
                <div class="time-dot"></div>
                <div class="time-text"><?php echo $time_ago; ?></div>
            </div>
            
            <!-- Apply button added here, before the description -->
          <div class="apply-btn-container">
    <a href="enroll_research.php?id=<?php echo $row['id']; ?>" class="apply-btn">
        <span>Enroll</span>
        <i class="fas fa-download"></i>
    </a>
</div>
            
            <p class="project-description">
                <?php 
                $description = $row["description"] ?? "Description of the research post written by the Author. Varius bibendum egestas imperdiet purus ndks dolor. Pellentesque quis blandit aenean sed nisl.";
                
                // Display full description if it exists, otherwise use default
                echo htmlspecialchars(substr($description, 0, 200)); // Limit description length
                if (strlen($description) > 200) {
                    echo "...";
                }
                ?>
            </p>
        </div>
        
        <div class="project-image">
            <?php if ($media_path && in_array($file_extension, $image_extensions)): ?>
                <img src="<?php echo htmlspecialchars($full_media_url); ?>" alt="Project">
            <?php elseif ($media_path): ?>
                <a href="<?php echo htmlspecialchars($full_media_url); ?>" download style="display: block; text-align: center; padding: 20px; text-decoration: none; color: #2d3748;">
                    <i class="fas fa-file" style="font-size: 50px;"></i>
                    <p>Download <?php echo htmlspecialchars(basename($media_path)); ?> (<?php echo strtoupper($file_extension); ?>)</p>
                </a>
            <?php else: ?>
                <img src="ai-image.jpg" alt="Project">
            <?php endif; ?>
        </div>
        
        <div class="card-footer">
            <div class="like-section">
                <div class="like-avatars">
                    <img src="default-avatar.jpg" alt="Like" class="like-avatar">
                </div>
                <div class="like-text">
                    <?php 
                    // Static like count for now - can be updated with actual data
                    echo "Faizan and 123 other likes"; 
                    ?>
                </div>
            </div>
            <div class="comment-count">22 comments</div>
        </div>
        
        <div class="action-buttons">
            <a href="#" class="action-button">
                <i class="far fa-thumbs-up"></i>
                <span>Like</span>
            </a>
            <a href="#" class="action-button">
                <i class="far fa-comment"></i>
                <span>Comment</span>
            </a>
            <a href="#" class="action-button">
                <i class="fas fa-download"></i>
                <span>Download</span>
            </a>
            <a href="#" class="action-button">
                <i class="fas fa-share"></i>
                <span>Share</span>
            </a>
            <a href="#" class="more-options">
                <i class="fas fa-ellipsis-h"></i>
            </a>
        </div>
    </div>
    <?php
        }
    } else {
        echo "<p style='text-align: center; margin-top: 50px;'>No research posts found</p>";
    }
    
    $conn->close();
    ?>
</body>
</html>