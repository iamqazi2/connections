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
    $profilePicturePath = "backend/" . $row['profile_image']; // Assuming images are stored in "uploads/profiles/"
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="output.css">
    <title>Home</title>
      <link href="styles.css" rel="stylesheet"/>
</head>
<body class="bg">
    <?php include 'navbar.php'; ?>

    <!-- Main Layout -->
    <div class="flex space-x-4 p-4">
<!-- Left Sidebar -->
<div class="w-1/4 bg-white shadow-md p-4 rounded-lg ">
    <!-- Sidebar Items -->
    <ul class="space-y-4 mt-10">
        <!-- Dashboard (Active by default) -->
        <li class="flex items-center space-x-3 hover:bg-gray-100 pl-10 rounded-lg p-2 cursor-pointer active-item">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#023564] active:text-[#023564] active:shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3z" />
            </svg>
            <span class="text-[#023564] font-semibold  active:text-[#023564]">Personalized Feed</span>
        </li>

        <!-- Community -->
        <li class="flex items-center space-x-3 hover:bg-gray-100 pl-10 rounded-lg p-2 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#023564] hover:text-[#023564]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7v10l5-5 5 5V7" />
            </svg>
            <span class="text-[#023564] font-semibold  hover:text-[#023564]">Community</span>
        </li>

        <!-- Explore -->
        <li class="flex items-center space-x-3 hover:bg-gray-100 pl-10 rounded-lg p-2 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#023564] hover:text-[#023564]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-[#023564] font-semibold  hover:text-[#023564]">Explore</span>
        </li>

        <!-- Opportunity -->
        <li class="flex items-center space-x-3 hover:bg-gray-100 pl-10 rounded-lg p-2 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#023564] hover:text-[#023564]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="text-[#023564] font-semibold  hover:text-[#023564]">Opportunity</span>
        </li>

        <!-- Settings -->
        <li class="flex items-center space-x-3 hover:bg-gray-100 pl-10 rounded-lg p-2 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#023564] hover:text-[#023564]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.104 0 2 .896 2 2s-.896 2-2 2-2-.896-2-2 .896-2 2-2zm0 4c-1.104 0-2-.896-2-2s.896-2 2-2 2 .896 2 2-.896 2-2 2zM12 16c-2.667 0-5.333 1-7.5 3S2 22 2 22h20s-2-3-4.5-3S14.667 16 12 16z" />
            </svg>
            <span class="text-[#023564] font-semibold  hover:text-[#023564]">Settings</span>
        </li>
    </ul>
</div>



        <!-- Center Container -->
        <div class="w-1/2">
    <!-- Add Post Section -->
    <div class="">
        
        <!-- Profile & Post Type Bar -->
        <div class="bg-white  shadow-md rounded-lg p-4 mb-14"><div class=" flex items-center mb-4">
            <!-- User Profile -->
            <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" alt="User Profile" class="w-16 h-16 rounded-full mr-4">
            
            <!-- Post Type Bar -->
            <div style="box-shadow: 0px 0px 4px 0px #2A97FC1A;" class="flex justify-between items-center bg-white rounded-full  py-4 px-2 flex-grow">
                <span class="text-[#023564] opacity-50 mx-2">Post new Job, Internship, Research and startup Idea</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 text-[#023564] opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
            </div>
        </div>

        </div>

        <!-- Post Card -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <!-- Post Header -->
            <div class="flex justify-between mb-4">
                <span class="text-sm text-gray-500">Post Type</span>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500 mr-2">Created by</span>
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="User Profile" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <!-- Post Body -->
            <div class="flex mb-4">
                <!-- Media Overview -->
                <div class="w-20 h-20 rounded-lg overflow-hidden mr-4 bg-gray-200">
                    <img src="https://via.placeholder.com/150" alt="Media Preview" class="w-full h-full object-cover">
                </div>

                <!-- Post Details -->
                <div class="flex-grow">
                    <h3 class="text-lg font-semibold mb-2">Post Title</h3>
                    <p class="text-gray-500 mb-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-blue-500 text-sm">#Tag1</span>
                        <span class="text-blue-500 text-sm">#Tag2</span>
                    </div>
                </div>

                <!-- Enroll Button -->
                <div class="flex items-center space-x-2">
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-full flex items-center">
                        Enroll
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Post Description -->
            <p class="text-gray-600 mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.</p>

            <!-- Post Media -->
            <div class="mb-4">
                <img src="https://via.placeholder.com/600x300" alt="Post Media" class="w-full h-64 object-cover rounded-lg">
            </div>

            <!-- Social Actions -->
            <div class="flex items-center space-x-4">
                <button class="flex items-center text-gray-600 hover:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Like
                </button>
                <button class="flex items-center text-gray-600 hover:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Comment
                </button>
                <button class="flex items-center text-gray-600 hover:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Share
                </button>
                <button class="flex items-center text-gray-600 hover:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Download
                </button>
            </div>
        </div>
    </div>
</div>


        <!-- Right Section -->
        <div class="w-1/4 bg-white p-4">
            <h2 class="text-xl font-semibold mb-4">Explore</h2>
            <!-- Explore Card -->
            <div class="bg-white  rounded-lg p-4">
                <h3 class="font-semibold text-lg">Suggested Posts</h3>
                <ul class="space-y-2 mt-4">
                    <li class="flex items-center">
                        <span>Post 1: Interesting Topic</span>
                    </li>
                    <li class="flex items-center">
                        <span>Post 2: Fun Discussion</span>
                    </li>
                    <li class="flex items-center">
                        <span>Post 3: Latest Trends</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
