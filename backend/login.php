
<?php
require 'config.php';

// Start session to persist user if needed
session_start();

// Get form data
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// Connect to database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Location: ../login.html?error=Database connection failed");
    exit();
}

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    // User exists, try to log in
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['username'] = $username;
        header("Location: ../login.html?message=Welcome back, $username!");
    } else {
        header("Location: ../login.html?error=Incorrect password");
    }
} else {
    // Register new user
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $hash]);
        $_SESSION['username'] = $username;
        header("Location: ../login.html?message=Account created for $username!");
    } catch (PDOException $e) {
        header("Location: ../login.html?error=Registration failed");
    }
}
exit();
?>
