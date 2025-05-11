<?php
session_start();

// 1. Include connection file with absolute path
$connectionPath = __DIR__ . '/../connection.php';
if (!file_exists($connectionPath)) {
    die("Connection file not found at: " . $connectionPath);
}
require_once $connectionPath;

// 2. Verify database connection
if (!isset($pdo)) {
    $_SESSION['error'] = "Database connection failed";
    header("Location: ../add_posts.php");
    exit();
}

// 3. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to create a post";
    header("Location: ../signin.php");
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
        $targetPath = $uploadDir . $fileName; // Full server path for file storage
        $media_path = $relativeUploadDir . $fileName; // Relative path for database

        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
            // File successfully uploaded, $media_path already set to relative path
        } else {
            $_SESSION['error'] = "File upload failed";
            header("Location: ../add_posts.php");
            exit();
        }
    }

    // 6. Database insertion using PDO
    try {
        $stmt = $pdo->prepare("INSERT INTO posts_table (user_id, description, media_path) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $description, $media_path]);
        $_SESSION['success'] = "Post created successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // 7. Redirect back to form
    header("Location: ../add_posts.php");
    exit();
}
?>