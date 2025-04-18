<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$eventsQuery = "SELECT id, title FROM events";
$eventsResult = $conn->query($eventsQuery);

$selected_event_id = $_POST['event_id'] ?? null;
$selected_year_level = $_POST['year_level'] ?? null;

$presentList = [];
$absentList = [];

if ($selected_event_id && $selected_year_level) {
    
    $presentQuery = "
        SELECT s.name, s.section, s.course, s.year_level
        FROM students s
        INNER JOIN attendance_requests a ON s.id = a.user_id
        WHERE a.event_id = ? AND (a.status = 'present' OR a.status = 'approved') AND s.year_level = ?
    ";
    $stmt = $conn->prepare($presentQuery);
    $stmt->bind_param("is", $selected_event_id, $selected_year_level);
    $stmt->execute();
    $presentResult = $stmt->get_result();
    while ($row = $presentResult->fetch_assoc()) {
        $presentList[] = $row;
    }
    $stmt->close();

    $absentQuery = "
        SELECT s.name, s.section, s.course, s.year_level
        FROM students s
        LEFT JOIN attendance_requests a 
            ON s.id = a.user_id AND a.event_id = ?
        WHERE s.year_level = ? AND (a.status IS NULL OR (a.status NOT IN ('present', 'approved')))
    ";
    $stmt = $conn->prepare($absentQuery);
    $stmt->bind_param("is", $selected_event_id, $selected_year_level);
    $stmt->execute();
    $absentResult = $stmt->get_result();
    while ($row = $absentResult->fetch_assoc()) {
        $absentList[] = $row;
    }
    $stmt->close();
}

$maxRows = max(count($presentList), count($absentList));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Summary by Event & Year Level</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
        }

        h2 {
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        select, button {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #1e3a8a;
            color: white;
        }

        .present {
            background-color: #dcfce7;
        }

        .absent {
            background-color: #fee2e2;
        }
    </style>
</head>
<body>

<h2>Attendance Summary</h2>

<form method="POST">
    <label for="event_id">Select Event:</label>
    <select name="event_id" id="event_id" required>
        <option value="">-- Choose an Event --</option>
        <?php
        $eventsResult = $conn->query("SELECT id, title FROM events");
        while ($event = $eventsResult->fetch_assoc()):
        ?>
            <option value="<?= $event['id']; ?>" <?= ($selected_event_id == $event['id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($event['title']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="year_level">Select Year Level:</label>
    <select name="year_level" id="year_level" required>
        <option value="">-- Choose Year Level --</option>
        <?php
        $years = ["1st Year", "2nd Year", "3rd Year", "4th Year"];
        foreach ($years as $year):
        ?>
            <option value="<?= $year; ?>" <?= ($selected_year_level == $year) ? 'selected' : ''; ?>>
                <?= $year; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">View Attendance</button>
</form>

<?php if ($selected_event_id && $selected_year_level): ?>
    <table>
        <tr>
            <th>Present / Approved Students</th>
            <th>Absent / Pending / No Record</th>
        </tr>
        <?php for ($i = 0; $i < $maxRows; $i++): ?>
            <tr>
                <td class="present">
                    <?php if (isset($presentList[$i])): ?>
                        <?= htmlspecialchars($presentList[$i]['name']) . " - " . htmlspecialchars($presentList[$i]['course']) . " " . htmlspecialchars($presentList[$i]['year_level']) . htmlspecialchars($presentList[$i]['section']); ?>
                    <?php endif; ?>
                </td>
                <td class="absent">
                    <?php if (isset($absentList[$i])): ?>
                        <?= htmlspecialchars($absentList[$i]['name']) . " - " . htmlspecialchars($absentList[$i]['course']) . " " . htmlspecialchars($absentList[$i]['year_level']) . htmlspecialchars($absentList[$i]['section']); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endfor; ?>
    </table>
<?php endif; ?>

</body>
</html>
