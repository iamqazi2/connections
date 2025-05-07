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

// Query to fetch all startups from the database, join with user_details table
// First, let's check what columns exist in startup table
$check_sql = "DESCRIBE startup";
$check_result = $conn->query($check_sql);
$startup_columns = [];

if ($check_result && $check_result->num_rows > 0) {
    while($column = $check_result->fetch_assoc()) {
        $startup_columns[] = $column['Field'];
        echo "<!-- Found column: " . $column['Field'] . " -->";
    }
}

// Build the SQL query based on available columns
if (in_array('user_id', $startup_columns)) {
    $sql = "SELECT s.*, ud.profile_image, ud.name as username FROM startup s 
            LEFT JOIN user_details ud ON s.user_id = ud.user_id 
            ORDER BY s.id ASC";
} else if (in_array('created_by', $startup_columns)) {
    $sql = "SELECT s.*, ud.profile_image, ud.name as username FROM startup s 
            LEFT JOIN user_details ud ON s.created_by = ud.user_id 
            ORDER BY s.id ASC";
} else {
    // Fallback: Still try to join with user_details using a possible user_id match
    $sql = "SELECT s.*, ud.profile_image, ud.name as username FROM startup s 
            LEFT JOIN user_details ud ON s.id = ud.user_id 
            ORDER BY s.id ASC";
    echo "<!-- Warning: Could not find user_id or created_by column in startup table, attempting fallback join -->";
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
    <title>Freelancing Projects</title>
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

        /* Add your styles here for the project card */
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

        .question{
            color:#4299e1;
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
                <div class="info-text"><?php echo htmlspecialchars($row["listing_type"] ?? 'Startup Project'); ?></div>
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
            $full_media_url = $media_path ?  $media_path : 'ai-image.jpg';
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
            <h2 class="project-title"><?php echo htmlspecialchars($row["title"] ?? 'Startup Project'); ?></h2>
            
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
                <a href="enroll_research.php" class="apply-btn">
                    <span>Enroll</span>
                    <img src="./images/plus.svg"/>
                </a>
            </div>
            
            <p class="project-description">
                <?php 
                $description = $row["description"] ?? "Description of the post written by the Author. Varius bibendum egestas imperdiet purus ndks dolor. Pellentesque quis blandit aenean sed nisl. Dolor nibh habitant mauris felis vivamus.";
                
                // Display full description if it exists, otherwise use default
                echo htmlspecialchars(substr($description, 0, 200)); // Limit description length
                if (strlen($description) > 200) {
                    echo "...";
                }
                ?>
            </p>
    <?php if (!empty($row['questions'])): ?>
        <div class="questions-container">
            <ul class="question">
                <?php 
                $questions = json_decode($row['questions'], true); // Assuming it's a JSON array
                if (is_array($questions)) {
                    foreach ($questions as $question) {
                        echo "<li>" . htmlspecialchars($question) . "</li>";
                    }
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>
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
        echo "<p style='text-align: center; margin-top: 50px;'>No startup projects found</p>";
    }
    
    $conn->close();
    ?>
</body>
</html>