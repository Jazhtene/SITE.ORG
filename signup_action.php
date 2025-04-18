<?php
session_start();
include 'db.php';


$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];
$age = $_POST['age'];
$sex = $_POST['sex'];
$address = $_POST['address'];
$contact_number = $_POST['contact_number'];
$student_id = $_POST['student_id'];
$course = $_POST['course'];
$year_level = $_POST['year_level'];
$section = $_POST['section'];


if ($role !== 'student' && $role !== 'admin') {
    die("Invalid role selected.");
}
$stmt_users = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) 
    VALUES (?, ?, ?, ?, NOW())");
$stmt_users->bind_param("ssss", $name, $email, $password, $role);
$stmt_users->execute();

$table = $role === 'student' ? 'students' : 'admins';

$stmt = $conn->prepare("INSERT INTO $table 
    (name, email, password, role, created_at, age, sex, address, contact_number, student_id, course, year_level, section) 
    VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssisssssss", $name, $email, $password, $role, $age, $sex, $address, $contact_number, $student_id, $course, $year_level, $section);

if ($stmt->execute()) {
    echo "Registration successful! You can now <a href='login.php'>log in</a>.";
} else {
    echo "Error: " . $stmt->error;
}
?>
