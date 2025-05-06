<?php 
header('Content-Type: application/json');
session_start();

// Include database connection
require '../connection.php';  // This will include the PDO connection

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get action parameter
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'] ?? null; // Replace with your auth logic

    if (!$userId) {
        throw new Exception('User not authenticated');
    }

    // Handle both next and save actions
    if ($action === 'next' || $action === 'save') {
        // Process and validate common data
        $profileData = [
            'name' => cleanInput($_POST['name'] ?? ''),
            'headline' => cleanInput($_POST['headline'] ?? ''),
            'city' => cleanInput($_POST['city'] ?? ''),
            'homeland' => cleanInput($_POST['homeland'] ?? ''),
            'campus' => cleanInput($_POST['campus'] ?? ''),
            'department' => cleanInput($_POST['department'] ?? ''),
            'degree' => cleanInput($_POST['degree'] ?? ''),
            'discipline' => cleanInput($_POST['discipline'] ?? '')
        ];

        // Handle profile image upload
        $profileImage = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $profileImage = handleFileUpload(
                $_FILES['profile_image'],
                'uploads/profiles/',
                ['image/jpeg', 'image/png', 'image/gif'],
                2 * 1024 * 1024 // 2MB max
            );
        }

        // If there was a valid profile image, assign it
        $profileData['profile_image'] = $profileImage ? 'uploads/profiles/' . basename($profileImage) : null;

        // Store in session
        $_SESSION['user_profile'] = $profileData;

        // For save action, process additional data and save to database
        if ($action === 'save') {
            // Process resume upload (optional)
            $resume = null;
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $resume = handleFileUpload(
                    $_FILES['resume'],
                    'uploads/resumes/',
                    ['application/pdf', 'application/msword'],
                    5 * 1024 * 1024 // 5MB max
                );
            }

            // Process other optional fields
            $additionalData = [
                'website' => !empty($_POST['website']) ? cleanInput($_POST['website']) : null,
                'linkedin' => !empty($_POST['linkedin']) ? cleanInput($_POST['linkedin']) : null,
                'behance' => !empty($_POST['behance']) ? cleanInput($_POST['behance']) : null,
                'dribbble' => !empty($_POST['dribbble']) ? cleanInput($_POST['dribbble']) : null,
                'flickr' => !empty($_POST['flickr']) ? cleanInput($_POST['flickr']) : null,
                'interests' => isset($_POST['interests']) ? json_decode($_POST['interests'], true) : [],
                'resume' => $resume
            ];

            // Combine all data
            $fullData = array_merge($profileData, $additionalData);
            $interestsJson = json_encode($fullData['interests']);

            // Build SQL query with named parameters
            $sql = "INSERT INTO user_details (
                user_id, name, headline, profile_image, city, homeland, 
                campus, department, degree, discipline, resume, 
                website, linkedin, behance, dribbble, flickr, interests, created_at
            ) VALUES (
                :user_id, :name, :headline, :profile_image, :city, :homeland, 
                :campus, :department, :degree, :discipline, :resume, 
                :website, :linkedin, :behance, :dribbble, :flickr, :interests, NOW()
            )";

            // Prepare statement
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':name', $fullData['name']);
            $stmt->bindParam(':headline', $fullData['headline']);
            $stmt->bindParam(':profile_image', $fullData['profile_image'], $fullData['profile_image'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':city', $fullData['city']);
            $stmt->bindParam(':homeland', $fullData['homeland']);
            $stmt->bindParam(':campus', $fullData['campus']);
            $stmt->bindParam(':department', $fullData['department']);
            $stmt->bindParam(':degree', $fullData['degree']);
            $stmt->bindParam(':discipline', $fullData['discipline']);
            $stmt->bindParam(':resume', $fullData['resume'], $fullData['resume'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':website', $fullData['website'], $fullData['website'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':linkedin', $fullData['linkedin'], $fullData['linkedin'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':behance', $fullData['behance'], $fullData['behance'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':dribbble', $fullData['dribbble'], $fullData['dribbble'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':flickr', $fullData['flickr'], $fullData['flickr'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':interests', $interestsJson);

            // Execute statement
            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => 'Profile saved successfully',
                    'redirect' => 'dashboard'
                ];
            } else {
                throw new Exception("Database error: " . implode(" ", $stmt->errorInfo()));
            }
        } else {
            // For next action, just return success
            $response = [
                'status' => 'success',
                'message' => 'Step 1 data stored successfully'
            ];
        }
    } else {
        throw new Exception("Invalid action specified");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Profile Error: " . $e->getMessage());
}

echo json_encode($response);

function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function handleFileUpload($file, $targetDir, $allowedTypes, $maxSize) {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Verify file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) {
        throw new Exception(" Allowed: " . implode(', ', $allowedTypes));
    }

    // Verify file size
    if ($file['size'] > $maxSize) {
        throw new Exception("File too large. Max size: " . ($maxSize / 1024 / 1024) . "MB");
    }

    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . strtolower($ext);
    $targetPath = $targetDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Failed to save uploaded file");
    }

    return $targetPath;
}
