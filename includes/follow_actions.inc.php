<?php
if(isset($_POST['follow'])) {
    $user_to_follow = $_POST['user_id'];
    $current_user = $_SESSION['user_id'];
    
    // Check if not already following
    $check_sql = "SELECT * FROM followers WHERE user_id = '$user_to_follow' AND follower_id = '$current_user'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) == 0) {
        // Add follow
        $sql = "INSERT INTO followers (user_id, follower_id) VALUES ('$user_to_follow', '$current_user')";
        mysqli_query($conn, $sql);

        // Create notification
        $current_username = $_SESSION['user_username'];
        $notification_message = "@$current_username started following you";
        $sql = "INSERT INTO notifications (user_id, from_user_id, type, message) 
                VALUES ('$user_to_follow', '$current_user', 'follow', '$notification_message')";
        mysqli_query($conn, $sql);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if(isset($_POST['unfollow'])) {
    $user_to_unfollow = $_POST['user_id'];
    $current_user = $_SESSION['user_id'];
    
    $sql = "DELETE FROM followers WHERE user_id = '$user_to_unfollow' AND follower_id = '$current_user'";
    mysqli_query($conn, $sql);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?> 