<?php
include("database.php");
session_start();

if(isset($_SESSION['user_username']) && isset($_SESSION['user_password'])){
    header("location: user.php");
    exit();
}

include("includes/login.inc.php");
include("includes/register.inc.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevConnect • Login or Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/styles/auth.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fab fa-connectdevelop"></i>
                <span>DevConnect</span>
            </div>
        </div>
    </nav>

    <main class="auth-container">
        <div class="auth-welcome">
            <div class="welcome-content">
                <h1><i class="fab fa-connectdevelop"></i> DevConnect</h1>
                <h2>Join the Developer Community</h2>
                <div class="feature-list">
                    <div class="feature-item">
                        <i class="fas fa-code"></i>
                        <p>Share your code and projects</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <p>Connect with fellow developers</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lightbulb"></i>
                        <p>Learn and grow together</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-forms">
            <div class="forms-container">
                <?php if(isset($login_error)): ?>
                    <div class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>
                
                <?php if(isset($register_error)): ?>
                    <div class="error-message"><?php echo $register_error; ?></div>
                <?php endif; ?>
                
                <?php if(isset($register_success)): ?>
                    <div class="success-message"><?php echo $register_success; ?></div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="auth-form login-form">
                    <h2>Welcome Back</h2>
                    <p class="form-subtitle">Sign in to your account</p>
                    
                    <div class="form-group">
                        <label for="loginUsername">Username</label>
                        <div class="input-with-icon">
                            <i class="far fa-user"></i>
                            <input type="text" name="loginUsername" id="loginUsername" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="loginPassword" id="loginPassword" required>
                        </div>
                    </div>

                    <button type="submit" name="login" class="auth-button">
                        Sign In <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <!-- Register Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="auth-form register-form">
                    <h2>Create Account</h2>
                    <p class="form-subtitle">Join our community today</p>

                    <div class="form-group">
                        <label for="regUsername">Username</label>
                        <div class="input-with-icon">
                            <i class="far fa-user"></i>
                            <input type="text" name="regUsername" id="regUsername" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="regEmail">Email</label>
                        <div class="input-with-icon">
                            <i class="far fa-envelope"></i>
                            <input type="email" name="regEmail" id="regEmail" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="regPassword">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="regPassword" id="regPassword" required>
                        </div>
                    </div>

                    <button type="submit" name="register" class="auth-button">
                        Create Account <i class="fas fa-user-plus"></i>
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>