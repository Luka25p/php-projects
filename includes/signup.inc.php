<?php
$regError = "";
if(isset($_POST["submit"])){
    if(empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"])){
        $regError = "fill everething";
    }
    else{
        $username = htmlspecialchars($_POST["username"]);
        $password = htmlspecialchars($_POST["password"]);
        $email = htmlspecialchars($_POST["email"]);

        $hashdPsw = password_hash($password, PASSWORD_DEFAULT);
        $stmt = "INSERT INTO users (username, password, email) VALUES ('$username','$hashdPsw','$email');";

        mysqli_query($conn,$stmt); 
        header("location: lesson1.php");            
    }   
}