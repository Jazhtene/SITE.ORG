<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
  
<div class="logo-container">
    <img src="123-removebg-preview.png" alt="SITE Logo">
    
</div>

    
    <div class="login-container">
        <h2>Login</h2>
        <form action="login_action.php" method="POST">
            Email: <input type="email" name="email" required><br><br>
            Password: <input type="password" name="password" required><br><br>
            <button type="submit">Login</button>
        </form>
        <p>No account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>
