<?php

if(isset($_POST["post"])){
    if(empty($_POST["textTittle"]) || empty($_POST["postText"])){
        header("location: user.php");
    }else{
        $post_tittle = $_POST["textTittle"];
        $post_text = $_POST["postText"];
        $stmt = "INSERT INTO posts (post_tittle, post_text, user_id) VALUES ('$post_tittle', '$post_text', $_SESSION[user_id])";

        mysqli_query($conn, $stmt);
        header("location: user.php");
    }
}
