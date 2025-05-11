<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: the_login_page.php');
    exit;
}

$sessionUserId = $_SESSION['user_id'];

// Include database connection
require_once 'connection.php';

// Map session user_id to the correct id in user_details
$currentUserId = null;
try {
    $stmt = $pdo->prepare('SELECT id FROM user_details WHERE id = ?');
    $stmt->execute([$sessionUserId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentUserId = $user['id'] ?? null;
    if (!$currentUserId) {
        die('Logged-in user not found in database. Check session user_id.');
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Handle connection request
$requestMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'])) {
    $receiverId = (int)$_POST['receiver_id'];
    
    if ($receiverId && $receiverId !== $currentUserId) {
        try {
            // Check if request already exists
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM connection_requests WHERE sender_id = ? AND receiver_id = ? AND status = "pending"');
            $stmt->execute([$currentUserId, $receiverId]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare('INSERT INTO connection_requests (sender_id, receiver_id, status, created_at) VALUES (?, ?, "pending", NOW())');
                $stmt->execute([$currentUserId, $receiverId]);
                $requestMessage = 'Connection request sent! Sender ID: ' . $currentUserId . ', Receiver ID: ' . $receiverId;
            } else {
                $requestMessage = 'Connection request already pending!';
            }
        } catch (PDOException $e) {
            $requestMessage = 'Error sending connection request: ' . $e->getMessage();
        }
    } else {
        $requestMessage = 'Invalid request. Receiver ID: ' . $receiverId . ', Current User ID: ' . $currentUserId;
    }
}

// Handle accept/reject connection request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['sender_id'])) {
    $senderId = (int)$_POST['sender_id'];
    $action = $_POST['action'];
    
    if ($senderId && in_array($action, ['accept', 'reject'])) {
        try {
            $newStatus = $action === 'accept' ? 'accepted' : 'rejected';
            $stmt = $pdo->prepare('UPDATE connection_requests SET status = ? WHERE sender_id = ? AND receiver_id = ? AND status = "pending"');
            $stmt->execute([$newStatus, $senderId, $currentUserId]);
            $requestMessage = $action === 'accept' ? 'Connection accepted!' : 'Connection rejected!';
        } catch (PDOException $e) {
            $requestMessage = 'Error processing request: ' . $e->getMessage();
        }
    }
}

// Handle remove connection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['connection_id'])) {
    $connectionId = (int)$_POST['connection_id'];
    try {
        $stmt = $pdo->prepare('UPDATE connection_requests SET status = "rejected" WHERE id = ? AND status = "accepted" AND (sender_id = ? OR receiver_id = ?)');
        $stmt->execute([$connectionId, $currentUserId, $currentUserId]);
        $requestMessage = 'Connection removed!';
    } catch (PDOException $e) {
        $requestMessage = 'Error removing connection: ' . $e->getMessage();
    }
}

// Handle search query
$searchQuery = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
}

// Fetch data for each tab with search filter
try {
    // Base condition for search
    $searchCondition = $searchQuery ? 'AND (name LIKE ? OR headline LIKE ?)' : '';
    $searchParams = $searchQuery ? ["%$searchQuery%", "%$searchQuery%"] : [];

    // Explore Users (all users except current user)
    $stmt = $pdo->prepare("SELECT id, name, profile_image, headline FROM user_details WHERE id != ? $searchCondition");
    $stmt->execute(array_merge([$currentUserId], $searchParams));
    $exploreUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // All Connections (accepted connections)
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.profile_image, u.headline, cr.id as connection_id
        FROM user_details u
        INNER JOIN connection_requests cr ON 
            (cr.sender_id = ? AND cr.receiver_id = u.id AND cr.status = 'accepted')
            OR (cr.receiver_id = ? AND cr.sender_id = u.id AND cr.status = 'accepted')
        WHERE 1=1 $searchCondition
    ");
    $stmt->execute(array_merge([$currentUserId, $currentUserId], $searchParams));
    $connections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Connection Requests (pending requests to current user)
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.profile_image, u.headline, cr.sender_id
        FROM user_details u
        INNER JOIN connection_requests cr ON cr.sender_id = u.id
        WHERE cr.receiver_id = ? AND cr.status = 'pending' $searchCondition
    ");
    $stmt->execute(array_merge([$currentUserId], $searchParams));
    $connectionRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recommended (all users except current user, same as explore)
    $stmt = $pdo->prepare("SELECT id, name, profile_image, headline FROM user_details WHERE id != ? $searchCondition");
    $stmt->execute(array_merge([$currentUserId], $searchParams));
    $recommendedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $exploreUsers = $connections = $connectionRequests = $recommendedUsers = [];
    $errorMessage = 'Error fetching users: ' . $e->getMessage();
}

$base_url = "backend/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Management UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        .tab-indicator {
            height: 3px;
            background-color: #1a56db;
            bottom: 0;
            position: absolute;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Navigation Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex justify-between">
                <div class="flex flex-1 relative">
                    <div class="w-1/4 py-4 px-2 text-center cursor-pointer tab-item relative active" data-tab="explore">
                        <div class="text-gray-500 text-sm">Explore Users</div>
                        <div class="text-blue-800 font-semibold text-xl"><?php echo count($exploreUsers); ?></div>
                        <div class="tab-indicator w-full"></div>
                    </div>
                    <div class="w-1/4 py-4 px-2 text-center cursor-pointer tab-item relative" data-tab="connections">
                        <div class="text-gray-500 text-sm">All Connections</div>
                        <div class="text-gray-700 font-semibold text-xl"><?php echo count($connections); ?></div>
                    </div>
                    <div class="w-1/4 py-4 px-2 text-center cursor-pointer tab-item relative" data-tab="requests">
                        <div class="text-gray-500 text-sm">Connection Requests</div>
                        <div class="text-gray-700 font-semibold text-xl"><?php echo count($connectionRequests); ?></div>
                    </div>
                    <div class="w-1/4 py-4 px-2 text-center cursor-pointer tab-item relative" data-tab="recommended">
                        <div class="text-gray-500 text-sm">Recommended</div>
                        <div class="text-gray-700 font-semibold text-xl"><?php echo count($recommendedUsers); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="relative mb-8">
            <form method="GET" action="">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" class="w-full bg-blue-100 rounded-full py-3 px-6 text-gray-700 focus:outline-none" placeholder="Search">
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>
        
        <!-- Request Feedback -->
        <?php if ($requestMessage): ?>
            <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded-lg">
                <?php echo htmlspecialchars($requestMessage); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($errorMessage)): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>
        
        <!-- Tab Content -->
        <div class="tab-content" id="explore">
            <h2 class="text-gray-700 text-lg font-medium mb-4">Explore Users</h2>
            <div class="space-y-4">
                <?php if (empty($exploreUsers)): ?>
                    <p class="text-gray-500">No users found.</p>
                <?php else: ?>
                    <?php foreach ($exploreUsers as $user): ?>
                        <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="<?php echo htmlspecialchars($base_url . ($user['profile_image'] ?: '/default-profile.png')); ?>" alt="Profile" class="h-12 w-12 rounded-full mr-4 object-cover">
                                <div>
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h3>
                                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['headline'] ?: 'No headline'); ?></p>
                                </div>
                            </div>
                            <?php
                            $stmt = $pdo->prepare('SELECT COUNT(*) FROM connection_requests WHERE sender_id = ? AND receiver_id = ? AND status = "pending"');
                            $stmt->execute([$currentUserId, $user['id']]);
                            $requestSent = $stmt->fetchColumn() > 0;
                            ?>
                            <?php if (!$requestSent): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <button type="submit" class="bg-white text-blue-600 border border-blue-600 rounded-full px-6 py-2 flex items-center hover:bg-blue-50 transition-colors">
                                        Add
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-500">Request Pending</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-content hidden" id="connections">
            <h2 class="text-gray-700 text-lg font-medium mb-4">All Connections</h2>
            <div class="space-y-4">
                <?php if (empty($connections)): ?>
                    <p class="text-gray-500">No connections found.</p>
                <?php else: ?>
                    <?php foreach ($connections as $user): ?>
                        <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="<?php echo htmlspecialchars($base_url . ($user['profile_image'] ?: '/default-profile.png')); ?>" alt="Profile" class="h-12 w-12 rounded-full mr-4 object-cover">
                                <div>
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h3>
                                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['headline'] ?: 'No headline'); ?></p>
                                </div>
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="connection_id" value="<?php echo htmlspecialchars($user['connection_id']); ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="bg-white text-red-600 border border-red-600 rounded-full px-6 py-2 flex items-center hover:bg-red-50 transition-colors">
                                    Remove
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-content hidden" id="requests">
            <h2 class="text-gray-700 text-lg font-medium mb-4">Connection Requests</h2>
            <div class="space-y-4">
                <?php if (empty($connectionRequests)): ?>
                    <p class="text-gray-500">No connection requests found.</p>
                <?php else: ?>
                    <?php foreach ($connectionRequests as $user): ?>
                        <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="<?php echo htmlspecialchars($base_url . ($user['profile_image'] ?: '/default-profile.png')); ?>" alt="Profile" class="h-12 w-12 rounded-full mr-4 object-cover">
                                <div>
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h3>
                                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['headline'] ?: 'No headline'); ?></p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <form method="POST" action="">
                                    <input type="hidden" name="sender_id" value="<?php echo htmlspecialchars($user['sender_id']); ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="bg-white text-green-600 border border-green-600 rounded-full px-6 py-2 flex items-center hover:bg-green-50 transition-colors">
                                        Accept
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="">
                                    <input type="hidden" name="sender_id" value="<?php echo htmlspecialchars($user['sender_id']); ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="bg-white text-red-600 border border-red-600 rounded-full px-6 py-2 flex items-center hover:bg-red-50 transition-colors">
                                        Reject
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-content hidden" id="recommended">
            <h2 class="text-gray-700 text-lg font-medium mb-4">Recommended</h2>
            <div class="space-y-4">
                <?php if (empty($recommendedUsers)): ?>
                    <p class="text-gray-500">No users found.</p>
                <?php else: ?>
                    <?php foreach ($recommendedUsers as $user): ?>
                        <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="<?php echo htmlspecialchars($base_url . ($user['profile_image'] ?: '/default-profile.png')); ?>" alt="Profile" class="h-12 w-12 rounded-full mr-4 object-cover">
                                <div>
                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h3>
                                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['headline'] ?: 'No headline'); ?></p>
                                </div>
                            </div>
                            <?php
                            $stmt = $pdo->prepare('SELECT COUNT(*) FROM connection_requests WHERE sender_id = ? AND receiver_id = ? AND status = "pending"');
                            $stmt->execute([$currentUserId, $user['id']]);
                            $requestSent = $stmt->fetchColumn() > 0;
                            ?>
                            <?php if (!$requestSent): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <button type="submit" class="bg-white text-blue-600 border border-blue-600 rounded-full px-6 py-2 flex items-center hover:bg-blue-50 transition-colors">
                                        Add
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-500">Request Pending</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-item');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    // Update tab styling
                    tabs.forEach(t => {
                        t.classList.remove('active');
                        t.querySelector('.tab-indicator')?.remove();
                        t.querySelector('div:nth-child(2)').classList.remove('text-blue-800');
                        t.querySelector('div:nth-child(2)').classList.add('text-gray-700');
                    });
                    
                    tab.classList.add('active');
                    tab.querySelector('div:nth-child(2)').classList.remove('text-gray-700');
                    tab.querySelector('div:nth-child(2)').classList.add('text-blue-800');
                    
                    const indicator = document.createElement('div');
                    indicator.className = 'tab-indicator w-full';
                    tab.appendChild(indicator);

                    // Show/hide tab content
                    const targetTab = tab.getAttribute('data-tab');
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        if (content.id === targetTab) {
                            content.classList.remove('hidden');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>