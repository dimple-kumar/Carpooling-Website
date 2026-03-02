<?php
session_start();
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $checkSql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $checkResult = $conn->query($checkSql);

        if ($checkResult && $checkResult->num_rows > 0) {
            $message = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['username'] = $username;
                header("Location: index.html");
                exit();
            } else {
                $message = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Car Pooling Service</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body class="page-background">
    <div class="login-container">
        <h2>Register</h2>
        <?php if ($message): ?>
            <p style="color:red; text-align:center;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required />

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required />

            <input type="submit" value="Register" />
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
