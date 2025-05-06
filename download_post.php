<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'], $_POST['post_id'])) exit;

$user_id = $_SESSION['user_id'];
$post_id = (int) $_POST['post_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO post_downloads (post_id, user_id) VALUES (?, ?)");
    $stmt->execute([$post_id, $user_id]);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>