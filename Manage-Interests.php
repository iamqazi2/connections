<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Interests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'custom-blue': '#023564',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-gray-50 p-4"> 

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "connections";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get logged-in user ID
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Initialize variables
    $interests = [];
    $success_message = '';
    $error_message = '';
    
    if ($user_id) {
        // Fetch user's interests (stored as JSON in the interests column)
        $stmt = $conn->prepare("SELECT interests FROM user_details WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Decode the JSON string into an array
        $interests = $result && !empty($result['interests']) ? json_decode($result['interests'], true) : [];
        if (!is_array($interests)) {
            $interests = [];
        }
    }
    
    // All available interests (matching the display format)
    $all_interests = [
        'Artificial Intelligence', 'Blockchain', 'Design and Arts', 'React JS',
        'App development', 'Virtual Reality Developer', 'User Experience Design',
        'Tag 1 related', 'react-native'
    ];
    
} catch(PDOException $e) {
    $error_message = "Connection failed: " . $e->getMessage();
}
?>

<div class="flex min-h-screen">
    <div class="w-full">
        <!-- Success Message -->
        <?php if ($success_message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Selected Interests Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 w-full">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-700">Your Interests</h2>
                    <p class="text-sm text-gray-500"><?php echo count($interests); ?> interests selected</p>
                </div>
                <button id="editInterests" class="flex items-center text-custom-blue hover:opacity-80">
                    <i class="fas fa-edit mr-2"></i>
                    <span class="font-medium">Edit</span>
                </button>
            </div>
            
            <div id="interestsContainer" class="flex flex-col gap-2">
                <?php if (empty($interests)): ?>
                    <p class="text-gray-500">No interests selected.</p>
                <?php else: ?>
                    <?php foreach ($interests as $interest): ?>
                        <div class="bg-custom-blue text-white px-4 py-2 rounded-full flex items-center w-fit">
                            <span><?php echo htmlspecialchars($interest); ?></span>
                            <button class="ml-2 focus:outline-none remove-tag" data-tag="<?php echo htmlspecialchars($interest); ?>">×</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Add More Interests Section (hidden by default) -->
        <div id="editSection" class="hidden">
            <h2 class="text-lg font-medium text-gray-700 mb-4">Add More Interests</h2>
            
            <!-- Search Bar -->
            <div class="relative mb-6">
                <input type="text" id="searchInput" placeholder="Search the tags" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-custom-blue">
                <button class="absolute right-3 top-2.5 text-gray-500">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <!-- Recommended Tags -->
            <div class="mb-6">
                <h3 class="text-gray-600 font-bold text-base leading-[100%] mb-4">Recommended</h3>
                <div id="recommendedTags" class="flex flex-wrap gap-3">
                    <?php foreach ($all_interests as $tag): ?>
                        <button class="flex items-center border border-custom-blue text-gray-700 px-4 py-2 rounded-full hover:bg-gray-100 add-tag" data-tag="<?php echo htmlspecialchars($tag); ?>">
                            <span><?php echo htmlspecialchars($tag); ?></span>
                            <span class="ml-2 text-custom-blue">+</span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Save Button -->
            <form id="saveForm" method="POST" action="">
                <input type="hidden" name="save_interests" value="1">
                <div id="selectedInterests"></div>
                <button type="submit" id="saveChanges" class="bg-custom-blue text-white px-6 py-2 rounded-lg hover:bg-opacity-90">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.getElementById('editInterests');
    const editSection = document.getElementById('editSection');
    const interestsContainer = document.getElementById('interestsContainer');
    const searchInput = document.getElementById('searchInput');
    const recommendedTags = document.getElementById('recommendedTags');
    const selectedInterests = document.getElementById('selectedInterests');
    const saveForm = document.getElementById('saveForm');
    const successMessage = document.createElement('div');
    let selected = <?php echo json_encode($interests); ?>;
    
    // Toggle edit mode
    editButton.addEventListener('click', function() {
        editSection.classList.toggle('hidden');
        interestsContainer.classList.toggle('opacity-50');
        if (!editSection.classList.contains('hidden')) {
            editButton.innerHTML = '<i class="fas fa-times mr-2"></i><span class="font-medium">Cancel</span>';
        } else {
            editButton.innerHTML = '<i class="fas fa-edit mr-2"></i><span class="font-medium">Edit</span>';
        }
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tags = recommendedTags.querySelectorAll('.add-tag');
        tags.forEach(tag => {
            const tagName = tag.getAttribute('data-tag').toLowerCase();
            tag.style.display = tagName.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Add tag functionality
    recommendedTags.addEventListener('click', function(e) {
        if (e.target.closest('.add-tag')) {
            const tag = e.target.closest('.add-tag').getAttribute('data-tag');
            if (!selected.includes(tag)) {
                selected.push(tag);
                updateInterests();
                updateSelectedInputs();
            }
        }
    });
    
    // Remove tag functionality
    interestsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag')) {
            const tag = e.target.getAttribute('data-tag');
            selected = selected.filter(item => item !== tag);
            updateInterests();
            updateSelectedInputs();
        }
    });
    
    // Handle form submission via AJAX
    saveForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(saveForm);
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Update interests from server response
            selected = Array.from(formData.getAll('interests[]'));
            updateInterests();
            updateSelectedInputs();
            
            // Show success message
            successMessage.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6';
            successMessage.innerHTML = '<p>Interests successfully saved!</p>';
            saveForm.parentNode.insertBefore(successMessage, saveForm);
            
            // Hide edit section and reset button
            editSection.classList.add('hidden');
            interestsContainer.classList.remove('opacity-50');
            editButton.innerHTML = '<i class="fas fa-edit mr-2"></i><span class="font-medium">Edit</span>';
            
            // Remove success message after 3 seconds
            setTimeout(() => {
                successMessage.remove();
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            const errorMessage = document.createElement('div');
            errorMessage.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6';
            errorMessage.innerHTML = '<p>Failed to save interests. Please try again.</p>';
            saveForm.parentNode.insertBefore(errorMessage, saveForm);
        });
    });
    
    // Update displayed interests
    function updateInterests() {
        if (selected.length === 0) {
            interestsContainer.innerHTML = `<p class="text-gray-500">No interests selected.</p>`;
        } else {
            interestsContainer.innerHTML = selected.map(tag => `
                <div class="bg-custom-blue text-white px-4 py-2 rounded-full flex items-center w-fit">
                    <span>${tag}</span>
                    <button class="ml-2 focus:outline-none remove-tag" data-tag="${tag}">×</button>
                </div>
            `).join('');
        }
    }
    
    // Update hidden form inputs
    function updateSelectedInputs() {
        selectedInterests.innerHTML = selected.map(tag => `
            <input type="hidden" name="interests[]" value="${tag}">
        `).join('');
    }
    
    // Initialize selected inputs
    updateSelectedInputs();
});
</script>

</body>
</html>