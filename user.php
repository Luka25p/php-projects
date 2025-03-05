<?php
include("database.php");
session_start();

// Handle sign out
if(isset($_POST["signOut"])){
    session_destroy();
    header("location: lesson1.php");
    exit(); // Add exit after redirect
}

// Check if user is logged in
if(!isset($_SESSION['user_username']) || !isset($_SESSION['user_password'])){
    header("location: lesson1.php");
    exit(); // Add exit after redirect
}

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
                <span>DevConnect</span>
            </div>
            <div class="nav-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <div class="nav-actions">
                <a href="#" class="nav-icon"><i class="far fa-bell"></i></a>
                <a href="#" class="nav-icon"><i class="far fa-envelope"></i></a>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <button type="submit" name="signOut" class="signout-btn">
                        Sign Out <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="layout">
        <aside class="left-sidebar">
            <div class="profile-quick-view">
                <div class="profile-banner"></div>
                <img src="https://api.dicebear.com/7.x/micah/svg?seed=<?php echo $_SESSION["user_username"];?>" 
                     alt="Profile" class="profile-pic">
                <div class="profile-info">
                    <h2><?php echo $_SESSION["user_username"];?></h2>
                    <p class="profile-email"><?php echo $_SESSION['user_email'];?></p>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-number">258</span>
                        <span class="stat-label">Posts</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">1.4K</span>
                        <span class="stat-label">Following</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">2.8K</span>
                        <span class="stat-label">Followers</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="create-post-card">
                <form action="<?php $_SERVER["PHP_SELF"]?>" method="post" class="post-form">
                    <div class="post-form-header">
                        <img src="https://api.dicebear.com/7.x/micah/svg?seed=<?php echo $_SESSION["user_username"];?>" 
                             alt="Profile" class="post-avatar">
                        <input type="text" name="textTittle" placeholder="Post title" class="post-title-input">
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
        </main>

        <aside class="right-sidebar">
            <div class="trending-section">
                <h3>Trending Topics</h3>
                <div class="trending-topics">
                    <div class="topic">
                        <span class="topic-category">Programming</span>
                        <h4>#JavaScript</h4>
                        <span class="topic-posts">2.4K posts</span>
                    </div>
                    <div class="topic">
                        <span class="topic-category">Tech</span>
                        <h4>#AI</h4>
                        <span class="topic-posts">1.8K posts</span>
                    </div>
                    <div class="topic">
                        <span class="topic-category">Development</span>
                        <h4>#WebDev</h4>
                        <span class="topic-posts">956 posts</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <?php
        if(isset($_POST["signOut"])){
            header("location: lesson1.php");
            session_destroy();   
        }
    ?>
</body>
</html>
