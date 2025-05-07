<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Include database connection
require_once __DIR__ . '/connection.php';

// 2. Validate session data exists
if (!isset($_SESSION['research_idea'])) {
    $_SESSION['error'] = "No research idea data found. Start over.";
    header("Location: research_post.php");
    exit();
}

// 3. Retrieve data from session
$research_idea = $_SESSION['research_idea'];
$title = $research_idea['title'] ?? '';
$description = $research_idea['description'] ?? '';
$media_path = $research_idea['media'] ?? null;
$focus_tags = !empty($research_idea['focus']) ? json_encode($research_idea['focus']) : null;
$question1 = $research_idea['question1'] ?? null;
$word_limit1 = $research_idea['word_limit1'] ?? null;
$question2 = $research_idea['question2'] ?? null;
$word_limit2 = $research_idea['word_limit2'] ?? null;

// Get the current user ID from the session
$user_id = $_SESSION['user_id'] ?? 0;

// 4. Database operations
try {
    // Verify database connection
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception("Database connection failed");
    }

    // Create the research_posts table if it doesn't exist
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS research_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            media_path VARCHAR(255) DEFAULT NULL,
            focus_tags TEXT DEFAULT NULL,
            question1 TEXT DEFAULT NULL,
            word_limit1 VARCHAR(20) DEFAULT NULL,
            question2 TEXT DEFAULT NULL,
            word_limit2 VARCHAR(20) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
    $pdo->exec($createTableQuery);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'post') {
        // Prepare the SQL statement to insert the data into research_posts
        $stmt = $pdo->prepare("
            INSERT INTO research_posts (
                user_id, title, description, media_path, focus_tags, 
                question1, word_limit1, question2, word_limit2
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Execute the statement with the form data
        $stmt->execute([
            $user_id,
            $title,
            $description,
            $media_path,
            $focus_tags,
            $question1,
            $word_limit1,
            $question2,
            $word_limit2
        ]);

        // Clear session data
        unset($_SESSION['research_idea']);

        // Set a success message
        $_SESSION['success'] = "Research idea posted successfully!";

        // Redirect back to the form page
        header("Location: research_post.php");
        exit;
    } else {
        // If the form wasn't submitted properly, redirect with an error
        $_SESSION['error'] = "Invalid form submission.";
        header("Location: research_post.php");
        exit;
    }
} catch (PDOException $e) {
    // Handle database errors
    error_log('Database error: ' . $e->getMessage());
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: research_post.php");
    exit;
} catch (Exception $e) {
    // Handle other errors
    error_log('Error: ' . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    header("Location: research_post.php");
    exit;
}
?>