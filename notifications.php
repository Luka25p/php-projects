<?php
include("database.php");
session_start();
require_once __DIR__ . '/includes/dbh.inc.php';
require_once __DIR__ . '/includes/notifications.inc.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Only mark all notifications as read if explicitly requested
if (isset($_GET['mark_all_read']) && $_GET['mark_all_read'] === 'true') {
    if (isset($_SESSION['user_id'])) {
        markAllNotificationsRead($conn, $_SESSION['user_id']);
        // Redirect to remove the parameter from URL
        header("Location: notifications.php");
        exit();
    }
}

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - DevConnect</title>
    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include_once 'header.php'; ?>

    <div class="container">
        <div class="notifications-container">
            <div class="notifications-header">
                <h1>Notifications</h1>
                <div class="notification-actions">
                    <button class="mark-all-read-btn" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i> Mark all as read
                    </button>
                    <div class="notification-filters">
                        <button class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>" data-filter="all">
                            All
                        </button>
                        <button class="filter-btn <?php echo $filter === 'unread' ? 'active' : ''; ?>" data-filter="unread">
                            Unread
                        </button>
                        <button class="filter-btn <?php echo $filter === 'mentions' ? 'active' : ''; ?>" data-filter="mentions">
                            Mentions
                        </button>
                    </div>
                </div>
            </div>

            <div class="notifications-list" id="notificationsList">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </div>

            <div class="pagination" id="pagination">
                <!-- Pagination will be inserted here -->
            </div>

            <div class="no-notifications" style="display: none;">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications found</p>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationsList = document.getElementById('notificationsList');
        const paginationContainer = document.getElementById('pagination');
        const noNotifications = document.querySelector('.no-notifications');
        const loadingSpinner = document.querySelector('.loading-spinner');
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        let currentPage = <?php echo $current_page; ?>;
        let currentFilter = '<?php echo $filter; ?>';
        let isLoading = false;

        function loadNotifications(page = 1, filter = currentFilter) {
            if (isLoading) return;
            
            isLoading = true;
            loadingSpinner.style.display = 'block';
            notificationsList.innerHTML = '';
            
            const formData = new FormData();
            formData.append('action', 'get_notifications');
            formData.append('page', page);
            formData.append('filter', filter);

            fetch('includes/notification_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        return data;
                    } catch (e) {
                        console.error('Server response:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (!data || typeof data !== 'object') {
                    throw new Error('Invalid response format');
                }

                if (data.notifications.length === 0) {
                    noNotifications.style.display = 'block';
                    paginationContainer.style.display = 'none';
                } else {
                    noNotifications.style.display = 'none';
                    displayNotifications(data.notifications);
                    updatePagination(data.pagination);
                }

                // Update unread count in header
                if (typeof data.unread_count !== 'undefined') {
                    updateUnreadCount(data.unread_count);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsList.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Error loading notifications: ${error.message}</p>
                        <button onclick="loadNotifications(currentPage, currentFilter)" class="retry-button">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `;
            })
            .finally(() => {
                isLoading = false;
                loadingSpinner.style.display = 'none';
            });
        }

        function displayNotifications(notifications) {
            notifications.forEach(notification => {
                const notificationElement = document.createElement('div');
                notificationElement.className = `notification-item ${notification.is_read ? '' : 'unread'}`;
                notificationElement.dataset.id = notification.id;
                
                const avatarHtml = notification.profile_pic ? 
                    `<img src="${notification.profile_pic}" alt="${notification.username}" onerror="this.src='assets/images/default-avatar.png'">` :
                    `<img src="https://api.dicebear.com/7.x/micah/svg?seed=${notification.username}" alt="${notification.username}" class="default-avatar">`;
                
                notificationElement.innerHTML = `
                    <div class="notification-content">
                        <div class="notification-avatar">
                            ${avatarHtml}
                        </div>
                        <div class="notification-text">
                            <p>${notification.message}</p>
                            <span class="notification-time">${notification.time_ago}</span>
                        </div>
                    </div>
                    ${notification.link ? `<a href="${notification.link}" class="notification-link" onclick="event.preventDefault(); window.location.href='${notification.link}'"></a>` : ''}
                `;

                if (!notification.is_read) {
                    notificationElement.addEventListener('click', () => {
                        markAsRead(notification.id);
                        if (notification.link) {
                            window.location.href = notification.link;
                        }
                    });
                } else if (notification.link) {
                    notificationElement.addEventListener('click', () => {
                        window.location.href = notification.link;
                    });
                }

                notificationsList.appendChild(notificationElement);
            });
        }

        function updatePagination(pagination) {
            paginationContainer.style.display = 'flex';
            paginationContainer.innerHTML = '';

            // Previous button
            if (pagination.current_page > 1) {
                const prevButton = createPaginationButton('Previous', pagination.current_page - 1);
                paginationContainer.appendChild(prevButton);
            }

            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (
                    i === 1 || 
                    i === pagination.total_pages || 
                    (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
                ) {
                    const pageButton = createPaginationButton(i, i);
                    if (i === pagination.current_page) {
                        pageButton.classList.add('active');
                    }
                    paginationContainer.appendChild(pageButton);
                } else if (
                    i === pagination.current_page - 3 || 
                    i === pagination.current_page + 3
                ) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'pagination-ellipsis';
                    ellipsis.textContent = '...';
                    paginationContainer.appendChild(ellipsis);
                }
            }

            // Next button
            if (pagination.current_page < pagination.total_pages) {
                const nextButton = createPaginationButton('Next', pagination.current_page + 1);
                paginationContainer.appendChild(nextButton);
            }
        }

        function createPaginationButton(text, page) {
            const button = document.createElement('button');
            button.className = 'pagination-btn';
            button.textContent = text;
            button.addEventListener('click', () => loadNotifications(page, currentFilter));
            return button;
        }

        function markAsRead(notificationId) {
            const formData = new FormData();
            formData.append('action', 'mark_read');
            formData.append('notification_id', notificationId);

            fetch('includes/notification_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                if (data.success) {
                    const notification = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (notification) {
                        notification.classList.remove('unread');
                    }
                    loadNotifications(currentPage, currentFilter);
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                // Show error message to user
                const notification = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (notification) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.innerHTML = `
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Failed to mark as read: ${error.message}</p>
                        <button onclick="markAsRead(${notificationId})" class="retry-button">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    `;
                    notification.appendChild(errorDiv);
                }
            });
        }

        function updateUnreadCount(count) {
            const unreadBadge = document.querySelector('.notification-badge');
            if (unreadBadge) {
                unreadBadge.textContent = count;
                unreadBadge.style.display = count > 0 ? 'block' : 'none';
            }
        }

        function markAllAsRead() {
            const formData = new FormData();
            formData.append('action', 'mark_all_read');

            fetch('includes/notification_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                if (data.success) {
                    // Reload notifications with the current filter
                    loadNotifications(currentPage, currentFilter);
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Failed to mark all notifications as read: ${error.message}</p>
                    <button onclick="markAllAsRead()" class="retry-button">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                `;
                notificationsList.appendChild(errorDiv);
            });
        }

        // Event listeners
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.dataset.filter;
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                currentFilter = filter;
                currentPage = 1;
                loadNotifications(currentPage, filter);
            });
        });

        // Initial load
        loadNotifications(currentPage, currentFilter);
    });
    </script>

    <style>
    .notifications-container {
        max-width: 800px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }

    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .notification-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .mark-all-read-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 20px;
        background: #fff;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
    }

    .mark-all-read-btn:hover {
        background: #f8f9fa;
        border-color: #ccc;
    }

    .mark-all-read-btn:active {
        background: #f1f3f5;
    }

    .mark-all-read-btn i {
        font-size: 1rem;
        color: #6c757d;
    }

    .notification-filters {
        display: flex;
        gap: 0.5rem;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 20px;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn.active {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .notification-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s ease;
        cursor: pointer;
        position: relative;
        transition: transform 0.2s ease;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .notification-item.unread {
        background-color: #f0f7ff;
    }

    .notification-content {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        flex: 1;
    }

    .notification-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .notification-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .notification-avatar i {
        font-size: 2rem;
        color: #6c757d;
    }

    .notification-text {
        flex: 1;
    }

    .notification-text p {
        margin: 0;
        color: #333;
    }

    .notification-time {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .notification-link {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .notification-item:hover .notification-link {
        opacity: 1;
    }

    .loading-spinner {
        display: none;
        justify-content: center;
        padding: 2rem;
    }

    .loading-spinner i {
        font-size: 2rem;
        color: #007bff;
    }

    .no-notifications {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .no-notifications i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }

    .pagination-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .pagination-btn.active {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .pagination-ellipsis {
        padding: 0.5rem;
        color: #6c757d;
    }

    .error-message {
        text-align: center;
        padding: 2rem;
        color: #dc3545;
        background: #fff;
        border-radius: 8px;
        margin: 1rem 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .error-message i {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #dc3545;
    }

    .retry-button {
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .retry-button:hover {
        background: #0056b3;
    }

    .retry-button i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }

    .default-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    </style>
</body>
</html> 