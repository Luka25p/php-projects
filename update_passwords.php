<?php
include("database.php");

// Get all users
$sql = "SELECT id, password FROM users";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $old_password = $row['password'];
    
    // Only hash if not already hashed
    if(strlen($old_password) < 60) { // bcrypt hashes are always 60 characters
        $hashed_password = password_hash($old_password, PASSWORD_DEFAULT);
        
        // Update the password
        $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $id";
        mysqli_query($conn, $update_sql);
    }
}

echo "Passwords updated successfully";
?> 