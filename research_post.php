<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Post</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .widthss{
            width:30%;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php
    if (session_status() == PHP_SESSION_NONE) session_start();
    // Display success message if set
    
    if (isset($_SESSION['success'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    <?php
    // Display error message if set
    if (isset($_SESSION['error'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    <div class="flex min-h-screen">
     
        <div class="flex-1 p-6">
            <div class=" p-6 bg-white rounded-lg shadow-sm">
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
                        $_SESSION['research_idea'] = [
                            'title' => htmlspecialchars($_POST['title'] ?? ''),
                            'description' => htmlspecialchars($_POST['description'] ?? '')
                        ];
                        if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
                            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'svg', 'mp4'];
                            $filename = $_FILES['media']['name'];
                            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $max_size = 25 * 1024 * 1024;
                            if (in_array($filetype, $allowed)) {
                                if ($_FILES['media']['size'] <= $max_size) {
                                    $new_filename = uniqid() . '.' . $filetype;
                                    $upload_dir = __DIR__ . '/research_media/';
                                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                                    $target_path = $upload_dir . $new_filename;
                                    if (move_uploaded_file($_FILES['media']['tmp_name'], $target_path)) {
                                        $_SESSION['research_idea']['media'] = $target_path;
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
                        $current_step = 2;
                    } elseif ($current_step == 2) {
                        $_SESSION['research_idea']['focus'] = $_POST['focus'] ?? [];
                        $current_step = 3;
                    } elseif ($current_step == 3) {
                        $_SESSION['research_idea']['question1'] = htmlspecialchars($_POST['question1'] ?? '');
                        $_SESSION['research_idea']['word_limit1'] = $_POST['word_limit1'] ?? '0-100';
                        $_SESSION['research_idea']['question2'] = htmlspecialchars($_POST['question2'] ?? '');
                        $_SESSION['research_idea']['word_limit2'] = $_POST['word_limit2'] ?? '0-100';
                        $current_step = 4;
                    }
                }
                
                // Handle back button functionality
                if (isset($_POST['back'])) {
                    $current_step = max(1, $current_step - 1);
                }
                
                // Handle cancel button functionality
                if (isset($_POST['cancel'])) {
                    unset($_SESSION['research_idea']);
                    header("Location: index.php");
                    exit;
                }
                ?>
                <div class="mb-8 p-6">
                    <h1 class="text-2xl text-center font-bold text-blue-900">Post Research Idea</h1>
                    <p class="text-gray-600 mt-1">Share your idea with people so that you can collaborate with them to learn more about that.</p>
                </div>
                <!-- Progress Bar -->
                <div class="mb-8 relative">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo min(100, ($current_step * 33.33)); ?>%"></div>
                    </div>
                    <div class="text-right mt-1 text-sm text-gray-600"><?php echo $current_step; ?>/4</div>
                </div>
                <?php if ($current_step == 1): ?>
                <!-- Step 1: Basic Information -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="step" value="1">
                    <div class="mb-6">
                        <label for="title" class="block mb-2 font-medium text-blue-900">Research Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter the title of the opportunity" 
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            value="<?php echo isset($_SESSION['research_idea']['title']) ? $_SESSION['research_idea']['title'] : ''; ?>" required>
                    </div>
                    <div class="mb-6">
                        <label for="description" class="block mb-2 font-medium text-blue-900">Write Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Enter the description of the job"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo isset($_SESSION['research_idea']['description']) ? $_SESSION['research_idea']['description'] : ''; ?></textarea>
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
                                <?php if (isset($_SESSION['research_idea']['media'])): ?>
                                    <p class="mt-2 text-green-600 file-info">File uploaded: <?php echo basename($_SESSION['research_idea']['media']); ?></p>
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
                <!-- Step 2: Focus of Research -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="step" value="2">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900">Focus of Research</h2>
                        <div class="mb-4 mt-4">
                            <div class="relative">
                                <input type="text" id="search_focus" placeholder="Search the tags" 
                                    class="w-full p-3 pl-4 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" class="absolute right-3 top-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-6">
                            <p class="mb-2 text-gray-700">Recommended</p>
                            <div class="grid grid-cols-3 gap-3">
                                <?php
                                $focus_areas = ['Tag related to Research', 'Tag related to Research', 'Tag related to Research', 'Tag related to Research', 'Tag related to Research', 'Tag related to Research', 'Tag related to Research', 'Tag related to Research', 'Tag related to Research'];
                                $selected_focus = $_SESSION['research_idea']['focus'] ?? [];
                                foreach ($focus_areas as $index => $area):
                                    $is_selected = in_array($area, $selected_focus);
                                ?>
                                <label class="tag-btn flex items-center justify-between p-3 border border-blue-600 rounded-full text-blue-900 hover:bg-blue-50 cursor-pointer <?php echo $is_selected ? 'bg-blue-100' : ''; ?>">
                                    <span><?php echo $area; ?></span>
                                    <input type="checkbox" name="focus[]" value="<?php echo $area; ?>" class="hidden" <?php echo $is_selected ? 'checked' : ''; ?>>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 plus-icon <?php echo $is_selected ? 'hidden' : ''; ?>">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </label>
                                <?php endforeach; ?>
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
                <?php elseif ($current_step == 3): ?>
                <!-- Step 3: Questions for Joining Users -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="step" value="3">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Questions for Joining Users</h2>
                        <div class="space-y-6">
                            <!-- Question 1 -->
                            <div>
                                <label for="question1" class="block mb-2 font-medium text-blue-900">Q1. Question to ask from the user who wants to join?</label>
                                <input type="text" id="question1" name="question1" placeholder="Enter your question" 
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="<?php echo isset($_SESSION['research_idea']['question1']) ? $_SESSION['research_idea']['question1'] : ''; ?>" required>
                                <div class="mt-3">
                                    <p class="text-gray-700 mb-2">Word Limit</p>
                                    <div class="flex gap-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="word_limit1" value="0-100" class="mr-2" 
                                                <?php echo (isset($_SESSION['research_idea']['word_limit1']) && $_SESSION['research_idea']['word_limit1'] == '0-100') ? 'checked' : ''; ?> required>
                                            0-100 words
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="word_limit1" value="100-200" class="mr-2" 
                                                <?php echo (isset($_SESSION['research_idea']['word_limit1']) && $_SESSION['research_idea']['word_limit1'] == '100-200') ? 'checked' : ''; ?>>
                                            100-200 words
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="word_limit1" value="200-500" class="mr-2" 
                                                <?php echo (isset($_SESSION['research_idea']['word_limit1']) && $_SESSION['research_idea']['word_limit1'] == '200-500') ? 'checked' : ''; ?>>
                                            200-500 words
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- Question 2 -->
                            <div>
                                <label for="question2" class="block mb-2 font-medium text-blue-900">Q2. Question to ask from the user who wants to join?</label>
                                <input type="text" id="question2" name="question2" placeholder="Enter your question" 
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="<?php echo isset($_SESSION['research_idea']['question2']) ? $_SESSION['research_idea']['question2'] : ''; ?>" required>
                                <div class="mt-3">
                                    <p class="text-gray-700 mb-2">Word Limit</p>
                                    <div class="flex gap-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="word_limit2" value="0-100" class="mr-2" 
                                                <?php echo (isset($_SESSION['research_idea']['word_limit2']) && $_SESSION['research_idea']['word_limit2'] == '0-100') ? 'checked' : ''; ?> required>
                                            0-100 words
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="word_limit2" value="100-200" class="mr-2" 
                                                <?php echo (isset($_SESSION['research_idea']['word_limit2']) && $_SESSION['research_idea']['word_limit2'] == '100-200') ? 'checked' : ''; ?>>
                                            100-200 words
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="word_limit2" value="200-500" class="mr-2" 
                                                <?php echo (isset($_SESSION['research_idea']['word_limit2']) && $_SESSION['research_idea']['word_limit2'] == '200-500') ? 'checked' : ''; ?>>
                                            200-500 words
                                        </label>
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
                                Next
                            </button>
                        </div>
                    </div>
                </form>
                <?php elseif ($current_step == 4): ?>
                <!-- Step 4: Review Details -->
                <form action="process_research_idea.php" method="post">
                    <input type="hidden" name="step" value="4">
                    <input type="hidden" name="action" value="post">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-blue-900 mb-4">Review Details</h2>
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-2">Research Idea</h3>
                            <h4 class="text-md font-medium"><?php echo $_SESSION['research_idea']['title'] ?? 'Research Title'; ?></h4>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <?php
                                $focus = $_SESSION['research_idea']['focus'] ?? ['Focus of Interest', 'Focus of Interest', 'Focus of Interest'];
                                foreach ($focus as $f): ?>
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        <?php echo htmlspecialchars($f); ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (count($focus) > 3): ?>
                                    <span class="text-sm text-gray-500">+<?php echo count($focus) - 3; ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="mt-4 text-gray-700">
                                <?php echo $_SESSION['research_idea']['description'] ?? "Lorem ipsum dolor sit amet consectetur. Vestibulum duis mauris vel elit uttior tellus justo eget. Purus eget feugiat consequat elit quam utrices integer toror. Turpis a bibendum morbi accumsan amet felis. Risus in vel pellentesque lobortis aliquet nunc nunc odio senectus. Cursus quam vel quisque egestas egestas etiam mi. Purus ipsum non augue dictum nunc a Cursus sollicitudin sit purus vitae pharetra egestas in cursus ac. Nullam nunc hac sit convallis donec posuere amet. Tincidunt ultrices auctor pellentesque amet. Dictum nunc egestas etiam suspendisse elementum a facilisis. Tempus pulvinar ligula."; ?>
                            </p>
                            <?php if (isset($_SESSION['research_idea']['media'])): ?>
                            <div class="mt-4 flex items-center p-3 bg-gray-100 rounded-lg">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">
                                        <?php echo basename($_SESSION['research_idea']['media']); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="flex flex-wrap gap-2 mt-6">
                                <?php 
                                $sample_tags = $_SESSION['research_idea']['focus'] ?? ['Tag related to Research', 'Tag related to Research', 'Tag related to Research'];
                                foreach ($sample_tags as $tag): ?>
                                    <span class="inline-flex items-center px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded-full">
                                        <?php echo htmlspecialchars($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (count($sample_tags) > 3): ?>
                                    <span class="text-sm text-gray-500">+<?php echo count($sample_tags) - 3; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-6 space-y-4">
                                <div>
                                    <p class="block mb-2 font-medium text-blue-900">Q1. <?php echo $_SESSION['research_idea']['question1'] ?? 'Question to ask from the user who wants to join?'; ?></p>
                                    <p class="text-gray-700">Word Limit: <?php echo $_SESSION['research_idea']['word_limit1'] ?? '0-100'; ?> words</p>
                                </div>
                                <div>
                                    <p class="block mb-2 font-medium text-blue-900">Q2. <?php echo $_SESSION['research_idea']['question2'] ?? 'Question to ask from the user who wants to join?'; ?></p>
                                    <p class="text-gray-700">Word Limit: <?php echo $_SESSION['research_idea']['word_limit2'] ?? '0-100'; ?> words</p>
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
        .tag-btn input:checked + .plus-icon {
            display: none;
        }
        .tag-btn input:checked ~ .plus-icon {
            display: none;
        }
        .tag-btn input:checked {
            background-color: #EBF5FF;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tagButtons = document.querySelectorAll('.tag-btn');
            tagButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    if (checkbox.checked) {
                        this.classList.add('bg-blue-100');
                        this.querySelector('.plus-icon').classList.add('hidden');
                    } else {
                        this.classList.remove('bg-blue-100');
                        this.querySelector('.plus-icon').classList.remove('hidden');
                    }
                });
            });
            const searchInput = document.getElementById('search_focus');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    tagButtons.forEach(btn => {
                        const tagText = btn.querySelector('span').textContent.toLowerCase();
                        if (tagText.includes(searchTerm)) {
                            btn.style.display = '';
                        } else {
                            btn.style.display = 'none';
                        }
                    });
                });
            }
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