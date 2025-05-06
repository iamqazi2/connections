<?php
session_start();
require 'connection.php'; // Ensure $pdo is defined

// Dev error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get user ID from session
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die("Unauthorized access");
}

// Default paths
$defaultProfileImage = null;
$defaultResume = null;

// Function to handle file uploads
function handleFileUpload($file, $targetDir, $allowedTypes, $maxSize, $prefix = '') {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }

    // Verify file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) {
        throw new Exception("Invalid file type. Allowed: " . implode(', ', $allowedTypes));
    }

    // Verify file size
    if ($file['size'] > $maxSize) {
        throw new Exception("File too large. Max size: " . ($maxSize / 1024 / 1024) . "MB");
    }

    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $prefix . uniqid() . '.' . $ext;
    $targetPath = $targetDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Failed to save uploaded file");
    }

    return $targetPath;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_profile'])) {
            // Handle profile update
            $name = cleanInput($_POST['name'] ?? '');
            $headline = cleanInput($_POST['headline'] ?? '');
            $discipline = cleanInput($_POST['discipline'] ?? '');

            $profileImagePath = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $profileImagePath = handleFileUpload(
                    $_FILES['profile_image'],
                    'backend/uploads/profiles/',
                    ['image/jpeg', 'image/png', 'image/gif'],
                    2 * 1024 * 1024, // 2MB max
                    'profile_'
                );
            } 

            // Prepare SQL with conditional profile image update
            $sql = "UPDATE user_details SET 
                    name = :name, 
                    headline = :headline, 
                    discipline = :discipline";
            $params = [
                'name' => $name,
                'headline' => $headline,
                'discipline' => $discipline,
                'id' => $userId
            ];

            if ($profileImagePath) {
                $sql .= ", profile_image = :profile_image";
                $params['profile_image'] = $profileImagePath;
            }

            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

        } elseif (isset($_POST['update_links'])) {
            // Handle website and social media links update
            $website = cleanInput($_POST['website'] ?? '');
            $linkedin = cleanInput($_POST['linkedin'] ?? '');
            $behance = cleanInput($_POST['behance'] ?? '');
            $dribbble = cleanInput($_POST['dribbble'] ?? '');
            $flickr = cleanInput($_POST['flickr'] ?? '');

            $sql = "UPDATE user_details SET 
                    website = :website, 
                    linkedin = :linkedin, 
                    behance = :behance, 
                    dribbble = :dribbble, 
                    flickr = :flickr 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'website' => $website ?: null,
                'linkedin' => $linkedin ?: null,
                'behance' => $behance ?: null,
                'dribbble' => $dribbble ?: null,
                'flickr' => $flickr ?: null,
                'id' => $userId
            ]);

        } elseif (isset($_POST['update_resume'])) {
            // Handle resume update
            $resumePathDb = null;
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $resumePathDb = handleFileUpload(
                    $_FILES['resume'],
                    'backend/uploads/resumes/',
                    ['application/pdf'],
                    5 * 1024 * 1024, // 5MB max
                    'resume_'
                );
            }

            if ($resumePathDb) {
                $sql = "UPDATE user_details SET resume = :resume WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'resume' => $resumePathDb,
                    'id' => $userId
                ]);
            }
        }

        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all user details
$sql = "SELECT name, headline, discipline, website, linkedin, behance, dribbble, flickr, profile_image, resume 
        FROM user_details WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Set values or fallbacks
$name = $user['name'] ?? 'No Name';
$title = $user['headline'] ?? 'No Title';
$education = $user['discipline'] ?? 'No Education Info';
$website_url = $user['website'] ?? '#';
$linkedin_link = $user['linkedin'] ?? '#';
$behance_link = $user['behance'] ?? '#';
$dribbble_link = $user['dribbble'] ?? '#';
$flickr_link = $user['flickr'] ?? '#';
$profilePicturePath = !empty($user['profile_image']) ? $user['profile_image'] : $defaultProfileImage;
$resumePath = !empty($user['resume']) ? $user['resume'] : $defaultResume;

function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
function publicPath($path) {
    return 'backend/' . ltrim($path, '/backend');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profile Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans bg-gray-50 p-4">
  <?php include 'navbar.php'; ?>

  <div class="flex min-h-screen">
    <!-- Left Sidebar -->
    <div class="w-64 bg-white p-6 border-r border-gray-200">
      <?php include 'left-sidebar-2.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="w-[1046px] mx-auto bg-white rounded-lg shadow-sm">

      <!-- Profile Section -->
      <div class="bg-white rounded-lg shadow-md mb-6 p-5">
        <div class="flex justify-between items-center mb-4">
          <div class="flex items-center">
            <img src="<?php echo htmlspecialchars(publicPath($profilePicturePath)); ?>" alt="Profile Picture" class="w-[120px] h-[120px] rounded-full mr-4 object-cover">
            <div>
              <h1 class="text-xl font-bold text-[rgba(2,53,100,1)]"><?php echo htmlspecialchars($name); ?></h1>
              <p class="text-gray-600"><?php echo htmlspecialchars($title); ?></p>
              <p class="text-gray-500 text-[16px]"><?php echo htmlspecialchars($education); ?></p>
            </div>
          </div>
          <button onclick="openModal('profileModal')" class="text-blue-500 flex items-center border border-blue-500 rounded px-3 py-1 hover:bg-blue-50">
            <i class="fas fa-edit mr-1"></i> Edit
          </button>
        </div>
      </div>

      <!-- Website and Accounts -->
      <div class="bg-white rounded-lg shadow-md mb-6 p-5">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-[rgba(2,53,100,1)]">Website and Accounts</h2>
          <button onclick="openModal('linksModal')" class="text-blue-500 flex items-center border border-blue-500 rounded px-3 py-1 hover:bg-blue-50">
            <i class="fas fa-edit mr-1"></i> Edit
          </button>
        </div>

        <!-- Website -->
        <div class="flex items-center py-2 border-b">
          <div class="w-8 h-8 rounded-full bg-blue-900 flex items-center justify-center mr-3">
            <i class="fas fa-globe text-white text-[16px]"></i>
          </div>
          <div class="flex-grow">
            <div class="flex items-center gap-[40px)">
              <p class="text-[16px] font-medium">Website</p>
              <a href="<?php echo htmlspecialchars($website_url); ?>" class="text-gray-500 hover:text-blue-500" target="_blank">
                <?php echo htmlspecialchars($website_url); ?>
              </a>
            </div>
          </div>
        </div>

        <!-- Social Media Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
          <!-- LinkedIn -->
          <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
              <i class="fab fa-linkedin-in text-white"></i>
            </div>
            <p class="text-[16px] text-gray-600 mt-1">LinkedIn</p>
            <div class="flex items-center text-xs text-blue-500 mt-1">
              <span><?php echo htmlspecialchars($linkedin_link); ?></span>
            </div>
          </div>

          <!-- Behance -->
          <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
              <i class="fab fa-behance text-white"></i>
            </div>
            <p class="text-[16px] text-gray-600 mt-1">Behance</p>
            <div class="flex items-center text-xs text-blue-500 mt-1">
              <span><?php echo htmlspecialchars($behance_link); ?></span>
            </div>
          </div>

          <!-- Dribbble -->
          <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-pink-400 flex items-center justify-center">
              <i class="fab fa-dribbble text-white"></i>
            </div>
            <p class="text-[16px] text-gray-600 mt-1">Dribbble</p>
            <div class="flex items-center text-xs text-blue-500 mt-1">
              <span><?php echo htmlspecialchars($dribbble_link); ?></span>
            </div>
          </div>

          <!-- Flickr -->
          <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center">
              <i class="fab fa-flickr text-white"></i>
            </div>
            <p class="text-[16px] text-gray-600 mt-1">Flickr</p>
            <div class="flex items-center text-xs text-blue-500 mt-1">
              <span><?php echo htmlspecialchars($flickr_link); ?></span>
            </div>
          </div>
        </div>
      </div>
     
      <!-- Resume Section -->
      <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-[rgba(2,53,100,1)]">My Resume</h2>
          <button onclick="openModal('resumeModal')" class="text-blue-500 flex items-center border border-blue-500 rounded px-3 py-1 hover:bg-blue-50">
            <i class="fas fa-edit mr-1"></i> Edit
          </button>
        </div>

        <?php if ($resumePath): ?>
          <a href="<?php echo htmlspecialchars($resumePath); ?>" class="text-blue-500 hover:underline text-[16px] mb-4 inline-block" target="_blank" download>Download Resume</a>
          <div id="resume-content" class="border rounded-md overflow-hidden">
            <iframe src="<?php echo htmlspecialchars($resumePath); ?>" class="w-full h-[556px]" frameborder="0"></iframe>
          </div>
        <?php else: ?>
          <p class="text-gray-500">Resume not uploaded.</p>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- Profile Modal -->
  <div id="profileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Edit Profile</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_profile" value="1">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Headline</label>
          <input type="text" name="headline" value="<?php echo htmlspecialchars($title); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Discipline</label>
          <input type="text" name="discipline" value="<?php echo htmlspecialchars($education); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Profile Image (JPEG, PNG, GIF, max 2MB)</label>
          <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif" class="mt-1 block w-full">
        </div>
        <div class="flex justify-end">
          <button type="button" onclick="closeModal('profileModal')" class="mr-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
          <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Links Modal -->
  <div id="linksModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Edit Website and Accounts</h2>
      <form method="POST">
        <input type="hidden" name="update_links" value="1">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Website</label>
          <input type="url" name="website" value="<?php echo htmlspecialchars($website_url); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">LinkedIn</label>
          <input type="url" name="linkedin" value="<?php echo htmlspecialchars($linkedin_link); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Behance</label>
          <input type="url" name="behance" value="<?php echo htmlspecialchars($behance_link); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Dribbble</label>
          <input type="url" name="dribbble" value="<?php echo htmlspecialchars($dribbble_link); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Flickr</label>
          <input type="url" name="flickr" value="<?php echo htmlspecialchars($flickr_link); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="flex justify-end">
          <button type="button" onclick="closeModal('linksModal')" class="mr-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
          <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Resume Modal -->
  <div id="resumeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Edit Resume</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_resume" value="1">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Upload Resume (PDF, max 5MB)</label>
          <input type="file" name="resume" accept="application/pdf" class="mt-1 block w-full">
        </div>
        <div class="flex justify-end">
          <button type="button" onclick="closeModal('resumeModal')" class="mr-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
          <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(modalId) {
      document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }
  </script>
</body>
</html>