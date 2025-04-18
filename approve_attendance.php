<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle approve or reject action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $attendance_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action !== 'approve' && $action !== 'reject') {
        header("Location: approve_attendance.php");
        exit();
    }

    $status = $action === 'approve' ? 'approved' : 'rejected';

    // Get user_id for notification
    $getUser = $conn->prepare("SELECT user_id FROM attendance_requests WHERE id = ?");
    $getUser->bind_param("i", $attendance_id);
    $getUser->execute();
    $userResult = $getUser->get_result();
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['user_id'];

    // Update the attendance request
    $update = $conn->prepare("UPDATE attendance_requests SET status = ? WHERE id = ?");
    $update->bind_param("si", $status, $attendance_id);
    $update->execute();

    // Prepare notification
    $message = $status === 'approved'
        ? "✅ Your attendance has been approved!"
        : "❌ Your attendance has been rejected.";

    $notify = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $notify->bind_param("is", $user_id, $message);
    $notify->execute();

    header("Location: approve_attendance.php");
    exit();
}

// Query for pending requests
$query = "
    SELECT ar.id, ar.date, s.name, s.id AS student_id
    FROM attendance_requests ar
    JOIN students s ON ar.user_id = s.id
    WHERE ar.status = 'pending'
    ORDER BY ar.date DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Attendance - Admin</title>
    <link rel="stylesheet" href="style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <div class="navbar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="approve_attendance.php" class="active">Approve Attendance</a>
        <a href="balance.php">Balance</a>
        <a href="event.php">Event</a>
        <a href="view_attendance.php">View Attendance</a>
        <a href="general_report.php">General Report</a>
    </div>


    <a href="admin_dashboard.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>


    <div class="content">
        <h2>Pending Attendance Requests</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td>
                                <a class="action-button approve" href="?action=approve&id=<?php echo $row['id']; ?>">Approve</a>
                                <a class="action-button deny" href="?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending attendance requests found.</p>
        <?php endif; ?>
    </div>

</body>
</html>
