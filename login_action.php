<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'siteattendance'); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

function checkUser($conn, $email, $password, $table, $role) {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } else if ($role === 'student') {
                if (strtolower($user['course']) === 'it') {
                    header("Location: student_dashboard.php");
                } else {
                    showError("❌ Access denied. Only IT students are allowed.");
                }
            }
            exit();
        }
    }
    return false;
}


function showError($message) {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login Failed</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #f2f2f2;
                font-family: Arial, sans-serif;
            }
            .error-box {
                background-color: #ffe0e0;
                color: #b30000;
                padding: 25px 40px;
                border-radius: 10px;
                border: 1px solid #ff4d4d;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                font-size: 18px;
                text-align: center;
            }
            .back-btn {
                margin-top: 15px;
                display: inline-block;
                background-color: #007BFF;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                font-size: 14px;
            }
            .back-btn:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="error-box">
            ' . $message . '
            <br><br>
            <a class="back-btn" href="login.php">Back to Login</a>
        </div>
    </body>
    </html>';
}


if (checkUser($conn, $email, $password, 'admins', 'admin')) {
    exit();
}


if (checkUser($conn, $email, $password, 'students', 'student')) {
    exit();
}


showError("❌ Invalid email or password.");
?>
