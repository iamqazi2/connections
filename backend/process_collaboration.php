<?php
session_start();

// 1. Include database connection
require_once __DIR__ . '/../connection.php'; // Adjust path if needed

// 2. Validate session data exists
if (!isset($_SESSION['collaboration'])) {
    $_SESSION['error'] = "No collaboration data found. Start over.";
    header("Location: ../collaboration.php");
    exit();
}

// 3. Retrieve data from session
$collaboration = $_SESSION['collaboration'];
$title = $collaboration['title'];
$description = $collaboration['description'];
$tags = isset($collaboration['tags']) ? json_encode($collaboration['tags']) : null;
$only_connections = $collaboration['only_connections'] ?? 0;
$request_to_join = $collaboration['request_to_join'] ?? 0;
$enable_max_limit = $collaboration['enable_max_limit'] ?? 0;
$media_path = $collaboration['media'] ?? null;

// 4. Database insertion
try {
    $stmt = $pdo->prepare("
        INSERT INTO collaborations 
        (title, description, tags, only_connections, request_to_join, enable_max_limit, media_path)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $title, 
        $description, 
        $tags, 
        $only_connections, 
        $request_to_join, 
        $enable_max_limit, 
        $media_path
    ]);

    // 5. Clear session data
    unset($_SESSION['collaboration']);
    
    $_SESSION['success'] = "Collaboration posted successfully!";
    header("Location: ../collaboration.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: ../collaboration.php");
    exit();
}
?>