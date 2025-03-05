<?php
include("database.php");
session_start();

if(!isset($_SESSION['user_username']) || !isset($_SESSION['user_password'])){
    header("location: lesson1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed • DevConnect</title>
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
            <div class="nav-links">
                <a href="user.php" class="nav-link">My Profile</a>
                <a href="feed.php" class="nav-link active">Feed</a>
            </div>
            <div class="nav-actions">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <button type="submit" name="signOut" class="signout-btn">
                        Sign Out <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="layout">
        <main class="main-content feed-page">
            <div class="posts-feed">
                <?php
                // Get all posts with user information
                $sql = "SELECT posts.*, users.username, users.email 
                        FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        ORDER BY posts.created_at DESC";
                $result = mysqli_query($conn, $sql);

                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <section class="post-card">
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="https://api.dicebear.com/7.x/micah/svg?seed=<?php echo $row["username"]; ?>" 
                                         alt="Profile" class="post-avatar">
                                    <div class="user-details">
                                        <h3 class="username"><?php echo htmlspecialchars($row["username"]); ?></h3>
                                        <span class="post-time"><?php echo date('F j, Y, g:i a', strtotime($row["created_at"])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="post-content">
                                <h2 class="post-title"><?php echo htmlspecialchars($row["post_tittle"]); ?></h2>
                                <p class="post-text"><?php echo nl2br(htmlspecialchars($row["post_text"])); ?></p>
                            </div>
                            <div class="post-actions">
                                <button class="action-btn like-btn">
                                    <i class="far fa-heart"></i>
                                    <span>Like</span>
                                </button>
                                <button class="action-btn comment-btn">
                                    <i class="far fa-comment"></i>
                                    <span>Comment</span>
                                </button>
                                <button class="action-btn share-btn">
                                    <i class="far fa-share-square"></i>
                                    <span>Share</span>
                                </button>
                            </div>
                        </section>
                        <?php
                    }
                } else {
                    echo "<p class='no-posts'>No posts yet</p>";
                }
                ?>
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
        exit();
    }
    ?>
</body>
</html> 