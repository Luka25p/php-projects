<?php
$stmt = "SELECT * FROM posts WHERE user_id = $_SESSION[user_id]";
$result = mysqli_query($conn, $stmt);
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo "<section>";
        echo $_SESSION["user_username"];
        echo "<h3 class='headers'>". $row["post_tittle"] . '</h3>';
        echo '<p class="postText">' . $row['post_text'] . '</p>' ;
        echo '<p class="time">' . $row['post_date'] . '</p>';
        echo "</section>"; 
    }
}