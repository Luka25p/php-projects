<?php
function ensureNotificationsTable($conn) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        from_user_id INT,
        message TEXT NOT NULL,
        type VARCHAR(50) NOT NULL,
        link VARCHAR(255),
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL
    )";

    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Failed to create notifications table: " . mysqli_error($conn));
    }
}

function createTestNotification($conn, $user_id) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "INSERT INTO notifications (user_id, message, type, is_read) VALUES (?, 'Welcome to DevConnect! This is a test notification.', 'system', FALSE)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare test notification statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create test notification: " . mysqli_stmt_error($stmt));
    }
    return true;
}

function getUnreadCount($conn, $user_id) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare unread count statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to get unread count: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return (int)$row['count'];
}

function getNotifications($conn, $user_id, $limit = 15) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "SELECT n.*, u.username 
            FROM notifications n 
            JOIN users u ON n.from_user_id = u.id 
            WHERE n.user_id = ? 
            ORDER BY n.created_at DESC 
            LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare get notifications statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $limit);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to get notifications: " . mysqli_stmt_error($stmt));
    }
    return mysqli_stmt_get_result($stmt);
}

function markNotificationRead($conn, $notification_id, $user_id) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare mark read statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ii", $notification_id, $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to mark notification as read: " . mysqli_stmt_error($stmt));
    }

    return mysqli_affected_rows($conn) > 0;
}

function markAllNotificationsRead($conn, $user_id) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "UPDATE notifications SET is_read = TRUE WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare mark all read statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to mark all notifications as read: " . mysqli_stmt_error($stmt));
    }

    return true;
}

function createNotification($conn, $user_id, $message, $type, $from_user_id = null, $link = null) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "INSERT INTO notifications (user_id, from_user_id, message, type, link) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare notification statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "iisss", $user_id, $from_user_id, $message, $type, $link);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create notification: " . mysqli_stmt_error($stmt));
    }

    return mysqli_insert_id($conn);
}

function createProfileVisitNotification($conn, $visitor_id, $profile_id) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }
    
    // Don't create notification if user visits their own profile
    if ($visitor_id == $profile_id) {
        return false;
    }
    
    // Get visitor's username
    $visitor_sql = "SELECT username FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $visitor_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare visitor username statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $visitor_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to get visitor username: " . mysqli_stmt_error($stmt));
    }
    $result = mysqli_stmt_get_result($stmt);
    $visitor = mysqli_fetch_assoc($result);
    
    $message = "@" . $visitor['username'] . " viewed your profile";
    $link = "user.php?id=" . $visitor_id;
    
    return createNotification(
        $conn,
        $profile_id,      // user_id (profile owner)
        $message,         // message
        'profile_visit',  // type
        $visitor_id,      // from_user_id (visitor)
        $link            // link to visitor's profile
    );
}

// Mark notification as read
if(isset($_POST['mark_read'])) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $notification_id = (int)$_POST['notification_id'];
    $user_id = (int)$_SESSION['user_id'];
    
    $sql = "UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare mark read statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $notification_id, $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to mark notification as read: " . mysqli_stmt_error($stmt));
    }
    
    if(isset($_POST['ajax'])) {
        echo 'success';
        exit;
    }
}

function hasNotifications($conn, $user_id) {
    if (!$conn) {
        throw new Exception("Database connection is required");
    }

    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare has notifications statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to check notifications: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return (int)$row['count'] > 0;
}

// Ensure notifications table exists when this file is included
if (isset($conn)) {
    try {
        ensureNotificationsTable($conn);
        
        // Create a test notification for the current user if they're logged in and have no notifications
        if (isset($_SESSION['user_id'])) {
            if (!hasNotifications($conn, $_SESSION['user_id'])) {
                createTestNotification($conn, $_SESSION['user_id']);
            }
        }
    } catch (Exception $e) {
        error_log("Error in notifications setup: " . $e->getMessage());
    }
}

function getTimeAgo($timestamp) {
    if (!$timestamp) return "Unknown time";
    
    $time_ago = strtotime($timestamp);
    if ($time_ago === false) return "Invalid time";
    
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    
    if ($time_difference < 0) return "Just now";
    
    if ($time_difference < 60) {
        return "Just now";
    } elseif ($time_difference < 3600) {
        $minutes = round($time_difference / 60);
        return $minutes . ($minutes == 1 ? " minute ago" : " minutes ago");
    } elseif ($time_difference < 86400) {
        $hours = round($time_difference / 3600);
        return $hours . ($hours == 1 ? " hour ago" : " hours ago");
    } elseif ($time_difference < 604800) {
        $days = round($time_difference / 86400);
        return $days . ($days == 1 ? " day ago" : " days ago");
    } elseif ($time_difference < 2592000) {
        $weeks = round($time_difference / 604800);
        return $weeks . ($weeks == 1 ? " week ago" : " weeks ago");
    } else {
        $months = round($time_difference / 2592000);
        return $months . ($months == 1 ? " month ago" : " months ago");
    }
}
?> 