<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancing Project Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <?php
    // Start output buffering to prevent headers already sent errors
    ob_start();

    // Start session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Handle form submission and cancel button
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle cancel button
        if (isset($_POST['cancel'])) {
            unset($_SESSION['application_data']);
            header("Location: index.php");
            exit;
        }

        // Handle form submission
        $proposal = htmlspecialchars($_POST['proposal'] ?? '');
        $_SESSION['application_data'] = ['proposal' => $proposal];

        // Handle file upload
        if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'svg', 'mp4'];
            $filename = $_FILES['media']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $max_size = 25 * 1024 * 1024;
            if (in_array($filetype, $allowed)) {
                if ($_FILES['media']['size'] <= $max_size) {
                    $new_filename = uniqid() . '.' . $filetype;
                    $upload_dir = __DIR__ . '/Uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $target_path = $upload_dir . $new_filename;
                    if (move_uploaded_file($_FILES['media']['tmp_name'], $target_path)) {
                        $_SESSION['application_data']['media'] = $target_path;
                    } else {
                        $_SESSION['error'] = "File upload failed.";
                    }
                } else {
                    $_SESSION['error'] = "File exceeds 25MB.";
                }
            } else {
                $_SESSION['error'] = "Invalid file type.";
            }
        }

        // Redirect to confirmation page
        header("Location: confirm_application.php");
        exit;
    }

    // Sample project data
    $project_data = [
        'name' => 'NAME OF FREELANCING PROJECT',
        'focus' => ['Focus of Interest', 'Focus of Interest', 'Focus of Interest', 'Focus of Int', '+2'],
        'date' => '0 day ago',
        'description' => 'Wetter Solutions is seeking a creative Remote UI/UX Designer to join our innovative team. In this role, you will be responsible for crafting intuitive and engaging user experiences for our digital products. You will collaborate closely with product managers and developers to translate user needs into compelling designs. The ideal candidate should have a strong portfolio showcasing their design process and an understanding of user-centered design principles. If you’re passionate about creating seamless user interfaces and enhancing user satisfaction, we want to hear from you!',
        'details' => ['💳 $60-70k', '🕒 0-6 months', '⏰ 08am-05pm'],
        'tags' => ['UI/UX Designer', 'Tag 2', 'Tag 3', 'Tag 3', 'Tag 3', 'Tag 3', 'Tag 3', 'Tag 3', '+2']
    ];

    // Include navbar
    include 'navbar.php';
    ?>

    <!-- Display error message -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="flex min-h-screen">
        <!-- Left Sidebar -->
        <div class="w-64 bg-white p-6 border-r border-gray-200">
            <div class="space-y-4">
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 0112z"/></svg>
                    <span class="ml-3">Simple Post</span>
                </a>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 0112z"/></svg>
                    <span class="ml-3">Opportunity Listing</span>
                </a>
                <a href="#" class="flex items-center p-2 text-blue-900 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-900" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 0112z"/></svg>
                    <span class="ml-3">Research Idea</span>
                </a>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 0112z"/></svg>
                    <span class="ml-3">Startup Idea</span>
                </a>
                <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 0112z"/></svg>
                    <span class="ml-3">Post a Collaboration</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="max-w-2xl mx-auto my-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded flex items-center justify-center mr-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900">
                            <path d="M12 2l2 2 2-2h4a2 2 0 0 1 2 2v4l-2 2 2 2v4a2 2 0 0 1-2 2h-4l-2 2-2-2H6a2 2 0 0 1-2-2v-4l2-2-2-2V4a2 2 0 0 1 2-2h4z"/>
                            <path d="M12 12v6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-blue-900"><?php echo $project_data['name']; ?></h2>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <?php foreach ($project_data['focus'] as $focus): ?>
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    <?php echo htmlspecialchars($focus); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"><?php echo $project_data['date']; ?></p>
                    </div>
                </div>

                <p class="text-blue-900 text-sm mb-4"><?php echo $project_data['description']; ?></p>

                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($project_data['details'] as $detail): ?>
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            <?php echo htmlspecialchars($detail); ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <div class="mb-6">
                    <p class="text-blue-900 font-medium mb-2">RELATED TAGS</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($project_data['tags'] as $tag): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                <?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <p class="text-blue-900 font-medium mb-4">Apply Now *</p>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-blue-900 font-medium mb-2">WRITE PROPOSAL</label>
                        <textarea name="proposal" rows="4" placeholder="Enter your proposal and tell why you are fit for the opportunity" class="w-full p-3 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>

                    <div class="mb-8">
                        <label class="block mb-2 font-medium text-blue-900">Add Media <span class="font-normal text-gray-500">(Supporting Documents)</span></label>
                        <div class="border border-dashed border-blue-500 rounded-lg p-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900 mb-2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <p class="mb-2 text-sm text-gray-700">Drag and Drop or <label for="media" class="text-blue-500 cursor-pointer">Click to Upload</label></p>
                                <input type="file" id="media" name="media" class="hidden">
                                <p class="text-xs text-gray-500">Supported formats: JPG, PNG, PDF, SVG, MP4 <span class="ml-1">Max Size: 25MB</span></p>
                                <?php if (isset($_SESSION['application_data']['media'])): ?>
                                    <p class="mt-2 text-green-600 file-info">File uploaded: <?php echo basename($_SESSION['application_data']['media']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="submit" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                            Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('media');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const fileName = this.files[0].name;
                        const fileSize = Math.round(this.files[0].size / 1024);
                        const parent = this.closest('.border-dashed');
                        let fileInfo = parent.querySelector('.file-info');
                        if (!fileInfo) {
                            fileInfo = document.createElement('p');
                            fileInfo.className = 'mt-2 text-green-600 file-info';
                            parent.appendChild(fileInfo);
                        }
                        fileInfo.textContent = `Selected: ${fileName} (${fileSize} KB)`;
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php
// Flush output buffer
ob_end_flush();
?>