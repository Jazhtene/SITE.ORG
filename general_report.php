<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$eventsResult = $conn->query("SELECT id, title, date FROM events ORDER BY date DESC");

$selected_event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;

$report = [];
$totalPresent = 0;
$totalAbsent = 0;

if ($selected_event_id) {
    $query = "
        SELECT 
            s.name AS student_name,
            e.description AS activity_description,
            e.date,
            COALESCE(a.status, 'absent') AS status
        FROM students s
        CROSS JOIN events e
        LEFT JOIN attendance_requests a ON s.id = a.user_id AND e.id = a.event_id
        WHERE e.id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $selected_event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
        if ($row['status'] === 'present' || $row['status'] === 'approved') {
            $totalPresent++;
        } else {
            $totalAbsent++;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>General Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            padding: 40px;
        }

        h2 {
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        select {
            padding: 8px;
            font-size: 16px;
            margin-right: 10px;
        }

        .btn {
            padding: 8px 16px;
            font-size: 16px;
            background-color: #1e3a8a;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #3749b2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background-color: #1e3a8a;
            color: white;
        }

        .present {
            background-color: #d1fae5;
        }

        .absent {
            background-color: #fee2e2;
        }

        .totals {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>📊 General Attendance Report</h2>

<form method="GET">
    <label for="event_id"><strong>Select Event:</strong></label>
    <select name="event_id" id="event_id" required>
        <option value="">-- Choose Event --</option>
        <?php while ($event = $eventsResult->fetch_assoc()): ?>
            <option value="<?= $event['id'] ?>" <?= ($selected_event_id == $event['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($event['title']) ?> (<?= $event['date'] ?>)
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit" class="btn">View Report</button>
</form>

<?php if ($selected_event_id): ?>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Activity Description</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($report) > 0): ?>
                <?php foreach ($report as $row): ?>
                    <tr class="<?= $row['status'] === 'present' || $row['status'] === 'approved' ? 'present' : 'absent' ?>">
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['activity_description']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td>
                            <?= ($row['status'] === 'present' || $row['status'] === 'approved') ? 'Present' : 'Absent' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center;">No students found for this event.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="totals">
        <strong>Total Present:</strong> <?= $totalPresent ?><br>
        <strong>Total Absent:</strong> <?= $totalAbsent ?>
    </div>
<?php endif; ?>

</body>
</html>
