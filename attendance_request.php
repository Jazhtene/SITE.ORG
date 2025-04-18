<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$alreadySubmitted = false;
$attendanceStatus = "";
$event_id = $_POST['event_id'] ?? null;
$activity_description = $_POST['activity_description'] ?? null; 

$events = $conn->query("SELECT * FROM events WHERE status = 'active' AND date >= CURDATE() ORDER BY date ASC");

if ($events === false) {
    die('Query failed: ' . $conn->error);
}

if ($events->num_rows == 0) {
    $message = "⚠️ No active events available at the moment.";
}

$notif_stmt = $conn->prepare("
    SELECT ar.id, e.title, e.date 
    FROM attendance_requests ar
    JOIN events e ON ar.event_id = e.id
    WHERE ar.user_id = ? AND ar.status = 'approved' AND (ar.notified = 0 OR ar.notified IS NULL)
");
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result();
$notifications = [];

while ($row = $notif_result->fetch_assoc()) {
    $notifications[] = "✅ Your attendance for <strong>" . htmlspecialchars($row['title']) . "</strong> (" . date('F d, Y', strtotime($row['date'])) . ") has been approved!";


    $update = $conn->prepare("UPDATE attendance_requests SET notified = 1 WHERE id = ?");
    $update->bind_param("i", $row['id']);
    $update->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if ($event_id) {
        
        if (empty($activity_description)) {
            $message = "⚠️ Please describe the activities you participated in before submitting your attendance.";
        } else {
            $check = $conn->prepare("SELECT * FROM attendance_requests WHERE user_id = ? AND event_id = ?");
            $check->bind_param("ii", $user_id, $event_id);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $alreadySubmitted = true;
                $row = $result->fetch_assoc();
                $attendanceStatus = $row['status'];
                if ($attendanceStatus === 'approved') {
                    $message = "✅ Your attendance for this event has already been approved.";
                } elseif ($attendanceStatus === 'pending') {
                    $message = "⏳ Attendance already submitted. Awaiting admin approval.";
                } else {
                    $message = "❌ Attendance submission failed previously. Contact admin.";
                }
            } else {
                
                $stmt = $conn->prepare("INSERT INTO attendance_requests (user_id, event_id, date, status, activity_description) VALUES (?, ?, CURDATE(), 'pending', ?)"); 
                $stmt->bind_param("iis", $user_id, $event_id, $activity_description); 
                $stmt->execute();
                $alreadySubmitted = true;
                $message = "✅ Attendance submitted successfully. Waiting for approval.";
            }
        }
    } else {
        $message = "⚠️ Please select an event.";
    }
}

$history = $conn->prepare("
    SELECT ar.*, e.title, e.date 
    FROM attendance_requests ar
    JOIN events e ON ar.event_id = e.id
    WHERE ar.user_id = ?
    ORDER BY e.date DESC
");
$history->bind_param("i", $user_id);
$history->execute();
$attendance_history = $history->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Attendance</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #1e40af;
        }
        select, textarea, button {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }
        button {
            background-color: #2563eb;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #1e3a8a;
        }
        .message, .notif {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            background-color: #dbeafe;
            color: #1e3a8a;
        }
        table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
        }
        .approved { color: green; font-weight: bold; }
        .pending { color: orange; font-weight: bold; }
        .denied { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>

    <?php foreach ($notifications as $notif): ?>
        <div class="notif"><?php echo $notif; ?></div>
    <?php endforeach; ?>


    <?php if (!$alreadySubmitted): ?>
        <form method="post">
            <label for="event_id">Choose an event:</label>
            <select name="event_id" id="event_id" required>
                <option value="">-- Select Event --</option>
                <?php while ($event = $events->fetch_assoc()): ?>
                    <option value="<?= $event['id'] ?>">
                        <?= htmlspecialchars($event['title']) ?> (<?= date('F d, Y', strtotime($event['date'])) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="activity_description">What activities did you participate in? (Please describe):</label>
            <textarea name="activity_description" id="activity_description" rows="4" required></textarea>

            <button type="submit" name="submit">Submit Attendance</button>
        </form>
    <?php endif; ?>


    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    
    <h3>Your Attendance History</h3>
    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>Status</th>
                <th>Activity Description</th> 
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $attendance_history->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= date('F d, Y', strtotime($row['date'])) ?></td>
                    <td class="<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['activity_description']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
