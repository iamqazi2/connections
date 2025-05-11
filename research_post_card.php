<?php
// Database connection
$host = 'localhost';
$dbname = 'connections';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch 2 distinct posts from research_posts table
    $stmt = $pdo->query("SELECT DISTINCT title, description, created_at, media_path FROM research_posts ORDER BY created_at DESC LIMIT 2");
    $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
   

    
    // Trim media_path to start from 'research_media'
    $trimmed_opportunities = [];
    foreach ($opportunities as $opportunity) {
        $trimmed_opportunity = $opportunity;
        if (!empty($opportunity['media_path'])) {
            $trimmed_path = strstr($opportunity['media_path'], 'research_media');
            $trimmed_opportunity['media_path'] = $trimmed_path !== false ? $trimmed_path : $opportunity['media_path'];
        }
        $trimmed_opportunities[] = $trimmed_opportunity;
    }
    $opportunities = $trimmed_opportunities;
    
    // Ensure exactly 2 distinct posts
    $default_opportunities = [
        [
            'title' => 'Senior UI/UX Designer | Figma Expert',
            'description' => 'Innovative Designs Inc.',
            'created_at' => '2025-05-09 10:00:00',
            'media_path' => 'research_media/uiux_designer.png'
        ],
        [
            'title' => 'Junior Graphic Designer | Illustrator Enthusiast',
            'description' => 'Creative Studio',
            'created_at' => '2025-05-08 12:00:00',
            'media_path' => 'research_media/graphic_designer.png'
        ]
    ];
    
    // If fewer than 2 posts, supplement with defaults
    if (count($opportunities) < 2) {
        $opportunities = array_merge($opportunities, array_slice($default_opportunities, count($opportunities), 2 - count($opportunities)));
    }
    
    // Limit to exactly 2 posts
    $opportunities = array_slice($opportunities, 0, 2);
    
  
    
} catch (PDOException $e) {
    // On error, use default data
    $opportunities = [
        [
            'title' => 'Senior UI/UX Designer | Figma Expert',
            'description' => 'Innovative Designs Inc.',
            'created_at' => '2025-05-09 10:00:00',
            'media_path' => 'research_media/uiux_designer.png'
        ],
        [
            'title' => 'Junior Graphic Designer | Illustrator Enthusiast',
            'description' => 'Creative Studio',
            'created_at' => '2025-05-08 12:00:00',
            'media_path' => 'research_media/graphic_designer.png'
        ]
    ];
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Opportunities</title>
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
        .job-company {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }
        .job-time {
            font-size: 14px;
            color: rgba(2, 53, 100, 0.5);
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="job-card-container">
        <?php foreach ($opportunities as $index => $opportunity): ?>
        <div class="job-card" id="job-card-<?php echo $index; ?>">
            <div class="job-logo">
                <img src="/connections/<?php echo htmlspecialchars($opportunity['media_path']); ?>" 
                     alt="Company Logo" 
                     onerror="this.src='/connections/research_media/default.png'">
            </div>
            <div class="job-details">
                <h3 class="job-title"><?php echo htmlspecialchars($opportunity['title']); ?></h3>
                <div class="job-company"><?php echo htmlspecialchars($opportunity['description']); ?></div>
                <div class="job-time">Time Created: <?php echo htmlspecialchars($opportunity['created_at']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>