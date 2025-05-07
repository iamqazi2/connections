<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed Navigation</title>
    <style>
       
        
        .feed-container {
            background-color: white;
            width: 360px;
            border-radius: 16px;
            padding: 12px 8px;
             box-shadow: 0 10px 15px -3px rgba(63, 139, 201, 0.5), 0 4px 6px -2px rgba(63, 139, 201, 0.25) !important;
      border: 1px solid rgba(63, 139, 201, 0.5); 
        }
        
        .feed-item {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            border-radius: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .feed-item:last-child {
            margin-bottom: 0;
        }
        
        .feed-item.active {
            background-color: #f6f8fc;
        }
        
        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            width: 24px;
            height: 24px;
        }
        
        .icon {
            color: #8B96A5;
            width: 18px;
            height: 18px;
        }
        
        .active .icon {
            color: rgba(63, 139, 201, 1);
        }
        
        .feed-item-text {
            font-size: 16px;
            color: #8B96A5;
            font-weight: 400;
        }
        
        .active .feed-item-text {
            color: rgba(63, 139, 201, 1);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php
    // PHP to handle which tab is active
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'personalized';
    ?>

    <div class="feed-container">
        <a href="?tab=personalized" class="feed-item <?php echo ($active_tab == 'personalized') ? 'active' : ''; ?>">
            <div class="icon-container">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
                </svg>
            </div>
            <span class="feed-item-text">Personalized Feed</span>
        </a>
        
        <a href="?tab=communities" class="feed-item <?php echo ($active_tab == 'communities') ? 'active' : ''; ?>">
            <div class="icon-container">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
                </svg>
            </div>
            <span class="feed-item-text">From Communities</span>
        </a>
    </div>
</body>
</html>