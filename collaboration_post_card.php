<?php
// Including the database connection
$host = 'localhost';
$dbname = 'connections';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch data from opportunity table including media_path
    $stmt = $pdo->query("SELECT title, description, media_path FROM collaborations_tables_new");
    $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Default values if no data is found
    if (empty($opportunities)) {
        $opportunities = [
            [
                'title' => 'Senior UI/UX Designer | Figma Expert',
                'company' => 'Company Name',
                'location' => 'Location (Type: Remote)',
                'created_at' => 'Time Created',
                'media_path' => 'path/to/default/image.png'
            ],
            [
                'title' => 'Junior Graphic Designer | Illustrator Enthusiast',
                'company' => 'Creative Studio',
                'location' => 'Location (Type: In-Office)',
                'created_at' => '2 days ago',
                'media_path' => 'path/to/default/image.png'
            ],
            [
                'title' => 'Product Designer | Adobe XD Specialist',
                'company' => 'Tech Startup',
                'location' => 'Location (Type: Hybrid)',
                'created_at' => '1 week ago',
                'media_path' => 'path/to/default/image.png'
            ],
            [
                'title' => 'UX Researcher | Sketch Pro',
                'company' => 'Digital Agency',
                'location' => 'Location (Type: Remote)',
                'created_at' => '3 weeks ago',
                'media_path' => 'path/to/default/image.png'
            ]
        ];
    }
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
        .job-company {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }
        .job-location, .job-time {
            font-size: 14px;
            color: rgba(2, 53, 100, 0.5);
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="job-card-container">
        <?php foreach ($opportunities as $opportunity): ?>
        <div class="job-card">
            <div class="job-logo">
                <img src="<?php echo htmlspecialchars($opportunity['media_path']); ?>" alt="Company Logo">
            </div>
            <div class="job-details">
                <h3 class="job-title"><?php echo htmlspecialchars($opportunity['title']); ?></h3>
                <div class="job-company"><?php echo htmlspecialchars($opportunity['description']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
