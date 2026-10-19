<?php
include "includes/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    // Validation
    if ($full_name == "" || $email == "" || $password == "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password != $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Is the email already registered?
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "This email is already registered.";
        } else {
            // Save the new user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $full_name, $email, $hashed);
            mysqli_stmt_execute($stmt);
            $success = "Registration successful! You can now log in.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - SportsHub</title>
</head>
<body>
    <h1>Register</h1>

    <?php if ($error != "") { echo "<p style='color:red;'>$error</p>"; } ?>
    <?php if ($success != "") { echo "<p style='color:green;'>$success</p>"; } ?>

    <form method="POST" action="register.php">
        <label>Full Name</label><br>
        <input type="text" name="full_name"><br><br>

        <label>Email</label><br>
        <input type="text" name="email"><br><br>

        <label>Password</label><br>
        <input type="password" name="password"><br><br>

        <label>Confirm Password</label><br>
        <input type="password" name="confirm_password"><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>