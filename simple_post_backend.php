<?php
// post_interactions.php - Handles all post interaction functionality
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if (!$post_id) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid post ID']);
    exit();
}

switch ($action) {
    case 'like':
        handleLike($pdo, $user_id, $post_id);
        break;
    case 'unlike':
        handleUnlike($pdo, $user_id, $post_id);
        break;
    case 'comment':
        $comment_text = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';
        if (!empty($comment_text)) {
            addComment($pdo, $user_id, $post_id, $comment_text);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Comment cannot be empty']);
        }
        break;
    case 'download':
        trackDownload($pdo, $user_id, $post_id);
        break;
    case 'share':
        trackShare($pdo, $user_id, $post_id);
        break;
    case 'get_likes':
        getLikes($pdo, $post_id);
        break;
    case 'get_comments':
        getComments($pdo, $post_id);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

// Handle liking a post
function handleLike($pdo, $user_id, $post_id) {
    try {
        // Check if user already liked this post
        $check = $pdo->prepare("SELECT id FROM post_likes WHERE user_id = ? AND post_id = ?");
        $check->execute([$user_id, $post_id]);
        
        if ($check->rowCount() === 0) {
            // Add new like
            $stmt = $pdo->prepare("INSERT INTO post_likes (user_id, post_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$user_id, $post_id]);
            
            // Get updated like count
            $countStmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ?");
            $countStmt->execute([$post_id]);
            $count = $countStmt->fetch(PDO::FETCH_ASSOC)['like_count'];
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Post liked', 'count' => $count, 'liked' => true]);
        } else {
            // User already liked this post
            header('Content-Type: application/json');
            echo json_encode(['status' => 'info', 'message' => 'Already liked']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Handle unliking a post
function handleUnlike($pdo, $user_id, $post_id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM post_likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        
        if ($stmt->rowCount() > 0) {
            // Get updated like count
            $countStmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ?");
            $countStmt->execute([$post_id]);
            $count = $countStmt->fetch(PDO::FETCH_ASSOC)['like_count'];
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Post unliked', 'count' => $count, 'liked' => false]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'info', 'message' => 'Not liked yet']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Add a comment to a post
function addComment($pdo, $user_id, $post_id, $comment_text) {
    try {
        $stmt = $pdo->prepare("INSERT INTO post_comments (user_id, post_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $post_id, $comment_text]);
        
        // Get the newly created comment with user info
        $commentId = $pdo->lastInsertId();
        $commentStmt = $pdo->prepare("
            SELECT c.*, u.username, u.profile_image 
            FROM post_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $commentStmt->execute([$commentId]);
        $comment = $commentStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get updated comment count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as comment_count FROM post_comments WHERE post_id = ?");
        $countStmt->execute([$post_id]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['comment_count'];
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Comment added', 
            'comment' => $comment,
            'count' => $count
        ]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Track post downloads
function trackDownload($pdo, $user_id, $post_id) {
    try {
        // Record the download action
        $stmt = $pdo->prepare("INSERT INTO post_downloads (user_id, post_id, downloaded_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $post_id]);
        
        // Get post media path for download
        $mediaStmt = $pdo->prepare("SELECT media_path FROM posts_table WHERE id = ?");
        $mediaStmt->execute([$post_id]);
        $mediaPath = $mediaStmt->fetch(PDO::FETCH_ASSOC)['media_path'];
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Download tracked', 
            'media_path' => $mediaPath
        ]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Track post shares
function trackShare($pdo, $user_id, $post_id) {
    try {
        $stmt = $pdo->prepare("INSERT INTO post_shares (user_id, post_id, shared_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $post_id]);
        
        // Get share count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as share_count FROM post_shares WHERE post_id = ?");
        $countStmt->execute([$post_id]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['share_count'];
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Share tracked', 'count' => $count]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get users who liked a post
function getLikes($pdo, $post_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.profile_image, pl.created_at
            FROM post_likes pl
            JOIN users u ON pl.user_id = u.id
            WHERE pl.post_id = ?
            ORDER BY pl.created_at DESC
            LIMIT 20
        ");
        $stmt->execute([$post_id]);
        $likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get total like count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ?");
        $countStmt->execute([$post_id]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['like_count'];
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'likes' => $likes, 
            'total' => $count
        ]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get comments for a post
function getComments($pdo, $post_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, u.username, u.profile_image 
            FROM post_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$post_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get comment count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as comment_count FROM post_comments WHERE post_id = ?");
        $countStmt->execute([$post_id]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['comment_count'];
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'comments' => $comments, 
            'total' => $count
        ]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>