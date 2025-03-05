<?php
if(isset($_POST["login"])){
    $username = $_POST["loginUsername"];
    $password = $_POST["loginPassword"];
    
    // First get the user by username only
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn,$sql);
    
    if(mysqli_num_rows($result)){
        $row = mysqli_fetch_assoc($result);
        
        // Verify the password matches
        if(password_verify($password, $row["password"])){
            $_SESSION["user_username"] = $username;
            $_SESSION["user_password"] = $row["password"]; // Store hashed password in session
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_email"] = $row["email"];
            
            header("location: user.php");
            exit();
        } else {
            $login_error = "Wrong username or password";
        }
    }
    else{
        $login_error = "Wrong username or password";
    }
}
?>