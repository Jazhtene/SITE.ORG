<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f4f4;
        }

        .navbar {
            background-color: #1e3a8a;
            overflow: hidden;
            padding: 14px 20px;
            position: relative;
        }

        .navbar a {
            float: left;
            color: #ffffff;
            text-align: center;
            padding: 12px 16px;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar a:hover {
            background-color: #3b82f6;
            color: white;
        }

        .profile-link {
            position: absolute; 
            top: 10px; 
            right: 10px; 
            text-decoration: none;
        }

        .profile-icon {
            width: 35px;
            height: 35px;
            background-color: #ffffff;
            color: #1e3a8a;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            font-weight: bold;
            display: inline-block;
        }

        .content {
            padding: 30px;
        }

        h2 {
            color: #1e3a8a;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="attendance_request.php">Apply Attendance</a>
    <a href="view_event.php">View Event</a>
    <a href="view_balance.php">View Balance</a>
    <a href="logout.php">Logout</a>
    <a href="change_password.php">Change Password</a>
    
    <a href="view_profile.php" class="profile-link">
        <div class="profile-icon">
            <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
        </div>
    </a>
</div>

<div class="content">
    <h2>Welcome, <?php echo $_SESSION['name']; ?></h2>
    <p>You are logged in as <strong>Student</strong>.</p>
</div>

</body>
</html>
