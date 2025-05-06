<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'], $_POST['post_id'], $_POST['comment_text'])) exit;

$user_id = $_SESSION['user_id'];
$post_id = (int) $_POST['post_id'];
$comment = trim($_POST['comment_text']);

if ($comment === '') exit;

try {
    $stmt = $pdo->prepare("INSERT INTO post_comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $comment]);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>