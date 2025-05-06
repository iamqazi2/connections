<?php
session_start();

// 1. Include connection file with absolute path
<<<<<<< Updated upstream
$connectionPath = __DIR__ . '/../connection.php'; // Added missing slash
if (!file_exists($connectionPath)) {
    die("Connection file not found at: " . $connectionPath); // Show path for debugging
=======
$connectionPath = __DIR__ . '/../connection.php';
if (!file_exists($connectionPath)) {
    die("Connection file not found at: " . $connectionPath);
>>>>>>> Stashed changes
}
require_once $connectionPath;

// 2. Verify database connection
if (!isset($pdo)) {
    $_SESSION['error'] = "Database connection failed";
<<<<<<< Updated upstream
    header("Location: ../add_posts.php"); // Fixed path
    exit();
}

// 3. Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = trim($_POST['description']);
    $media_path = null;

    // 4. File upload handling
    if (!empty($_FILES['media']['name'])) {
        $uploadDir = __DIR__ . '/uploads/';
=======
    header("Location: ../add_posts.php");
    exit();
}

// 3. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to create a post";
    header("Location: ../login.php");
    exit();
}

// 4. Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = trim($_POST['description'] ?? '');
    $media_path = null;
    $user_id = $_SESSION['user_id'];

    // 5. File upload handling
    if (!empty($_FILES['media']['name'])) {
        $uploadDir = __DIR__ . '/uploads/';
        $relativeUploadDir = 'backend/uploads/'; // Relative path for database
>>>>>>> Stashed changes
        
        // Create upload directory if missing
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                $_SESSION['error'] = "Failed to create upload directory";
                header("Location: ../add_posts.php");
                exit();
            }
        }

        // File validation
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'svg', 'mp4'];
        $fileExt = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
        $maxSize = 25 * 1024 * 1024; // 25MB

        if (!in_array($fileExt, $allowedTypes)) {
            $_SESSION['error'] = "Invalid file type";
            header("Location: ../add_posts.php");
            exit();
        }

        if ($_FILES['media']['size'] > $maxSize) {
            $_SESSION['error'] = "File too large (max 25MB)";
            header("Location: ../add_posts.php");
            exit();
        }

        // Generate unique filename
        $fileName = uniqid() . '.' . $fileExt;
<<<<<<< Updated upstream
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
            $media_path = $targetPath;
=======
        $targetPath = $uploadDir . $fileName; // Full server path for file storage
        $media_path = $relativeUploadDir . $fileName; // Relative path for database

        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
            // File successfully uploaded, $media_path already set to relative path
>>>>>>> Stashed changes
        } else {
            $_SESSION['error'] = "File upload failed";
            header("Location: ../add_posts.php");
            exit();
        }
    }

<<<<<<< Updated upstream
    // 5. Database insertion using PDO
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (description, media_path) VALUES (?, ?)");
        $stmt->execute([$description, $media_path]);
        
=======
    // 6. Database insertion using PDO
    try {
        $stmt = $pdo->prepare("INSERT INTO posts_table (user_id, description, media_path) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $description, $media_path]);
>>>>>>> Stashed changes
        $_SESSION['success'] = "Post created successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

<<<<<<< Updated upstream
    // 6. Redirect back to form
=======
    // 7. Redirect back to form
>>>>>>> Stashed changes
    header("Location: ../add_posts.php");
    exit();
}
?>