<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$today = date("Y-m-d");
$conn->query("UPDATE events SET status = 'active' WHERE date = '$today'");
$conn->query("UPDATE events SET status = 'inactive' WHERE date != '$today'");

$query = "SELECT * FROM events ORDER BY date ASC";
$events = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $event_id = $_POST['event_id'];
    $status = $_POST['status'];
    $title = $_POST['title'];
    $date = $_POST['date'];

    $update_query = $conn->prepare("UPDATE events SET title = ?, date = ?, status = ? WHERE id = ?");
    $update_query->bind_param("sssi", $title, $date, $status, $event_id);
    if ($update_query->execute()) {
        $message = "✅ Event updated successfully!";
    } else {
        $message = "❌ Failed to update event.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $insert_query = $conn->prepare("INSERT INTO events (title, date, status) VALUES (?, ?, ?)");
    $insert_query->bind_param("sss", $title, $date, $status);
    if ($insert_query->execute()) {
        $message = "✅ New event added successfully!";
    } else {
        $message = "❌ Failed to add new event.";
    }
}

// Delete event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $event_id = $_POST['event_id'];

    $delete_query = $conn->prepare("DELETE FROM events WHERE id = ?");
    $delete_query->bind_param("i", $event_id);
    if ($delete_query->execute()) {
        $message = "✅ Event deleted successfully!";
    } else {
        $message = "❌ Failed to delete event.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Events</title>
    <link rel="stylesheet" href="styless.css"> 
</head>
<body>

<h2>Manage Events</h2>

<?php if (isset($message)): ?>
    <div style="color: green;"><?php echo $message; ?></div>
<?php endif; ?>

<h3>Add New Event</h3>
<form method="POST">
    <label for="title">Event Title:</label><br>
    <input type="text" name="title" id="title" required><br><br>
    <label for="date">Event Date:</label><br>
    <input type="date" name="date" id="date" required><br><br>
    <label for="status">Event Status:</label><br>
    <select name="status" id="status" required>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select><br><br>
    <button type="submit" name="insert">Add Event</button>
</form>

<h3>Current Events</h3>
<table border="1">
    <thead>
        <tr>
            <th>Event Title</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $events->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo date('F d, Y', strtotime($row['date'])); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                    <input type="date" name="date" value="<?php echo date('Y-m-d', strtotime($row['date'])); ?>" required>
                    <select name="status" required>
                        <option value="active" <?php echo $row['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $row['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                    <button type="submit" name="update">Update</button>
                </form>

                <form method="POST" style="display: inline;">
                    <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
