<?php
$stmt = "SELECT * FROM posts WHERE user_id = $_SESSION[user_id]";
$result = mysqli_query($conn, $stmt);
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo "<section>";
        echo "<form action='user.php' method='post'>";
        echo "<h2>".$_SESSION["user_username"]. "</h2>";
        echo "<button type='submit' name='delete' class='delete' id='delete' onclick='refresh'>
                        delete". "<input type='text' name='postid' value='$row[post_id]' style='display: none;'>" ."
                    </button>
            </form>";
        echo "<h3 class='headers'>". $row["post_tittle"] . '</h3>';
        echo '<p class="postText">' . $row['post_text'] . '</p>' ;
        echo "<input type='checkbox'>"; 
        echo '<p class="time">' . $row['post_date'] . '</p>';
        echo "</section>"; 
    }
}
if(isset($_POST["delete"])){
    $postID = $_POST["postid"];
    if($postID == 0){
       echo "<h2 class='noPost-error'>there is no post</h2>";
    }else{
        $stmt ="DELETE FROM posts WHERE post_id = $postID";
        mysqli_query($conn,$stmt);
    }
}
?>
<script>
    document.getElementById("delete").addEventListener("click", ()=>{
        window.location.href = "user.php"
    })
</script>