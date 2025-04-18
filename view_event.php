<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include('db.php');

$query = "SELECT title, description, date FROM events ORDER BY date ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Events</title>
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

        .events-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: auto;
        }

        h2 {
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #1e3a8a;
            color: white;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        .back-button {
            display: block;
            width: 200px;
            margin: 30px auto;
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
    <div class="events-container">
        <h2>Upcoming Events</h2>

        <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                   
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center; color: #6b7280;">No upcoming events.</p>
        <?php endif; ?>

        <a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
