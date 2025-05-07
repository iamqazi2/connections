<?php
session_start();
require_once 'connection.php'; // Your provided connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Query to fetch all posts (no user_id filter)
    $stmt = $pdo->query("SELECT * FROM posts_table ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching posts: " . $e->getMessage());
}
?>
<?php
require 'auth.php';
?>
<?php
// Ensure session_start() is only called if no session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php'; // This includes your existing PDO connection

// Get the logged-in user ID from the session (assuming user is logged in)
$userId = $_SESSION['user_id'] ?? 0;  // Make sure to use the logged-in user's ID

// Default profile image in case there's no profile image available
$profilePicturePath = "images/profile.svg"; // Default image

// Fetch profile picture using PDO
$sql = "SELECT profile_image FROM user_details WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the profile image is available and update the path
if ($row && !empty($row['profile_image'])) {
    // If an image exists, use the path stored in the database
    $profilePicturePath =  $row['profile_image']; // Assuming images are stored in "uploads/profiles/"
} else {
    // If no image is found, the default profile image will be used
    $profilePicturePath = "images/profile.svg"; // Default image
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .width{
            min-height:100vh;
            overflow-y:auto;
        }
    </style>
</head>
<body class="bg-gray-100">
        <?php if (empty($posts)): ?>
            <p class="text-gray-600">No posts found.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="bg-white shadow-md rounded-lg p-4 mb-4">
                    <!-- Post Header -->
                    <div class="flex justify-between mb-4">
                        <span class="text-sm text-gray-500"><?php echo htmlspecialchars($post['post_type']); ?></span>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 mr-2">Created by You</span>
                            <img src="<?php echo htmlspecialchars($profilePicturePath); ?>"  alt="User Profile" class="w-8 h-8 rounded-full">
                        </div>
                    </div>

                    <!-- Post Body -->
                    <div class="flex mb-4">
                        <!-- Media Overview -->
                        <div class="w-20 h-20 rounded-lg overflow-hidden mr-4 bg-gray-200">
                            <img src="<?php echo htmlspecialchars($post['media_path']); ?>" alt="Media Preview" class="w-full h-full object-cover">
                        </div>

                        <!-- Post Details -->
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="text-gray-500 mb-2"><?php echo htmlspecialchars(substr($post['description'], 0, 100)); ?></p>
                            <div class="flex items-center space-x-2 mb-4">
                                <?php
                                $tags = explode(',', $post['tags']);
                                foreach ($tags as $tag) {
                                    echo '<span class="text-blue-500 text-sm">' . htmlspecialchars(trim($tag)) . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Post Description -->
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($post['description']); ?></p>

                    <!-- Post Media -->
                    <div class="mb-4">
                        <img src="<?php echo htmlspecialchars($post['media_path']); ?>" alt="Post Media" class="w-full h-[500px] object-cover rounded-lg">
                    </div>

                    <!-- Updated Social Actions to match the provided UI image -->
                    <div class="flex items-center justify-between px-4">
                        <button class="flex items-center text-[#023564] hover:text-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905v.714L7.05 5.95A5.5 5.5 0 002 11.5v.5c0 .163.045.325.135.47l2 3A2 2 0 006 17h4" />
                            </svg>
                            Like
                        </button>
                        <button class="flex items-center text-[#023564] hover:text-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Comment
                        </button>
                        <button class="flex items-center text-[#023564] hover:text-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </button>
                        <button class="flex items-center text-[#023564] hover:text-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            Share
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
</body>
</html>