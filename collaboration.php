<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Collaboration</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php'; ?>
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


    <div class="flex min-h-screen">
        <!-- Left Sidebar -->
        <div class="w-64 bg-white p-6 border-r border-gray-200">
            <?php include 'left-sidebar.php'; ?>
        </div>
        <div class="flex-1 p-6">
            <div class="max-w-4xl mx-auto my-10 p-6 bg-white rounded-lg shadow-sm">
                <?php
                // Define the current step based on form submission or default to step 1
                $current_step = isset($_POST['step']) ? intval($_POST['step']) : 1;
                
                // Start session only if not already started
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                // If we have form data being submitted, process it
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Store form data in session for later use
                    if ($current_step == 1) {
                        $_SESSION['collaboration'] = [
                            'title' => htmlspecialchars($_POST['title'] ?? ''),
                            'description' => htmlspecialchars($_POST['description'] ?? '')
                        ];
                        
                        // Handle file upload
                        if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
                            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'svg', 'mp4'];
                            $filename = $_FILES['media']['name'];
                            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $max_size = 25 * 1024 * 1024; // 25MB

                            if (in_array($filetype, $allowed)) {
                                if ($_FILES['media']['size'] <= $max_size) {
                                    // Generate unique filename in the format profile_<unique_id>.extension
                                    $unique_id = uniqid('collab_', true); // e.g., profile_6815143b52cb3
                                    $new_filename = $unique_id . '.' . $filetype;
                                    $upload_dir = __DIR__ . '/backend/uploads/'; // Ensure this directory exists
                                    if (!is_dir($upload_dir)) {
                                        mkdir($upload_dir, 0755, true);
                                    }
                                    $target_path = $upload_dir . $new_filename;

                                    if (move_uploaded_file($_FILES['media']['tmp_name'], $target_path)) {
                                        // Store the relative URL path in the session
                                        $_SESSION['collaboration']['media'] = "backend/uploads/" . $new_filename;
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
                        
                        // Progress to next step
                        $current_step = 2;
                    } elseif ($current_step == 2) {
                        // Store the selected tags
                        $_SESSION['collaboration']['tags'] = $_POST['tags'] ?? [];
                        
                        // Progress to next step
                        $current_step = 3;
                    } elseif ($current_step == 3) {
                        // Store join criteria settings
                        $_SESSION['collaboration']['only_connections'] = isset($_POST['only_connections']);
                        $_SESSION['collaboration']['request_to_join'] = isset($_POST['request_to_join']);
                        $_SESSION['collaboration']['enable_max_limit'] = isset($_POST['enable_max_limit']);
                        
                        // Progress to review step
                        $current_step = 4;
                    } elseif ($current_step == 4 && isset($_POST['action']) && $_POST['action'] == 'post') {
                        // This would be where you save to database in a real app
                        header("Location: collaboration_success.php");
                        exit;
                    }
                }
                
                // Handle back button functionality
                if (isset($_POST['back'])) {
                    $current_step = max(1, $current_step - 1);
                }
                
                // Handle cancel button functionality
                if (isset($_POST['cancel'])) {
                    // Clear session data
                    unset($_SESSION['collaboration']);
                    // Redirect to a landing page
                    header("Location: index.php");
                    exit;
                }
                
                // Common header for all steps
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

                <?php if ($current_step == 1): ?>
                <!-- Step 1: Basic Information -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="step" value="1">
                    
                    <div class="mb-6">
                        <label for="title" class="block mb-2 font-medium text-blue-900">Collaboration Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter the Title of the Collaboration" 
                            class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?php echo isset($_SESSION['collaboration']['title']) ? $_SESSION['collaboration']['title'] : ''; ?>" required>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block mb-2 font-medium text-blue-900">Write Description</label>
                        <div class="relative">
                            <textarea id="description" name="description" rows="4" placeholder="Enter the description of the job"
                                class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo isset($_SESSION['collaboration']['description']) ? $_SESSION['collaboration']['description'] : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'; ?></textarea>
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
                        <label class="block mb-2 font-medium text-blue-900">Add Media <span class="font-normal text-gray-500">(Case Study of the Idea)</span></label>
                        <div class="border border-dashed border-blue-500 rounded-lg p-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900 mb-2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <p class="mb-2 text-sm text-gray-700">Drag and Drop or <label for="media" class="text-blue-500 cursor-pointer">Click to Upload</label></p>
                                <input type="file" id="media" name="media" class="hidden">
                                <p class="text-xs text-gray-500">Supported formats: JPG, PNG, PDF, SVG, MP4 <span class="ml-1">Max Size: 25MB</span></p>
                                <?php if (isset($_SESSION['collaboration']['media'])): ?>
                                    <p class="mt-2 text-green-600 file-info">File uploaded: <?php echo $_SESSION['collaboration']['media']; ?></p>
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
                            // In a real application, these tags might come from a database
                            $tags = [
                                'Research', 'Design', 'Development', 'Marketing',
                                'Analytics', 'Content Creation', 'Strategy', 'Project Management'
                            ];
                            
                            // Check if tags were previously selected
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
                <form action="backend/process_collaboration.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="step" value="4">
                    <input type="hidden" name="action" value="post">
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Review Details</h2>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-2">Collaboration</h3>
                            <h4 class="text-md font-medium"><?php echo $_SESSION['collaboration']['title'] ?? 'Learn Figma Together'; ?></h4>
                            
                            <div class="flex flex-wrap gap-2 mt-3">
                                <?php
                                $focus_tags = $_SESSION['collaboration']['tags'] ?? ['Focus of Interest', 'Focus of Interest', 'Focus of Interest'];
                                foreach ($focus_tags as $tag): ?>
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        <?php echo htmlspecialchars($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (count($focus_tags) > 0): ?>
                                    <span class="text-sm text-gray-500">+2</span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="mt-4 text-gray-700">
                                <?php echo $_SESSION['collaboration']['description'] ?? "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."; ?>
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
                                        <?php echo $_SESSION['collaboration']['media']; ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex flex-wrap gap-2 mt-6">
                                <?php 
                                $sample_tags = ['Tag 2', 'Tag 2', 'Tag 2', 'Tag 2', 'Tag 2', 'Tag 2'];
                                foreach ($sample_tags as $tag): ?>
                                    <span class="inline-flex items-center px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded-full">
                                        <?php echo $tag; ?>
                                    </span>
                                <?php endforeach; ?>
                                <span class="text-sm text-gray-500">+1</span>
                            </div>
                            
                            <div class="mt-6 space-y-4">
                                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                    <span class="text-gray-700">Only Connections</span>
                                    <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                        <div class="w-12 h-6 bg-<?php echo (isset($_SESSION['collaboration']['only_connections']) && $_SESSION['collaboration']['only_connections']) ? 'blue-500' : 'gray-300'; ?> rounded-full"></div>
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
        /* Custom Toggle Switch Styles */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #3B82F6;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #3B82F6;
        }
        
        /* Tag Selection Styles */
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
        // JavaScript for the tag selection
        document.addEventListener('DOMContentLoaded', function() {
            // Handle tag selection
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
            
            // Handle tag search
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
            
            // Handle file upload display
            const fileInput = document.getElementById('media');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const fileName = this.files[0].name;
                        const fileSize = Math.round(this.files[0].size / 1024); // KB
                        
                        // You could add code here to display the selected file name
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