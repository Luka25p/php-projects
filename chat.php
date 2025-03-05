<?php
session_start();
require_once __DIR__ . '/includes/dbh.inc.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("location: lesson1.php");
    exit();
}

// Fetch chat messages
$current_user_id = $_SESSION['user_id'];
$chat_user_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($chat_user_id) {
    $sql = "SELECT * FROM chat_messages WHERE (sender_id = '$current_user_id' AND receiver_id = '$chat_user_id') 
            OR (sender_id = '$chat_user_id' AND receiver_id = '$current_user_id') ORDER BY created_at ASC";
    $result = mysqli_query($conn, $sql);
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with User</title>
    <link rel="stylesheet" href="assets/styles/chat.css">
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat with User ID: <?php echo htmlspecialchars($chat_user_id); ?></h2>
        </div>
        <div class="chat-messages" id="chatMessages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo $message['sender_id'] == $current_user_id ? 'sent' : 'received'; ?>">
                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                    <span class="message-time"><?php echo date('H:i', strtotime($message['created_at'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <form id="chatForm">
            <input type="text" id="messageInput" placeholder="Type a message..." required>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        // JavaScript for handling message sending
        const chatForm = document.getElementById('chatForm');
        const chatMessages = document.getElementById('chatMessages');

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value;

            // Send message via AJAX
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `message=${encodeURIComponent(message)}&receiver_id=<?php echo $chat_user_id; ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Append new message to chat
                    const newMessage = document.createElement('div');
                    newMessage.className = 'message sent';
                    newMessage.innerHTML = `<p>${message}</p><span class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
                    chatMessages.appendChild(newMessage);
                    messageInput.value = '';
                }
            });
        });
    </script>
</body>
</html> 