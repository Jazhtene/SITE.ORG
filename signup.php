<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>

  
    <div id="signup-section" class="signup-scroll-wrapper">
        <h2>Sign Up</h2>
        <form action="signup_action.php" method="POST">

            Full Name: <input type="text" name="name" required><br>

            Age: <input type="number" name="age" min="10" max="100" required><br>

            Sex:
            <select name="sex" required>
                <option value="">-- Select Sex --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Prefer not to say">Prefer not to say</option>
            </select><br>

            Birthdate: <input type="date" name="birthdate" required><br>

            Address: <input type="text" name="address" required><br>

            Contact Number: <input type="text" name="contact_number" pattern="[0-9]{11}" placeholder="e.g. 09XXXXXXXXX" required><br>

            Student ID: <input type="text" name="student_id" required><br>

            <label for="course">Course:</label>
<select name="course" required>
    <option value="">-- Select Course --</option>
    <option value="IT">IT</option>
    <option value="BFPT">BFPT</option>
    <option value="EDUC">EDUC</option>
</select><br>


            Year Level:
            <select name="year_level" required>
                <option value="">-- Select Year Level --</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
            </select><br>

            Section:
            <select name="section" required>
                <option value="">-- Select Section --</option>
                <option value="A">Section A</option>
                <option value="B">Section B</option>
                <option value="C">Section C</option>
                <option value="D">Section D</option>
                <option value="E">Section E</option>
                <option value="F">Section F</option>
            </select><br>

            Email: <input type="email" name="email" required><br>
            Password: <input type="password" name="password" required><br>

            Role:
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select><br>

            <button type="submit">Sign Up</button>
        </form>
    </div>

    <script>
    function scrollToSignup() {
        const signup = document.getElementById("signup-section");
        signup.scrollIntoView({ behavior: "smooth" });
    }
    </script>

</body>
</html>
