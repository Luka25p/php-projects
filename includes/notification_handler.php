<?php
// Start output buffering at the very beginning
ob_start();

// Disable error display but enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/notification_errors.log');

// Function to send JSON response and exit
function sendJsonResponse($data) {
    // Clear any previous output
    ob_clean();
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Output JSON response
    echo json_encode($data);
    exit;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Add detailed session debugging
error_log("Session Status: " . session_status());
error_log("Session ID: " . session_id());
error_log("Full Session Data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("Session user_id is missing. Available session keys: " . implode(', ', array_keys($_SESSION)));
    sendJsonResponse([
        'error' => 'Not logged in',
        'session_status' => session_status(),
        'session_id' => session_id(),
        'available_session_keys' => array_keys($_SESSION),
        'redirect' => 'login.php'
    ]);
}

// Validate user_id
$user_id = (int)$_SESSION['user_id'];
if ($user_id <= 0) {
    error_log("Invalid user_id in session: " . $_SESSION['user_id']);
    sendJsonResponse([
        'error' => 'Invalid user session',
        'user_id' => $user_id,
        'session_data' => $_SESSION,
        'redirect' => 'login.php'
    ]);
}

error_log("User ID verified: $user_id");

// Include required files
require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/notifications.inc.php';

// Log the incoming request
error_log("Notification request received - POST data: " . print_r($_POST, true));

// Check if database connection exists
if (!isset($conn) || !$conn) {
    error_log("Database connection failed in notification_handler.php");
    sendJsonResponse(['error' => 'Database connection failed']);
}

// Test database connection
if (!mysqli_ping($conn)) {
    error_log("Database connection lost: " . mysqli_error($conn));
    sendJsonResponse(['error' => 'Database connection lost']);
}

// Ensure notifications table exists
try {
    ensureNotificationsTable($conn);
} catch (Exception $e) {
    error_log("Error ensuring notifications table: " . $e->getMessage());
    sendJsonResponse(['error' => 'Database setup failed: ' . $e->getMessage()]);
}

// Validate and sanitize input
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// Log the action and user ID
error_log("Processing notification action: $action for user ID: $user_id");

// Validate action
if (!in_array($action, ['get_notifications', 'mark_read', 'mark_all_read'])) {
    error_log("Invalid action received: $action");
    sendJsonResponse(['error' => 'Invalid action']);
}

try {
    switch ($action) {
        case 'get_notifications':
            $filter = isset($_POST['filter']) ? trim($_POST['filter']) : 'all';
            $page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
            $per_page = 20;
            $offset = ($page - 1) * $per_page;

            // Log query parameters
            error_log("Query parameters - Filter: $filter, Page: $page, Per Page: $per_page, Offset: $offset");

            // Build query with proper filtering
            $where_clause = "WHERE n.user_id = ?";
            $params = [$user_id];
            $types = "i";
            
            if ($filter === 'unread') {
                $where_clause .= " AND n.is_read = FALSE";
            } elseif ($filter !== 'all') {
                $where_clause .= " AND n.type = ?";
                $params[] = $filter;
                $types .= "s";
            }

            // Get total count for pagination
            $count_sql = "SELECT COUNT(*) as total FROM notifications n $where_clause";
            error_log("Count SQL: $count_sql");
            error_log("Count Parameters: " . print_r($params, true));
            error_log("Count Types: $types");
            
            $count_stmt = mysqli_prepare($conn, $count_sql);
            if (!$count_stmt) {
                error_log("Count Prepare Error: " . mysqli_error($conn));
                throw new Exception("Count prepare failed: " . mysqli_error($conn));
            }
            
            // Always bind all parameters at once
            if (!mysqli_stmt_bind_param($count_stmt, $types, ...$params)) {
                error_log("Count Bind Error: " . mysqli_stmt_error($count_stmt));
                throw new Exception("Count bind failed: " . mysqli_stmt_error($count_stmt));
            }
            
            if (!mysqli_stmt_execute($count_stmt)) {
                error_log("Count Execute Error: " . mysqli_stmt_error($count_stmt));
                error_log("Count SQL State: " . mysqli_stmt_sqlstate($count_stmt));
                throw new Exception("Count execute failed: " . mysqli_stmt_error($count_stmt));
            }
            
            $count_result = mysqli_stmt_get_result($count_stmt);
            if (!$count_result) {
                error_log("Count Result Error: " . mysqli_error($conn));
                throw new Exception("Count result failed: " . mysqli_error($conn));
            }
            
            $total_count = mysqli_fetch_assoc($count_result)['total'];
            
            // Get notifications with pagination
            $sql = "SELECT n.*, u.username, u.id as from_user_id 
                    FROM notifications n 
                    LEFT JOIN users u ON n.from_user_id = u.id 
                    $where_clause 
                    ORDER BY n.created_at DESC 
                    LIMIT ? OFFSET ?";
            
            error_log("Main SQL: $sql");
            error_log("Main Parameters: " . print_r($params, true));
            error_log("Main Types: $types");
            
            $params[] = $per_page;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                error_log("Main Prepare Error: " . mysqli_error($conn));
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            // Always bind all parameters at once
            if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
                error_log("Main Bind Error: " . mysqli_stmt_error($stmt));
                throw new Exception("Main bind failed: " . mysqli_stmt_error($stmt));
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Main SQL Error: " . mysqli_stmt_error($stmt));
                error_log("Main SQL State: " . mysqli_stmt_sqlstate($stmt));
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            $notifications = mysqli_stmt_get_result($stmt);
            if (!$notifications) {
                error_log("Get Result Error: " . mysqli_error($conn));
                throw new Exception("Get result failed: " . mysqli_error($conn));
            }
            
            $notificationArray = [];
            while ($row = mysqli_fetch_assoc($notifications)) {
                $notificationArray[] = [
                    'id' => (int)$row['id'],
                    'message' => htmlspecialchars($row['message']),
                    'username' => $row['username'] ? htmlspecialchars($row['username']) : 'System',
                    'is_read' => (bool)$row['is_read'],
                    'link' => $row['link'] ? htmlspecialchars($row['link']) : '',
                    'type' => htmlspecialchars($row['type']),
                    'from_user_id' => $row['from_user_id'] ? (int)$row['from_user_id'] : null,
                    'profile_pic' => null,
                    'time_ago' => getTimeAgo($row['created_at']),
                    'created_at' => $row['created_at']
                ];
            }
            
            $response = [
                'notifications' => $notificationArray,
                'unread_count' => getUnreadCount($conn, $user_id),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $per_page,
                    'total_count' => (int)$total_count,
                    'total_pages' => ceil($total_count / $per_page)
                ]
            ];
            
            error_log("Response: " . json_encode($response));
            sendJsonResponse($response);
            break;

        case 'mark_read':
            if (!isset($_POST['notification_id'])) {
                throw new Exception('Notification ID is required');
            }
            $notification_id = (int)$_POST['notification_id'];
            $success = markNotificationRead($conn, $notification_id, $user_id);
            sendJsonResponse(['success' => $success]);
            break;

        case 'mark_all_read':
            $success = markAllNotificationsRead($conn, $user_id);
            sendJsonResponse(['success' => $success]);
            break;
    }
} catch (Exception $e) {
    error_log("Notification error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendJsonResponse([
        'error' => 'Failed to process notification request',
        'details' => $e->getMessage()
    ]);
}
?> 