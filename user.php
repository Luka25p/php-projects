<?php
include("database.php");
session_start();

// Handle sign out first, before any output
if(isset($_POST["signOut"])){
    session_destroy();
    header("location: lesson1.php");
    exit();
}

// Check if user is logged in
if(!isset($_SESSION['user_username']) || !isset($_SESSION['user_password'])){
    header("location: lesson1.php");
    exit();
}

// Get profile ID from URL
$profile_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];



include("includes/post.inc.php");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home • <?php echo $_SESSION["user_username"];?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/styles/main.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fab fa-connectdevelop"></i>
                <span>lukasWeb</span>
            </div>
            <div class="nav-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <div class="nav-actions">
                <a href="feed.php">
                    <i class="fas fa-globe"></i>
                    Feed
                </a>
                <a href="#">
                    <i class="far fa-bell"></i>
                </a>
                <a href="#"><i class="far fa-envelope"></i></a>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <button type="submit" name="signOut">Sign Out</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="layout">
        <aside class="left-sidebar">
            <div class="profile-quick-view">
                <div class="profile-banner"></div>
                <img src="imgs/img1.jpg"  class="profile-pic">
                <div class="profile-info">
                    <h2><?php echo $_SESSION["user_username"];?></h2>
                    <p class="profile-email"><?php echo $_SESSION['user_email'];?></p>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-number">
                            <?php 
                                $postCount = "SELECT COUNT(user_id) AS NumberOfpost FROM posts where user_id = $_SESSION[user_id];";
                                $postCountResulte = mysqli_query($conn,$postCount);
                                $postCounter = mysqli_fetch_assoc($postCountResulte);
                                echo $postCounter["NumberOfpost"];
                            ?>
                        </span>
                        <span class="stat-label">Posts</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">
                            0
                        </span>
                        <span class="stat-label">Following</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">
                            0
                        </span>
                        <span class="stat-label">Followers</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="create-post-card">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" class="post-form">
                    <div class="post-form-header">
                        <img src="imgs/img1.jpg"  class="post-avatar">
                        <input type="text" name="textTittle" placeholder="Post title here..." class="post-title-input">
                    </div>
                    <textarea name="postText" placeholder="What's on your mind?"
                              class="post-content-input"></textarea>
                    <div class="post-form-actions">
                        <div class="post-attachments">
                            <button type="button" class="attachment-btn">
                                <i class="far fa-image"></i>
                            </button>
                            <button type="button" class="attachment-btn">
                                <i class="far fa-file-alt"></i>
                            </button>
                            <button type="button" class="attachment-btn">
                                <i class="far fa-smile"></i>
                            </button>
                        </div>
                        <button type="submit" name="post" class="post-submit-btn">Post</button>
                    </div>
                </form>
            </div>

            <div class="posts-feed">
                <?php include("includes/posts.inc.php")?>
            </div>

            <div>
                <h1>hello</h1>
            </div>
        </main>

</body>
</html>