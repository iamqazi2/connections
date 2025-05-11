<?php
require 'auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

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
  <title>Profile</title>
  <style>
    .sidebar-container {
      width: 100% !important;
      box-shadow: 0 10px 15px -3px rgba(63, 139, 201, 0.5), 0 4px 6px -2px rgba(63, 139, 201, 0.25) !important;
      padding: 1rem;
      border: 1px solid rgba(63, 139, 201, 0.5); 
      border-radius: 0.5rem;
      height: fit-content !important;
    }
    .hidden {
      display: none;
    }
    .sidebar-containers {
      background-color: white;
      box-shadow: 0 10px 15px -3px rgba(63, 139, 201, 0.5), 0 4px 6px -2px rgba(63, 139, 201, 0.25) !important;
      padding: 1rem;
      border: 1px solid rgba(63, 139, 201, 0.5); 
      border-radius: 0.5rem;
      height: fit-content !important;
    }
    .border {
      border: 1px solid rgba(63, 139, 201, 0.5) !important;
    }
    .borders {
      border: 1px solid rgba(63, 139, 201, 0.5) !important;
    }
    .column {
      display: flex;
      flex-direction: column;
    }

    /* Full width when profile is visible */
    #profile:not(.hidden) {
      width: 100% !important;
    }
    .widthsss{
        width:30%;
    }
  </style>
</head>
<body class="bg">
  <?php include 'navbar.php'; ?>

  <div class="flex gap-10 w-full p-4 h-screen">
    <div class="column widthsss">
      <?php include 'profile_sidebar.php'; ?>
    </div>

    <!-- Center Container -->
    <div class="flex-1 h-full overflow-y-auto">
      <div id="profile" class="post-section w-full">
        <?php include 'my-profile.php'; ?>
      </div>

      <div id="connections" class="post-section hidden">
        <?php include 'connections_ui.php'; ?>
      </div>

      <div id="manage-interest" class="post-section hidden">
        <?php include 'Manage-Interests.php'; ?>
      </div>

      <div id="settings" class="post-section hidden">
        <?php include 'settings.php'; ?>
      </div>
    </div>
  </div>

  <script>
    document.querySelectorAll('.sidebar-container li').forEach(item => {
      item.addEventListener('click', () => {
        const target = item.getAttribute('data-target');
        document.querySelectorAll('.post-section').forEach(section => {
          section.classList.add('hidden');
        });
        document.getElementById(target).classList.remove('hidden');
      });
    });
  </script>
</body>
</html>