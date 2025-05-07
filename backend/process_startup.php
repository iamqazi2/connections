<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to post a startup idea";
    header("Location: /connections/signin.php");
    exit();
}

require_once __DIR__ . '/../connection.php';

// Verify database connection
if (!$pdo) {
    $_SESSION['error'] = "Failed to connect to the database";
    header("Location: /connections/startup.php");
    exit();
}

// Check if startup data is provided (either via session or POST)
if (!isset($_SESSION['startup']) && !isset($_POST['title'])) {
    $_SESSION['error'] = "No startup data found. Please start over.";
    header("Location: /connections/startup.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Use POST data if available, but prioritize session for media_path
$startup = isset($_POST['title']) ? [
    'title' => $_POST['title'],
    'description' => $_POST['description'],
    'tags' => isset($_POST['tags']) ? $_POST['tags'] : [],
    'questions' => isset($_POST['questions']) ? $_POST['questions'] : [],
    'media' => isset($_SESSION['startup']['media']) ? $_SESSION['startup']['media'] : ($_POST['media_path'] ?? null)
] : $_SESSION['startup'];

// Validate required fields
if (empty($startup['title']) || empty($startup['description'])) {
    $_SESSION['error'] = "Title and description are required";
    header("Location: /connections/startup.php");
    exit();
}

$title = $startup['title'];
$description = $startup['description'];
$tags = !empty($startup['tags']) ? json_encode($startup['tags']) : null;
$questions = !empty($startup['questions']) ? json_encode($startup['questions']) : null;
$media_path = $startup['media'] ?? null;

// Log media_path for debugging
$debug_log = date('Y-m-d H:i:s') . " - media_path: " . var_export($media_path, true) . "\n";
file_put_contents(__DIR__ . '/debug.log', $debug_log, FILE_APPEND);

try {
    // Verify table schema: Ensure 'startups' table has columns: user_id, title, description, tags, questions, media_path
    $stmt = $pdo->prepare("
        INSERT INTO startup
        (user_id, title, description, tags, questions, media_path)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        $title, 
        $description, 
        $tags,
        $questions,
        $media_path
    ]);

    // Clear session data
    unset($_SESSION['startup']);
    unset($_SESSION['current_step']);
    
    $_SESSION['success'] = "Startup idea posted successfully!";
    header("Location: /connections/startup.php");
    exit();

} catch (PDOException $e) {
    // Log detailed error to file for debugging
    $error_log = date('Y-m-d H:i:s') . " - Database error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    file_put_contents(__DIR__ . '/error.log', $error_log, FILE_APPEND);
    
    $_SESSION['error'] = "Failed to save startup idea. Please try again.";
    header("Location: /connections/startup.php");
    exit();
}
?>