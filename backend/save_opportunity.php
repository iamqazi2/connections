<?php
// Enable error reporting for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_reporting(E_ALL);

// Ensure JSON response
header('Content-Type: application/json');

try {
    // Check if connection.php exists
    if (!file_exists('../connection.php')) {
        throw new Exception('connection.php not found');
    }

    // Include connection file
    require_once '../connection.php';

    // Verify PDO connection
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }

    // Create the opportunity table if it doesn't exist
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS opportunity (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            listing_type ENUM('jobs', 'internship', 'freelance') NOT NULL,
            company VARCHAR(255) NOT NULL,
            location ENUM('onsite', 'remote', 'hybrid') NOT NULL,
            start_date VARCHAR(10),
            end_date VARCHAR(10),
            budget_amount DECIMAL(10, 2),
            budget_cycle ENUM('hourly', 'daily', 'weekly', 'monthly') NOT NULL,
            budget_currency ENUM('usd', 'eur', 'gbp', 'inr') NOT NULL,
            job_timing VARCHAR(50),
            job_shift ENUM('morning', 'evening', 'vary'),
            description TEXT,
            tags JSON,
            media_path VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($createTableQuery);

    // Validate required fields
    $requiredFields = ['title', 'listing_type', 'company', 'location', 'budget_cycle', 'budget_currency'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field]) && empty($_FILES[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            exit;
        }
    }

    // Prepare data
    $title = $_POST['title'] ?? '';
    $listing_type = $_POST['listing_type'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $budget_amount = !empty($_POST['budget_amount']) ? floatval($_POST['budget_amount']) : null;
    $budget_cycle = $_POST['budget_cycle'] ?? '';
    $budget_currency = $_POST['budget_currency'] ?? '';
    $job_timing = !empty($_POST['job_timing']) ? $_POST['job_timing'] : null;
    $job_shift = !empty($_POST['job_shift']) ? $_POST['job_shift'] : null;
    $description = !empty($_POST['description']) ? $_POST['description'] : null;
    $tags = !empty($_POST['tags']) ? $_POST['tags'] : '[]';

    // Handle media upload
    $media_path = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $allowedFormats = ['image/jpeg', 'image/png', 'application/pdf', 'image/svg+xml', 'video/mp4'];
        $maxSize = 25 * 1024 * 1024; // 25MB
        $file = $_FILES['media'];

        if (!in_array($file['type'], $allowedFormats)) {
            echo json_encode(['success' => false, 'message' => 'Unsupported file format.']);
            exit;
        }
        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'File size exceeds 25MB limit.']);
            exit;
        }

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create uploads directory');
            }
        }
        if (!is_writable($uploadDir)) {
            throw new Exception('Uploads directory is not writable');
        }
        $media_path = $uploadDir . uniqid() . '_' . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $media_path)) {
            throw new Exception('Failed to upload media file');
        }
    }

    // Insert data into the database
    $stmt = $pdo->prepare("
        INSERT INTO opportunity (
            title, listing_type, company, location, start_date, end_date,
            budget_amount, budget_cycle, budget_currency, job_timing, job_shift,
            description, tags, media_path
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");
    $stmt->execute([
        $title,
        $listing_type,
        $company,
        $location,
        $start_date,
        $end_date,
        $budget_amount,
        $budget_cycle,
        $budget_currency,
        $job_timing,
        $job_shift,
        $description,
        $tags,
        $media_path
    ]);

    echo json_encode(['success' => true, 'message' => 'Opportunity posted successfully']);

} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>