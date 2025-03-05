<?php
session_start();
require_once __DIR__ . '/includes/dbh.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Insert message into the database
    $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, created_at) VALUES ('$sender_id', '$receiver_id', '$message', NOW())";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?> 