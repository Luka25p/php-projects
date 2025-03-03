<?php
include("database.php");
include("includes/signup.inc.php");
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .wrapper {
            display: flex;
            gap: 20px;
        }
        .index-login-signup {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h4 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="index-login-signup">
            <h4>Sign Up</h4>
            <p>
                <?php echo $regError;?>
            </p>
            <form action="<?php $_SERVER["PHP_SELF"]?>" method="post">
                <input type="text" name="username" placeholder="Username" >
                <input type="password" name="password" placeholder="Password" >
                <input type="password" name="passwordRepeat" placeholder="Repeat Password" >
                <input type="email" name="email" placeholder="Email" >
                <button type="submit" name="submit">Sign Up</button>
            </form>            
        </div>
        <div class="index-login-signup">
            <h4>Login</h4>
            <form action="<?php $_SERVER["PHP_SELF"]?>" method="post">
                <input type="text" name="logusername" placeholder="Username" >
                <input type="password" name="logpassword" placeholder="Password" >
                <button type="submit" name="logsubmit">Login</button>
            </form>            
        </div>
    </div>
</body>
</html>
<?php
include("includes/login.inc.php");
mysqli_close($conn);
?>