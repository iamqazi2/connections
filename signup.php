<?php
$emailError = $passwordError = $successMessage = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Email validation
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@numls\.edu\.pk$/', $email)) {
        $emailError = "Only numls.edu.pk emails are allowed.";
    }

    // Password validation pattern
    $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
    if (!preg_match($passwordPattern, $password)) {
        $passwordError = "Password must be at least 8 characters and include a number, a special character, and a letter.";
    }

    if (!$emailError && !$passwordError) {
        include 'connection.php';

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $emailError = "Email already registered.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                $stmt->execute([$email, $hashedPassword]);
                
                // Set success message
                $successMessage = "Account created successfully!";
                
                // Delay redirect to show success message
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'signin';
                    }, 2000);
                </script>";
                $pdo = null;
                // Exit to prevent further processing
                // exit;
            } catch (PDOException $e) {
                error_log("Error during signup: " . $e->getMessage());
                $emailError = "An error occurred while creating your account.";
            }
        }

        $pdo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="output.css"/>
    <title>Signup</title>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center relative">
    <div class="absolute top-6 left-6">
        <img src="images/group.svg" alt="Logo">
    </div>

    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h1 class="text-4xl text-[#023564] font-bold mb-6 text-center">Signup</h1>
        <p class="mb-4 text-[#015CBF] text-center">"Sign up now to begin your journey with us!"</p>

        <?php if ($successMessage): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                <p><?php echo $successMessage; ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div class="relative">
                <input type="email" name="email" placeholder="Email “numls.edu.pk”" class="block w-full px-4 py-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($email ?? '') ?>" required>
                <?php if ($emailError): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $emailError ?></p>
                <?php endif; ?>
            </div>
            
            <div class="relative">
                <input type="password" name="password" id="password" placeholder="Password" class="block w-full px-4 py-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <?php if ($passwordError): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $passwordError ?></p>
                <?php endif; ?>
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.375C4.927 6.147 7.185 4 12 4s7.073 2.147 8.02 4.375c.42.95.42 2.3 0 3.25C19.073 13.853 16.815 16 12 16s-7.073-2.147-8.02-4.375c-.42-.95-.42-2.3 0-3.25z M3 3l18 18" />
                </svg>
            </div>

            <p class="mb-4 text-[#015CBF] text-right">Already have an account?
                <a href="signin.php" class="pl-2 underline text-[#015CBF] font-bold">Sign In</a>
            </p>

            <button type="submit" class="bg-customBlue w-full h-[60px] text-white px-6 py-2 rounded-lg hover:bg-[#023564]">
                Sign Up
            </button>
        </form>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyePath = document.getElementById('eyePath');

        eyeIcon.addEventListener('click', () => {
            const isPasswordVisible = passwordInput.type === 'text';
            passwordInput.type = isPasswordVisible ? 'password' : 'text';

            if (isPasswordVisible) {
                eyePath.setAttribute('d', 'M3.98 8.375C4.927 6.147 7.185 4 12 4s7.073 2.147 8.02 4.375c.42.95.42 2.3 0 3.25C19.073 13.853 16.815 16 12 16s-7.073-2.147-8.02-4.375c-.42-.95-.42-2.3 0-3.25z M3 3l18 18');
            } else {
                eyePath.setAttribute('d', 'M15 12c0 1.657-1.343 3-3 3s-3-1.343-3-3 1.343-3 3-3 3 1.343 3 3zM2.458 12C3.732 7.813 7.983 5 12 5s8.268 2.813 9.542 7c-1.274 4.187-5.525 7-9.542 7s-8.268-2.813-9.542-7z');
            }
        });
    </script>
</body>
</html>