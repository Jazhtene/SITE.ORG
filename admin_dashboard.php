<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

 
    <div class="navbar">
        <a href="approve_attendance.php">Approve/Deny Attendance</a>
        <a href="balance.php">Balance</a>
        <a href="event.php">Event</a>
        <a href="present_absent.php">View Attendance</a>
        <a href="general_report.php">General Report</a>
    </div>

    
    <div class="content">
        <h2>Welcome, <?php echo $_SESSION['name']; ?></h2>
        <p>You are logged in as <strong>Admin</strong>.</p>
    </div>

</body>
</html>
