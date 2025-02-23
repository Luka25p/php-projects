<?php
include("postdatabase.php")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            border: none;
            text-decoration: none;
            list-style: none;
        }
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 20px;
        }
        .postCont{
            display: flex;
            gap: 100px;
        }
        .postCont section{
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 300px;
            height: 300px;
            color: rgb(224, 224, 224);
            background-color: rgb(36, 36, 36);
            padding: 20px;
            border-radius: 10px;
            h3{
                text-align: center;
                color: rgb(224, 224, 224);
                border-bottom: 1px solid rgba(218, 218, 218, 0.46);
                padding-bottom: 10px;
            }

        }
        .time{
            color: rgb(175, 175, 175);
            font-size: 14px;
            text-align: end;
        }
        .post{
            height: 150px;
            overflow: auto;
        }
        .cancel{
            width: fit-content;
            color: white;
            background-color: transparent;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
        }
        .none{
            display: none;
        }
    </style>
</head>
<body>


<script src="script.js"></script>
</body>
</html>
<?php
    $query = "SELECT * FROM post";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0 ){

        echo "<div class='postCont'>";
        while($row = mysqli_fetch_assoc($result)){
            echo "<section>";
            $userid = htmlspecialchars($row["id"]);
            echo "<form action='posts.php' method='post'><input type='submit' value='x' class='cancel' name='cancel'><input type='text' value='$userid' name='userid' class='none'></form>";
            echo "<h3 class='headers'>". $row["username"] . '</h3>';
            echo '<p class="post">' . $row['post'] . '</p>' ;
            echo '<p class="time">' . $row['upload_date'] . '</p>';
            echo "</section>";
        };
        echo "</div>";
        if(isset($_POST["cancel"])){
            $useridd = $_POST["userid"];
            $delete = "DELETE FROM post WHERE id = '$useridd'";
            mysqli_query($conn, $delete);
            header("location:posts.php");
        };   
    };

mysqli_close($conn)
?>