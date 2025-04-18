<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include('db.php');

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password != $confirm_password) {
        $error_message = "New password and confirmation do not match.";
    } else {
        $query = "SELECT password FROM students WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];

            if (password_verify($current_password, $stored_password)) {
                
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                
                $update_query = "UPDATE students SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("si", $hashed_password, $user_id);

                if ($update_stmt->execute()) {
                    $success_message = "Password successfully updated!";
                } else {
                    $error_message = "Failed to update password. Please try again.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
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

        .form-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .form-container h2 {
            text-align: center;
            color: #1e3a8a;
        }

        .form-container label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container input[type="submit"] {
            background-color: #1e3a8a;
            color: white;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #2563eb;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="attendance_request.php">Apply Attendance</a>
    <a href="view_event.php">View Event</a>
    <a href="view_balance.php">View Balance</a>
    <a href="logout.php">Logout</a>
    <a href="view_profile.php">View Profile</a>
</div>

<div class="content">
    <div class="form-container">
        <h2>Change Password</h2>
        
        <?php
        if (isset($error_message)) {
            echo "<div class='message'>$error_message</div>";
        }

        if (isset($success_message)) {
            echo "<div class='message success'>$success_message</div>";
        }
        ?>

        <form method="POST" action="change_password.php">
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" id="current_password" required>

            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <input type="submit" value="Update Password">
        </form>
    </div>
</div>

</body>
</html>
