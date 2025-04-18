<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include('db.php');

$user_id = $_SESSION['user_id']; 
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "No user found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile</title>
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

        .profile-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .profile-container h2 {
            text-align: center;
            color: #1e3a8a;
        }

        .profile-info {
            margin-bottom: 10px;
        }

        .profile-info strong {
            display: inline-block;
            width: 120px;
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
    <div class="profile-container">
        <h2>Your Profile</h2>
        
        <div class="profile-info">
            <strong>Name:</strong> <?php echo $student['name']; ?>
        </div>
        <div class="profile-info">
            <strong>Age:</strong> <?php echo $student['age']; ?>
        </div>
        <div class="profile-info">
            <strong>Sex:</strong> <?php echo $student['sex']; ?>
        </div>
        <div class="profile-info">
            <strong>Address:</strong> <?php echo $student['address']; ?>
        </div>
        <div class="profile-info">
            <strong>Course:</strong> <?php echo $student['course']; ?>
        </div>
        <div class="profile-info">
            <strong>Year Level:</strong> <?php echo $student['year_level']; ?>
        </div>
        <div class="profile-info">
            <strong>Section:</strong> <?php echo $student['section']; ?>
        </div>
        <div class="profile-info">
            <strong>Contact Number:</strong> <?php echo $student['contact_number']; ?>
        </div>
        <div class="profile-info">
            <strong>Student ID:</strong> <?php echo $student['student_id']; ?>
        </div>
        <div class="profile-info">
            <strong>Current Balance:</strong> ₱<?php echo number_format($student['balance'], 2); ?>
        </div>

        <a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
