<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Collaboration</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .widthss {
            width: 30%;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['success'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="flex min-h-screen">
       
        <div class="flex-1">
            <div class="my-6 p-6 bg-white rounded-lg shadow-sm">
                <?php
                // Define the current step
                $current_step = isset($_POST['step']) ? intval($_POST['step']) : 1;

                // Process form submission
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if ($current_step == 1) {
                        $_SESSION['collaboration'] = [
                            'title' => htmlspecialchars($_POST['title'] ?? ''),
                            'description' => htmlspecialchars($_POST['description'] ?? '')
                        ];

                        // Handle file upload
                        if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
                            error_log("File upload attempt: " . print_r($_FILES['media'], true));
                            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'svg', 'mp4'];
                            $filename = $_FILES['media']['name'];
                            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $max_size = 25 * 1024 * 1024; // 25MB

                            if ($_FILES['media']['error'] !== 0) {
                                $_SESSION['error'] = "File upload error: " . $_FILES['media']['error'];
                                error_log("File upload error code: " . $_FILES['media']['error']);
                            } elseif (!in_array($filetype, $allowed)) {
                                $_SESSION['error'] = "Invalid file type: $filetype";
                                error_log("Invalid file type: $filetype");
                            } elseif ($_FILES['media']['size'] > $max_size) {
                                $_SESSION['error'] = "File exceeds 25MB: " . $_FILES['media']['size'];
                                error_log("File too large: " . $_FILES['media']['size']);
                            } else {
                                $unique_id = uniqid('collab_', true);
                                $new_filename = $unique_id . '.' . $filetype;
                                $upload_dir = realpath(__DIR__ . '/backend/uploads') . '/';
                                if (!is_dir($upload_dir)) {
                                    if (!mkdir($upload_dir, 0755, true)) {
                                        $_SESSION['error'] = "Failed to create upload directory.";
                                        error_log("Failed to create directory: $upload_dir");
                                    }
                                }

                                // Check directory permissions
                                if (!is_writable($upload_dir)) {
                                    $_SESSION['error'] = "Upload directory is not writable.";
                                    error_log("Directory not writable: $upload_dir");
                                } else {
                                    $target_path = $upload_dir . $new_filename;
                                    error_log("Target path: $target_path");
                                    error_log("Temporary file: " . $_FILES['media']['tmp_name']);

                                    if (move_uploaded_file($_FILES['media']['tmp_name'], $target_path)) {
                                        $_SESSION['collaboration']['media'] = "backend/uploads/" . $new_filename;
                                        error_log("File uploaded successfully: $target_path");
                                        error_log("Session media path set: " . $_SESSION['collaboration']['media']);
                                    } else {
                                        $_SESSION['error'] = "Failed to move uploaded file.";
                                        error_log("Failed to move file to: $target_path");
                                        error_log("Temporary file exists: " . (file_exists($_FILES['media']['tmp_name']) ? 'Yes' : 'No'));
                                    }
                                }
                            }
                        } else {
                            error_log("No file uploaded or no file selected.");
                        }

                        error_log("Session collaboration after Step 1: " . print_r($_SESSION['collaboration'], true));
                        session_write_close();
                        session_start();
                        $current_step = 2;
                    } elseif ($current_step == 2) {
                        $_SESSION['collaboration']['tags'] = $_POST['tags'] ?? [];
                        error_log("Session collaboration after Step 2: " . print_r($_SESSION['collaboration'], true));
                        $current_step = 3;
                    } elseif ($current_step == 3) {
                        $_SESSION['collaboration']['only_connections'] = isset($_POST['only_connections']);
                        $_SESSION['collaboration']['request_to_join'] = isset($_POST['request_to_join']);
                        $_SESSION['collaboration']['enable_max_limit'] = isset($_POST['enable_max_limit']);
                        error_log("Session collaboration after Step 3: " . print_r($_SESSION['collaboration'], true));
                        $current_step = 4;
                    } elseif ($current_step == 4 && isset($_POST['action']) && $_POST['action'] == 'post') {
                        // Handled by process_collaboration.php
                    }
                }

                // Handle back button
                if (isset($_POST['back'])) {
                    $current_step = max(1, $current_step - 1);
                }

                // Handle cancel button
                if (isset($_POST['cancel'])) {
                    if (isset($_SESSION['collaboration']['media'])) {
                        $file_path = __DIR__ . '/../' . $_SESSION['collaboration']['media'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                            error_log("Deleted uploaded file: $file_path");
                        }
                    }
                    unset($_SESSION['collaboration']);
                    header("Location: index.php");
                    exit;
                }
                ?>

                <div class="mb-8 p-6">
                    <h1 class="text-2xl text-center font-bold text-blue-900">Post Collaboration</h1>
                    <p class="text-gray-600 mt-1">Share your collaboration with people so that you can work together on something amazing.</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-8 relative">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo min(100, ($current_step * 33.33)); ?>%"></div>
                    </div>
                    <div class="text-right mt-1 text-sm text-gray-600"><?php echo $current_step; ?>/3</div>
                </div>

                <?php
                error_log("Current step: $current_step, Media path in session: " . ($_SESSION['collaboration']['media'] ?? 'Not set'));
                ?>

                <?php if ($current_step == 1): ?>
                <!-- Step 1: Basic Information -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="step" value="1">
                    
                    <div class="mb-6">
                        <label for="title" class="block mb-2 font-medium text-blue-900">Collaboration Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter the Title of the Collaboration" 
                            class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?php echo isset($_SESSION['collaboration']['title']) ? htmlspecialchars($_SESSION['collaboration']['title']) : ''; ?>" required>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block mb-2 font-medium text-blue-900">Write Description</label>
                        <div class="relative">
                            <textarea id="description" name="description" rows="4" placeholder="Enter the description of the job"
                                class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo isset($_SESSION['collaboration']['description']) ? htmlspecialchars($_SESSION['collaboration']['description']) : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'; ?></textarea>
                            <button type="button" class="absolute right-3 bottom-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="21" y1="10" x2="3" y2="10"></line>
                                    <line x1="21" y1="6" x2="3" y2="6"></line>
                                    <line x1="21" y1="14" x2="3" y2="14"></line>
                                    <line x1="21" y1="18" x2="3" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block mb-2 font-medium text-blue-900">Add Media <span class="font-normal text-gray-500">(Case Study of the Idea, Optional)</span></label>
                        <div class="border border-dashed border-blue-500 rounded-lg p-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900 mb-2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <p class="mb-2 text-sm text-gray-700">Drag and Drop or <label for="media" class="text-blue-500 cursor-pointer">Click to Upload</label></p>
                                <input type="file" id="media" name="media" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.svg,.mp4">
                                <p class="text-xs text-gray-500">Supported formats: JPG, PNG, PDF, SVG, MP4 <span class="ml-1">Max Size: 25MB</span></p>
                                <p id="file-info" class="mt-2 text-green-600 hidden"></p>
                                <?php if (isset($_SESSION['collaboration']['media'])): ?>
                                    <p class="mt-2 text-green-600">File uploaded: <?php echo htmlspecialchars($_SESSION['collaboration']['media']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="submit" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                            Next
                        </button>
                    </div>
                </form>

                <?php elseif ($current_step == 2): ?>
                <!-- Step 2: Focus of Collaboration -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="step" value="2">
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900">Focus of Collaboration</h2>
                        
                        <div class="mb-4 mt-4">
                            <div class="relative">
                                <input type="text" id="search_tags" placeholder="Search the tags" 
                                    class="w-full p-3 pl-4 pr-10 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" class="absolute right-3 top-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-6">
                            <?php
                            $tags = ['Research', 'Design', 'Development', 'Marketing', 'Analytics', 'Content Creation', 'Strategy', 'Project Management'];
                            $selected_tags = $_SESSION['collaboration']['tags'] ?? [];
                            foreach ($tags as $index => $tag):
                                $is_selected = in_array($tag, $selected_tags);
                            ?>
                            <div class="flex items-center">
                                <label for="tag_<?php echo $index; ?>" class="tag-btn flex items-center justify-between p-3 border border-blue-600 rounded-full text-blue-900 hover:bg-blue-50 cursor-pointer w-full <?php echo $is_selected ? 'bg-blue-50' : ''; ?>">
                                    <span>Tag I related to <?php echo $tag; ?></span>
                                    <input type="checkbox" id="tag_<?php echo $index; ?>" name="tags[]" value="<?php echo $tag; ?>" class="hidden" <?php echo $is_selected ? 'checked' : ''; ?>>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="plus-icon">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="submit" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
                            Cancel
                        </button>
                        <div class="flex gap-3">
                            <button type="submit" name="back" class="px-6 py-2 border border-blue-600 text-blue-900 rounded-lg hover:bg-blue-50">
                                Back
                            </button>
                            <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                                Next
                            </button>
                        </div>
                    </div>
                </form>

                <?php elseif ($current_step == 3): ?>
                <!-- Step 3: Join Criteria -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="step" value="3">
                    
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-blue-900 mb-6">Join Criteria</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <label for="only_connections" class="text-gray-700">Only Connections</label>
                                <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                    <input type="checkbox" name="only_connections" id="only_connections" 
                                           class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                           <?php echo (isset($_SESSION['collaboration']['only_connections']) && $_SESSION['collaboration']['only_connections']) ? 'checked' : ''; ?>/>
                                    <label for="only_connections" 
                                           class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <label for="request_to_join" class="text-gray-700">Request to join</label>
                                <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                    <input type="checkbox" name="request_to_join" id="request_to_join"
                                           class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                           <?php echo (isset($_SESSION['collaboration']['request_to_join']) && $_SESSION['collaboration']['request_to_join']) ? 'checked' : ''; ?>/>
                                    <label for="request_to_join"
                                           class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <label for="enable_max_limit" class="text-gray-700">Enable Max Limit</label>
                                <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                    <input type="checkbox" name="enable_max_limit" id="enable_max_limit"
                                           class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                           <?php echo (isset($_SESSION['collaboration']['enable_max_limit']) && $_SESSION['collaboration']['enable_max_limit']) ? 'checked' : ''; ?>/>
                                    <label for="enable_max_limit"
                                           class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="submit" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
                            Cancel
                        </button>
                        <div class="flex gap-3">
                            <button type="submit" name="back" class="px-6 py-2 border border-blue-600 text-blue-900 rounded-lg hover:bg-blue-50">
                                Back
                            </button>
                            <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                                Next
                            </button>
                        </div>
                    </div>
                </form>

                <?php elseif ($current_step == 4): ?>
                <!-- Step 4: Review Details -->
                <?php error_log("Step 4 session data: " . print_r($_SESSION['collaboration'], true)); ?>
                <form action="backend/process_collaboration.php" method="post">
                    <input type="hidden" name="step" value="4">
                    <input type="hidden" name="action" value="post">
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Review Details</h2>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-2">Collaboration</h3>
                            <h4 class="text-md font-medium"><?php echo htmlspecialchars($_SESSION['collaboration']['title'] ?? 'Learn Figma Together'); ?></h4>
                            
                            <div class="flex flex-wrap gap-2 mt-3">
                                <?php
                                $focus_tags = $_SESSION['collaboration']['tags'] ?? ['Focus of Interest'];
                                foreach ($focus_tags as $tag): ?>
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        <?php echo htmlspecialchars($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            
                            <p class="mt-4 text-gray-700">
                                <?php echo htmlspecialchars($_SESSION['collaboration']['description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'); ?>
                            </p>
                            
                            <?php if (isset($_SESSION['collaboration']['media'])): ?>
                            <div class="mt-4 flex items-center p-3 bg-gray-100 rounded-lg">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">
                                        <?php echo htmlspecialchars($_SESSION['collaboration']['media']); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-6 space-y-4">
                                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                    <span class="text-gray-700">Only Connections</span>
                                    <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                        <div class="w-12 h-6 bg-<?php echo (isset($_SESSION['collaboration']['only_connections']) && $_SESSION['collaboration']['only_connections']) ? 'blue-500' : 'gray-300'; ?> rounded-full"></div>
                                        <div class="absolute top-0 left-0 w-6ხ

                                        <div class="absolute top-0 left-0 w-6 h-6 bg-white rounded-full border-4 border-<?php echo (isset($_SESSION['collaboration']['only_connections']) && $_SESSION['collaboration']['only_connections']) ? 'blue-500' : 'gray-300'; ?>" style="<?php echo (isset($_SESSION['collaboration']['only_connections']) && $_SESSION['collaboration']['only_connections']) ? 'transform: translateX(100%);' : ''; ?>"></div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                    <span class="text-gray-700">Request to join</span>
                                    <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                        <div class="w-12 h-6 bg-<?php echo (isset($_SESSION['collaboration']['request_to_join']) && $_SESSION['collaboration']['request_to_join']) ? 'blue-500' : 'gray-300'; ?> rounded-full"></div>
                                        <div class="absolute top-0 left-0 w-6 h-6 bg-white rounded-full border-4 border-<?php echo (isset($_SESSION['collaboration']['request_to_join']) && $_SESSION['collaboration']['request_to_join']) ? 'blue-500' : 'gray-300'; ?>" style="<?php echo (isset($_SESSION['collaboration']['request_to_join']) && $_SESSION['collaboration']['request_to_join']) ? 'transform: translateX(100%);' : ''; ?>"></div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                    <span class="text-gray-700">Enable Max Limit</span>
                                    <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                        <div class="w-12 h-6 bg-<?php echo (isset($_SESSION['collaboration']['enable_max_limit']) && $_SESSION['collaboration']['enable_max_limit']) ? 'blue-500' : 'gray-300'; ?> rounded-full"></div>
                                        <div class="absolute top-0 left-0 w-6 h-6 bg-white rounded-full border-4 border-<?php echo (isset($_SESSION['collaboration']['enable_max_limit']) && $_SESSION['collaboration']['enable_max_limit']) ? 'blue-500' : 'gray-300'; ?>" style="<?php echo (isset($_SESSION['collaboration']['enable_max_limit']) && $_SESSION['collaboration']['enable_max_limit']) ? 'transform: translateX(100%);' : ''; ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="submit" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
                            Cancel
                        </button>
                        <div class="flex gap-3">
                            <button type="submit" name="back" class="px-6 py-2 border border-blue-600 text-blue-900 rounded-lg hover:bg-blue-50">
                                Back
                            </button>
                            <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                                Post
                            </button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #3B82F6;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #3B82F6;
        }
        .tag-btn input:checked + svg {
            display: none;
        }
        .tag-btn input:checked ~ .plus-icon:before {
            content: '';
            position: absolute;
            width: 12px;
            height: 2px;
            background-color: #3B82F6;
            transform: rotate(45deg);
        }
        .tag-btn input:checked {
            background-color: #EBF5FF;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tag selection
            const tagButtons = document.querySelectorAll('.tag-btn');
            tagButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    if (checkbox.checked) {
                        this.classList.add('bg-blue-50');
                    } else {
                        this.classList.remove('bg-blue-50');
                    }
                });
            });

            // Tag search
            const searchInput = document.getElementById('search_tags');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    tagButtons.forEach(btn => {
                        const tagText = btn.querySelector('span').textContent.toLowerCase();
                        if (tagText.includes(searchTerm)) {
                            btn.parentElement.style.display = '';
                        } else {
                            btn.parentElement.style.display = 'none';
                        }
                    });
                });
            }

            // File upload display
            const fileInput = document.getElementById('media');
            const fileInfo = document.getElementById('file-info');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const fileName = this.files[0].name;
                        const fileSize = Math.round(this.files[0].size / 1024);
                        fileInfo.textContent = `Selected: ${fileName} (${fileSize} KB)`;
                        fileInfo.classList.remove('hidden');
                    } else {
                        fileInfo.textContent = '';
                        fileInfo.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>