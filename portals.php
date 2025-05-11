<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Inter', sans-serif;
        }
        .portal-card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .tab-section {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .tab-section:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <!-- Portals Summary Card (functioning as tabs) -->
        <div class="bg-white rounded-xl shadow-sm portal-card mb-8">
            <div class="flex flex-wrap">
                <!-- All Portals -->
                <div id="tab-all" class="w-1/3 p-6 text-center border-r border-gray-200 tab-section active-tab">
                    <p class="text-gray-500 mb-1">All Portals</p>
                    <p class="text-gray-900 font-bold text-2xl">04</p>
                    <div class="h-1 w-full bg-blue-800 mt-4"></div>
                </div>
                
                <!-- Created by you -->
                <div id="tab-created" class="w-1/3 p-6 text-center border-r border-gray-200 tab-section">
                    <div class="flex justify-center">
                        <p class="text-gray-500 mb-1">Created by you</p>
                    </div>
                    <p class="text-gray-900 font-bold text-2xl">01</p>
                    <div class="h-1 w-full bg-transparent mt-4"></div>
                </div>
                
                <!-- Joined by you -->
                <div id="tab-joined" class="w-1/3 p-6 text-center tab-section">
                    <p class="text-gray-500 mb-1">Joined by you</p>
                    <p class="text-gray-900 font-bold text-2xl">03</p>
                    <div class="h-1 w-full bg-transparent mt-4"></div>
                </div>
            </div>
        </div>
        
        <!-- Portal Listings with content sections for each tab -->
        <div class="bg-white rounded-xl shadow-sm portal-card">
            <!-- All Portals Content (visible by default) -->
            <div id="content-all" class="content-section">
               <?php include 'job_post_card.php'; ?>
               <?php include 'research_post_card.php'; ?>
               <?php include 'startup_post_card.php'; ?>
               <?php include 'collaboration_post_card.php'; ?>
               <?php include 'simple_post_card.php'; ?>
            </div>
            
            <!-- Created by you Content (initially hidden) -->
            <div id="content-created" class="content-section" style="display: none;">
                <!-- Include the personal_job_card.php file when 'Created by you' tab is clicked -->
                <div id="personal-job-card-container">
                    <!-- This content will be dynamically loaded -->
                </div>
            </div>
            
            <!-- Joined by you Content (initially hidden) -->
            <div id="content-joined" class="content-section" style="display: none;">
                <!-- Content for Joined by you tab (same as before) -->
            </div>
        </div>
    </div>

    <script>
        // Tab functionality using the statistics sections as tabs
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-section');
            const contentSections = document.querySelectorAll('.content-section');
            
            // Function to show content for selected tab
            function showContent(tabId) {
                // First hide all content sections
                contentSections.forEach(section => {
                    section.style.display = 'none';
                });
                
                // Show the selected content section
                const activeContent = document.getElementById('content-' + tabId.replace('tab-', ''));
                if (activeContent) {
                    activeContent.style.display = 'block';
                    
                    // For "Created by you" tab, load the personal_job_card.php file dynamically
                    if (tabId === 'tab-created') {
                        const jobCardContainer = document.getElementById('personal-job-card-container');
                        fetch('personal_job_card.php')
                            .then(response => response.text())
                            .then(html => {
                                jobCardContainer.innerHTML = html;
                            })
                            .catch(error => {
                                console.error('Error loading personal job card:', error);
                            });
                    }
                }
            }
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove indicator from all tabs
                    tabs.forEach(t => {
                        t.querySelector('div.h-1').classList.remove('bg-blue-800');
                        t.querySelector('div.h-1').classList.add('bg-transparent');
                    });
                    
                    // Add active indicator to clicked tab
                    this.querySelector('div.h-1').classList.remove('bg-transparent');
                    this.querySelector('div.h-1').classList.add('bg-blue-800');
                    
                    // Show corresponding content
                    showContent(this.id);
                });
            });
            
            // Initialize with first tab active
            showContent('tab-all');
        });
    </script>
</body>
</html>
