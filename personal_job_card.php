<?php
// Start session to access logged-in user data
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php"); // Redirect to login page if not logged in
    exit();
}

// Including the database connection
$host = 'localhost';
$dbname = 'connections';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch data from opportunity table for the logged-in user
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT title, created_at, media_path 
                          FROM research_posts 
                          WHERE user_id = :user_id 
                          LIMIT 4");
    $stmt->execute(['user_id' => $user_id]);
    $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
$base_url = "backend/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }
        .job-card-container {
            max-width: 100%;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
            padding: 15px;
        }
        .job-card {
            display: flex;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .job-card:last-child {
            border-bottom: none;
        }
        .job-logo {
            width: 100px;
            height: 100px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .job-logo img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 5px;
        }
        .job-details {
            flex: 1;
        }
        .job-title {
            font-size: 16px;
            font-weight: 500;
            color: #4a90e2;
            margin-bottom: 5px;
            text-decoration: none;
        }
        .job-time {
            font-size: 14px;
            color: rgba(2, 53, 100, 0.5);
            margin-bottom: 3px;
        }
        .no-posts {
            text-align: center;
            font-size: 16px;
            color: #666;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="job-card-container">
        <?php if (empty($opportunities)): ?>
            <p class="no-posts">No posts yet</p>
        <?php else: ?>
            <?php foreach ($opportunities as $opportunity): ?>
                <div class="job-card">
                    <div class="job-logo">
                        <img src="<?php echo htmlspecialchars($base_url . $opportunity['media_path']); ?>" alt="Company Logo">
                    </div>
                    <div class="job-details">
                        <h3 class="job-title"><?php echo htmlspecialchars($opportunity['title']); ?></h3>
                        <div class="job-time">Time Created: <?php echo htmlspecialchars($opportunity['created_at']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>