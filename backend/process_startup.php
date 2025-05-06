
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to post a startup idea";
    header("Location: /connections/signin.php");
    exit();
}

require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['startup'])) {
    $_SESSION['error'] = "No startup data found. Please start over.";
    header("Location: /connections/startup.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Retrieve startup data
$startup = $_SESSION['startup'];
$title = $startup['title'];
$description = $startup['description'];
$tags = isset($startup['tags']) ? json_encode($startup['tags']) : null;
$questions = isset($startup['questions']) ? json_encode($startup['questions']) : null;
$media_path = $startup['media'] ?? null;

try {
    $stmt = $pdo->prepare("
        INSERT INTO startups 
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
    
    $_SESSION['success'] = "Startup idea posted successfully!";
    header("Location: /connections/startup.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: /connections/startup.php");
    exit();
}
?>
