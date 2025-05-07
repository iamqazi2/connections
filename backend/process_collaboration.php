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
    error_log("Error: No collaboration session data");
    header("Location: ../collaboration.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to post a collaboration.";
    error_log("Error: No user_id in session");
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
$user_id = $_SESSION['user_id'];

// Validate file existence
if ($media_path && !file_exists(__DIR__ . '/../' . $media_path)) {
    error_log("Media file not found on server: " . $media_path);
    $media_path = null;
}

// Log data before insertion
error_log("Data before DB insert: title=$title, media_path=" . ($media_path ?? 'NULL'));

// Validate required fields
if (empty($title) || empty($description)) {
    $_SESSION['error'] = "Title and description are required.";
    error_log("Error: Missing title or description");
    header("Location: ../collaboration.php");
    exit();
}

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
        $user_id
    ]);

    if ($success && $stmt->rowCount() === 1) {
        error_log("Collaboration inserted successfully, media_path: " . ($media_path ?? 'NULL'));
        // Clear session data
        unset($_SESSION['collaboration']);
        $_SESSION['success'] = "Collaboration posted successfully!";
        header("Location: ../collaboration.php");
        exit();
    } else {
        error_log("Database insert failed. Rows affected: " . $stmt->rowCount());
        throw new Exception("Failed to insert data into the database.");
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
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