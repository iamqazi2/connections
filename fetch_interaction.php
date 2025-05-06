<?php
require_once 'connection.php';

if (!isset($_GET['post_id'])) exit;

$post_id = (int) $_GET['post_id'];

// Likes
$likes = $pdo->prepare("SELECT u.name FROM post_likes pl JOIN users u ON pl.user_id = u.id WHERE post_id = ?");
$likes->execute([$post_id]);
$like_users = $likes->fetchAll(PDO::FETCH_COLUMN);

// Comments
$comments = $pdo->prepare("SELECT u.name, u.profile_pic, pc.comment_text, pc.created_at FROM post_comments pc JOIN users u ON pc.user_id = u.id WHERE post_id = ? ORDER BY pc.created_at DESC");
$comments->execute([$post_id]);
$comment_data = $comments->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'likes' => $like_users,
    'comments' => $comment_data
]);
?>