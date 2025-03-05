<?php
if(isset($_POST["register"])){
    $username = $_POST["regUsername"];
    $email = $_POST["regEmail"];
    $password = $_POST["regPassword"];
    
    // Hash password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, email) 
            VALUES ('$username', '$hashed_password', '$email')";
    
    try{
        mysqli_query($conn,$sql);
        $register_success = "You have been registered";
    }
    catch(mysqli_sql_exception){
        $register_error = "That username is taken";
    }
}
?> 