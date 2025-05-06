<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding Post</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php'; ?>

    <div class="flex min-h-screen">
        <!-- Left Sidebar -->
        <div class="w-64 bg-white p-6 border-r border-gray-200">
            <?php include 'left-sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-center text-blue-900">Adding Post</h1>
                    <p class="text-gray-500 text-center text-sm mt-1">Share updates and post anything you want to share with your connections</p>
                    
                    <!-- Progress Bar -->
                    <div class="mt-6 relative">
                        <div class="w-full bg-gray-200 h-1 rounded-full">
                            <div class="bg-blue-500 h-1 rounded-full" style="width: 100%;"></div>
                        </div>
                        <div class="text-gray-400 text-xs absolute right-0 top-2">1/1</div>
                    </div>
                </div>

                <!-- Form Content -->
                <form action="backend/process_post.php" method="post" enctype="multipart/form-data" class="px-6 pb-6">
                    <!-- Description Field -->
                    <div class="mb-6">
                        <label for="description" class="block text-blue-900 font-medium mb-2">Write Description</label>
                        <div class="relative">
                            <textarea id="description" name="description" rows="4" 
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                placeholder="Enter the description of the job"></textarea>
                            <button type="button" class="absolute right-3 bottom-3 text-gray-400">
                                <i class="fas fa-align-left"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Media Upload Section -->
                    <div class="mb-6">
                        <label class="block text-blue-900 font-medium mb-2">Add Media</label>
                        <div id="upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-8 flex flex-col items-center justify-center hover:border-blue-500 transition-colors cursor-pointer">
                            <div class="text-blue-500 mb-3">
                                <i id="upload-icon" class="far fa-file-alt text-3xl"></i>
                            </div>
                            <p id="upload-text" class="text-center mb-2">
                                <span class="text-gray-500">Drag and Drop or</span> 
                                <span class="text-blue-500 font-medium">Click to Upload</span>
                            </p>
                            <p id="file-name" class="text-green-600 font-medium text-sm mb-2 hidden"></p>
                            <p class="text-gray-400 text-sm text-center">
                                Supported formats: JPG, PNG, PDF, SVG, MP4 <span class="font-medium">Max Size: 25MB</span>
                            </p>
                            <input type="file" name="media" id="media" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.svg,.mp4">
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-between mt-8">
                        <button type="button" class="px-6 py-2 border border-gray-300 text-gray-500 rounded-md hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-10 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors">
                            Post
                        </button>
                    </div>
                    
                    <!-- Success/Error Messages -->
                    <div class="mt-6">
                    <?php
                    // Display success/error messages
                    if (isset($_SESSION['success'])) {
                        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">'.$_SESSION['success'].'</div>';
                        unset($_SESSION['success']);
                    }
                    if (isset($_SESSION['error'])) {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">'.$_SESSION['error'].'</div>';
                        unset($_SESSION['error']);
                    }
                    ?></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Script to handle the file upload click functionality
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('upload-area');
            const fileInput = document.getElementById('media');
            const uploadIcon = document.getElementById('upload-icon');
            const uploadText = document.getElementById('upload-text');
            const fileNameDisplay = document.getElementById('file-name');
            
            // Function to update UI when file is selected
            function updateFileUI(fileName) {
                // Change icon to check mark indicating success
                uploadIcon.classList.remove('fa-file-alt');
                uploadIcon.classList.add('fa-check-circle');
                
                // Display the file name
                fileNameDisplay.textContent = fileName;
                fileNameDisplay.classList.remove('hidden');
                
                // Change upload area border to green
                uploadArea.classList.add('border-green-500');
                uploadArea.classList.add('bg-green-50');
                
                // Update text to show file is selected
                uploadText.innerHTML = '<span class="text-green-600">File successfully uploaded!</span>';
                
                // Add a subtle animation
                uploadIcon.classList.add('animate-pulse');
                setTimeout(() => {
                    uploadIcon.classList.remove('animate-pulse');
                }, 1000);
            }
            
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    updateFileUI(fileName);
                }
            });
            
            // Drag and drop functionality
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-blue-500');
                this.classList.add('bg-blue-50');
            });
            
            uploadArea.addEventListener('dragleave', function() {
                if (!fileInput.files.length) {
                    this.classList.remove('border-blue-500');
                    this.classList.remove('bg-blue-50');
                }
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-500');
                this.classList.remove('bg-blue-50');
                
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    const fileName = e.dataTransfer.files[0].name;
                    updateFileUI(fileName);
                }
            });
        });
    </script>
</body>
</html>