<?php
require 'auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

// Define base URL for image paths
$directory = "backend/";
$baseUrl = $directory;

$userId = $_SESSION['user_id'] ?? 0;

$profilePicturePath = "images/profile.svg";

if ($userId !== 0) {
    $sql = "SELECT profile_image FROM user_details WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['profile_image'])) {
        $fullImagePath = $baseUrl . $row['profile_image'];
        if (file_exists($fullImagePath)) {
            $profilePicturePath = $fullImagePath;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="output.css">
  <link rel="stylesheet" href="styles.css">
  <title>Post Dashboard</title>
  <style>
     .sidebar-container {
      width: 100% !important;
      background-color: white;
      box-shadow: 0 10px 15px -3px rgba(63, 139, 201, 0.5), 0 4px 6px -2px rgba(63, 139, 201, 0.25) !important;
      padding: 1rem;
      border: 1px solid rgba(63, 139, 201, 0.5); 
      border-radius: 0.5rem;
      height: fit-content !important;
      margin-top:30px;
    }
  
    .columns {
    width:30%;
    }
  </style>
</head>
<body class="bg">
  <?php include 'navbar.php'; ?>

  <!-- Main Layout -->
  <div class="flex space-x-4 p-4 h-screen">
    <div class="columns">
      <?php include 'post-sidebar.php'; ?>
    </div>

    <!-- Center Container -->
    <div class="w-full h-full flex flex-col space-y-6" id="content-container">
        <!-- Content will be loaded here dynamically -->
        <?php include 'add_posts.php'; ?> <!-- Load Simple Posts by default -->
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const sidebarItems = document.querySelectorAll('.post-sidebar-item');
      const contentContainer = document.getElementById('content-container');

      sidebarItems.forEach(item => {
        item.addEventListener('click', async () => {
          // Remove active class from all items
          sidebarItems.forEach(i => {
            i.classList.remove('post-sidebar-active', 'bg-gray-100');
          });

          // Add active class to clicked item
          item.classList.add('post-sidebar-active', 'bg-gray-100');

          // Determine which file to fetch based on the text content
          const text = item.querySelector('span').textContent;
          let file;
          switch (text) {
            case 'Simple Posts':
              file = 'add_posts.php';
              break;
            case 'New Opportunities':
              file = 'opportunity.php';
              break;
            case 'New Research Ideas':
              file = 'research_post.php';
              break;
            case 'Startup Ideas':
              file = 'startup.php';
              break;
            case 'Collaborations':
              file = 'collaboration.php';
              break;
            default:
              file = 'add_posts.php';
          }

          try {
            const response = await fetch(file);
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            const content = await response.text();
            contentContainer.innerHTML = content;
          } catch (error) {
            contentContainer.innerHTML = '<p>Error loading content. Please try again.</p>';
            console.error('Error:', error);
          }
        });
      });
    });
  </script>
</body>
</html>