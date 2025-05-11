<?php
// startup.php in the root directory
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cancel form
    if (isset($_POST['cancel'])) {
        unset($_SESSION['startup']);
        unset($_SESSION['current_step']);
        header("Location: /startup.php");
        exit();
    }

    // Back button
    if (isset($_POST['back'])) {
        $_SESSION['current_step'] = max(1, ($_POST['step'] ?? 1) - 1);
        header("Location: startup.php");
        exit();
    }

    $current_step = $_POST['step'] ?? 1;

    // Step 1 - Basic Info + File Upload
    if ($current_step == 1) {
        $_SESSION['startup'] = [
            'title' => htmlspecialchars($_POST['title'] ?? ''),
            'description' => htmlspecialchars($_POST['description'] ?? '')
        ];

        if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'svg', 'mp4'];
            $filename = $_FILES['media']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $max_size = 25 * 1024 * 1024; // 25MB

            $upload_dir = __DIR__ . '/backend/uploads/';
            $new_filename = uniqid() . '.' . $filetype;
            $target_path = $upload_dir . $new_filename;

            $log_file = __DIR__ . '/upload.log';
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Upload attempt: " . print_r($_FILES, true) . "\n", FILE_APPEND);

            if (!in_array($filetype, $allowed)) {
                $_SESSION['error'] = "Invalid file type. Allowed types: " . implode(', ', $allowed);
            } elseif ($_FILES['media']['size'] > $max_size) {
                $_SESSION['error'] = "File exceeds 25MB size limit.";
            } else {
                if (!file_exists($upload_dir)) {
                    if (!mkdir($upload_dir, 0777, true)) {
                        $_SESSION['error'] = "Failed to create uploads directory. Check permissions.";
                        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Failed to create directory: $upload_dir\n", FILE_APPEND);
                        header("Location: startup.php");
                        exit();
                    }
                }

                if (!is_writable($upload_dir)) {
                    $_SESSION['error'] = "Uploads directory not writable.";
                    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Not writable: $upload_dir\n", FILE_APPEND);
                    header("Location: startup.php");
                    exit();
                }

                if (move_uploaded_file($_FILES['media']['tmp_name'], $target_path)) {
                    $_SESSION['startup']['media'] = 'backend/uploads/' . $new_filename;
                    file_put_contents($log_file, date('Y-m-d H:i:s') . " - File uploaded to: $target_path\n", FILE_APPEND);
                } else {
                    $_SESSION['error'] = "File upload failed.";
                    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Failed to move to: $target_path\n", FILE_APPEND);
                }
            }
        } elseif (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
            $_SESSION['error'] = "Upload error code: " . $_FILES['media']['error'];
            file_put_contents(__DIR__ . '/upload.log', date('Y-m-d H:i:s') . " - Upload error: " . $_FILES['media']['error'] . "\n", FILE_APPEND);
        }

        $_SESSION['current_step'] = 2;
    }

    // Step 2 - Tags
    elseif ($current_step == 2) {
        $_SESSION['startup']['tags'] = $_POST['tags'] ?? [];
        $_SESSION['current_step'] = 3;
    }

    // Step 3 - Questions
    elseif ($current_step == 3) {
        $_SESSION['startup']['questions'] = $_POST['questions'] ?? [];
        $_SESSION['current_step'] = 4;
    }

    // Step 4 - Submission complete or next logic

    header("Location: startup.php");
    exit();
}

// Initialize default step
$current_step = $_SESSION['current_step'] ?? 1;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Startup Idea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .widthss {
            width: 30%;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <?php if (isset($_SESSION['success'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="flex min-h-screen">
        <!-- Left Sidebar -->
       
        <div class="flex-1 p-6">
            <div class="mx-auto p-6 bg-white rounded-lg shadow-sm">
                <div class="mb-8 p-6">
                    <h1 class="text-2xl text-center font-bold text-blue-900">Post Startup Idea</h1>
                    <p class="text-gray-600 mt-1">Share your idea with others to collaborate and learn more.</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-8 relative">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo min(100, ($current_step * 25)); ?>%"></div>
                    </div>
                    <div class="text-right mt-1 text-sm text-gray-600"><?php echo $current_step; ?>/4</div>
                </div>

                <?php if ($current_step == 1): ?>
                <!-- Step 1: Basic Information -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="step" value="<?php echo $current_step; ?>">
                    <div class="mb-6">
                        <label for="title" class="block mb-2 font-medium text-blue-900">Startup Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter the Title of the opportunity"
                            class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?php echo isset($_SESSION['startup']['title']) ? $_SESSION['startup']['title'] : ''; ?>" required>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block mb-2 font-medium text-blue-900">Write Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Enter the description of the idea"
                            class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo isset($_SESSION['startup']['description']) ? $_SESSION['startup']['description'] : ''; ?></textarea>
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
                                <input type="file" id="media" name="media" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.svg,.mp4">
                                <p class="text-xs text-gray-500">Supported formats: JPG, PNG, PDF, SVG, MP4 <span class="ml-1">Max Size: 25MB</span></p>
                                <?php if (isset($_SESSION['startup']['media'])): ?>
                                    <p class="mt-2 text-green-600 file-info">File uploaded: <?php echo basename($_SESSION['startup']['media']); ?></p>
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
                <!-- Step 2: Focus of Startup -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="step" value="<?php echo $current_step; ?>">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900">Focus of Startup</h2>
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
                            $selected_tags = $_SESSION['startup']['tags'] ?? [];
                            foreach ($tags as $index => $tag):
                                $is_selected = in_array($tag, $selected_tags);
                            ?>
                            <div class="flex items-center">
                                <label for="tag_<?php echo $index; ?>" class="tag-btn flex items-center justify-between p-3 border border-blue-600 rounded-full text-blue-900 hover:bg-blue-50 cursor-pointer w-full <?php echo $is_selected ? 'bg-blue-50' : ''; ?>">
                                    <span><?php echo $tag; ?></span>
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
                <!-- Step 3: Requirements Questions -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="step" value="<?php echo $current_step; ?>">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-6">Add Requirements Questions to Join</h2>
                        <div id="questions-container">
                            <?php $questions = $_SESSION['startup']['questions'] ?? []; ?>
                            <?php foreach ($questions as $index => $question): ?>
                            <div class="mb-4 question-item">
                                <input type="text" name="questions[]" placeholder="Question <?php echo $index + 1; ?>"
                                    value="<?php echo htmlspecialchars($question); ?>"
                                    class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($questions)): ?>
                            <div class="mb-4 question-item">
                                <input type="text" name="questions[]" placeholder="Question 1"
                                    class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-question" class="mt-4 text-blue-500 hover:text-blue-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Question
                        </button>
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
                <form action="backend/process_startup.php" method="post">
                    <input type="hidden" name="step" value="<?php echo $current_step; ?>">
                    <input type="hidden" name="action" value="post">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Review Details</h2>
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-2">Startup</h3>
                            <h4 class="text-md font-medium"><?php echo htmlspecialchars($_SESSION['startup']['title'] ?? 'No Title'); ?></h4>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <?php
                                $focus_tags = $_SESSION['startup']['tags'] ?? [];
                                foreach ($focus_tags as $tag): ?>
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        <?php echo htmlspecialchars($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <p class="mt-4 text-gray-700"><?php echo htmlspecialchars($_SESSION['startup']['description'] ?? 'No Description'); ?></p>
                            <?php if (isset($_SESSION['startup']['media'])): ?>
                            <div class="mt-4 flex items-center p-3 bg-gray-100 rounded-lg">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium"><?php echo basename($_SESSION['startup']['media']); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($_SESSION['startup']['questions'])): ?>
                            <div class="mt-6 space-y-4">
                                <h4 class="font-semibold">Questions for Joiners:</h4>
                                <?php foreach ($_SESSION['startup']['questions'] as $index => $question): ?>
                                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                    <span class="text-gray-700"><?php echo htmlspecialchars($question); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex justify-between mt-8">
                        <button type="submit" name="cancel" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
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

            // Handle adding questions
            const addQuestionBtn = document.getElementById('add-question');
            if (addQuestionBtn) {
                addQuestionBtn.addEventListener('click', function() {
                    const container = document.getElementById('questions-container');
                    const questionCount = container.querySelectorAll('.question-item').length + 1;
                    const div = document.createElement('div');
                    div.className = 'mb-4 question-item';
                    div.innerHTML = `
                        <input type="text" name="questions[]" placeholder="Question ${questionCount}"
                            class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    `;
                    container.appendChild(div);
                });
            }
        });
    </script>
</body>
</html>