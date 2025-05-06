<?php
// Include the database connection
require_once 'connection.php';

// Fetch three users from the user_details table
try {
    $stmt = $pdo->prepare("SELECT * FROM user_details LIMIT 6");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore People</title>
    <style>


.explore-container {
   
    border-radius: 15px;
}

.explore-heading {
    color: #24315e;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
}

.explore-divider {
    border-bottom: 1px solid #e0e0f0;
    margin-bottom: 25px;
}

/* Profile card styling */
.profile-card {
    display: flex;
    margin-bottom: 25px;
    padding: 10px 0;
}

.profile-left {
    margin-right: 15px;
}

.profile-image {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
    background-color: #f0f0f0;
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}

.profile-name {
    color: #24315e;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
}

.profile-headline {
    color: #8a94a6;
    font-size: 16px;
}

.profile-course {
    color: #24315e;
    font-size: 12px;
    font-weight: 400;
    margin-bottom: 10px;
}

.interests-container {
    display: flex;
    align-items: center;
    color: #8a94a6;
    font-size: 14px;
    margin-bottom: 5px;
}

.interests-label {
    margin-right: 10px;
}

.interests-dot {
    margin: 0 8px;
    color: #8a94a6;
}

.interests-tag {
    margin-right: 10px;
    color: #6a7998;
}

/* Button styling */
.action-button-container {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
}

.connect-button, .learn-button {
    display: inline-flex;
    align-items: center;
    padding: 8px 15px;
    border-radius: 25px;
    border: 1px solid #3b82f6;
    color: #3b82f6;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.3s ease;
}

.connect-button:hover, .learn-button:hover {
    background-color: #3b82f6;
    color: white;
}

.arrow-icon {
    margin-left: 5px;
    font-size: 14px;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .explore-container {
        padding: 15px;
    }
    
    .profile-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 15px 0;
    }
    
    .profile-left {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .action-button-container {
        position: static;
        transform: none;
        margin-top: 15px;
    }
}

.no-users {
    text-align: center;
    color: #8a94a6;
    padding: 20px 0;
}
    </style>

</head>
<body>
    <div class="explore-container">
        <h1 class="explore-heading">Explore People</h1>
        <div class="explore-divider"></div>

        <!-- User Profile Section -->
        <!-- User Profile Section -->
<?php if (!empty($users)): ?>
    <?php foreach ($users as $user): ?>
        <div class="profile-card">
            <div class="profile-left">
                <div class="profile-image">
                    <?php 
                        // Display profile picture if available, otherwise use placeholder
                        $profilePic = !empty($user['profile_image']) ? $user['profile_image'] : 'default-profile.png';
                    ?>
                    <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Image">
                </div>
            </div>
            <div class="profile-info">
                <!-- Display user name and headline for all users -->
                <h2 class="profile-name"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></h2>
                <p class="profile-headline"><?php echo htmlspecialchars($user['headline'] ?? 'User'); ?></p>

                <div class="action-button-container">
                        <!-- For the first user, show "Connect" button -->
                        <a href="#" class="connect-button">
                            Connect
                            <span class="arrow-icon">↗</span>
                        </a>
                   
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no-users">No users found</p>
<?php endif; ?>

    </div>
</body>
</html>