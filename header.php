<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
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
            <a href="feed.php">
                <i class="fas fa-globe"></i>
                Feed
            </a>
            <a href="notifications.php">
                <i class="far fa-bell"></i>
                <?php
                if (isset($_SESSION['user_id'])) {
                    $unread_count = getUnreadCount($conn, $_SESSION['user_id']);
                    if ($unread_count > 0) {
                        echo "<span class='badge'>$unread_count</span>";
                    }
                }
                ?>
            </a>
            <a href="user.php">
                <i class="fas fa-user"></i>
                Profile
            </a>
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                <button type="submit" name="signOut" class="signout-btn">
                    Sign Out <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</nav> 