<?php
include("./config/db.php");
session_start();

try {
    // Get list of users who have chatted with better error handling
    $users_query = "
        SELECT DISTINCT user_email, MAX(created_at) as last_message 
        FROM chat_messages 
        GROUP BY user_email 
        ORDER BY last_message DESC
    ";
    
    $users_result = $conn->query($users_query);
    
    if ($users_result === false) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $has_users = $users_result && $users_result->num_rows > 0;

    // Get unread message counts
    $unread_query = "
        SELECT user_email, COUNT(*) as unread_count 
        FROM chat_messages 
        WHERE sender = 'user' AND is_read = FALSE 
        GROUP BY user_email
    ";
    
    $unread_result = $conn->query($unread_query);
    
    if ($unread_result) {
        while ($row = $unread_result->fetch_assoc()) {
            $unread_counts[$row['user_email']] = $row['unread_count'];
        }
    }

} catch (Exception $e) {
    error_log("Chat dashboard error: " . $e->getMessage());
    $error_message = "Unable to load chat data. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat Dashboard - BACKSTAGE PASS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/js/script.js">
    <style>
        
       
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
        }

        .main-content h2 {
            color: #e0123f;
            font-size: 2.5rem;
            margin-bottom: 30px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .chat-dashboard {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #e0123f 0%, #c81038 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .dashboard-header h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .users-list {
            padding: 25px;
            max-height: 600px;
            overflow-y: auto;
        }

        .user-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .user-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #e0123f;
        }

        .user-item.unread {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
            border-color: #e0123f;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .user-details {
            flex: 1;
        }

        .user-email {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .last-message {
            color: #666;
            font-size: 0.9rem;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .unread-badge {
            background: #e0123f;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .chat-btn {
            background: linear-gradient(135deg, #e0123f 0%, #c81038 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chat-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(224, 18, 63, 0.3);
        }

        .no-users {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-users h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #e0123f;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .user-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .user-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <?php include("./includes/topbar.php"); ?>

    <div class="admin-container">
        <?php include("./includes/sidebar.php"); ?>
        
        <main class="main-content">
            <h2><i class="fas fa-comments"></i>Chat Dashboard</h2>
            
            <div class="chat-dashboard">
                <div class="dashboard-header">
                    <h3><i class="fas fa-comments"></i> User Conversations</h3>
                    <p>Manage conversations with users</p>
                </div>

                 <div class="users-list">
                    <?php if (isset($error_message)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $error_message; ?>
                        </div>
                    
                    <?php elseif ($has_users): ?>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <?php
                            $unread_count = $unread_counts[$user['user_email']] ?? 0;
                            $last_message = date('M j, g:i A', strtotime($user['last_message']));
                            ?>
                            <div class="user-item <?php echo $unread_count > 0 ? 'unread' : ''; ?>">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['user_email'], 0, 1)); ?>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-email"><?php echo htmlspecialchars($user['user_email']); ?></div>
                                        <div class="last-message">Last activity: <?php echo $last_message; ?></div>
                                    </div>
                                </div>
                                <div class="user-actions">
                                    <?php if ($unread_count > 0): ?>
                                        <div class="unread-badge" title="<?php echo $unread_count; ?> unread messages">
                                            <?php echo $unread_count; ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="admin_chat.php?user=<?php echo urlencode($user['user_email']); ?>" class="chat-btn">
                                        <i class="fas fa-comment"></i> Chat
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-users">
                            <h3>No conversations yet</h3>
                            <p>Users will appear here when they start chatting with you.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
