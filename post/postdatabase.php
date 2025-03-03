<?php
    $db_hostName = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "posts";
    $conn = "";

    try{
        $conn = mysqli_connect($db_hostName, $db_username, $db_password, $db_name);
    }catch(mysqli_sql_exception){
        echo"culd not connected <br>";
    };

?>