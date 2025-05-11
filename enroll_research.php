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

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "connections";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Drop existing research_answers table if it exists
        $conn->exec("DROP TABLE IF EXISTS research_answers");
        
        // Create answers table with correct structure
        $sql = "CREATE TABLE research_answers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            research_id INT NOT NULL,
            answers JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (research_id) REFERENCES research_posts(id)
        )";
        $conn->exec($sql);
    } catch(PDOException $e) {
        $_SESSION['error'] = "Connection failed: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }

    // Get research ID from URL
    $research_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Initialize research data and questions array
    $research_data = [
        'name' => 'Untitled Research',
        'focus_tags' => [],
        'date' => date('d M Y')
    ];
    $questions = [];

    // Fetch research data with questions
    if ($research_id > 0) {
        try {
            // Fetch research with all columns including question1, question2, etc.
            $stmt = $conn->prepare("SELECT * FROM research_posts WHERE id = :id");
            $stmt->execute(['id' => $research_id]);
            $research = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($research) {
                // Set basic research data
                $research_data = [
                    'name' => htmlspecialchars($research['title'] ?? 'Untitled Research'),
                    'focus_tags' => array_filter(array_map('trim', explode(',', $research['focus_tags'] ?? ''))),
                    'date' => date('d M Y', strtotime($research['created_at'] ?? 'now'))
                ];

                // Extract questions directly from the columns
                if (!empty($research['question1'])) {
                    $questions[] = [
                        'text' => htmlspecialchars($research['question1']),
                        'word_limit' => htmlspecialchars($research['word_limit1'] ?? '0-500 words')
                    ];
                }
                
                if (!empty($research['question2'])) {
                    $questions[] = [
                        'text' => htmlspecialchars($research['question2']),
                        'word_limit' => htmlspecialchars($research['word_limit2'] ?? '0-500 words')
                    ];
                }
                
                // Check for additional questions if they exist in the database
                foreach ($research as $key => $value) {
                    if (preg_match('/^question(\d+)$/', $key, $matches)) {
                        $num = $matches[1];
                        if ($num > 2 && !empty($value)) {
                            $word_limit_key = "word_limit$num";
                            $questions[] = [
                                'text' => htmlspecialchars($value),
                                'word_limit' => htmlspecialchars($research[$word_limit_key] ?? '0-500 words')
                            ];
                        }
                    }
                }
            } else {
                $_SESSION['error'] = "Research not found for ID: $research_id.";
                header("Location: index.php");
                exit;
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid research ID.";
        header("Location: index.php");
        exit;
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = "You must be logged in to submit answers.";
                header("Location: signin.php");
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $answers = [];
            $all_filled = true;

            // Collect and validate answers
            foreach ($questions as $index => $question) {
                $answer = $_POST['answer' . ($index + 1)] ?? '';
                if (empty($answer)) {
                    $all_filled = false;
                    $_SESSION['error'] = "Please fill all required fields.";
                    header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $research_id);
                    exit;
                }
                $answers[$index + 1] = htmlspecialchars($answer);
            }

            if ($all_filled) {
                // Save answers as JSON in a single row
                $stmt = $conn->prepare("INSERT INTO research_answers (estimuser_id, research_id, answers) VALUES (:user_id, :research_id, :answers)");
                $stmt->execute([
                    'user_id' => $user_id,
                    'research_id' => $research_id,
                    'answers' => json_encode($answers)
                ]);

                // Store in session for confirmation
                $_SESSION['research_answers'] = $answers;
                $_SESSION['research_id'] = $research_id;
                header("Location: enroll_research.php");
                exit;
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error saving answers: " . $e->getMessage();
            header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $research_id);
            exit;
        }
    }

    // Include navbar
    include 'navbar.php';

    // Logic to transform media_path to web-accessible URL
    $image_url = 'Uploads/placeholder.jpg'; // Default placeholder
    if (!empty($research['media_path'])) {
        // Check if media_path contains 'research_media/'
        $media_path = $research['media_path'];
        $research_media_pos = strpos($media_path, 'research_media/');
        if ($research_media_pos !== false) {
            // Extract the path starting from 'research_media/'
            $relative_path = substr($media_path, $research_media_pos);
            // Construct the web-accessible URL (assuming research_media/ is under web root)
            $image_url = $relative_path;
        }
    }
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

    <!-- Debug information (remove in production) -->
    <div class="flex min-h-screen">
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="max-w-2xl mx-auto my-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded flex items-center justify-center mr-4">
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Research Image" class="w-full h-full object-cover rounded">
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-blue-900"><?php echo $research_data['name']; ?></h2>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <?php if (!empty($research_data['focus_tags'])): ?>
                                <?php foreach ($research_data['focus_tags'] as $focus): ?>
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        <?php echo htmlspecialchars($focus); ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    No focus specified
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"><?php echo $research_data['date']; ?></p>
                    </div>
                </div>
                <p class="text-blue-900 font-medium mb-4">Answer the following questions to Join *</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $research_id; ?>" method="post" class="space-y-6">
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $index => $question): ?>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-blue-900 font-medium">Q<?php echo $index + 1; ?>. <?php echo $question['text']; ?></label>
                                    <span class="text-red-500 text-sm"><?php echo $question['word_limit']; ?></span>
                                </div>
                                <label class="block text-gray-600 mb-2">Write Answer</label>
                                <textarea name="answer<?php echo $index + 1; ?>" rows="4" placeholder="Enter your answer" class="w-full p-3 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-red-500">No questions available for this research.</p>
                    <?php endif; ?>
                    <div class="flex justify-between mt-8">
                        <!-- Cancel as a link to avoid form submission -->
                        <a href="dashboard.php" class="px-6 py-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800" <?php echo empty($questions) ? 'disabled' : ''; ?>>
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