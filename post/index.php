<?php
    include("postdatabase.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <style>
        textarea{
            width: 100%;
            height:300px;
            padding: 10px;
            border-radius: 5px;
            background-color: rgb(31, 31, 31);
            color: white;
            resize: none;
            font-size: 18px;
        }
    </style>
</head>
<body>
        <?php
            include("footer.html")
        ?>
</body>
</html>
<?php
    if(isset($_POST["submitPost"])){
        $username = $_POST["username"];
        $postinp = $_POST["post"];
        if(empty($username) || empty($postinp)){
            echo 'username or post is empty';
        }else{
            $post = "INSERT INTO post (username , post) VALUES ('$username','$postinp'); ";
            mysqli_query($conn, $post);
            header('location: index.php');
        }
    };
    mysqli_close($conn)
?>