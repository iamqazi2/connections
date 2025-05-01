<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posting an Opportunity</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {   
            theme: {
                extend: {
                    colors: {
                        'purple-light': '#f5f0ff',
                        'purple-border': '#9c6bff',
                        'navy': '#0A2558'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">       <?php include 'navbar.php'; ?>

    <div class="flex min-h-screen">
        <!-- Left Sidebar -->
        <div class="w-64 bg-white p-6 border-r border-gray-200">
          <?php include 'left-sidebar.php'; ?>
        </div>
        <div class="flex-1 p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-md shadow-sm">   
        <!-- Header -->
        <div class="mb-6 p-6">
            <h1 class="text-2xl font-bold text-center text-[rgba(2,53,100,1)]">Posting an Opportunity</h1>
            <p class="text-gray-500 text-center text-sm">List jobs, internships, and freelancing projects to hire people for the companies as a recruiter</p>
            
            <!-- Progress Bar -->
            <div class="mt-6 relative">
                <div class="w-full bg-grey-200 h-2 rounded-full border border-dashed border-grey-border">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: 33%;"></div>
                </div>
                <div class="text-grey-600 text-sm absolute right-0 top-3">1/3</div>
            </div>
        </div>

        <!-- Form Content -->
        <form action="process_opportunity.php" method="post" class="space-y-6">
            <!-- Opportunity Title -->
            <div class="border border-dashed rounded-lg p-4">
                <label for="title" class="block text-[rgba(2,53,100,1)] font-medium mb-2">Opportunity Title</label>
                <input type="text" id="title" name="title" 
                    class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:border-purple-600" 
                    placeholder="Enter the Title of the opportunity.">
            </div>
            
            <!-- Type of Listing -->
            <div class="border border-dashed rounded-lg p-4">

                <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Type of Listing *</label>
                <div class="flex flex-wrap gap-4">
                    <button type="button" class="flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                        Jobs Listing
                        <span class="ml-2 text-lg">+</span>
                    </button>
                    <button type="button" class="flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                        Internship Listing
                        <span class="ml-2 text-lg">+</span>
                    </button>
                    <button type="button" class="flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                        Freelancing Project
                        <span class="ml-2 text-lg">+</span>
                    </button>
                </div>
            </div>
            
            <!-- Select the Company -->
            <div class="border border-dashed rounded-lg p-4">
                <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Select the Company</label>
                <div class="relative">
                    <input type="text" id="company" name="company" 
                        class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:border-purple-600" 
                        placeholder="Select the company providing the opportunity">
                    <span class="absolute right-3 top-3 text-[rgba(2,53,100,1)]">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
            </div>
            
            <!-- Job Location -->
            <div class=" flex border border-dashed  rounded-lg p-4">
                <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Which option best describes this job's location? *</label>
                <div class="flex items-center space-x-6 mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="location" value="onsite" class="form-radio text-purple-600 h-4 w-4 ml-10 ">
                        <span class="ml-2">On site</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="location" value="remote" class="form-radio text-purple-600 h-4 w-4 ml-4">
                        <span class="ml-2">Remote</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="location" value="hybrid" class="form-radio text-purple-600 h-4 w-4 ml-4">
                        <span class="ml-2">Hybrid</span>
                    </label>
                </div>
            </div>
            
            <!-- Project Cycle -->
            <div class="border border-dashed rounded-lg p-4">
                <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Project Cycle</label>
                <div class="flex items-center">
                    <div class="w-1/2 pr-2">
                        <input type="text" id="start_date" name="start_date" 
                            class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:border-purple-600" 
                            placeholder="DD/MM/YYYY">
                    </div>
                    <div class="px-4">-</div>
                    <div class="w-1/2 pl-2">
                        <input type="text" id="end_date" name="end_date" 
                            class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:border-purple-600" 
                            placeholder="DD/MM/YYYY">
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-between mt-6">
                <button type="button" class="px-6 py-2 text-red-500 border border-red-500 rounded-md hover:bg-red-50 transition-colors ml-6 mb-6 mt-6">
                    Cancel
                </button>
                <button type="submit" class="px-8 py-2 bg-[rgba(2,53,100,1)] text-white rounded-md hover:bg-[rgba(2,53,100,1)] transition-colors mr-6 mb-6 mt-6">
                    Next
                </button>
            </div>
        </form>
    </div>

    <script>
        // Script to handle date inputs and button selection
        document.addEventListener('DOMContentLoaded', function() {
            // Handle listing type button selection
            const listingButtons = document.querySelectorAll('[type="button"]');
            listingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    listingButtons.forEach(btn => {
                        btn.classList.remove('bg-navy', 'text-white');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('bg-navy', 'text-white');
                });
            });
            
            // Simple date input formatting
            const dateInputs = document.querySelectorAll('[id$="_date"]');
            dateInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 8) {
                        value = value.substring(0, 8);
                    }
                    
                    if (value.length > 4) {
                        value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4);
                    } else if (value.length > 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2);
                    }
                    
                    e.target.value = value;
                });
            });
        });
    </script>
</body>
</html>