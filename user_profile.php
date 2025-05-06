<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecticus - Setup Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-white">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Logo and Header -->
        <div class="flex flex-col md:flex-row items-center mb-8">
            <div class="mb-4 md:mb-0">
                <a href="#" class="text-blue-600 font-bold text-2xl flex items-center">
                   <img src="images/logo_full.svg" alt="Logo" class="">
                </a>
            </div>
        </div>
        <h1 class="text-[#023564] text-center mb-8 text-[32px] font-[700]">Setup your Profile</h1>

        <!-- Step 1: Basic Profile Information -->
        <div id="step1" class="step-content">
            <!-- Profile Form -->
            <div style="box-shadow: 0px 0px 20px 0px #00000026;" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row">
                    <div class="w-full md:w-2/3 pr-0 md:pr-6">
                        <!-- Name Field -->
                        <div class="mb-6">
                            <label for="name" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Name</label>
                            <input type="text" id="name" placeholder="Enter your legal Name" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Profile Headline -->
                        <div class="mb-6">
                            <label for="headline" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Profile Headline</label>
                            <input type="text" id="headline" placeholder="Tell us the best defines you" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <!-- Profile Image Upload -->
                    <div class="w-full md:w-1/3 mt-6 md:mt-0">
                        <p class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Profile Image</p>
                        <div class="border-2 border-dashed border-[#3F8BC9] rounded-lg bg-[#2A97FC0D] p-6 flex flex-col items-center justify-center h-48">
                            <div class="text-blue-600 mb-2">
                                <i class="far fa-file-image text-xl"></i>
                            </div>
                            <p class="text-sm text-gray-600 text-center mb-1">Recommended 400x400 pixels</p>
                            <p class="text-sm text-gray-600 text-center">JPG, JPEG, PNG, GIF file</p>
                            <label for="profile-upload" class="cursor-pointer mt-2">
                                <input name="profile_image" type="file" id="profile-upload" class="hidden" accept=".jpg,.jpeg,.png,.gif">
                                <span class="inline-block bg-white border border-blue-500 text-blue-500 rounded-md px-3 py-1 text-sm">+</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div style="box-shadow: 0px 0px 20px 0px #00000026;" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current City -->
                    <div>
                        <label for="city" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Current City</label>
                        <div class="relative">
                            <select id="city" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>Select City</option>
                                <option value="new-york">New York</option>
                                <option value="london">London</option>
                                <option value="tokyo">Tokyo</option>
                                <option value="paris">Paris</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-blue-900">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Homeland -->
                    <div>
                        <label for="homeland" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Homeland</label>
                        <div class="relative">
                            <select id="homeland" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>Select Homeland</option>
                                <option value="usa">United States</option>
                                <option value="uk">United Kingdom</option>
                                <option value="japan">Japan</option>
                                <option value="france">France</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-blue-900">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campus and Department -->
            <div style="box-shadow: 0px 0px 20px 0px #00000026;" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Select Campus -->
                    <div>
                        <label for="campus" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Select Campus</label>
                        <div class="relative">
                            <select id="campus" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>Select Campus</option>
                                <option value="main">Main Campus</option>
                                <option value="north">North Campus</option>
                                <option value="south">South Campus</option>
                                <option value="east">East Campus</option>
                                <option value="islamabad">Main (Islamabad)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-blue-900">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Select Department -->
                    <div>
                        <label for="department" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Select Department</label>
                        <div class="relative">
                            <select id="department" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>Select Department</option>
                                <option value="engineering">Engineering</option>
                                <option value="business">Business</option>
                                <option value="arts">Arts & Humanities</option>
                                <option value="science">Science</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-blue-900">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Degree and Discipline -->
            <div style="box-shadow: 0px 0px 20px 0px #00000026;" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Select Degree -->
                    <div>
                        <label for="degree" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Select Degree</label>
                        <div class="relative">
                            <select id="degree" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>Select degree Level</option>
                                <option value="bachelor">Bachelor's</option>
                                <option value="master">Master's</option>
                                <option value="phd">PhD</option>
                                <option value="diploma">Diploma</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-blue-900">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Select Discipline -->
                    <div>
                        <label for="discipline" class="block text-[#1E1E1ECC] text-[24px] font-[600] mb-2">Select Discipline</label>
                        <div class="relative">
                            <select id="discipline" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>Select your Discipline</option>
                                <option value="cs">Computer Science</option>
                                <option value="ee">Electrical Engineering</option>
                                <option value="me">Mechanical Engineering</option>
                                <option value="business">Business Administration</option>
                                <option value="economics">Economics</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-blue-900">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Additional Information -->
        <div id="step2" class="step-content hidden">
            <!-- Main Content -->
            <div style="box-shadow: 0px 0px 20px 0px #00000026;" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <!-- Resume Upload Section -->
                <div class="mb-8">
                    <label class="block text-[#1E1E1ECC] text-[24px] font-medium mb-4">Add Resume</label>
                    <div class="border-2 border-dashed border-blue-200 rounded-lg p-6 flex flex-col items-center justify-center">
                        <div class="text-blue-600 mb-4">
                            <i class="far fa-file-alt text-xl"></i>
                        </div>
                        <p class="text-gray-600 text-center mb-2">
                            Drag and Drop or <span class="text-blue-500 cursor-pointer">Click to Upload</span>
                        </p>
                        <p class="text-sm text-gray-500 text-center">
                            Supported formats: PDF, DOC, DOCX. Max Size: 25MB
                        </p>
                        <input type="file" id="resume-upload" accept=".pdf,.doc,.docx" />
                    </div>
                </div>

                <!-- Website Section -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-[#1E1E1ECC] text-[24px] font-medium">Add your website <span class="text-gray-400 text-sm">(Optional)</span></label>
                        <span class="text-gray-400 text-sm">Link your Accounts <span class="text-gray-400 text-sm">(Optional)</span></span>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-start gap-4">
                        <!-- Website input -->
                        <div class="w-full md:w-1/2 flex items-center">
                            <input type="text" placeholder="https://domain-name.com" class="flex-1 px-4 py-3 border border-[#D9D9D9] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button class="ml-2 bg-gray-200 hover:bg-gray-300 text-[#1E1E1ECC] text-[24px] px-4 py-3 rounded-lg">
                                Link Website
                            </button>
                        </div>
                        
                        <!-- Social media buttons -->
                        <div class="flex gap-2 mt-4 md:mt-0">
                            <a href="#" class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg w-10 h-10 flex items-center justify-center social-btn" data-field="linkedin">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <input type="hidden" id="linkedin" name="linkedin" value="">
                            
                            <a href="#" class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg w-10 h-10 flex items-center justify-center social-btn" data-field="behance">
                                <i class="fab fa-behance"></i>
                            </a>
                            <input type="hidden" id="behance" name="behance" value="">
                            
                            <a href="#" class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg w-10 h-10 flex items-center justify-center social-btn" data-field="dribbble">
                                <i class="fab fa-dribbble"></i>
                            </a>
                            <input type="hidden" id="dribbble" name="dribbble" value="">
                            
                            <a href="#" class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg w-10 h-10 flex items-center justify-center social-btn" data-field="flickr">
                                <i class="fab fa-flickr"></i>
                            </a>
                            <input type="hidden" id="flickr" name="flickr" value="">
                        </div>
                    </div>
                </div>

                <!-- Interests Section -->
                <div>
                    <label class="block text-[#1E1E1ECC] text-[24px] font-medium mb-4">Add your Interests</label>
                    
                    <!-- Search bar -->
                    <div class="relative mb-6">
                        <input type="text" placeholder="Search the tags" class="w-full px-4 py-3 border border-[#D9D9D9] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <!-- Recommended Section -->
                    <p class="text-gray-500 mb-4">Recommended</p>
                    
                    <!-- Interest tags - first row -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="artificial-intelligence">
                            Artificial Intelligence
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="blockchain">
                            Blockchain
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="design-and-arts">
                            Design and Arts
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="react-js">
                            React JS
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="react-native">
                            React Native
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                    </div>

                    <!-- Interest tags - second row -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="app-development">
                            App development
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="virtual-reality-developer">
                            Virtual Reality Developer
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="user-experience-design">
                            User Experience Design
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="data-science">
                            Data Science
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                    </div>

                    <!-- Interest tags - third row -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="machine-learning">
                            Machine Learning
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="cloud-computing">
                            Cloud Computing
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="cyber-security">
                            Cyber Security
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                        <button type="button" class="interest-tag bg-white border border-[#D9D9D9] hover:bg-gray-100 text-[#1E1E1ECC] text-[24px] px-3 py-2 rounded-full text-sm flex items-center" data-interest="devops">
                            DevOps
                            <span class="ml-2 text-blue-500"><i class="fas fa-plus"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center">
            <!-- Back Button (only visible on step 2) -->
            <button id="backBtn" class="text-[#1E1E1ECC] text-[24px] hover:text-gray-900 hidden">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div id="emptyBackSpace" class="w-4"></div>
            
            <!-- Next/Continue Button and Page Counter -->
            <div class="flex flex-col items-center">
                <button id="nextBtn" class="bg-[#023564] w-[300px] hover:bg-blue-900 text-white font-medium py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mb-4">
                    Next
                </button>
                <button id="continueBtn" class="bg-[#023564] hover:bg-blue-900 text-white font-medium py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-[300px] mb-4 hidden">
                    Continue to App
                </button>
                <div id="pageCounter" class="text-[#023564CC] text-sm">
                    1/2
                </div>
            </div>
            
            <!-- Empty div to maintain layout -->
            <div class="w-4"></div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const DEBUG_MODE = true;
    let currentStep = 1;
    const totalSteps = 2;
    let storedProfileImage = null;

    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const nextBtn = document.getElementById('nextBtn');
    const continueBtn = document.getElementById('continueBtn');
    const backBtn = document.getElementById('backBtn');
    const pageCounter = document.getElementById('pageCounter');

    init();

    function init() {
        setupEventListeners();
        initializeInterestTags();
        initializeFileUploads();
        updateUI();
        logDebug('Application initialized');
    }

    function setupEventListeners() {
        nextBtn.addEventListener('click', handleNextStep);
        continueBtn.addEventListener('click', handleFormSubmission);
        backBtn.addEventListener('click', handleBackStep);
    }

    async function handleNextStep(e) {
        e.preventDefault();
        logDebug('Step 1 submission started');
        toggleLoading(nextBtn, true);

        try {
            const requiredFields = ['name', 'headline', 'city', 'homeland'];
            const missingFields = validateFields(requiredFields);

            if (missingFields.length) {
                throw new Error(`Please fill in: ${missingFields.join(', ')}`);
            }

            const formData = prepareStep1Data();
            formData.append('action', 'next');

            const data = await submitForm('backend/user_details.php', formData);

            if (data.status === 'success') {
                currentStep = 2;
                updateUI();
                logDebug('Moved to step 2');
            } else {
                throw new Error(data.message || 'Failed to proceed to next step');
            }
        } catch (error) {
            logError('Step 1 error:', error.message);
            showError(error.message);
        } finally {
            toggleLoading(nextBtn, false);
        }
    }

    async function handleFormSubmission(e) {
        e.preventDefault();
        logDebug('Final form submission started');
        toggleLoading(continueBtn, true);

        try {
            const requiredFields = ['name', 'headline'];
            const missingFields = validateFields(requiredFields);
            if (missingFields.length) {
                throw new Error(`Please fill in: ${missingFields.join(', ')}`);
            }

            const formData = prepareStep1Data();

            if (storedProfileImage) {
                formData.append('profile_image', storedProfileImage);
                logDebug('Re-appended stored profile image');
            }

            prepareStep2Data(formData);
            formData.append('action', 'save');

            const resumeFileInput = document.getElementById('resume-upload');
            if (!resumeFileInput.files[0]) {
                throw new Error('Please upload your resume.');
            }

            const data = await submitForm('backend/user_details.php', formData);

            if (data.status === 'success') {
                logDebug('Form submission successful');
                window.location.href = data.redirect || 'dashboard';
            } else {
                throw new Error(data.message || 'Submission failed');
            }
        } catch (error) {
            logError('Submission error:', error.message);
            showError(error.message);
        } finally {
            toggleLoading(continueBtn, false);
        }
    }

    function handleBackStep(e) {
        e.preventDefault();
        if (currentStep > 1) {
            currentStep--;
            updateUI();
            logDebug('Moved back to step 1');
        }
    }

    function prepareStep1Data() {
        const formData = new FormData();
        const fields = ['name', 'headline', 'city', 'homeland', 'campus', 'department', 'degree', 'discipline'];

        fields.forEach(field => {
            const el = document.getElementById(field);
            if (el) {
                formData.append(field, el.value.trim());
                logDebug(`Added field ${field}: ${el.value.trim()}`);
            }
        });

        const profileUpload = document.getElementById('profile-upload');
        if (profileUpload?.files[0]) {
            storedProfileImage = profileUpload.files[0];
            formData.append('profile_image', storedProfileImage);
            logDebug('Added and stored profile image');
        }

        return formData;
    }

    function prepareStep2Data(formData) {
        const resumeUpload = document.getElementById('resume-upload');
        if (resumeUpload?.files[0]) {
            formData.append('resume', resumeUpload.files[0]);
            logDebug('Added resume file');
        }

        const websiteInput = document.querySelector('input[placeholder="https://domain-name.com"]');
        if (websiteInput?.value.trim()) {
            formData.append('website', websiteInput.value.trim());
            logDebug(`Added website: ${websiteInput.value.trim()}`);
        }

        ['linkedin', 'behance', 'dribbble', 'flickr'].forEach(field => {
            const el = document.getElementById(field);
            formData.append(field, el?.value.trim() || '');
            logDebug(`Added ${field}: ${el?.value.trim()}`);
        });

        const selectedInterests = Array.from(document.querySelectorAll('.interest-tag.selected'))
            .map(tag => tag.dataset.interest)
            .filter(Boolean);
        formData.append('interests', JSON.stringify(selectedInterests));
        logDebug('Added interests:', selectedInterests);
    }

    function validateFields(fields) {
        return fields.filter(field => {
            const el = document.getElementById(field);
            return !el || !el.value.trim();
        });
    }

    async function submitForm(url, formData) {
        logDebug('Submitting form to:', url);
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        const text = await response.text();

        try {
            const data = JSON.parse(text);
            logDebug('Response data:', data);
            return data;
        } catch (error) {
            logError('Invalid JSON response:', text);
            throw new Error('Invalid server response');
        }
    }

    function updateUI() {
        pageCounter.textContent = `${currentStep}/${totalSteps}`;
        if (currentStep === 1) {
            step1.classList.remove('hidden');
            step2.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            continueBtn.classList.add('hidden');
            backBtn.classList.add('hidden');
        } else {
            step1.classList.add('hidden');
            step2.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            continueBtn.classList.remove('hidden');
            backBtn.classList.remove('hidden');
        }
    }

    function toggleLoading(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        } else {
            button.disabled = false;
            button.textContent = button === continueBtn ? 'Continue to App' : 'Next';
        }
    }

    function showError(message) {
        logError('User error:', message);
        alert(`Error: ${message}`);
    }

    function initializeInterestTags() {
        document.querySelectorAll('.interest-tag').forEach(tag => {
            tag.addEventListener('click', function () {
                this.classList.toggle('selected');
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-plus');
                    icon.classList.toggle('fa-check');
                }
                this.classList.toggle('bg-blue-100');
                this.classList.toggle('border-blue-500');
                logDebug(`Interest toggled: ${this.dataset.interest}`);
            });
        });
    }

    function initializeFileUploads() {
        const profileUpload = document.getElementById('profile-upload');
        if (profileUpload) {
            profileUpload.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    showError('Please upload a JPG, PNG, or GIF file.');
                    profileUpload.value = '';
                    return;
                }
                storedProfileImage = file;
                const reader = new FileReader();
                reader.onload = function (event) {
                    const uploadArea = profileUpload.closest('div');
                    if (uploadArea) {
                        uploadArea.innerHTML = `
                            <img src="${event.target.result}" class="w-24 h-24 rounded-full object-cover mb-2">
                            <label for="profile-upload" class="cursor-pointer mt-2">
                                <input type="file" id="profile-upload" class="hidden" accept=".jpg,.jpeg,.png,.gif">
                            </label>
                        `;
                        initializeFileUploads();
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        const resumeUpload = document.getElementById('resume-upload');
        if (resumeUpload) {
            resumeUpload.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowedTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                if (!allowedTypes.includes(file.type)) {
                    showError('Please upload a PDF, DOC, or DOCX file.');
                    resumeUpload.value = '';
                    return;
                }
                const fileNameDisplay = document.getElementById('resume-filename');
                if (fileNameDisplay) {
                    fileNameDisplay.textContent = file.name;
                }
            });
        }
    }

    function logDebug(...args) {
        if (DEBUG_MODE) console.log('[DEBUG]', ...args);
    }

    function logError(...args) {
        console.error('[ERROR]', ...args);
    }
});
</script>
</body>
</html>