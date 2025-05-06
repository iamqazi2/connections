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
      <img src="images/logo.svg" alt="Logo" class="h-24 w-24" />
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
      <li><a href="#" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons.svg"><span class="text-xs">Home</span></a></li>
      <li><a href="#" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons1.svg"><span class="text-xs">Explore</span></a></li>
      <li><a href="#" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons2.svg"><span class="text-xs">My Portals</span></a></li>
      <!-- <li><a href="#" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons3.svg"><span class="text-xs">Messages</span></a></li> -->
      <li><a href="#" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons4.svg"><span class="text-xs">Notifications</span></a></li>
      <li><a href="#" class="flex flex-col items-center text-gray-800 hover:text-blue-400"><img src="images/icons5.svg" class="h-10"><span class="text-xs">Add Post</span></a></li>
    </ul>

    <!-- Profile Dropdown -->
    <div class="relative">
      <button id="profileDropdownButton" class="flex items-center btns texts space-x-2  px-4  ">
        <img src="<?php echo htmlspecialchars($fullImagePath); ?>" alt="Profile Picture" class="h rounded-full" />
        <span>My Profile</span>
      </button>

      <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white text-black rounded-lg shadow-md z-10">
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">View Profile</a>
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
        <form action="backend/logout.php" method="POST">
          <button type="submit" class="block px-4 py-2 text-left w-full hover:bg-gray-100">Logout</button>
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
