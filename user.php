<?php
include("database.php");
session_start();
include("includes/post.inc.php");

if(isset($_SESSION['user_username']) && isset($_SESSION['user_password'])){
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        *{
            outline: none;
            border: none;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 50px;
            padding: 20px;
            align-items: center;
        }
        .profile-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .profile-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .profile-card h2 {
            margin: 10px 0;
        }
        .profile-card p {
            color: #666;
        }
        .profile-card button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        .profile-card button:hover {
            background: #0056b3;
        }
        .posts{
            display: flex;
            gap: 20px;
            section{
                background: rgb(27, 27, 27);
                width: 300px;
                height: 400px;
                color: white;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                border-radius: 10px;
                padding: 20px;
                .headers{
                    text-align: end;
                }
                .postText{
                    height: 100%;
                    background: rgb(44, 44, 44);
                    padding: 10px;
                    border-radius: 5px;
                }
                .time{
                    font-size: 14px;
                    text-align: end;
                    color:rgb(216, 216, 216);
                }
            }
        }
        .postForm{
            display: flex;
            flex-direction: column;
            background: rgb(27, 27, 27);
            padding: 20px;
            border-radius: 10px;
            color: #f4f4f4;
            section{
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                padding: 10px;
            }
        }
        .textarea{
            resize: none;
            display: inline-block;
            background: rgb(53, 53, 53);
            color: #f4f4f4;
            width: 400px;
            font-size: 18px;
            padding: 10px;
            border-radius: 5px;
        }
        .textarea2{
            resize: none;
            background: rgb(53, 53, 53);
            width: 400px;
            height: 300px;
            color: #f4f4f4;
            border-radius: 5px;

        }
        .postbtn{
            width: fit-content;
            padding: 5px 60px;
            border-radius: 10px;
            color: #f4f4f4;
            font-size: 18px;
            background-color: rgb(53, 53, 53);
            margin: 20px auto;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form action="<?php $_SERVER["PHP_SELF"]?>" method="post" class="profile-card">
        <img src="https://via.placeholder.com/100" alt="User Picture">
        <h2><?php echo $_SESSION["user_username"];?></h2>
        <p>email: <?php echo $_SESSION['user_email'];?></p>
        <p>id: <?php echo $_SESSION["user_id"]?></p>
        <button type="submit" name="signOut">sign out</button>
    </form>
    <?php
        if(isset($_POST["signOut"])){
            header("location: lesson1.php");
            session_destroy();   
        }
    ?>
    <form action="<?php $_SERVER["PHP_SELF"]?>" method="post" class="postForm">
        <section>
            <label for="textTittle">Text Tittle</label>
            <textarea name="textTittle" id="textTittle" class="textarea"></textarea>            
        </section>
        <section>
            <label for="post">post</label>
            <textarea name="postText" id="post" class="textarea2"></textarea>            
        </section>
        <input type="submit" value="post" name="post" class="postbtn">
    </form>
    <div class="posts">
        <?php include("includes/posts.inc.php")?>
    </div>
</body>
</html>
<?php

}
else{
    header("location: lesson1.php");
}
?>
