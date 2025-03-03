<?php
    if(isset($_POST["logsubmit"])){
        $logusername = htmlspecialchars( $_POST["logusername"]);
        $logpassword = htmlspecialchars( $_POST["logpassword"]);

        $stmt = "SELECT * from users where username = '$logusername'";
        $result = mysqli_query($conn, $stmt);

        if(mysqli_num_rows($result) === 1){
           $row = mysqli_fetch_array($result);
            if($row["username"] == $logusername && password_verify($logpassword,$row["password"]) == true){
                $_SESSION['user_username'] = $row['username'];
                $_SESSION['user_password'] = $row['password'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id'] = $row['id'];
                echo "you are loged in";
                header("location: user.php");
            }
            else{
                header("location: lesson1.php");
                exit();
            }
        }
       
    };