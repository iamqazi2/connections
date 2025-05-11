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
// $debugMessages = "Debug: Session User ID = " . $userId . "<br>";

$profilePicturePath = "images/profile.svg";

if ($userId === 0) {
    // $debugMessages .= "Debug: No user_id in session. Please ensure you are logged in.<br>";
} else {
    $sql = "SELECT profile_image FROM user_details WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // $debugMessages .= "Debug: User found in user_details table.<br>";
        if (!empty($row['profile_image'])) {
            // Prepend baseUrl to the image path from the database
            $fullImagePath = $baseUrl . $row['profile_image'];
            // $debugMessages .= "Debug: Profile image path from DB (adjusted) = " . $fullImagePath . "<br>";
            
            // Use the adjusted path for display
            $profilePicturePath = $fullImagePath;
            
            // Check if the image file exists at the adjusted path
            if (!file_exists($fullImagePath)) {
                // $debugMessages .= "Debug: Image file does not exist at path: " . $fullImagePath . ". Falling back to default.<br>";
                $profilePicturePath = "images/profile.svg";
            }
        } else {
            // $debugMessages .= "Debug: Profile image is empty or NULL for user ID " . $userId . ". Using default image.<br>";
        }
    } else {
        // $debugMessages .= "Debug: No user found in user_details table with ID " . $userId . ". Using default image.<br>";
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
  <title>Home</title>
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
    .hidden {
  display: none;
}

    .sidebar-containers {
      width: 25% !important;
      background-color: white;
      box-shadow: 0 10px 15px -3px rgba(63, 139, 201, 0.5), 0 4px 6px -2px rgba(63, 139, 201, 0.25) !important;
      padding: 1rem;
      border: 1px solid rgba(63, 139, 201, 0.5); 
      border-radius: 0.5rem;
      height: fit-content !important;
    }
    .border{
        border:1px solid rgba(63, 139, 201, 0.5) !important;
    }
    .borders{
        border:1px solid rgba(63, 139, 201, 0.5) !important;
    }
    .column{
      display:flex;
      flex-direction:column;
      
    }
    
  </style>
</head>
<body class="bg">
  <!-- Debug Output -->
  <!-- <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 5px;">
    <?php echo $debugMessages; ?>
  </div> -->

  <?php include 'navbar.php'; ?>

  <!-- Main Layout -->
  <div class="flex   space-x-4 p-4 h-screen">
    
  
 <div class="column">
   <?php include 'side-bar-feeds.php'; ?>
   <?php include 'left-sidebar.php'; ?>
    
    <!-- Left Sidebar -->
    <!-- <div 
     class="w-1/4 sidebar-container bg-white !shadow-lg !shadow-[#3F8BC9] p-4 border border-[#3F8BC9] rounded-lg !h-[340px] overflow-y-auto">
      <ul class="space-y-4 mt-10">
         <li class="flex items-center space-x-3 hover:bg-gray-100 p-4 rounded-lg cursor-pointer">
          <img src="images/simple.svg" alt="House Icon" class="w-6 h-6 text-gray-500">
          <span class="text-gray-500 font-medium">Simple Post</span>
        </li>
        <li class="flex items-center space-x-3 hover:bg-gray-100 p-4 rounded-lg cursor-pointer">
          <img src="images/opportunity.svg" alt="House Icon" class="w-6 h-6 text-gray-500">
          <span class="text-gray-500 font-medium">New Opportunities</span>
        </li>
        <li class="flex items-center space-x-3 hover:bg-gray-100 p-4 rounded-lg cursor-pointer">
          <img src="images/research.svg" alt="House Icon" class="w-6 h-6 text-gray-500">
          <span class="text-gray-500 font-medium">New Research Ideas</span>
        </li>
        <li class="flex items-center space-x-3 hover:bg-gray-100 p-4 rounded-lg cursor-pointer">
          <img src="images/startup.svg" alt="House Icon" class="w-6 h-6 text-gray-500">
          <span class="text-gray-500 font-medium">New Startup Ideas</span>
        </li>
        <li class="flex items-center space-x-3 hover:bg-gray-100 p-4 rounded-lg cursor-pointer">
          <img src="images/collaboration.svg" alt="House Icon" class="w-6 h-6 text-gray-500">
          <span class="text-gray-500 font-medium">New Collaborations</span>
        </li>
      </ul>
    </div> -->
 </div>

    <!-- Center Container -->
    <div class="w-1/2 ">
      <div class="h-full flex flex-col space-y-6 p-4">
        <!-- Add Post Section -->
        <div class="bg-white shadow-md borders rounded-lg p-4">
          <div class="flex items-center mb-4">
            <!-- User Profile -->
            <img src="<?php echo htmlspecialchars($fullImagePath); ?>" alt="User Profile" class="w-16 h-16 rounded-full mr-4">
            <!-- Post Type Bar -->
            <div style="box-shadow: 0px 0px 4px 0px #2A97FC1A; " class="flex border justify-between items-center bg-white rounded-full py-4 px-2 flex-grow">
              <span class="text-[#023564] opacity-50 mx-2">Post new Job, Internship, Research and startup Idea</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 text-[#023564] opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
          </div>
        </div>

        <div class="h-full overflow-y-auto">
         <div id="all-posts" class="post-section">
  <?php include 'startup_posts.php'; ?>
  <?php include 'collaborations_posts.php'; ?>
  <?php include 'job_posts.php'; ?>
  <?php include 'simple_posts.php'; ?>
   <?php include 'research_post_ui.php'; ?>
</div>

<div id="simple-posts" class="post-section hidden">
  <?php include 'simple_posts.php'; ?>
</div>

<div id="job-posts" class="post-section hidden">
  <?php include 'job_posts.php'; ?>
</div>

<div id="startup-posts" class="post-section hidden">
  <?php include 'startup_posts.php'; ?>
</div>
<div id="research_post_ui" class="post-section hidden">
  <?php include 'research_post_ui.php'; ?>
</div>

<div id="collaboration-posts" class="post-section hidden">
  <?php include 'collaborations_posts.php'; ?>
</div>

        </div>
      </div>
    </div>

    <!-- Right Section -->
    <div class="w-1/4 sidebar-containers  bg-white p-4 h-full overflow-y-auto">
       <?php include 'explore_ui.php'; ?>
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