<?php
// Start session
session_start();

// Debug: Log script execution
error_log("process_collaboration.php executed");

// Include database connection
require_once __DIR__ . '/../connection.php';

// Validate session data
if (!isset($_SESSION['collaboration'])) {
    $_SESSION['error'] = "No collaboration data found. Please start over.";
    header("Location: ../collaboration.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to post a collaboration.";
    header("Location: ../collaboration.php");
    exit();
}

// Retrieve and validate session data
$collaboration = $_SESSION['collaboration'];
$title = $collaboration['title'] ?? '';
$description = $collaboration['description'] ?? '';
$tags = isset($collaboration['tags']) ? json_encode($collaboration['tags']) : null;
$only_connections = isset($collaboration['only_connections']) ? (int)$collaboration['only_connections'] : 0;
$request_to_join = isset($collaboration['request_to_join']) ? (int)$collaboration['request_to_join'] : 0;
$enable_max_limit = isset($collaboration['enable_max_limit']) ? (int)$collaboration['enable_max_limit'] : 0;
$media_path = $collaboration['media'] ?? null;
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Validate required fields
if (empty($title) || empty($description)) {
    $_SESSION['error'] = "Title and description are required.";
    header("Location: ../collaboration.php");
    exit();
}

// Debug: Log session data
error_log("Session data: " . print_r($collaboration, true));

// Database insertion
try {
    $stmt = $pdo->prepare("
        INSERT INTO collaborations_tables_new 
        (title, description, tags, only_connections, request_to_join, enable_max_limit, media_path, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $success = $stmt->execute([
        $title,
        $description,
        $tags,
        $only_connections,
        $request_to_join,
        $enable_max_limit,
        $media_path,
        $user_id // Add user_id to the query
    ]);

    if (!$success) {
        throw new Exception("Failed to insert data into the database.");
    }

    // Clear session data
    unset($_SESSION['collaboration']);
    
    $_SESSION['success'] = "Collaboration posted successfully!";
    header("Location: ../collaboration.php");
    exit();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage() . " | Query: INSERT INTO collaborations ...");
    $_SESSION['error'] = "Failed to post collaboration: Database error occurred.";
    header("Location: ../collaboration.php");
    exit();
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to post collaboration: " . $e->getMessage();
    header("Location: ../collaboration.php");
    exit();
}
?>