<?php
session_start();

// 1. Include connection file with absolute path
$connectionPath = __DIR__ . '/../connection.php'; // Added missing slash
if (!file_exists($connectionPath)) {
    die("Connection file not found at: " . $connectionPath); // Show path for debugging
}
require_once $connectionPath;

// 2. Verify database connection
if (!isset($pdo)) {
    $_SESSION['error'] = "Database connection failed";
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
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
            $media_path = $targetPath;
        } else {
            $_SESSION['error'] = "File upload failed";
            header("Location: ../add_posts.php");
            exit();
        }
    }

    // 5. Database insertion using PDO
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (description, media_path) VALUES (?, ?)");
        $stmt->execute([$description, $media_path]);
        
        $_SESSION['success'] = "Post created successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // 6. Redirect back to form
    header("Location: ../add_posts.php");
    exit();
}
?>