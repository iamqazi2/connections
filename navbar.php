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
    $sql = "SELECT profile_image FROM user_details WHERE user_id = :id";
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
  <title>ConnectIn Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
  <link href="output.css" rel="stylesheet"/>
  <link href="styles.css" rel="stylesheet"/>
  <style>
    .seacrh{
      width:320px;
      height:54px;
    }
    .ul li a{
      color:rgba(63, 139, 201, 0.5);
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="text-white bg ">
  <div class=" mx-auto flex justify-between items-center py-4 px-6">
    
    <!-- Logo and Search -->
    <div class="flex items-center space-x-4">
      <a href="dashboard.php"><img src="images/logo.svg" alt="Logo" class="h-24 w-24" /></a>
      <div class="relative">
        <input 
          type="text" 
          placeholder="Search..." 
          class="text-sm text-black seacrh rounded-full px-6 py-2 focus:outline-none focus:ring focus:ring-gray-100"
          style="background-color: #C9DCEF;" />
        <img class="absolute right-4 top-1/2 transform -translate-y-1/2 h-8 w-8 text-gray-600" src="images/search.svg" />
      </div>
    </div>

    <!-- Navbar Menu -->
    <ul class="flex ul space-x-20">
      <li><a href="dashboard.php" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons.svg"><span class="text-xs">Home</span></a></li>
      <li><a href="dashboard.php" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons1.svg"><span class="text-xs">Explore</span></a></li>
      <li><a href="portals.php" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons2.svg"><span class="text-xs">My Portals</span></a></li>
      <li><a href="chat_component.php" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons3.svg"><span class="text-xs">Messages</span></a></li>
      <li><a href="#" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons4.svg"><span class="text-xs">Notifications</span></a></li>
      <li><a href="post_dashboard.php" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons5.svg" class="h-10"><span class="text-xs">Add Post</span></a></li>
    </ul>

    <!-- Profile Dropdown -->
    <div class="relative">
      <button id="profileDropdownButton" class="flex items-center btns texts space-x-2  px-4  ">
        <img src="<?php echo htmlspecialchars($fullImagePath); ?>" alt="Profile Picture" class="h rounded-full" />
        <span>My Profile</span>
      </button>

     <div id="profileDropdownMenu"
     class="hidden absolute right-0 mt-2 w-56 bg-white text-gray-800 rounded-xl shadow-lg z-50 transition-all duration-300 ease-in-out ring-1 ring-gray-200">
  
  <a href="profile_dashboard.php" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors rounded-t-xl">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
         stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M5.121 17.804A13.937 13.937 0 0012 20c3.042 0 5.824-1.02 8.029-2.732M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
    View Profile
  </a>

  <a href="" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
         stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9.75 3a.75.75 0 00-.75.75v1.5c0 .414.336.75.75.75h.75v1.5h-3v3h3V15H6.75a.75.75 0 00-.75.75v1.5c0 .414.336.75.75.75H9v1.5a.75.75 0 001.5 0v-1.5h3v1.5a.75.75 0 001.5 0v-1.5h2.25a.75.75 0 00.75-.75v-1.5a.75.75 0 00-.75-.75H15v-4.5h3v-3h-3V6h.75a.75.75 0 00.75-.75v-1.5a.75.75 0 00-.75-.75H14.25a.75.75 0 00-.75.75v1.5a.75.75 0 00.75.75H15v1.5h-3v-1.5h.75a.75.75 0 00.75-.75v-1.5a.75.75 0 00-.75-.75H9.75z" />
    </svg>
    Settings
  </a>

  <form action="backend/logout.php" method="POST">
    <button type="submit"
            class="flex items-center gap-3 px-5 py-3 text-left w-full hover:bg-red-50 transition-colors text-red-600 font-medium rounded-b-xl">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5m0 0a2 2 0 011.293.464l1.293 1.293A2 2 0 0116 8h1a2 2 0 012 2v4a2 2 0 01-2 2h-1a2 2 0 01-1.414-.586l-1.293-1.293A2 2 0 0113 13v-2" />
      </svg>
      Logout
    </button>
  </form>
</div>

    </div>
  </div>
</nav>

<!-- Dropdown Toggle Script -->
<script>
  const dropdownButton = document.getElementById('profileDropdownButton');
  const dropdownMenu = document.getElementById('profileDropdownMenu');

  dropdownButton.addEventListener('click', () => {
    dropdownMenu.classList.toggle('hidden');
  });

  window.addEventListener('click', (e) => {
    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.classList.add('hidden');
    }
  });
</script>
</body>
</html>
