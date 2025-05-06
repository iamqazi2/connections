<?php
session_start();

$emailError = $passwordError = '';
$email = '';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Trim password to avoid accidental spaces

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
        // Database connection (replace with actual DB connection code)
        include 'connection.php';

        // Fetch user from DB
        try {
            $stmt = $pdo->prepare("SELECT id, password, is_first_login FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                // If "Remember Me" is checked, set cookies
                if (isset($_POST['remember_me'])) {
                    setcookie('email', $email, time() + (86400 * 30), "/"); // 30 days
                    setcookie('password', $password, time() + (86400 * 30), "/");
                } else {
                    setcookie('email', '', time() - 3600, "/");
                    setcookie('password', '', time() - 3600, "/");
                }

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;

                // Check if it's the user's first login
                if ($user['is_first_login']) {
                    // Update is_first_login to 0
                    $updateStmt = $pdo->prepare("UPDATE users SET is_first_login = 0 WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                    // Redirect to screen.php for first login
                    header("Location: user_profile.php");
                } else {
                    // Redirect to dashboard.php for subsequent logins
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                // Password incorrect
                $passwordError = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            // Log any errors
            error_log("Error fetching user: " . $e->getMessage());
            $passwordError = "An error occurred while processing your request.";
        }

        // Close connection
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
    <title>Sign In</title>

    <style>.bg-color {
  background-color: #023564;
}</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center relative">

    <!-- Logo at the top-left corner -->
    <div class="absolute top-6 left-6">
        <img src="images/group.svg" alt="Logo" class="">
    </div>

    <!-- Form Container -->
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h1 class="text-4xl text-[#023564] font-bold mb-6 text-left">Welcome Back</h1>
        <p class="mb-4 text-[#015CBF] text-left">Login into your account</p>
        <form action="" method="POST" class="space-y-4">
            <!-- Email Input -->
            <div class="relative">
                <input type="email" name="email" placeholder="Email" value="<?= isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : htmlspecialchars($email ?? '') ?>" class="block w-full px-4 py-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <?php if ($emailError): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $emailError ?></p>
                <?php endif; ?>
            </div>
            <!-- Password Input -->
            <div class="relative">
                <input type="password" name="password" id="password" placeholder="Password" value="<?= isset($_COOKIE['password']) ? htmlspecialchars($_COOKIE['password']) : ''; ?>" class="block w-full px-4 py-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <!-- Eye Icon -->
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.375C4.927 6.147 7.185 4 12 4s7.073 2.147 8.02 4.375c.42.95.42 2.3 0 3.25C19.073 13.853 16.815 16 12 16s-7.073-2.147-8.02-4.375c-.42-.95-.42-2.3 0-3.25z M3 3l18 18" />
                </svg>
                <?php if ($passwordError): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $passwordError ?></p>
                <?php endif; ?>
            </div>

            <!-- Remember Me Toggle -->
            <div class="flex items-center justify-between mt-4">
                <label for="toggle" class="flex items-center cursor-pointer">
                    <input type="checkbox" id="toggle" name="remember_me" class="sr-only peer" <?= isset($_COOKIE['email']) ? 'checked' : ''; ?>>
                    <div class="block relative bg-gray-300 w-12 h-6 p-0.5 rounded-full peer-checked:bg-customBlue before:absolute before:bg-white before:w-5 before:h-5 before:rounded-full before:transition-all before:duration-500 before:left-0.5 peer-checked:before:left-6"></div>
                    <span class="text-sm text-gray-600 ml-3">Remember Me</span>
                </label>
                <p class="text-sm text-[#015CBF] cursor-pointer hover:underline">Forget Password?</p>
            </div>
            <!-- Sign In Button -->
            <button type="submit" class="bg-color w-full h-[60px] text-white px-6 py-2 rounded-lg hover:bg-[#023564] mt-6">
                Sign In
            </button>
        </form>

        <p class="my-6 text-[#015CBF] text-center">Don't have an account?<span class="mb-4 pl-2 underline text-[#015CBF] text-right font-bold">Sign Up!</span></p>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyePath = document.getElementById('eyePath');

        // Event listener to toggle password visibility and icon
        eyeIcon.addEventListener('click', () => {
            const isPasswordVisible = passwordInput.type === 'text';
            passwordInput.type = isPasswordVisible ? 'password' : 'text';

            if (isPasswordVisible) {
                // Show closed eye icon
                eyePath.setAttribute('d', 'M3.98 8.375C4.927 6.147 7.185 4 12 4s7.073 2.147 8.02 4.375c.42.95.42 2.3 0 3.25C19.073 13.853 16.815 16 12 16s-7.073-2.147-8.02-4.375c-.42-.95-.42-2.3 0-3.25z M3 3l18 18'); // Closed eye with cross
            } else {
                // Show open eye icon
                eyePath.setAttribute('d', 'M15 12c0 1.657-1.343 3-3 3s-3-1.343-3-3 1.343-3 3-3 3 1.343 3 3zM2.458 12C3.732 7.813 7.983 5 12 5s8.268 2.813 9.542 7c-1.274 4.187-5.525 7-9.542 7s-8.268-2.813-9.542-7z'); // Open eye
            }
        });
    </script>
</body>
</html>