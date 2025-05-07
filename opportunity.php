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
     <style>
        .widthss{
            width:30%;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php'; ?>
  
    </div>

    <div class="flex min-h-screen">
        <!-- Left Sidebar -->
        <div class="p-6 widthss">
            <?php include 'left-sidebar.php'; ?>
        </div>
        
        <div class="flex-1 p-6">
            <div class=" mx-auto bg-white rounded-md shadow-sm">   
                <!-- Header -->
                <div class="mb-6 p-6">
                    <h1 class="text-2xl font-bold text-center text-[rgba(2,53,100,1)]">Posting an Opportunity</h1>
                    <p class="text-gray-500 text-center text-sm">List jobs, internships and freelancing projects to hire people for the companies as a recruiter</p>
                    
                    <!-- Progress Bar -->
                    <div class="mt-6 relative">
                        <div class="w-full bg-grey-200 h-2 rounded-full border border-dashed border-grey-border">
                            <div id="progress-bar" class="bg-blue-600 h-1.5 rounded-full" style="width: 33%;"></div>
                        </div>
                        <div id="step-indicator" class="text-grey-600 text-sm absolute right-0 top-3">1/3</div>
                    </div>
                </div>

                <!-- Form Content -->
                <form id="opportunity-form" class="space-y-6">

                    <!-- Step 1 -->
                    <div id="step-1" class="form-step">
                        <!-- Opportunity Title -->
                        <div class="border border-dashed rounded-lg p-4">
                            <label for="title" class="block text-[rgba(2,53,100,1)] font-medium mb-2">Opportunity Title</label>
                            <input type="text" id="title" name="title" 
                                class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:border-purple-600" 
                                placeholder="Enter the Title of the opportunity.">
                        </div>
                        
                        <!-- Type of Listing -->
                        <div class="border border-dashed rounded-lg p-4 mt-4">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Type of Listing *</label>
                            <div class="flex flex-wrap gap-4">
                                <button type="button" class="listing-type-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-type="jobs">
                                    Jobs Listing
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="listing-type-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-type="internship">
                                    Internship Listing
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="listing-type-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-type="freelance">
                                    Freelancing Project
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Select the Company -->
                        <div class="border border-dashed rounded-lg p-4 mt-4">
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
                        <div class="border border-dashed rounded-lg p-4 mt-4">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Which option best describes this job's location? *</label>
                            <div class="flex items-center space-x-6 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="location" value="onsite" class="form-radio text-purple-600 h-4 w-4">
                                    <span class="ml-2">On site</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="location" value="remote" class="form-radio text-purple-600 h-4 w-4">
                                    <span class="ml-2">Remote</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="location" value="hybrid" class="form-radio text-purple-600 h-4 w-4">
                                    <span class="ml-2">Hybrid</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Project Cycle -->
                        <div class="border border-dashed rounded-lg p-4 mt-4">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Project Cycle</label>
                            <div class="flex items-center flex-wrap sm:flex-nowrap">
                                <div class="w-full sm:w-1/2 pr-0 sm:pr-2 mb-2 sm:mb-0">
                                    <input type="text" id="start_date" name="start_date" 
                                        class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:border-purple-600" 
                                        placeholder="DD/MM/YYYY">
                                </div>
                                <div class="px-4 hidden sm:block">-</div>
                                <div class="w-full sm:w-1/2 pl-0 sm:pl-2">
                                    <input type="text" id="end_date" name="end_date" 
                                        class="w-full p-3 border border-gray-500 rounded-lg focus:outline-none focus:border-purple-600" 
                                        placeholder="DD/MM/YYYY">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div id="step-2" class="form-step hidden">
                        <!-- Budget and Payment -->
                        <div class="mt-4">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Budget and Payment</label>
                            <div class="flex flex-wrap gap-4">
                                <div class="w-full md:w-1/3">
                                    <input type="text" id="budget_amount" name="budget_amount" 
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                                        placeholder="Enter Amount">
                                </div>
                                <div class="w-full md:w-1/3">
                                    <div class="relative">
                                        <select id="budget_cycle" name="budget_cycle" 
                                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 appearance-none">
                                            <option value="" disabled selected>Select Cycle</option>
                                            <option value="hourly">Hourly</option>
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-500"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <div class="relative">
                                        <select id="budget_currency" name="budget_currency" 
                                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 appearance-none">
                                            <option value="" disabled selected>Select Currency</option>
                                            <option value="usd">USD</option>
                                            <option value="eur">EUR</option>
                                            <option value="gbp">GBP</option>
                                            <option value="inr">INR</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-500"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job Timing -->
                        <div class="mt-8">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Job Timing</label>
                            <div class="flex flex-wrap gap-4">
                                <button type="button" class="job-timing-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-timing="0-10">
                                    0-10 hrs/wk
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="job-timing-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-timing="10-20">
                                    10-20 hrs/wk
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="job-timing-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-timing="20-30">
                                    20-30 hrs/wk
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                            </div>
                        </div>

                        <!-- Job Shift -->
                        <div class="mt-8">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Job Shift</label>
                            <div class="flex flex-wrap gap-4">
                                <button type="button" class="job-shift-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-shift="morning">
                                    Morning
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="job-shift-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-shift="evening">
                                    Evening
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="job-shift-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50" data-shift="vary">
                                    Vary Differently
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                            </div>
                        </div>

                        <!-- Add Media -->
                        <div class="mt-8">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Add Media</label>
                            <div class="border border-dashed border-blue-300 rounded-lg p-8 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="far fa-file-alt text-2xl text-gray-400 mb-2"></i>
                                    <p class="mb-2">Drag and Drop or <span class="text-blue-500">Click to Upload</span></p>
                                    <p class="text-sm text-gray-500">Supported formats: JPG, PNG, PDF, SVG, MP4 Max Size: 25MB</p>
                                </div>
                                <input type="file" id="media_upload" name="media_upload" class="hidden">
                            </div>
                        </div>

                        <!-- Write Description -->
                        <div class="mt-8">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Write Description</label>
                            <textarea id="description" name="description" rows="5" 
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600"
                                placeholder="Enter the description of the job"></textarea>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div id="step-3" class="form-step hidden">
                        <!-- Add Related Tags -->
                        <div class="mt-4">
                            <label class="block text-[rgba(2,53,100,1)] font-medium mb-2">Add Related Tags</label>
                            <div class="relative">
                                <input type="text" id="search_tags" name="search_tags" 
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                                    placeholder="Search the tags">
                                <div class="absolute inset-y-0 right-0 flex items-center px-3">
                                    <i class="fas fa-search text-gray-500"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-sm text-gray-500 mb-4">Recommended</h3>
                            <div class="flex flex-wrap gap-4">
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                                <button type="button" class="tag-btn flex items-center justify-between border border-navy rounded-full px-5 py-2 text-[rgba(2,53,100,1)] font-medium hover:bg-blue-50">
                                    Tag 1 related to job
                                    <span class="ml-2 text-lg">+</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Review Details Step -->
                    <div id="review-step" class="form-step hidden">
                        <div class="mt-4">
                            <h2 class="text-xl text-center text-gray-400 mb-6">Review Details</h2>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-start">
                                    <div class="w-16 h-16 bg-gray-200 rounded mr-4"></div>
                                    <div>
                                        <h3 id="review-title" class="text-blue-500 font-medium">Opportunity Title</h3>
                                        <p id="review-company" class="text-blue-500">Company name</p>
                                        <p id="review-location" class="text-blue-500">Job location</p>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap gap-4 mt-4">
                                    <div class="text-sm" id="review-location-type">· On-site</div>
                                    <div class="text-sm" id="review-salary">· 80k-90k USD</div>
                                    <div class="text-sm" id="review-cycle">· Monthly</div>
                                    <div class="text-sm" id="review-hours">· 32 hrs/wk</div>
                                    <div class="text-sm" id="review-shift">· Evening</div>
                                </div>
                                
                                <div class="mt-4 text-gray-700" id="review-description">
                                    Lorem ipsum dolor sit amet consectetur. Vestibulum dui mauris vel velit ultrices tellus justo eget. Purus eget feugiat consequat elit quam ultricies integer tortor. Turpis a bibendum ornare accumsan amet tellus. Risus in vel sollicitudin laboris aliquet nunc nunc odio senectus. Cursus quam vel quis iaculis egestas egestus etiam mi. Purus ipsum non augue dictum nunc a. Cursus sollicitudin sit purus vitae phartra egestas in cursus ac. Nullam nunc hac sit convallis donec posuere amet. Tincidunt ultrices auctor pellentesque amet. Dictum nunc egestas etiam suspendisse elementum a facilisis. Tempus pulvinar ligula.
                                </div>
                                
                                <div class="mt-4">
                                    <div class="w-24 h-24 bg-blue-900 rounded-lg flex items-center justify-center text-white">
                                        <div class="text-center">
                                            <div class="text-xs">WE ARE</div>
                                            <div class="font-bold text-lg">HIRING</div>
                                            <div class="text-xs">Joining Post.png</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <span class="px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm">Tag 2</span>
                                    <span class="px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm">Tag 2</span>
                                    <span class="px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm">Tag 2</span>
                                    <span class="px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm">Tag 2</span>
                                    <span class="px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm">Tag 2</span>
                                    <span class="px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm">Tag 2</span>
                                    <span class="text-sm">+1</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-between mt-6 p-4">
                        <button type="button" id="cancel-btn" class="px-6 py-2 text-red-500 border border-red-500 rounded-md hover:bg-red-50 transition-colors">
                            Cancel
                        </button>
                        <div>
                            <button type="button" id="back-btn" class="px-6 py-2 border border-[rgba(2,53,100,1)] text-[rgba(2,53,100,1)] rounded-md hover:bg-gray-50 transition-colors mr-3 hidden">
                                Back
                            </button>
                            <button type="button" id="next-btn" class="px-8 py-2 bg-[rgba(2,53,100,1)] text-white rounded-md hover:bg-blue-900 transition-colors">
                                Next
                            </button>
                            <button type="(propagated to next page)submit" id="post-btn" class="px-8 py-2 bg-[rgba(2,53,100,1)] text-white rounded-md hover:bg-blue-900 transition-colors hidden">
                                Post
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="./js/opportunity.js"></script>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form steps
            const formSteps = document.querySelectorAll('.form-step');
            const progressBar = document.getElementById('progress-bar');
            const stepIndicator = document.getElementById('step-indicator');
            const nextBtn = document.getElementById('next-btn');
            const backBtn = document.getElementById('back-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            const postBtn = document.getElementById('post-btn');
            
            let currentStep = 1;
            const totalSteps = 3;
            
            // Function to update the progress bar
            function updateProgressBar() {
                const progress = (currentStep / totalSteps) * 100;
                progressBar.style.width = `${progress}%`;
                stepIndicator.textContent = `${currentStep}/${totalSteps}`;
                
                // Show/hide back button
                if (currentStep > 1) {
                    backBtn.classList.remove('hidden');
                } else {
                    backBtn.classList.add('hidden');
                }
                
                // Show/hide next and post buttons
                if (currentStep === totalSteps) {
                    nextBtn.classList.add('hidden');
                    postBtn.classList.remove('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                    postBtn.classList.add('hidden');
                }
            }
            
            // Show the current step
            function showStep(stepNumber) {
                formSteps.forEach((step, index) => {
                    if (index === stepNumber - 1) {
                        step.classList.remove('hidden');
                    } else {
                        step.classList.add('hidden');
                    }
                });
                
                // Special case for review step (after step 3)
                if (stepNumber === 4) {
                    document.getElementById('review-step').classList.remove('hidden');
                    document.getElementById('step-3').classList.add('hidden');
                    populateReviewData();
                }
                
                updateProgressBar();
            }
            
            // Validate form inputs for current step
            function validateStep(stepNumber) {
                if (stepNumber === 1) {
                    const title = document.getElementById('title').value;
                    const company = document.getElementById('company').value;
                    const location = document.querySelector('input[name="location"]:checked');
                    const listingType = document.querySelector('.listing-type-btn.bg-navy');
                    if (!title || !company || !location || !listingType) {
                        alert('Please fill all required fields in Step 1.');
                        return false;
                    }
                } else if (stepNumber === 2) {
                    const budgetAmount = document.getElementById('budget_amount').value;
                    const budgetCycle = document.getElementById('budget_cycle').value;
                    const budgetCurrency = document.getElementById('budget_currency').value;
                    const jobTiming = document.querySelector('.job-timing-btn.bg-navy');
                    const jobShift = document.querySelector('.job-shift-btn.bg-navy');
                    if (!budgetAmount || !budgetCycle || !budgetCurrency || !jobTiming || !jobShift) {
                        alert('Please fill all required fields in Step 2.');
                        return false;
                    }
                } else if (stepNumber === 3) {
                    const tags = document.querySelectorAll('.tag-btn.bg-navy');
                    if (tags.length === 0) {
                        alert('Please select at least one tag in Step 3.');
                        return false;
                    }
                }
                return true;
            }
            
            // Populate review data
            function populateReviewData() {
                // Title
                document.getElementById('review-title').textContent = document.getElementById('title').value || 'Opportunity Title';
                
                // Company
                document.getElementById('review-company').textContent = document.getElementById('company').value || 'Company name';
                
                // Location
                const locationRadios = document.querySelectorAll('input[name="location"]');
                let selectedLocation = 'On-site';
                locationRadios.forEach(radio => {
                    if (radio.checked) {
                        selectedLocation = radio.value.charAt(0).toUpperCase() + radio.value.slice(1);
                    }
                });
                document.getElementById('review-location-type').textContent = `· ${selectedLocation}`;
                
                // Salary
                const budgetAmount = document.getElementById('budget_amount').value;
                const budgetCurrency = document.getElementById('budget_currency').value.toUpperCase();
                document.getElementById('review-salary').textContent = `· ${budgetAmount} ${budgetCurrency}`;
                
                // Cycle
                const budgetCycle = document.getElementById('budget_cycle').value;
                document.getElementById('review-cycle').textContent = `· ${budgetCycle.charAt(0).toUpperCase() + budgetCycle.slice(1)}`;
                
                // Hours
                const jobTiming = document.querySelector('.job-timing-btn.bg-navy');
                document.getElementById('review-hours').textContent = `· ${jobTiming ? jobTiming.dataset.timing : 'Not specified'} hrs/wk`;
                
                // Shift
                const jobShift = document.querySelector('.job-shift-btn.bg-navy');
                document.getElementById('review-shift').textContent = `· ${jobShift ? jobShift.dataset.shift.charAt(0).toUpperCase() + jobShift.dataset.shift.slice(1) : 'Not specified'}`;
                
                // Description
                document.getElementById('review-description').textContent = document.getElementById('description').value || 
                    'Lorem ipsum dolor sit amet consectetur. Vestibulum dui mauris vel velit ultrices tellus justo eget.';
                
                // Tags
                const tagsContainer = document.querySelector('.flex.flex-wrap.gap-2.mt-4');
                tagsContainer.innerHTML = '';
                const selectedTags = document.querySelectorAll('.tag-btn.bg-navy');
                selectedTags.forEach((tag, index) => {
                    if (index < 6) {
                        const span = document.createElement('span');
                        span.className = 'px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm';
                        span.textContent = tag.textContent.trim().replace('+', '');
                        tagsContainer.appendChild(span);
                    }
                });
                if (selectedTags.length > 6) {
                    const moreSpan = document.createElement('span');
                    moreSpan.className = 'text-sm';
                    moreSpan.textContent = `+${selectedTags.length - 6}`;
                    tagsContainer.appendChild(moreSpan);
                }
            }
            
            // Handle next button click
            nextBtn.addEventListener('click', function() {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        showStep(currentStep);
                    } else if (currentStep === totalSteps) {
                        currentStep = 4; // Review step
                        showStep(currentStep);
                    }
                }
            });
            
            // Handle back button click
            backBtn.addEventListener('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            
            // Handle cancel button click
            cancelBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel? All data will be lost.')) {
                    document.getElementById('opportunity-form').reset();
                    currentStep = 1;
                    showStep(currentStep);
                    // Reset all button selections
                    document.querySelectorAll('.listing-type-btn, .job-timing-btn, .job-shift-btn, .tag-btn').forEach(btn => {
                        btn.classList.remove('bg-navy', 'text-white');
                    });
                }
            });
            
            // Handle post button click
            postBtn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Your opportunity has been posted!');
                // In a real app, we would submit the form here
                // document.getElementById('opportunity-form').submit();
            });
            
            // Handle listing type button selection
            const listingButtons = document.querySelectorAll('.listing-type-btn');
            listingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    listingButtons.forEach(btn => {
                        btn.classList.remove('bg-navy', 'text-white');
                    });
                    this.classList.add('bg-navy', 'text-white');
                });
            });
            
            // Handle job timing button selection
            const jobTimingButtons = document.querySelectorAll('.job-timing-btn');
            jobTimingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    jobTimingButtons.forEach(btn => {
                        btn.classList.remove('bg-navy', 'text-white');
                    });
                    this.classList.add('bg-navy', 'text-white');
                });
            });
            
            // Handle job shift button selection
            const jobShiftButtons = document.querySelectorAll('.job-shift-btn');
            jobShiftButtons.forEach(button => {
                button.addEventListener('click', function() {
                    jobShiftButtons.forEach(btn => {
                        btn.classList.remove('bg-navy', 'text-white');
                    });
                    this.classList.add('bg-navy', 'text-white');
                });
            });
            
            // Handle tag button selection
            const tagButtons = document.querySelectorAll('.tag-btn');
            tagButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.classList.toggle('bg-navy');
                    this.classList.toggle('text-white');
                });
            });
            
            // Handle media upload
            const mediaUpload = document.getElementById('media_upload');
            const mediaArea = document.querySelector('.border.border-dashed.border-blue-300');
            mediaArea.addEventListener('click', () => mediaUpload.click());
            
            mediaArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                mediaArea.classList.add('bg-blue-50');
            });
            
            mediaArea.addEventListener('dragleave', () => {
                mediaArea.classList.remove('bg-blue-50');
            });
            
            mediaArea.addEventListener('drop', (e) => {
                e.preventDefault();
                mediaArea.classList.remove('bg-blue-50');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    mediaUpload.files = files;
                    validateFile(files[0]);
                }
            });
            
            mediaUpload.addEventListener('change', () => {
                if (mediaUpload.files.length > 0) {
                    validateFile(mediaUpload.files[0]);
                }
            });
            
            function validateFile(file) {
                const allowedFormats = ['image/jpeg', 'image/png', 'application/pdf', 'image/svg+xml', 'video/mp4'];
                const maxSize = 25 * 1024 * 1024; // 25MB
                if (!allowedFormats.includes(file.type)) {
                    alert('Unsupported file format. Please upload JPG, PNG, PDF, SVG, or MP4.');
                    mediaUpload.value = '';
                } else if (file.size > maxSize) {
                    alert('File size exceeds 25MB limit.');
                    mediaUpload.value = '';
                } else {
                    mediaArea.querySelector('p').textContent = `File selected: ${file.name}`;
                }
            }
            
            // Initialize first step
            showStep(currentStep);
        });
    </script> -->
</body>
</html>