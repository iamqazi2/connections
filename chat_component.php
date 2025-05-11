<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'connections';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch logged-in user's details
$user_id = $_SESSION['user_id'] ?? null;
$user_email = '';

if ($user_id) {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_email = $user['email'] ?? 'Guest';
}

// Fetch all users for the sidebar (excluding the logged-in user)
$users_stmt = $pdo->prepare("
    SELECT u.id, ud.name, ud.profile_image 
    FROM users u 
    INNER JOIN user_details ud ON u.id = ud.user_id 
    WHERE u.id != :id
");
$users_stmt->execute(['id' => $user_id]);
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unread message status
$unread_users = [];
foreach ($users as $user) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = :receiver_id AND sender_id = :sender_id AND is_read = 0");
    $stmt->execute(['receiver_id' => $user_id, 'sender_id' => $user['id']]);
    $unread = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    if ($unread > 0) {
        $unread_users[$user['id']] = true;
    }
}

// Fetch messages for the selected user and mark as read
$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$messages = [];
$selected_user_name = '';

if ($selected_user_id) {
    $stmt = $pdo->prepare("SELECT name FROM user_details WHERE user_id = :id");
    $stmt->execute(['id' => $selected_user_id]);
    $selected_user = $stmt->fetch(PDO::FETCH_ASSOC);
    $selected_user_name = $selected_user['name'] ?? 'Unknown';

    $messages_stmt = $pdo->prepare("
        SELECT m.*, ud.name as sender_name 
        FROM messages m 
        LEFT JOIN user_details ud ON m.sender_id = ud.user_id 
        WHERE (m.sender_id = :user_id AND m.receiver_id = :selected_user_id) 
        OR (m.sender_id = :selected_user_id AND m.receiver_id = :user_id) 
        ORDER BY m.created_at ASC
    ");
    $messages_stmt->execute(['user_id' => $user_id, 'selected_user_id' => $selected_user_id]);
    $messages = $messages_stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = :receiver_id AND sender_id = :sender_id AND is_read = 0");
    $stmt->execute(['receiver_id' => $user_id, 'sender_id' => $selected_user_id]);
}

// Function to log errors
function logError($message) {
    $log_file = 'upload_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Handle sending a new message or image
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_user_id) {
    $message = trim($_POST['message'] ?? '');
    $image_path = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/chat-images/';
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $error_message = "Failed to create upload directory.";
                logError($error_message . " Path: " . realpath($upload_dir));
            }
        }
        // Check writability
        if (!is_writable($upload_dir)) {
            $error_message = "Upload directory is not writable.";
            logError($error_message . " Path: " . realpath($upload_dir) . ", Permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4));
        }

        if (!$error_message) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = mime_content_type($_FILES['image']['tmp_name']);
            if (!in_array($file_type, $allowed_types)) {
                $error_message = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
                logError($error_message . " Detected type: $file_type");
            } else {
                $file_name = uniqid() . '_' . preg_replace("/[^A-Za-z0-9._-]/", '', basename($_FILES['image']['name']));
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $image_path = 'chat-images/' . $file_name; // Correct path for database
                } else {
                    $error_message = "Failed to move uploaded image.";
                    logError("Move failed. Temp: {$_FILES['image']['tmp_name']}, Dest: $file_path");
                    logError("Upload dir permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4));
                    logError("Temp file exists: " . (file_exists($_FILES['image']['tmp_name']) ? 'Yes' : 'No'));
                }
            }
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error_codes = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server size limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary directory',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload'
        ];
        $error_code = $_FILES['image']['error'];
        $error_message = "Image upload error: " . ($error_codes[$error_code] ?? "Unknown error (code: $error_code)");
        logError($error_message);
    }

    // Insert message and/or image
    if (!$error_message && ($message || $image_path)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, image_path, created_at, is_read) VALUES (:sender_id, :receiver_id, :message, :image_path, NOW(), 0)");
            $stmt->execute([
                'sender_id' => $user_id,
                'receiver_id' => $selected_user_id,
                'message' => $message  , // Allow NULL for message
                'image_path' => $image_path
            ]);
            header("Location: chat_component.php?user_id=$selected_user_id");
            exit;
        } catch (PDOException $e) {
            $error_message = "Error saving message/image: " . $e->getMessage();
            logError($error_message);
        }
    }
}
$base_url = "backend/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Component</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        #image-preview-container {
            display: none; /* Hide by default */
            margin-bottom: 10px;
            position: relative;
        }
        
        #image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        #remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ff4d4d;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
     <?php include 'navbar.php'; ?>
    <div class="flex p-6 h-screen">
        <!-- Sidebar: List of users -->
        <div class="w-1/4 bg-white  rounded-md border-[#023564] p-4">
           <div class="flex justify-between items-center ">
             <h2 class="text-lg font-semibold mb-4">Chats</h2>
            <a href="dashboard.php"> <img src="images/icon.svg" alt="home"/></a>
           </div>
            <div class="space-y-2">
                <?php foreach ($users as $user): ?>
                      <a href="chat_component.php?user_id=<?= $user['id'] ?>" class="flex justify-between items-center p-2 rounded-lg hover:bg-gray-100 <?php echo $selected_user_id == $user['id'] ? 'bg-blue-100' : ''; ?>">

              <div class="flex  items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold">
                            <img src="<?= htmlspecialchars($base_url . $user['profile_image']) ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="ml-3">
                            <?php if (isset($unread_users[$user['id']])): ?>
                            <?php endif; ?>
                            <p class="text-sm font-medium inline"><?= htmlspecialchars($user['name']) ?></p>
                            <p class="text-xs text-gray-500">12:00 PM</p>

                        </div>
                       </div>


                     <span class="text-[#2A97FC] text-6xl mr-1">•</span>
                    </a>

                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="w-3/4 flex flex-col">
            <!-- Chat Header -->
            <?php if ($selected_user_id): ?>
                <div class="bg-[#023564] p-4 !text-white border rounded-md border-[#023564] flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold">
                        <?php
                        $stmt = $pdo->prepare("SELECT profile_image FROM user_details WHERE user_id = :id");
                        $stmt->execute(['id' => $selected_user_id]);
                        $selected_user = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <img src="<?= htmlspecialchars($base_url . $selected_user['profile_image']) ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
                    </div>
                    <h2 class="ml-3 text-lg font-semibold"><?= htmlspecialchars($selected_user_name) ?></h2>
                </div>

                <!-- Error Message -->
                <?php if ($error_message): ?>
                    <div class="bg-red-100 text-red-700 p-3 m-4 rounded-lg">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <!-- Messages Area -->
                <div class="flex-1 p-4 overflow-y-auto bg-gray-50">
                    <?php foreach ($messages as $msg): ?>
                        <div class="mb-4 flex <?php echo $msg['sender_id'] == $user_id ? 'justify-end' : 'justify-start'; ?>">
                            <div class="<?php echo $msg['sender_id'] == $user_id ? 'bg-[#3F8BC9] text-white' : 'bg-[#023564] text-white'; ?> p-3 rounded-lg max-w-xs">
                                <?php if (!empty($msg['image_path']) && file_exists(__DIR__ . '/' . $msg['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($msg['image_path']) ?>" alt="Uploaded Image" class="max-w-full rounded-lg mb-2">
                                <?php elseif (!empty($msg['image_path'])): ?>
                                    <p class="text-xs text-red-200">Image not found</p>
                                <?php endif; ?>
                                <?php if (($msg['message'])): ?>
                                    <p class="text-sm"><?= htmlspecialchars($msg['message']) ?></p>
                                <?php endif; ?>
                                <p class="text-xs mt-1 opacity-75"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Message Input -->
                <div class="bg-[#023564] p-4 !text-white border rounded-md border-[#023564]">
                    <form method="POST" enctype="multipart/form-data" class="flex flex-col">
                        <!-- Image Preview Container (Above Input) -->
                        <div id="image-preview-container" class="mb-3">
                            <img id="image-preview" alt="Image Preview">
                            <div id="remove-image">×</div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <label class="cursor-pointer">
                                <i class="fas fa-image text-white"></i>
                                <input type="file" name="image" id="image-input" accept="image/*" class="hidden">
                            </label>
                            <input type="text" name="message" placeholder="Type something..." class="flex-1 text-black p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    <p>Select a user to start chatting</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('image-input');
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const imagePreview = document.getElementById('image-preview');
        const removeImageBtn = document.getElementById('remove-image');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block'; // Show the preview container
                };
                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                clearImagePreview();
            }
        });

        // Remove image when clicking the X button
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = ''; // Clear the file input
            clearImagePreview();
        });

        function clearImagePreview() {
            imagePreviewContainer.style.display = 'none';
            imagePreview.src = '';
        }
    </script>
</body>
</html>