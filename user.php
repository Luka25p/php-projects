<?php
include("database.php");
session_start();
require_once __DIR__ . '/includes/dbh.inc.php';
require_once __DIR__ . '/includes/notifications.inc.php';

// Handle sign out first, before any output
if(isset($_POST["signOut"])){
    session_destroy();
    header("location: lesson1.php");
    exit();
}

// Check if user is logged in
if(!isset($_SESSION['user_username']) || !isset($_SESSION['user_password'])){
    header("location: lesson1.php");
    exit();
}

// Get profile ID from URL
$profile_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// Create notification for profile visit
if (isset($_GET['id']) && $_GET['id'] != $_SESSION['user_id']) {
    createProfileVisitNotification($conn, $_SESSION['user_id'], $profile_id);
}

include("includes/post.inc.php");
include("includes/follow_actions.inc.php");

if (isset($_SESSION['user_id'])) {
    $unread_count = getUnreadCount($conn, $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home • <?php echo $_SESSION["user_username"];?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/styles/main.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fab fa-connectdevelop"></i>
                <span>DevConnect</span>
            </div>
            <div class="nav-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <div class="nav-actions">
                <a href="feed.php">
                    <i class="fas fa-globe"></i>
                    Feed
                </a>
                <a href="notifications.php">
                    <i class="far fa-bell"></i>
    <?php
                    if ($unread_count > 0) {
                        echo "<span class='badge'>$unread_count</span>";
                    }
                    ?>
                </a>
                <a href="#"><i class="far fa-envelope"></i></a>
                <i class="fas fa-comment-dots" onclick="openChat()"></i>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <button type="submit" name="signOut">Sign Out</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="layout">
        <aside class="left-sidebar">
            <div class="profile-quick-view">
                <div class="profile-banner"></div>
                <img src="https://api.dicebear.com/7.x/micah/svg?seed=<?php echo $_SESSION["user_username"];?>" 
                     alt="Profile" class="profile-pic">
                <div class="profile-info">
                    <h2><?php echo $_SESSION["user_username"];?></h2>
                    <p class="profile-email"><?php echo $_SESSION['user_email'];?></p>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-number">
                            <?php
                            $sql = "SELECT COUNT(*) as post_count FROM posts WHERE user_id = '$profile_id'";
                            $result = mysqli_query($conn, $sql);
                            $count = mysqli_fetch_assoc($result)['post_count'];
                            echo $count;
                            ?>
                        </span>
                        <span class="stat-label">Posts</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">
                            <?php
                            $sql = "SELECT COUNT(*) as following_count FROM followers WHERE follower_id = '$profile_id'";
                            $result = mysqli_query($conn, $sql);
                            $count = mysqli_fetch_assoc($result)['following_count'];
                            echo $count;
                            ?>
                        </span>
                        <span class="stat-label">Following</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">
                            <?php
                            $sql = "SELECT COUNT(*) as follower_count FROM followers WHERE user_id = '$profile_id'";
                            $result = mysqli_query($conn, $sql);
                            $count = mysqli_fetch_assoc($result)['follower_count'];
                            echo $count;
                            ?>
                        </span>
                        <span class="stat-label">Followers</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="create-post-card">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" class="post-form">
                    <div class="post-form-header">
                        <img src="https://api.dicebear.com/7.x/micah/svg?seed=<?php echo $_SESSION["user_username"];?>" 
                             alt="Profile" class="post-avatar">
                        <input type="text" name="textTittle" placeholder="Post title" class="post-title-input">
                    </div>
                    <textarea name="postText" placeholder="What's on your mind?"
                              class="post-content-input"></textarea>
                    <div class="post-form-actions">
                        <div class="post-attachments">
                            <button type="button" class="attachment-btn">
                                <i class="far fa-image"></i>
                            </button>
                            <button type="button" class="attachment-btn">
                                <i class="far fa-file-alt"></i>
                            </button>
                            <button type="button" class="attachment-btn">
                                <i class="far fa-smile"></i>
                            </button>
                        </div>
                        <button type="submit" name="post" class="post-submit-btn">Post</button>
                    </div>
    </form>
            </div>

            <div class="posts-feed">
        <?php include("includes/posts.inc.php")?>
    </div>
        </main>

        <aside class="right-sidebar">
            <div class="suggestions-section">
                <div class="section-header">
                    <h3><i class="fas fa-user-plus"></i> Suggested Users</h3>
                </div>
                <div class="suggested-users">
<?php
                    $current_user = $_SESSION["user_id"];
                    // Get users that current user is not following
                    $sql = "SELECT u.*, 
                            CASE WHEN f.follower_id IS NOT NULL THEN 1 ELSE 0 END as is_following
                            FROM users u
                            LEFT JOIN followers f ON u.id = f.user_id AND f.follower_id = '$current_user'
                            WHERE u.id != '$current_user'
                            LIMIT 5";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) > 0) {
                        while($user = mysqli_fetch_assoc($result)) {
                            ?>
                            <div class="user-card">
                                <div class="user-info">
                                    <img src="https://api.dicebear.com/7.x/micah/svg?seed=<?php echo $user['username']; ?>" 
                                         alt="Profile" class="user-avatar">
                                    <div class="user-details">
                                        <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                        <p class="user-email"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                                <form action="" method="post">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <a href="chat.php?id=<?php echo $user['id']; ?>" class="chat-btn">Chat</a>
                                    <?php if(!$user['is_following']): ?>
                                        <button type="submit" name="follow" class="follow-btn">
                                            <i class="fas fa-plus"></i>
                                            Follow
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="unfollow" class="unfollow-btn">
                                            <i class="fas fa-user-check"></i>
                                            Following
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='no-suggestions'>No suggestions available</p>";
                    }
                    ?>
                </div>

                <div class="quick-stats">
                    <h3><i class="fas fa-chart-line"></i> Your Activity</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-value">
                                <?php
                                $sql = "SELECT COUNT(*) as post_count FROM posts WHERE user_id = '$current_user'";
                                $result = mysqli_query($conn, $sql);
                                $count = mysqli_fetch_assoc($result)['post_count'];
                                echo $count;
                                ?>
                            </span>
                            <span class="stat-label">Posts</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-value">
                                <?php
                                $sql = "SELECT COUNT(*) as follower_count FROM followers WHERE user_id = '$current_user'";
                                $result = mysqli_query($conn, $sql);
                                $count = mysqli_fetch_assoc($result)['follower_count'];
                                echo $count;
                                ?>
                            </span>
                            <span class="stat-label">Followers</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-value">
                                <?php
                                $sql = "SELECT COUNT(*) as following_count FROM followers WHERE follower_id = '$current_user'";
                                $result = mysqli_query($conn, $sql);
                                $count = mysqli_fetch_assoc($result)['following_count'];
                                echo $count;
                                ?>
                            </span>
                            <span class="stat-label">Following</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
    function formatNotification(notification) {
        let icon = '';
        switch(notification.type) {
            case 'profile_visit':
                icon = '<i class="fas fa-user-clock"></i>';
                break;
            case 'follow':
                icon = '<i class="fas fa-user-plus"></i>';
                break;
            case 'like':
                icon = '<i class="fas fa-heart"></i>';
                break;
            case 'comment':
                icon = '<i class="fas fa-comment"></i>';
                break;
            default:
                icon = '<i class="fas fa-bell"></i>';
        }

        return `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                 onclick="handleNotificationClick(${notification.id}, '${notification.link}')">
                <img src="https://api.dicebear.com/7.x/micah/svg?seed=${notification.username}" 
                     alt="Avatar" 
                     class="notification-avatar">
                <div class="notification-content">
                    <div class="notification-icon">${icon}</div>
                    <p>${notification.message}</p>
                    <div class="notification-time">
                        <i class="far fa-clock"></i>
                        <span>${notification.time_ago}</span>
                    </div>
                </div>
            </div>
        `;
    }

    function loadNotifications() {
        const list = document.getElementById('notificationsList');
        
        // Show loading state
        list.innerHTML = '<div class="notification-item"><div class="notification-content"><p>Loading...</p></div></div>';
        
        fetch('includes/notification_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_notifications'
        })
        .then(response => response.json())
        .then(data => {
            if (data.notifications.length === 0) {
                list.innerHTML = `
                    <div class="no-notifications">
                        <i class="far fa-bell-slash"></i>
                        <p>No notifications yet</p>
                    </div>`;
                return;
            }

            list.innerHTML = data.notifications.map(notification => 
                formatNotification(notification)
            ).join('');

            // Update badge
            updateNotificationBadge(data.unread_count);
        })
        .catch(error => {
            list.innerHTML = `
                <div class="no-notifications">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Error loading notifications</p>
                </div>`;
        });
    }

    function updateNotificationBadge(count) {
        const badge = document.querySelector('.badge');
        if (count > 0) {
            if (!badge) {
                const notificationLink = document.querySelector('a[href="notifications.php"]');
                const newBadge = document.createElement('span');
                newBadge.className = 'badge';
                newBadge.textContent = count;
                notificationLink.appendChild(newBadge);
            } else {
                badge.textContent = count;
            }
        } else if (badge) {
            badge.remove();
        }
    }

    function handleNotificationClick(notificationId, link) {
        if (link) {
            window.location.href = link;
        }
    }

    function openChat() {
        // Add your code to open the chat window here
    }
    </script>
</body>
</html>