<?php
include('db.php');

$search_student_id = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_student_id = $_POST['search_student_id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $student_id = $_POST['id'];

        $get_id_stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
        $get_id_stmt->bind_param("i", $student_id);
        $get_id_stmt->execute();
        $get_id_result = $get_id_stmt->get_result();

        if ($get_id_result->num_rows > 0) {
            $student_row = $get_id_result->fetch_assoc();
            $internal_id = $student_row['id'];

            if (isset($_POST['update'])) {
                $balance = $_POST['balance'];
                $insert_query = "INSERT INTO balances (student_id, balance) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("id", $internal_id, $balance);
                if ($stmt->execute()) {
                    $success_message = "✅ Balance updated successfully!";
                }
            }

            if (isset($_POST['clear'])) {
                $insert_query = "INSERT INTO balances (student_id, balance) VALUES (?, 0)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("i", $internal_id);
                if ($stmt->execute()) {
                    $success_message = "✅ Balance cleared successfully!";
                }
            }
        } else {
            echo "<script>alert('Student not found.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Balance Management</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="back-to-dashboard">
    <a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

<h1>Manage Student Balances</h1>

<?php if (!empty($success_message)) : ?>
    <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0;">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<div class="search-bar-container">
    <form method="POST">
        <input type="number" class="search-bar" name="search_student_id" placeholder="Search by Student ID" value="<?php echo $search_student_id; ?>">
        <button type="submit" name="search" class="search-btn">Search</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Current Balance</th>
            <th>Update Balance</th>
            <th>Clear Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($search_student_id) {
            $sql = "SELECT s.student_id, s.name, 
                        COALESCE((
                            SELECT b.balance 
                            FROM balances b 
                            WHERE b.student_id = s.id 
                            ORDER BY b.id DESC 
                            LIMIT 1
                        ), 0) AS balance
                    FROM students s
                    WHERE s.student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $search_student_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql = "SELECT s.student_id, s.name, 
                        COALESCE((
                            SELECT b.balance 
                            FROM balances b 
                            WHERE b.student_id = s.id 
                            ORDER BY b.id DESC 
                            LIMIT 1
                        ), 0) AS balance
                    FROM students s";
            $result = $conn->query($sql);
        }

        while ($row = $result->fetch_assoc()) {
            $student_id = $row['student_id'];
            $name = $row['name'];
            $balance = $row['balance'];

            echo "<tr>";
            echo "<td>$student_id</td>";
            echo "<td>$name</td>";
            echo "<td>₱" . number_format($balance, 2) . "</td>";
            echo "<td>
                    <form method='POST'>
                        <input type='number' name='balance' value='$balance' step='0.01' required>
                        <input type='hidden' name='id' value='$student_id'>
                        <button class='button update-btn' name='update'>Update</button>
                    </form>
                  </td>";
            echo "<td>
                    <form method='POST'>
                        <input type='hidden' name='id' value='$student_id'>
                        <button class='button clear-btn' name='clear'>Clear</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
