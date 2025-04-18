<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include('db.php');

$user_id = $_SESSION['user_id']; 
$query = "SELECT balance FROM balances WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

if ($balance === null) {
    $balance = 0; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Balance</title>
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

        .content {
            padding: 30px;
        }

        .balance-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
            text-align: center;
        }

        .balance-container h2 {
            color: #1e3a8a;
        }

        .balance-container p {
            font-size: 18px;
        }

        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            background-color: #1e3a8a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="attendance_request.php">Apply Attendance</a>
    <a href="view_event.php">View Event</a>
    <a href="view_balance.php">View Balance</a>
    
    <a href="view_profile.php" class="profile-link">
        <div class="profile-icon">
            <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
        </div>
    </a>
</div>

<div class="content">
    <div class="balance-container">
        <h2>Your Current Balance</h2>
        
        <p>₱<?php echo number_format($balance, 2); ?></p>

        <a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
