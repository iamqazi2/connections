<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications and Privacy Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-gray-50 p-4">

  <div class="flex min-h-screen">
  
   <div class="w-[1046px]  mx-auto bg-white rounded-lg shadow-sm">
 
        <?php
        // Initialize variables for form submission
        $message = '';
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['save_notifications'])) {
                // Process notifications form
                $message = "Notification settings saved successfully!";
            } elseif (isset($_POST['save_privacy'])) {
                // Process privacy form
                $message = "Privacy settings saved successfully!";
            }
        }
        
        // Display success message if any
        if (!empty($message)) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">' . $message . '</div>';
        }
        ?>

        <!-- Notifications Section -->
        <div class="border border-blue-300 border-dashed rounded-lg p-6 mb-6">
            <div class="flex items-center mb-6">
                <h2 class="text-2xl font-bold text-[rgba(2,53,100,1)]

">Notifications</h2>
                <p class="ml-2 text-gray-600">Choose what communication you'd like to receive from us.</p>
            </div>
            
            <form method="post" action="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Systems Column -->
                    <div>
                        <h3 class="text-xl font-bold text-[rgba(2,53,100,1)]

 mb-4">Systems</h3>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Connection Request</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="systems_connection_request" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Job Listing</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="systems_job_listing" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Research Ideas</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="systems_research_ideas" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Startup Ideas</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="systems_startup_ideas" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <h3 class="text-xl font-bold text-[rgba(2,53,100,1)]

 mb-4 mt-6">Email</h3>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Connection Request</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_connection_request" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Job Listing</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_job_listing" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Research Ideas</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_research_ideas" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Join or Apply</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_join_apply" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Join/Apply Column -->
                    <div>
                        <h3 class="text-xl font-bold text-[rgba(2,53,100,1)]

 mb-4">Join/Apply</h3>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Research Portal</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="join_research_portal" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Startup Portal</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="join_startup_portal" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Apply Job</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="join_apply_job" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Apply Freelancing</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="join_apply_freelancing" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" name="save_notifications" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-8 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Privacy and Security Section -->
        <div class="border border-blue-300 border-dashed rounded-lg p-6 mb-6">
            <div class="flex items-center mb-6">
                <h2 class="text-2xl font-bold text-[rgba(2,53,100,1)]

">Privacy and Security</h2>
                <p class="ml-2 text-gray-600">Choose the way you want to protect your data.</p>
            </div>
            
            <form method="post" action="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Privacy Column -->
                    <div>
                        <h3 class="text-xl font-bold text-[rgba(2,53,100,1)]

 mb-4">Privacy</h3>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Public Profile</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="privacy_public_profile" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Share Phone Number</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="privacy_share_phone" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Share Email Address</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="privacy_share_email" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Show Profile Picture</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="privacy_show_picture" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Security Column -->
                    <div>
                        <h3 class="text-xl font-bold text-[rgba(2,53,100,1)]

 mb-4">Security</h3>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Reset Password</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="security_reset_password" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[rgba(2,53,100,1)]

">Logout of all Devices</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="security_logout_devices" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" name="save_privacy" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-8 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Sign Out Button -->
        <div class="border border-blue-300 border-dashed rounded-lg p-6">
            <a href="#" class="inline-flex items-center bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Sign out
            </a>
        </div>
    </div>

    <script>
        // Script to handle toggle switches
        document.addEventListener('DOMContentLoaded', function() {
            // You can add JavaScript functionality for the toggles here if needed
        });
    </script>
</body>
</html>