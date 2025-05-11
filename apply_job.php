<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();



// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "connections";

// Initialize job data with default values
$job_data = [
    'title' => 'Unknown Position',
    'company' => 'Unknown Company',
    'description' => 'No description available',
    'listing_type' => 'Not specified',
    'location' => 'Not specified',
    'salary' => 'Not specified',
    'job_timing' => 'Not specified',
    'created_at' => 'Unknown date',
    'media_path' => null,
    'tags' => []
];

// Set base URL for media paths
$base_url = "backend/"; // Adjust this to your actual base URL

// Only try to connect if we have a job ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    try {
        // Create database connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }
        
        // Get the job ID from URL parameter
        $job_id = intval($_GET['id']);
        
        // First, verify the job exists
        $check_sql = "SELECT id FROM opportunity WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if (!$check_stmt) {
            throw new Exception("Prepare check failed: " . $conn->error);
        }
        
        $check_stmt->bind_param("i", $job_id);
        if (!$check_stmt->execute()) {
            throw new Exception("Execute check failed: " . $check_stmt->error);
        }
        
        $check_result = $check_stmt->get_result();
        $check_stmt->close();
        
        if ($check_result->num_rows === 0) {
            throw new Exception("No job found with ID: " . $job_id);
        }
        
        // Now fetch the full job details
        $sql = "SELECT * FROM opportunity WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $job_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Calculate time ago
            $time_ago = "Recently";
            if (isset($row["created_at"])) {
                try {
                    $post_date = new DateTime($row["created_at"]);
                    $now = new DateTime();
                    $interval = $post_date->diff($now);
                    
                    if ($interval->d == 0) {
                        $time_ago = "Today";
                    } elseif ($interval->d == 1) {
                        $time_ago = "Yesterday";
                    } elseif ($interval->d < 7) {
                        $time_ago = $interval->d . " days ago";
                    } elseif ($interval->d < 30) {
                        $weeks = floor($interval->d / 7);
                        $time_ago = $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
                    } else {
                        $months = floor($interval->d / 30);
                        $time_ago = $months . " month" . ($months > 1 ? "s" : "") . " ago";
                    }
                } catch (Exception $e) {
                    error_log("Date parsing error: " . $e->getMessage());
                }
            }
            
            // Prepare tags
            $tags = [];
            if (isset($row["tags"]) && !empty($row["tags"])) {
                // Clean up tags format from your table
                $cleaned_tags = str_replace(['[', ']', 'Flag', 'related release', 'in plan.'], '', $row["tags"]);
                $tags = array_filter(array_map('trim', explode(',', $cleaned_tags)));
            }
            
            // Populate job_data array - adjust media_path with base URL
            $job_data = [
                'title' => $row['title'] ?? 'Unknown Position',
                'company' => $row['company'] ?? 'Unknown Company',
                'description' => $row['description'] ?? 'No description available',
                'listing_type' => $row['listing_type'] ?? 'Not specified',
                'location' => $row['location'] ?? 'Not specified',
                'salary' => ($row['budget_currency'] ?? '') . ' ' . 
                           ($row['budget_amount'] ?? '') . ' ' . 
                           ($row['budget_cycle'] ?? ''),
                'job_timing' => $row['job_timing'] ?? 'Not specified',
                'job_split' => $row['job_split'] ?? 'Not specified',
                'created_at' => $time_ago,
                'media_path' => $row['media_path'] ? $base_url . $row['media_path'] : null,
                'tags' => $tags
            ];
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        error_log("Job loading error: " . $e->getMessage());
        $_SESSION['error'] = "Error loading job details: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle cancel button
    if (isset($_POST['cancel'])) {
        unset($_SESSION['application_media']);
        header("Location: index.php");
        exit;
    }

    // Initialize variables
    $media_path = null;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Assuming user is logged in and user_id is stored in session
    
    // Handle file upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        $filename = $_FILES['media']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $max_size = 25 * 1024 * 1024; // 25MB
        
        if (in_array($filetype, $allowed)) {
            if ($_FILES['media']['size'] <= $max_size) {
                $new_filename = uniqid() . '.' . $filetype;
                $upload_dir = __DIR__ . '/upload_jobs_data/';
    error_log("Upload directory: " . $upload_dir);
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $target_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['media']['tmp_name'], $target_path)) {
                    $media_path = $target_path;
                    $_SESSION['application_media'] = $target_path;
                } else {
                    $_SESSION['error'] = "Failed to save uploaded file.";
                }
            } else {
                $_SESSION['error'] = "File size exceeds 25MB limit.";
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Allowed: JPG, PNG, PDF, DOC";
        }
    }

    // Save application to database if we have a job ID and user is logged in
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $user_id) {
        try {
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            if ($conn->connect_error) {
                throw new Exception("Database connection failed: " . $conn->connect_error);
            }
            
            $job_id = intval($_GET['id']);
            $application_date = date('Y-m-d H:i:s');
            $status = 'pending'; // You can set default status
            
            // Prepare SQL to insert application
            $sql = "INSERT INTO job_applications (job_id, user_id, application_date, media_path, status) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            // Bind parameters
            $stmt->bind_param("iisss", $job_id, $user_id, $application_date, $media_path, $status);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
            $conn->close();
            
            // Set success message
            $_SESSION['success'] = "Your application has been submitted successfully!";
            
        } catch (Exception $e) {
            error_log("Application save error: " . $e->getMessage());
            $_SESSION['error'] = "Error submitting application: " . $e->getMessage();
        }
    } elseif (!$user_id) {
        $_SESSION['error'] = "You must be logged in to apply for this job.";
    }

    // Redirect to confirmation page
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for <?php echo htmlspecialchars($job_data['title']); ?> at <?php echo htmlspecialchars($job_data['company']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .file-info {
            word-break: break-all;
        }
        .job-detail-icon {
            min-width: 24px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Display success message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php 
            echo htmlspecialchars($_SESSION['success']); 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Display error message -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="flex min-h-screen">
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="max-w-2xl mx-auto my-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Job Header -->
                <div class="flex items-center mb-6">
                    <?php if ($job_data['media_path']): ?>
                    <div class="w-24 h-24 rounded-md overflow-hidden mr-4">
                        <img src="<?php echo htmlspecialchars($job_data['media_path']); ?>" alt="Company Logo" class="w-full h-full object-cover">
                    </div>
                    <?php else: ?>
                    <div class="w-12 h-12 bg-[#023564] rounded flex items-center justify-center mr-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900">
                            <path d="M12 2l2 2 2-2h4a2 2 0 0 1 2 2v4l-2 2 2 2v4a2 2 0 0 1-2 2h-4l-2 2-2-2H6a2 2 0 0 1-2-2v-4l2-2-2-2V4a2 2 0 0 1 2-2h4z"/>
                            <path d="M12 12v6"/>
                        </svg>
                    </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="text-[32px] leading-[33px] font-bold text-[#023564]"><?php echo htmlspecialchars($job_data['company']); ?></h2>
                        <h1 class="text-[18px] font-normal text-[#2A97FC]"><?php echo htmlspecialchars($job_data['title']); ?></h1>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span class="inline-flex items-center px-2 py-1 bg-[#023564] text-white text-xs rounded-full">
                                <?php echo htmlspecialchars($job_data['created_at']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="mb-6">
                    <h3 class="text-blue-900 text-[18px] font-bold mb-2">JOB DESCRIPTION</h3>
                    <p class="text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($job_data['description']); ?></p>
                </div>

                <!-- Job Details Grid -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-blue-900  text-[18px] font-bold mb-3">JOB DETAILS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start">
                            <span class="job-detail-icon">📌</span>
                            <div class="ml-2">
                                <p class="text-sm text-gray-600">Job Type</p>
                                <p class="text-blue-900"><?php echo htmlspecialchars($job_data['listing_type']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="job-detail-icon">📍</span>
                            <div class="ml-2">
                                <p class="text-sm text-gray-600">Location</p>
                                <p class="text-blue-900"><?php echo htmlspecialchars($job_data['location']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="job-detail-icon">💰</span>
                            <div class="ml-2">
                                <p class="text-sm text-gray-600">Salary</p>
                                <p class="text-blue-900"><?php echo htmlspecialchars($job_data['salary']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="job-detail-icon">⏰</span>
                            <div class="ml-2">
                                <p class="text-sm text-gray-600">Timing</p>
                                <p class="text-blue-900"><?php echo htmlspecialchars($job_data['job_timing']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tags Section -->
                <?php if (!empty($job_data['tags'])): ?>
                <div class="mb-6">
                    <h3 class="text-blue-900 text-[18px] font-bold mb-2">RELATED TAGS</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php 
                        $tag_count = 0;
                        foreach ($job_data['tags'] as $tag): 
                            if ($tag_count < 8): // Limit to 8 tags
                        ?>
                            <span class="inline-flex items-center px-3 py-1 bg-[#023564] text-white text-sm rounded-full">
                                <?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php 
                            endif;
                            $tag_count++;
                        endforeach; 
                        
                        if (count($job_data['tags']) > 8): ?>
                            <span class="inline-flex items-center px-3 py-1 bg-[#023564] text-white text-sm rounded-full">
                                +<?php echo count($job_data['tags']) - 8; ?> more
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Application Form -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-blue-900 text-[32px] text-center font-bold mb-4">Apply Now</h3>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="mb-6 p-4 bg-yellow-50 text-yellow-800 rounded-lg">
                            <p>You must be <a href="signin.php" class="text-blue-600 hover:underline">logged in</a> to apply for this job.</p>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . (isset($_GET['id']) ? '?id=' . intval($_GET['id']) : ''); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                        <!-- File Upload Section -->
                        <div class="mb-6">
                            <label class="block mb-2 text-[18px] font-bold text-blue-900">Add Media <span class="font-normal text-gray-500">(Resume, Portfolio, etc.)</span></label>
                            <div class="border-2 border-dashed border-blue-300 rounded-lg p-8 text-center bg-blue-50">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500 mb-3">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-700">Drag and drop files here or <label for="media" class="text-blue-600 font-medium cursor-pointer hover:text-blue-900">click to browse</label></p>
                                    <input type="file" id="media" name="media" class="hidden" multiple>
                                    <p class="text-xs text-gray-500">Accepted formats: JPG, PNG, PDF, SVG, MP4 (Max 25MB each)</p>
                                    <?php if (isset($_SESSION['application_media'])): ?>
                                        <div class="mt-3 p-2 bg-green-50 rounded-md">
                                            <p class="text-green-600 text-sm file-info">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                <?php echo htmlspecialchars(basename($_SESSION['application_media'])); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between mt-8">
                            <button type="submit" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-colors" <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                <i class="fas fa-paper-plane mr-2"></i>Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('media');
            if (fileInput) {
                // Handle drag and drop
                const dropZone = fileInput.closest('.border-dashed');
                
                if (dropZone) {
                    // Prevent default drag behaviors
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        dropZone.addEventListener(eventName, preventDefaults, false);
                    });
                    
                    // Highlight drop zone when item is dragged over it
                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropZone.addEventListener(eventName, highlight, false);
                    });
                    
                    ['dragleave', 'drop'].forEach(eventName => {
                        dropZone.addEventListener(eventName, unhighlight, false);
                    });
                    
                    // Handle dropped files
                    dropZone.addEventListener('drop', handleDrop, false);
                }
                
                // Handle file selection via click
                fileInput.addEventListener('change', function() {
                    updateFileInfo(this.files);
                });
                
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                function highlight() {
                    dropZone.classList.add('border-blue-500', 'bg-[#023564]');
                }
                
                function unhighlight() {
                    dropZone.classList.remove('border-blue-500', 'bg-[#023564]');
                }
                
                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    fileInput.files = files;
                    updateFileInfo(files);
                }
                
                function updateFileInfo(files) {
                    if (files && files.length > 0) {
                        const parent = fileInput.closest('.border-dashed');
                        let fileInfo = parent.querySelector('.file-info');
                        
                        if (!fileInfo) {
                            fileInfo = document.createElement('div');
                            fileInfo.className = 'mt-3 p-2 bg-green-50 rounded-md';
                            parent.appendChild(fileInfo);
                        }
                        
                        let html = '';
                        for (let i = 0; i < Math.min(files.length, 3); i++) {
                            const file = files[i];
                            const fileSize = Math.round(file.size / 1024);
                            html += `<p class="text-green-600 text-sm"><i class="fas fa-check-circle mr-1"></i> ${file.name} (${fileSize} KB)</p>`;
                        }
                        
                        if (files.length > 3) {
                            html += `<p class="text-green-600 text-sm">+ ${files.length - 3} more files</p>`;
                        }
                        
                        fileInfo.innerHTML = html;
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php
// Flush output buffer
ob_end_flush();
?>