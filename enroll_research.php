<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Research Idea</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <?php
    // Start output buffering to prevent headers already sent errors
    ob_start();

    // Start session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Handle form submission and cancel button
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle cancel button
        if (isset($_POST['cancel'])) {
            unset($_SESSION['research_answers']);
            header("Location: index.php");
            exit;
        }

        // Handle form submission
        $answers = [];
        foreach ($questions as $index => $question) {
            $answers[$index] = htmlspecialchars($_POST['answer' . ($index + 1)] ?? '');
        }
        $_SESSION['research_answers'] = $answers;
        header("Location: confirm_join.php");
        exit;
    }

    // Sample research data (replace with actual data from session or database)
    $research_data = [
        'name' => 'Name of Research Idea',
        'focus' => ['Focus of Interest', 'Focus of Interest', 'Focus of Interest', 'Focus of Interest + 2'],
        'date' => '01 day ago'
    ];
    $questions = [
        [
            'text' => 'Question no 1 to ask from the user who wants to join?',
            'word_limit' => '0-100 words',
            'answer' => ''
        ],
        [
            'text' => 'Question no 2 to ask from the user who wants to join?',
            'word_limit' => '≤500 words',
            'answer' => ''
        ],
        [
            'text' => 'Question no 3 to ask from the user who wants to join?',
            'word_limit' => '100-500 words',
            'answer' => ''
        ]
    ];

    // Include navbar
    include 'navbar.php';
    ?>

    <!-- Display error message -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="max-w-4xl mx-auto mt-6 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="flex min-h-screen">
  
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="max-w-2xl mx-auto my-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded flex items-center justify-center mr-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900">
                            <path d="M12 2l2 2 2-2h4a2 2 0 0 1 2 2v4l-2 2 2 2v4a2 2 0 0 1-2 2h-4l-2 2-2-2H6a2 2 0 0 1-2-2v-4l2-2-2-2V4a2 2 0 0 1 2-2h4z"/>
                            <path d="M12 12v6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-blue-900"><?php echo $research_data['name']; ?></h2>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <?php foreach ($research_data['focus'] as $focus): ?>
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    <?php echo htmlspecialchars($focus); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"><?php echo $research_data['date']; ?></p>
                    </div>
                </div>
                <p class="text-blue-900 font-medium mb-4">Answer the following questions to Join *</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
                    <?php foreach ($questions as $index => $question): ?>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-blue-900 font-medium">Q<?php echo $index + 1; ?>. <?php echo $question['text']; ?></label>
                            <span class="text-red-500 text-sm"><?php echo $question['word_limit']; ?></span>
                        </div>
                        <label class="block text-gray-600 mb-2">Write Answer</label>
                        <textarea name="answer<?php echo $index + 1; ?>" rows="4" placeholder="Enter the answer" class="w-full p-3 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>
                    <?php endforeach; ?>
                    <div class="flex justify-between mt-8">
                        <button type="submit" name="cancel" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                            Enroll
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Flush output buffer
ob_end_flush();
?>